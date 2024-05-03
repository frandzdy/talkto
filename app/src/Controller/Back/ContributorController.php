<?php

namespace App\Controller\Back;

use App\Entity\Contributor;
use App\Form\Front\ContributorType;
use App\Service\ContributorManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gestion des utilisateurs.
 */
#[Route(path: '/contributors', name: 'contributors')]
class ContributorController extends AbstractController
{
    final public const USERS_PER_PAGE = 20;

    /**
     * Liste des utilisateurs.
     */
    #[Route(path: '', name: '', methods: ['GET'])]
    public function list(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 0) > 0 ? $request->query->getInt('page') : 1;
        $query = $em->getRepository(Contributor::class)->buildSearchQuery();

        $paginator = $paginator->paginate(
            $query,
            $page,
            self::USERS_PER_PAGE,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'c.fullname',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'ASC',
                PaginatorInterface::DISTINCT => false,
            ]
        );

        return $this->render(
            'back/contributor/list.html.twig',
            [
                'contributors' => $paginator,
            ]
        );
    }

    /**
     * Création / Edition d'un utilisateur.
     */
    #[Route(path: '/new', name: '_new', methods: ['GET', 'POST'])]
    #[Route(path: '/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ContributorManager $contributorManager,
        ?Contributor $contributor = null
    ): Response {
        $validationGroups = ['Default'];
        if (!$contributor instanceof Contributor) {
            $contributor = new Contributor();
            $validationGroups[] = 'creation';
            $formAction = $this->generateUrl('back_contributors_new');
        } else {
            $formAction = $this->generateUrl('back_contributors_edit', ['id' => $contributor->getId()]);
        }

        $form = $this->createForm(
            ContributorType::class,
            $contributor,
            ['validation_groups' => $validationGroups, 'action' => $formAction]
        );
        if ($form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $contributorManager->createOrUpdate($contributor);
            $this->addFlash('success', 'Modifications enregistrées avec succès.');

            return $this->json(['success' => true, 'redirectUrl' => $this->generateUrl('back_contributors')]);
        }

        return $this->render(
            'back/contributor/edit.html.twig',
            [
                'form' => $form,
            ]
        );
    }

    /**
     * Suppression d'un utilisateur.
     */
    #[Route(path: '/{id}/delete', name: '_delete', methods: ['POST'])]
    public function delete(ContributorManager $contributorManager, Contributor $contributor): Response
    {
        if (!$contributor->isDeletable() || $this->getUser() == $contributor) {
            throw $this->createAccessDeniedException();
        }

        try {
            $contributorManager->delete($contributor);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('back_contributors');
    }
}
