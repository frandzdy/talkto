<?php

namespace App\Controller;

use App\Entity\Matchs;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AdresseApi;
use App\Service\MatchManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
class MatchController extends AbstractController
{
    #[Route('/match/{token}', name: 'app_match')]
    public function match(string $token, UserRepository $userRepository): Response
    {

        return $this->render(
            'match/match.html.twig',
            [
                'userMatch' => $userRepository->findOneBy(['token' => $token]),
                'user' => $this->getUser()
            ]
        );
    }

    #[Route('/liste-match', name: 'app_match_list', options: ['expose' => true], methods: ['GET'])]
    public function list(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $res = [];
        $user->getMatchs()->filter(function (Matchs $match) use (&$res) {
            if (!$match->getIsRead()) {
                $res[] = $match->getUserMatch();
            }
        });
        $nbPage = ceil(count($res) / 3);
        $page = $request->query->getInt('page', 1);
        if ($page < $nbPage) {
            $nextPage = $page + 1;
        } else {
            $nextPage = null;
        }

        $pagination = $paginator->paginate(
            $res, /* query NOT result */
            $page, /*page number*/
            3 /*limit per page*/
        );

        return $this->render(
            'match/list.html.twig',
            [
                'matchs' => $pagination,
                'user' => $user,
                'nextPage' => $nextPage
            ]
        );
    }

    #[Route('/liste-match-secret', name: 'app_match_me')]
    public function listMatch(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();
        if ($user->getStripeStatus() !== 'active') {

            return $this->redirectToRoute('user_edit');
        }
        $res = $userRepository->getMatchingFromOtherUsers($user);
        $nbPage = ceil(count($res) / 3);
        $page = $request->query->getInt('page', 1);
        if ($page < $nbPage) {
            $nextPage = $page + 1;
        } else {
            $nextPage = null;
        }

        $pagination = $paginator->paginate(
            $res, /* query NOT result */
            $page, /*page number*/
            3 /*limit per page*/
        );

        return $this->render(
            'match/list.html.twig',
            [
                'matchs' => $pagination,
                'user' => $user,
                'nextPage' => $nextPage
            ]
        );
    }

    #[Route('/liste-match-suivant/{step}', name: 'app_match_list_next', options: ['expose' => true], methods: ['POST'])]
    public function listNext($step = null): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $res = [];
        $user->getMatchs()->filter(function (Matchs $match) use (&$res) {
            if (!$match->getIsRead()) {
                $res[] = $match->getUserMatch();
            }
        });
        $viewResult = $this->renderView(
            'match/_include_match.html.twig',
            [
                'matchs' => array_splice($res, $step, 3),
                'user' => $user
            ]
        );

        return $this->json(
            [
                'success' => true,
                'response' =>
                    [
                        'view' => $viewResult,
                        'step' => $step
                    ]
            ]
        );
    }

    #[Route('/generate-matches', name: 'app_generate_matches', options: ['expose' => true], methods: "POST")]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function generateMatches(MatchManager $matchManager): Response
    {
        // on récupère la liste des personnes selon le lieu où l'on se trouve
        $matchs = $matchManager->getMatchs($this->getUser());

        return $this->json(['success' => true, 'matchs' => $matchs], Response::HTTP_OK);
    }

    #[Route('/ajout-match/{token}', name: 'app_add_match', options: ["expose" => true], methods: "POST")]
    public function addMatch(
        string         $token,
        UserRepository $userRepository,
        LoggerInterface $logger,
        MatchManager   $matchManager
    ): Response {
        $responseMatching = null;
        try {
            $userConnected = $this->getUser();
            $match = $userRepository->findOneBy(['token' => $token]);

            $responseMatching = $matchManager->addMatch($userConnected, $match);

            return $this->json(['success' => true, 'response' => $responseMatching]);

        } catch (\Exception $e) {
            $logger->alert('Erreur lors de la suppression du match : ' . $e->getMessage());
        }

        return $this->json(['success' => false, 'response' => $responseMatching], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Supprime le match de la liste des matchs de l'utilisateur C
     */
    #[Route('/supprimer-match/{token}', name: 'app_remove_match', methods: "POST")]
    public function removeMatch(
        string                 $token,
        EntityManagerInterface $entityManager,
        UserRepository         $userRepository,
        Logger                 $logger
    ): Response {
        try {
            $userConnected = $this->getUser();
            $match = $userRepository->findOneBy(['token' => $token]);
            // on récupère la liste des matchs afin de retirer celui que l'on ne veut plus voir
            $userConnected->getMatchs()->filter(function (User $userMatch) use ($match, $userConnected) {
                if ($userMatch === $match) {
                    $userConnected->removeMatch($match);
                    $userConnected->addMyUnWantedMatch($match);
                }
            });
            $entityManager->flush();
        } catch (\Exception $e) {
            $logger->alert('Erreur lors de la suppression du match : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_match_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche le compte à rebours pour le temps restant avant la reprise du nb de spamiz
     */
    #[Route('/refuse-show-match', name: 'app_refuse_match', methods: "POST")]
    public function refuseMatch(): Response {

        return $this->render('match/countdown.html.twig');
    }
}
