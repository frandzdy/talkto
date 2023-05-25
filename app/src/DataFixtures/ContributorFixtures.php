<?php

namespace App\DataFixtures;

use App\Entity\Contributor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Intialisation d'un admin en back
 */
class ContributorFixtures extends Fixture
{
    /**
     * Constructor.
     */
    public function __construct(private UserPasswordEncoderInterface $passwordEncoder)
    {
    }

    /**
     * <@inheritDoc>
     */
    public function load(ObjectManager $manager)
    {
        $contributor = new Contributor();
        $contributor->setEmail('zz_qualifelec@webnet.fr');
        $contributor->setPassword($this->passwordEncoder->encodePassword($contributor, 'contributorpass'));
        $contributor->setRole(Contributor::ROLE_SUPERADMIN);
        $contributor->setFullname("Super Administrateur");

        $manager->persist($contributor);

        $manager->flush();
    }
}
