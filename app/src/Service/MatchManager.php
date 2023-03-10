<?php

namespace App\Service;

use App\Command\ReinitSpamCommand;
use App\Entity\Discussion;
use App\Entity\Matchs;
use App\Entity\User;
use App\Repository\MatchsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class MatchManager
{
    public function __construct(
        private UserRepository         $userRepository,
        private MatchsRepository       $matchsRepository,
        private EntityManagerInterface $entityManager,
        private MailerManager          $mailerManager
    )
    {
    }

    /**
     * Créer la liste des personnes rencontrées
     */
    public function getMatchs(User $user): ?bool
    {
        // on recherche les utilisateurs ici
        $matchListUsers = $this->userRepository->getMatchingUsers($user);
        foreach ($matchListUsers as $matchListUser) {
            if (!$this->matchsRepository->checkIfMatchExist($user, $matchListUser[0])) {
                $match = new Matchs();
                $match->setUserMatch($matchListUser[0]);
                $match->setUser($user);
                $match->setIsRead(false);
                $user->addMatch($match);
            }
        }
        $this->entityManager->flush();

        return true;
    }

    /**
     * Ajout un match à l'utilisateur connecté
     * 0 Pas d'ajout
     * 1 Ajout
     * 2 Ajout et match mutuel
     */
    public function addMatch(User $userConnected, User $match): int
    {
        $response = 0;
        $canAdd = true;
        // on ajoute le match afin de retirer celui que l'on ne veut plus voir
        $userConnected->getMyMatchs()->filter(function (User $userMatch) use ($match, &$canAdd) {
            if ($userMatch === $match) {
                $canAdd = false;
            }
        });
        if ($canAdd) {
            $response = 1;
            $userConnected->addMyMatch($match);
            $isMatchMutual = false;
            // on regarde la liste des matchs de notre match afin de savoir si c'est réciproque
            $match->getMyMatchs()->filter(function (User $userMatch) use ($userConnected, &$isMatchMutual) {
                if ($userMatch === $userConnected) {
                    $isMatchMutual = true;
                }
            });
            // si c'est mutuel alors on crée une discussion et on ajoute chaque utilisateur
            if ($isMatchMutual) {
                $discussion = new Discussion();
                $discussion->setName($userConnected->getLastname() . ' | ' . $match->getLastname())
                    ->setToken(hash('sha256', random_bytes(32)))
                    ->addUser($userConnected)
                    ->addUser($match);
                $this->entityManager->persist($discussion);

                $userConnected->addMyUser($match);
                $response = 2;
                $varsMatch = [
                    'user' => $match,
                    'match' => $userConnected
                ];
                $varsUserConnected = [
                    'user' => $userConnected,
                    'match' => $match
                ];
                // on retire les utilisateurs afin qu'il ne se refasse pas un match
                $userConnected->removeMyMatch($match);
                $match->removeMyMatch($userConnected);
                // si l'utilisateur à l'option de recevoir des emails
                if ($userConnected->getHasNotificationMatch()) {
                    $this->mailerManager->sendMailNotification($userConnected->getEmail(), 'emails/match.html.twig', $varsUserConnected);
                }
                if ($match->getHasNotificationMatch()) {
                    $this->mailerManager->sendMailNotification($match->getEmail(), 'emails/match.html.twig', $varsMatch);
                }
            }
            // si l'utilisateur connecté n'as pas d'abonnement alors on décompte un nb de Spam
            if ($userConnected->getStripeStatus() !== "active") {
                $userConnected->setNumberSpam($userConnected->getNumberSpam() - 1);
                // si l'utilisateur n'as plus de spam alors on indique dans combien de temps il faudra lui remettre ses Spams
                // remis à l'état initial lors du passage du cron
                /**
                 * @see ReinitSpamCommand
                 */
                if ($userConnected->getNumberSpam() === 0) {
                    // on ajout 12h à l'heure actuelle
                    $userConnected->setLastUpdateNumberSpam((new \DateTime())->add(new \DateInterval('PT12H00S')));
                }
            }
            $currentMatch = $this->matchsRepository->findOneBy(['userMatch' => $match->getId()]);
            if ($currentMatch) {
                $userConnected->removeMatch($currentMatch);
                $this->entityManager->remove($currentMatch);
            }
            $this->entityManager->flush();
        }

        return $response;
    }

    public function isMatch(User $userConnected, User $match): bool
    {
        $isMatch = false;
        $userConnected->getMyMatchs()->filter(function (User $userMatch) use ($match, &$isMatch) {
            if ($userMatch === $match) {
                $isMatch = true;
            }
        });

        return $isMatch;
    }

    public function hasMatch(User $user): ?array
    {
        return $this->userRepository->getMatchingFromOtherUsers($user);
    }
}
