<?php
    
    namespace App\Twig;
    
    use App\Entity\Abonnement;
    use App\Entity\InterestExit;
    use App\Entity\InterestHobbies;
    use App\Entity\InterestSports;
    use App\Entity\User;
    use App\Service\MatchManager;
    use App\Service\UserManager;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\EntityManagerInterface;
    use JetBrains\PhpStorm\Pure;
    use Twig\Extension\AbstractExtension;
    use Twig\TwigFunction;
    
    /**
     * Extension Twig de l'application
     */
    class AppExtension extends AbstractExtension
    {
        /**
         */
        public function __construct(
            private UserManager $userManager,
            private MatchManager $matchManager
        ) {}
        
        /**
         * @inheritDoc
         */
        public function getFunctions(): array
        {
            return [
                new TwigFunction('getAbonnements', [$this, 'getAbonnements']),
                new TwigFunction('getCommunInterest', [$this, 'getCommunInterest']),
                new TwigFunction('getDistance', [$this, 'getDistance']),
                new TwigFunction('isAMatch', [$this, 'isAMatch']),
                new TwigFunction('hasAMatch', [$this, 'hasAMatch']),
                new TwigFunction('numberCommunMatch', [$this, 'numberCommunMatch']),
            ];
        }
        
        /**
         * Retourne les abonnements dans twig
         */
        public function getAbonnements(string $type): ?array
        {
            return Abonnement::getAvailableAbonnements($type);
        }
        /**
         * Retourne les abonnements dans twig
         */
        public function getDistance(User $userConnected, User $userMatch): ?float
        {
            return $this->userManager->distance(
                $userConnected->getLat(),
                $userConnected->getLon(),
                $userMatch->getLat(),
                $userMatch->getLon()
            );
        }
        /**
         * Retourne les points commun entre deux personnes
         */
        public function getCommunInterest(User $userConnected, User $userMatch, string $type)
        {
            $function = 'get' . $type;
            
            return $this->$function($userConnected, $userMatch);
        }
        
        #[Pure] private function getHobbies(User $userConnected, User $userMatch, bool $numberOfCommun = false): string|int
        {
            $response = '';
            $count = 0;
            foreach ($userMatch->getInterestHobbies() as $interestHobbie) {
                if (InterestHobbies::DO_IT_YOURSELF === $interestHobbie
                    && in_array(
                        InterestHobbies::DO_IT_YOURSELF,
                        $userConnected->getInterestHobbies(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Bricoler</li>';
                    $count++;
                } elseif (InterestHobbies::DRAWING === $interestHobbie
                    && in_array(
                        InterestHobbies::DRAWING,
                        $userConnected->getInterestHobbies(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Dessiner / Peindre</li>';
                    $count++;
                } elseif (InterestHobbies::GARDENING === $interestHobbie
                    && in_array(
                        InterestHobbies::GARDENING,
                        $userConnected->getInterestHobbies(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Jardiner/li>';
                    $count++;
                } elseif (InterestHobbies::MOVIES === $interestHobbie
                    && in_array(
                        InterestHobbies::MOVIES,
                        $userConnected->getInterestHobbies(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Cinéphile</li>';
                    $count++;
                } elseif (InterestHobbies::PICTURES === $interestHobbie
                    && in_array(
                        InterestHobbies::PICTURES,
                        $userConnected->getInterestHobbies(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Photographier</li>';
                    $count++;
                } elseif (InterestHobbies::READING === $interestHobbie
                    && in_array(
                        InterestHobbies::READING,
                        $userConnected->getInterestHobbies(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Lire</li>';
                    $count++;
                }
            }

            if ($numberOfCommun) {
                return $count;
            } else {
                return $response;
            }
        }

        /**
         *
         */
        #[Pure] private function getSports(User $userConnected, User $userMatch, bool $numberOfCommun = false): string|int
        {
            $response = '';
            $count = 0;
            foreach ($userMatch->getInterestSports() as $interestHobbie) {
                if (InterestSports::BIKE === $interestHobbie && in_array(
                        InterestSports::BIKE,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Vélo / VTT / Cross</li>';
                    $count++;
                } elseif (InterestSports::FISHING === $interestHobbie && in_array(
                        InterestSports::FISHING,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Pêcher</li>';
                    $count++;
                } elseif (InterestSports::FITNESS === $interestHobbie && in_array(
                        InterestSports::FITNESS,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Fitness / Musculation</li>';
                    $count++;
                } elseif (InterestSports::FOOTING === $interestHobbie && in_array(
                        InterestSports::FOOTING,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Footing</li>';
                    $count++;
                } elseif (InterestSports::MMA === $interestHobbie && in_array(
                        InterestSports::MMA,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Sports de combat</li>';
                    $count++;
                } elseif (InterestSports::SKI === $interestHobbie && in_array(
                        InterestSports::SKI,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Skier</li>';
                    $count++;
                } elseif (InterestSports::SPORT_COLLECTIF === $interestHobbie && in_array(
                        InterestSports::SPORT_COLLECTIF,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Sports collectifs</li>';
                    $count++;
                } elseif (InterestSports::SPORT_DRIVE === $interestHobbie && in_array(
                        InterestSports::SPORT_DRIVE,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Sports automobiles</li>';
                    $count++;
                } elseif (InterestSports::SPORT_EXTREME === $interestHobbie && in_array(
                        InterestSports::SPORT_EXTREME,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Sports extrêmes</li>';
                    $count++;
                } elseif (InterestSports::SWIMMING === $interestHobbie && in_array(
                        InterestSports::SWIMMING,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Natation</li>';
                    $count++;
                } elseif (InterestSports::TENNIS === $interestHobbie && in_array(
                        InterestSports::TENNIS,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Tennis</li>';
                    $count++;
                } elseif (InterestSports::TREK === $interestHobbie && in_array(
                        InterestSports::TREK,
                        $userConnected->getInterestSports(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Randonnées</li>';
                    $count++;
                }
            }

            if ($numberOfCommun) {
                return $count;
            } else {
                return $response;
            }
        }
        
        #[Pure] private function getExists(User $userConnected, User $userMatch, bool $numberOfCommun = false): string|int
        {
            $response = '';
            $count = 0;
            foreach ($userMatch->getInterestExit() as $interestExit) {
                if (InterestExit::CINEMA === $interestExit && in_array(
                        InterestExit::CINEMA,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Cinéma</li>';
                    $count++;
                } elseif (InterestExit::CONCERT === $interestExit && in_array(
                        InterestExit::CONCERT,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Concerts</li>';
                    $count++;
                } elseif (InterestExit::CULTURAL_JOURNEY === $interestExit && in_array(
                        InterestExit::CULTURAL_JOURNEY,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Sortie culturelle</li>';
                    $count++;
                } elseif (InterestExit::JOURNEY === $interestExit && in_array(
                        InterestExit::JOURNEY,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Balades (mer, forêt, plage, etc)</li>';
                    $count++;
                } elseif (InterestExit::LOVING_WE === $interestExit && in_array(
                        InterestExit::LOVING_WE,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Week-ends en amoureux</li>';
                    $count++;
                } elseif (InterestExit::PARTY === $interestExit && in_array(
                        InterestExit::PARTY,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Fêtes \ soirée</li>';
                    $count++;
                } elseif (InterestExit::RESTAURANT === $interestExit && in_array(
                        InterestExit::RESTAURANT,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Restaurants</li>';
                    $count++;
                } elseif (InterestExit::SHOPPING === $interestExit && in_array(
                        InterestExit::SHOPPING,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Shopping</li>';
                    $count++;
                } elseif (InterestExit::SHOW === $interestExit && in_array(
                        InterestExit::SHOW,
                        $userConnected->getInterestExit(),
                        true
                    )) {
                    $response .= '<li class="list-group-item">Spectacles</li>';
                    $count++;
                }
            }

            if ($numberOfCommun) {
                return $count;
            } else {
                return $response;
            }
        }

        /**
         * Indique si le match en est un ou pas
         */
        public function isAMatch(User $userConnected, User $match): bool
        {
            return $this->matchManager->isMatch($userConnected, $match);
        }

        /**
         * Indique si le match a un match
         */
        public function hasAMatch(User $match): ?array
        {
            return $this->matchManager->hasMatch($match);
        }

        /**
         * Retourne le nombre de point commun entre deux personnes
         */
        public function numberCommunMatch(User $userConnected, User $match): int
        {
            return $this->getExists($userConnected, $match, true) +
                $this->getHobbies($userConnected, $match, true) +
                $this->getSports($userConnected, $match, true);
        }
    }
