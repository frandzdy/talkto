<?php

namespace App\DataFixtures;

use App\Entity\Contributor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Initialisation d'un admin en back.
 */
class ContributorFixtures extends Fixture
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly string $emailAdmin
    ) {}

    /**
     * <@inheritDoc>.
     */
    public function load(ObjectManager $manager): void
    {
        $contributor = new Contributor();
        $contributor->setEmail($this->emailAdmin);
        $contributor->setPassword($this->passwordEncoder->hashPassword($contributor, 'contributorpass'));
        $contributor->setRole(Contributor::ROLE_SUPER_ADMIN);
        $contributor->setFullname('Frandzdy Sanon');

        $manager->persist($contributor);

        $manager->flush();
    }
}
