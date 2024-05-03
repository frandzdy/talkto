<?php

namespace App\Service;

use App\Entity\Contributor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class ContributorManager
{
    /**
     * Constructeur.
     */
    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHasher) {}

    /**
     * Crée ou modifie un utilisateur.
     */
    public function createOrUpdate(Contributor $contributor): void
    {
        if (!$contributor->getId()) {
            $contributor->setRole(Contributor::ROLE_ADMIN);
        }

        if (null !== $contributor->plainPassword && '' !== $contributor->plainPassword && '0' !== $contributor->plainPassword) {
            $this->changeUserPassword($contributor, $contributor->plainPassword);
        }

        $this->em->persist($contributor);
        $this->em->flush();
    }

    /**
     * Modifie le mot de passe d'un utilisateur.
     */
    public function changeUserPassword(Contributor $contributor, string $plainPassword): void
    {
        $contributor->setPassword($this->passwordHasher->hashPassword($contributor, $plainPassword));

        $this->em->flush();
    }

    /**
     * Supprime un utilisateur.
     */
    public function delete(Contributor $contributor): void
    {
        $this->em->remove($contributor);
        $this->em->flush();
    }
}
