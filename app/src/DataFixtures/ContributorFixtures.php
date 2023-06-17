<?php

namespace App\DataFixtures;

use App\Entity\Contributor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Intialisation d'un admin en back
 *
 * // implements DependentFixtureInterface
 */
class ContributorFixtures extends Fixture
{
    /**
     * Constructor.
     */
    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
    }

    /**
     * <@inheritDoc>
     */
    public function load(ObjectManager $manager)
    {
        $contributor = new Contributor();
        $contributor->setEmail('zz_qualifelec@webnet.fr');
        $contributor->setPassword($this->passwordEncoder->hashPassword($contributor, 'contributorpass'));
        $contributor->setRole(Contributor::ROLE_SUPERADMIN);
        $contributor->setFullname("Super Administrateur");

        $manager->persist($contributor);

        $manager->flush();
    }
}
