<?php
    
    namespace App\Service;
    
    use App\Entity\Genre;
    use App\Entity\Picture;
    use App\Entity\User;
    use App\Repository\PictureRepository;
    use App\Repository\UserRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Psr\Log\LoggerInterface;
    
    class UserManager
    {
        public function __construct(
            private EntityManagerInterface $entityManager,
            private FileUploadManager $fileUploadManager,
            private StripeManager $stripeManager,
            private PictureRepository $pictureRepository,
            private UserRepository $userRepository,
            private LoggerInterface $logger,
            private AdresseApi $adresseApi,
        ) {}

        /**
         * Retourne un user prêt pour la création
         */
        public function createUser() : user
        {
            $user = (new User())
                ->setCreatedAt(new \DateTime())
                ->setStripeStatus('incomplete')
                ->setStripeCustomerId(null)
                ->setHasNotificationMatch(true)
                ->setHideAccount(false)
                ->setHideAccount24(false)
                ->setInterestGenre(Genre::CIVILITY_WOMEN)
                ->setToken(hash('sha256', random_bytes(32)));

            return $user;
        }

        /**
         * Créer ou met à jour un utilisateur
         */
        public function saveOrEditUser(User $user, $pictureFileDatas, $update = false): bool
        {
            $i = 0;
            foreach ($pictureFileDatas as $picture) {
                $fileName = $this->fileUploadManager->uploadFile('profile_picture', $picture);
                $pic = new Picture();
                $pic->setName($fileName);
                $pic->setToken(hash('sha256', random_bytes(32)));
                $user->addPicture($pic);
                if (!$user->getPrincipalPicture() && $i === 0) {
                    $user->setPrincipalPicture($fileName);
                }
                $this->entityManager->persist($pic);
                $i++;
            }
            if ($update) {
                $user->setUpdatedAt(new \DateTime());
            } else {
                $this->entityManager->persist($user);
            }
            // on regarde si on cache le compte pendant 24 H
            if ($user->getHideAccount24()) {
                $user->setHideAccountDate24((new \DateTime())->add(new \DateInterval('PT24H00S')));
            }
            $this->entityManager->flush();
            // si on n'a pas de compte stripe
            if (!$user->getStripeCustomerId()) {
                $customer = $this->stripeManager->createCustomer($user);
                $user->setStripeCustomerId($customer->id);
                $this->entityManager->flush();
            }

            return true;
        }
        
        /**
         * Supprime une photo du compte utilisateur serveur et bdd
         */
        public function deleteUserPicture(string $token, User $user): bool
        {
            try {
                $picture = $this->pictureRepository->findOneBy(['token' => $token]);
                // on supprime le fichier du serveur du compte
                $user->removePicture($picture);
                $this->fileUploadManager->removeFile('profile_picture', $picture->getName());
                $this->fileUploadManager->removeFileLiip('profil', $picture->getName());
                $this->fileUploadManager->removeFileLiip('profil_collection', $picture->getName());
                $this->fileUploadManager->removeFileLiip('miniature', $picture->getName());
                $this->fileUploadManager->removeFileLiip('match', $picture->getName());
                // si on a le même fichier que celui affiché dans la page profil alors on le retire
                if ($user->getPrincipalPicture() === $picture->getName()) {
                    $user->setPrincipalPicture(null);
                }
                $this->entityManager->remove($picture);
                $this->entityManager->flush();
                return true;
            } catch (\Exception $e) {
                $this->logger->alert("Erreur lors de la suppression de la photo de profile : " . $e->getMessage());
                
                return false;
            }
        }
        
        /**
         * Défini une photo en tant que photo de profil
         */
        public function addUserPictureToPrincipal(string $token, User $user): bool
        {
            try {
                $picture = $this->pictureRepository->findOneBy(['token' => $token]);
                $user->setPrincipalPicture($picture->getName());
                $this->entityManager->flush();
                
                return true;
            } catch (\Exception $e) {
                $this->logger->alert("Erreur lors de la définition de photo de profile : " . $e->getMessage());
                
                return false;
            }
        }
        
        /**
         * flush en base de données la création ou la mise à jour
         */
        public function saveUser(): void
        {
            $this->entityManager->flush();
        }
        
        /**
         * Récupère la liste de tous les user afin de regarder s'ils ont dépassé la limite afin
         * de réinitialiser leur compte.
         */
        public function reinitSpamUsers(): ?int
        {
            $users = $this->userRepository->getUsersToReinitAccount();
            $compteur = 0;
            foreach ($users as $user) {
                $user = $user[0];
                /**
                 * @var User $user
                 */
                $user->setNumberSpam(20);
                $user->setlastUpdateNumberSpam(null);
                $compteur++;
            }
            $this->entityManager->flush();
            
            return $compteur;
        }

        /**
         * Récupère la liste de tous les user afin de regarder s'ils ont dépassé la limite afin
         * de réinitialiser leur compte.
         */
        public function reactivateAccount(): ?int
        {
            $users = $this->userRepository->getUsersToReactivateAccount();
            $compteur = 0;
            foreach ($users as $user) {
                $user = $user[0];
                /**
                 * @var User $user
                 */
                $user->setHideAccount24(false);
                $user->setHideAccountDate24(null);
                $compteur++;
            }
            $this->entityManager->flush();

            return $compteur;
        }

        /**
         * Retourne la distance entre 2 points gps
         */
        public function distance($lat1, $lng1, $lat2, $lng2, $miles = false) : float
        {
            $pi80 = M_PI / 180;
            $lat1 *= $pi80;
            $lng1 *= $pi80;
            $lat2 *= $pi80;
            $lng2 *= $pi80;
    
            $r = 6372.797; // rayon moyen de la Terre en km
            $dlat = $lat2 - $lat1;
            $dlng = $lng2 - $lng1;
            $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin(
                    $dlng / 2) * sin($dlng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $km = $r * $c;
    
            return ($miles ? ($km * 0.621371192) : $km);
        }

        /**
         * check si on a pas de lat alors on la récupère avec la ville
         */
        public function checkCoord(User $user){
            if (!$user->getLat() && $user->getCity()) {
                $res = $this->adresseApi->searchAddress($user->getCity());
                $user->setLat($res['lat']);
                $user->setLon($res['lon']);
                $this->saveUser();
            }
        }
    }
