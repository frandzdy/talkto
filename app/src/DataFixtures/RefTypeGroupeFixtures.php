<?php
    
    namespace App\DataFixtures;
    
    use App\Entity\RefTypeGroupe;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Persistence\ObjectManager;
    
    class RefTypeGroupeFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            $refTypeGroupe = new RefTypeGroupe();
            $refTypeGroupe->setLibelle('IND');
            
            $manager->flush();
            
            $refTypeGroupe = new RefTypeGroupe();
            $refTypeGroupe->setLibelle('MULTI');
    
            $manager->flush();
        }
    }
