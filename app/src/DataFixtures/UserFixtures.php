<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Civility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Intialisation d'un admin en back.
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly string $emailSupport
    ) {}

    /**
     * <@inheritDoc>.
     */
    public function load(ObjectManager $manager): void
    {
        $country = $this->getReference(CountryFixtures::COUNTRY_FR);
        $user = new User();
        $user->setEmail($this->emailSupport);
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'supportpass'));
        $user->setRole(User::ROLE_SUPPORT);
        $user->setLastname('Reented')
            ->setFirstname('Support')
            ->setAddress('')
            ->setCity('')
            ->setPhone('')
            ->setZipCode('')
            ->setCountry($country)
            ->setGenre(Civility::MEN)
            ->setTerms(true)
        ;

        $manager->persist($user);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        // TODO: Implement getDependencies() method.
        return [
            CountryFixtures::class,
        ];
    }
}
