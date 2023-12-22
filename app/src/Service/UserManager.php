<?php

namespace App\Service;

use App\Entity\Country;
use App\Entity\Picture;
use App\Entity\User;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploadManager $fileUploadManager,
        private StripeManager $stripeManager,
        private PictureRepository $pictureRepository,
        private UserRepository $userRepository,
        private LoggerInterface $logger,
        private AdresseApi $adresseApi,
        private UserPasswordHasherInterface $passwordHasher,
        private MailerManager $mailer
    ) {
    }

    /**
     * Retourne un user prêt pour la création soit locataire, soit bailleur.
     */
    public function createUser(int $typeAccount = 1): user
    {
        $country = $this->entityManager->getRepository(Country::class)->findOneBy(['code' => 'FR']);

        return (new User())
            ->setRole(1 === $typeAccount ? User::ROLE_USER : User::ROLE_SELLER)
            ->setCountry($country);
    }

    /**
     * Créer ou met à jour un utilisateur.
     */
    public function saveOrEditUser(User $user, UploadedFile $pictureFileData = null, $isGuess = false): bool
    {
        if ($pictureFileData instanceof UploadedFile) {
            $fileName = $this->fileUploadManager->uploadFile('profile_picture', $pictureFileData);
            $pic = new Picture();
            $pic->setName($fileName);
            $this->entityManager->persist($pic);
            $user->setPicture($pic);
        }

        // si on est un compte invité
        if (!$user->getIsGuess() && !$user->getId() && $isGuess) {
            $user->setRole(User::ROLE_GUESS);
            $hashPassword = $this->passwordHasher->hashPassword($user, $user->getFirstname().$user->getLastname());
            $this->userRepository->upgradePassword($user, $hashPassword);
            // email de création de compte invitée
            $this->mailer->sendMailNotification(
                $user->getEmail(),
                'emails/create_guess.html.twig',
                [
                    'user' => $user,
                ]
            );
        }

        if (!$user->getId()) {
            $this->entityManager->persist($user);
        }

        // si on n'a pas de compte stripe
        if (!$user->getStripeCustomerId() && (User::ROLE_USER === $user->getRole())) {
            $customer = $this->stripeManager->createCustomer($user);
            $user->setStripeCustomerId($customer->id);
        } elseif (!$user->getStripeAccountId() && User::ROLE_SELLER === $user->getRole()) {
            $customer = $this->stripeManager->createCustomer($user);
            $user->setStripeCustomerId($customer->id);
            $account = $this->stripeManager->createAccount($user);
            $user->setStripeAccountId($account->id);
        }

        $this->checkCoord($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Supprime une photo du compte utilisateur serveur et bdd.
     */
    public function deleteUserPicture(string $token, User $user): bool
    {
        try {
            $picture = $this->pictureRepository->findOneBy(['token' => $token]);
            // on supprime le fichier du serveur du compte
            $user->setPicture(null);
            $this->fileUploadManager->removeFile('profile_picture', $picture->getName());
            $this->fileUploadManager->removeFileLiip('profil', $picture->getName());
            $this->fileUploadManager->removeFileLiip('profil_miniature', $picture->getName());

            $this->entityManager->remove($picture);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $exception) {
            $this->logger->alert('Erreur lors de la suppression de la photo de profile : '.$exception->getMessage());

            return false;
        }
    }

    /**
     * flush en base de données la création ou la mise à jour.
     */
    public function saveUser(): void
    {
        $this->entityManager->flush();
    }

    /**
     * Retourne la distance entre 2 points gps.
     */
    public function distance($lat1, $lng1, $lat2, $lng2, $miles = false): float
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
            $dlng / 2
        ) * sin($dlng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        return $miles ? ($km * 0.621371192) : $km;
    }

    /**
     * Check si on n'a pas de lat alors, on la récupère avec la ville.
     */
    public function checkCoord(User $user): void
    {
        if (!$user->getLat() && $user->getCity()) {
            $res = $this->adresseApi->searchUserAddress($user);
            $user->setLat($res['lat']);
            $user->setLon($res['lon']);
            $this->saveUser();
        }
    }

    /**
     * Supprime un utilisateur logiquement.
     */
    public function deleteCustomer(User $user): void
    {
        $user->setDeletedAt(new \DateTime());
        $this->entityManager->flush();
    }
}
