<?php
    
    namespace App\Entity;
    
    use App\Repository\UserRepository;
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\Common\Collections\Collection;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Validator\Constraints as Assert;
    
    /**
     * @ORM\Table()
     * @ORM\Entity(repositoryClass=UserRepository::class)
     * @UniqueEntity(fields={"email"}, message="E-mail déjà enregistré.")
     * @ORM\HasLifecycleCallbacks()
     */
    class User implements UserInterface, PasswordAuthenticatedUserInterface
    {
        use TraitTimestamp, TraitToken, TraitIntro;

        const ROLE_USER = 'ROLE_USER';
        const ROLE_ADMIN = 'ROLE_ADMIN';
        const ROLE_ABO_1 = 'ROLE_SPAM';
        const ROLE_ABO_2 = 'ROLE_SPAMMEUR';
        const ROLE_ABO_3 = 'ROLE_LOVER';
        /**
         * @ORM\Id
         * @ORM\GeneratedValue
         * @ORM\Column(type="integer")
         */
        private ?int $id;
        
        /**
         * L'e-mail de l'utilisateur
         *
         * @ORM\Column(type="string", length=180, unique=true)
         * @Assert\NotBlank(message="Information requise.")
         * @Assert\Email(message="Format E-mail incorrect.")
         */
        private ?string $email;
        
        /**
         * Ses différents role dans l'application pour défaut ROLE_USER
         *
         * @ORM\Column(type="json")
         */
        private ?array $roles = [];
        
        /**
         * le nom de famille de l'utilisateur
         *
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?string $lastname;
        
        /**
         * le prénom de l'utilisateur
         *
         * @ORM\Column(type="string", length=255)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?string $firstname;
        
        /**
         * l'âge de l'utilisateur
         *
         * @ORM\Column(type="integer")
         * @Assert\NotBlank(message="Information requise.")
         * @Assert\Range(min="18", max="120", notInRangeMessage="Vous devez avoir un âge compris entre {{ min }} et {{ max }} ans.")
         */
        private ?int $age;
        
        /**
         * la taille de l'utilisateur
         *
         * @ORM\Column(type="integer", options={"default":0})
         * @Assert\Range(min="0", max="300", notInRangeMessage="Vous devez avoir une taille comprise entre {{ min }} et {{ max }} cm.")
         */
        private ?int $size;
        
        /**
         * Le genre de l'utilisateur
         *
         * @ORM\Column(type="smallint", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?int $genre;

        /**
         * Ville de l'utilisateur
         *
         * @ORM\Column(type="text", nullable=true)
         */
        private ?string $city;

        /**
         * Les utilisateurs dont je suis en contact Match mutuel
         *
         * @ORM\ManyToMany(targetEntity=User::class)
         * @ORM\JoinTable(name="user_contact",
         *     joinColumns={@ORM\JoinColumn(name="me", referencedColumnName="id")},
         *     inverseJoinColumns={@ORM\JoinColumn(name="my_contact", referencedColumnName="id")}
         * )
         */
        private $myUsers;
        
        /**
         * Toutes les photos de l'utilisateur
         *
         * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="user", orphanRemoval=true, cascade={"persist"})
         */
        private $pictures;
    
        /**
         * Les différents groupe de discussion de l'utilisateur
         *
         * @ORM\ManyToMany(targetEntity="App\Entity\Discussion", mappedBy="users")
         */
        private $discussions;
    
        /**
         * @ORM\OneToMany(targetEntity="App\Entity\Notification", mappedBy="user", cascade={"persist","remove"})
         */
        private $notifications;
    
        /**
         * Les sorties que l'utilisateur souhaite rechercher
         *
         * @ORM\Column(type="json", nullable=true)
         */
        private $interestExit;
    
        /**
         * Les passions que l'utilisateur souhaite rechercher
         *
         * @ORM\Column(type="json", nullable=true)
         */
        private $interestHobbies;
    
        /**
         * Les sports que l'utilisateur souhaite rechercher
         *
         * @ORM\Column(type="json", nullable=true)
         */
        private $interestSports;
    
        /**
         * La distance de recherche de l'utilisateur
         *
         * @ORM\Column(type="integer", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?int $interestDistance;

        /**
         * L'age de préférence de recherche de l'utilisateur
         *
         * @ORM\Column(type="integer", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         * @Assert\Range(min="18", max="120", notInRangeMessage="L'âge doit être compris entre {{ min }} et {{ max }} ans.")
         */
        private ?int $interestAge;

        /**
         * A propos de l'utilisateur
         *
         * @ORM\Column(type="text", nullable=true)
         */
        private ?string $description;
    
        /**
         * la photo de profil de l'utilisateur
         *
         * @ORM\Column(type="string", nullable=true)
         */
        private $principalPicture;
        
        /**
         * La liste des messsages que l'utilisateur à envoyer
         *
         * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sender")
         */
        private $messagesSender;
    
        /**
         * Utilisateur que j'ai liké
         *
         * @ORM\ManyToMany(targetEntity=User::class)
         * @ORM\JoinTable(name="user_matchs",
         *     joinColumns={@ORM\JoinColumn(name="me", referencedColumnName="id")},
         *     inverseJoinColumns={@ORM\JoinColumn(name="my_match", referencedColumnName="id")}
         * )
         */
        private $myMatchs;
    
        /**
         * Utilisateur que j'ai pas liké
         *
         * @ORM\ManyToMany(targetEntity=User::class)
         * @ORM\JoinTable(name="user_unwanted_matchs",
         *     joinColumns={@ORM\JoinColumn(name="me", referencedColumnName="id")},
         *     inverseJoinColumns={@ORM\JoinColumn(name="my_unwanted_match", referencedColumnName="id")}
         * )
         */
        private $myUnWantedMatchs;
        
        /**
         * Latitude de la position de l'utilisateur
         *
         * @ORM\Column(type="float", nullable=true)
         */
        private ?string $lat = null;
    
        /**
         * Longitude de la position de l'utilisateur
         *
         * @ORM\Column(type="float", nullable=true)
         */
        private ?string $lon = null;

        /**
         * Match correspondant aux critière de cherche de l'utilisateur
         *
         * @ORM\OneToMany(targetEntity=Matchs::class, mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
         */
        private $matchs;
    
        /**
         * Affiche ou non l'age
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private ?bool $hideAge = null;
    
        /**
         * Affiche ou non la distance sur la fiche du profile
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private ?bool $hideDistance = null;

        /**
         * Affiche ou non le compte lors des recherches
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private ?bool $hideAccount = null;

        /**
         * Affiche ou non le compte lors des recherches pendant 24 heures
         *
         * @ORM\Column(type="boolean", nullable=true)
         */
        private ?bool $hideAccount24 = null;

        /**
         * Indique la date de la dernière mise en invisibilité du compte pendant 24 heures
         *
         * @ORM\Column(type="datetime", nullable=true)
         */
        private ?\DateTime $hideAccountDate24 = null;

        /**
         * Indique si l'utilisateur est fumeur ou non
         *
         * @ORM\Column(type="smallint", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?int $smoker;
    
        /**
         * Indique si l'utilisateur à des enfants ou non
         * @ORM\Column(type="smallint", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?int $children;

        /**
         * Indique si l'utilisateur souhaite rechercher des utilisateurs qui sont fumeur ou non
         *
         * @ORM\Column(type="smallint", nullable=false)
         */
        private ?int $interestGenre;

        /**
         * Indique si l'utilisateur souhaite rechercher des utilisateurs qui sont fumeur ou non
         *
         * @ORM\Column(type="smallint", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?int $interestSmoker;
    
        /**
         * Indique si l'utilisateur souhaite rechercher des utilisateurs qui ont des enfants ou non
         *
         * @ORM\Column(type="smallint", nullable=false)
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?int $interestChildren;
    
        /**
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private ?string $stripeCustomerId;
    
        /**
         * look_up
         * @ORM\Column(type="string", length=255, nullable=true)
         */
        private ?string $stripePlan;
        
        /**
         * @ORM\Column(type="string", length=255, nullable=true, options={"default":"incomplete"})
         */
        private ?string $stripeStatus;
    
        /**
         * @ORM\Column(type="integer", length=2, nullable=true)
         */
        private ?int $numberSpam;
    
        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private ?\DateTime $lastUpdateNumberSpam;

        /**
         * Notifier si match
         *
         * @ORM\Column(type="boolean", nullable=false, options={"default":true})
         */
        private ?bool $hasNotificationMatch;

        public function __construct()
        {
            $this->pictures = new ArrayCollection();
            $this->myUsers = new ArrayCollection();
            $this->discussions = new ArrayCollection();
            $this->notifications = new ArrayCollection();
            $this->messagesSender = new ArrayCollection();
            $this->matchs = new ArrayCollection();
        }
        
        public function getId(): ?int
        {
            return $this->id;
        }
        
        public function getEmail(): ?string
        {
            return $this->email;
        }
        
        public function setEmail(string $email): self
        {
            $this->email = $email;
            
            return $this;
        }
        
        /**
         * @deprecated since Symfony 5.3, use getUserIdentifier instead
         */
        public function getUsername(): string
        {
            return (string)$this->email;
        }
        public function getUserIdentifier(): string
        {
            return (string)$this->email;
        }
        /**
         * @see UserInterface
         */
        public function getRoles(): array
        {
            $roles = $this->roles;
            // guarantee every user at least has ROLE_USER
            $roles[] = 'ROLE_USER';
            
            return array_unique($roles);
        }
        
        public function setRoles(array $roles): self
        {
            $this->roles = array_unique($roles);
            
            return $this;
        }
        
        public function getLastname(): ?string
        {
            return $this->lastname;
        }
        
        public function setLastname(?string $lastname): self
        {
            $this->lastname = $lastname;
            
            return $this;
        }
        
        public function getFirstname(): ?string
        {
            return $this->firstname;
        }
        
        public function setFirstname(?string $firstname): self
        {
            $this->firstname = $firstname;
            
            return $this;
        }
        
        public function getAge(): ?int
        {
            return $this->age;
        }
        
        public function setAge(?int $age): self
        {
            $this->age = $age;
            
            return $this;
        }
        
        public function getSize(): ?string
        {
            return $this->size;
        }
        
        public function setSize(?string $size): self
        {
            $this->size = $size;
            
            return $this;
        }
        
        public function getGenre(): ?int
        {
            return $this->genre;
        }
        
        public function setGenre(?int $genre): self
        {
            $this->genre = $genre;
            
            return $this;
        }
        
        /**
         * @return Collection|Picture[]
         */
        public function getPictures(): Collection
        {
            return $this->pictures;
        }
        
        public function addPicture(Picture $picture): self
        {
            if (!$this->pictures->contains($picture)) {
                $this->pictures[] = $picture;
                $picture->setUser($this);
            }
            
            return $this;
        }
        
        public function removePicture(Picture $picture): self
        {
            if ($this->pictures->removeElement($picture)) {
                // set the owning side to null (unless already changed)
                if ($picture->getUser() === $this) {
                    $picture->setUser(null);
                }
            }
            
            return $this;
        }
    
        /**
         * @return Collection|User[]
         */
        public function getMyUsers(): Collection
        {
            return $this->myUsers;
        }
    
        public function addMyUser(User $myUser): self
        {
            if (!$this->myUsers->contains($myUser)) {
                $this->myUsers[] = $myUser;
                $myUser->addMyUser($this);
            }
        
            return $this;
        }
    
        public function removeMyUser(User $myUser): self
        {
            if ($this->myUsers->contains($myUser)) {
                $this->myUsers->removeElement($myUser);
                $myUser->removeMyUser($this);
            }
        
            return $this;
        }
    
        /**
         * @return Collection|Discussion[]
         */
        public function getDiscussions(): Collection
        {
            return $this->discussions;
        }
    
        public function addDiscussion(Discussion $discussion): self
        {
            if (!$this->discussions->contains($discussion)) {
                $this->discussions[] = $discussion;
                $discussion->addUser($this);
            }
        
            return $this;
        }
    
        public function removeDiscussion(Discussion $discussion): self
        {
            if ($this->discussions->contains($discussion)) {
                $this->discussions->removeElement($discussion);
                $discussion->removeUser($this);
            }
        
            return $this;
        }
    
        /**
         * @return Collection|Notification[]
         */
        public function getNotifications(): Collection
        {
            return $this->notifications;
        }
    
        public function addNotification(Notification $notification): self
        {
            if (!$this->notifications->contains($notification)) {
                $this->notifications[] = $notification;
                $notification->setUser($this);
            }
        
            return $this;
        }
    
        public function removeNotification(Notification $notification): self
        {
            if ($this->notifications->contains($notification)) {
                $this->notifications->removeElement($notification);
                // set the owning side to null (unless already changed)
                if ($notification->getUser() === $this) {
                    $notification->setUser(null);
                }
            }
        
            return $this;
        }

        public function getInterestExit()
        {
            return $this->interestExit;
        }
    
        public function setInterestExit($interestExit): self
        {
            $this->interestExit = $interestExit;
            
            return $this;
        }

        public function getInterestHobbies()
        {
            return $this->interestHobbies;
        }
    
        public function setInterestHobbies($interestHobbies): self
        {
            $this->interestHobbies = $interestHobbies;
    
            return $this;
        }
    
        public function getInterestSports()
        {
            return $this->interestSports;
        }

        public function setInterestSports($interestSports): self
        {
            $this->interestSports = $interestSports;
    
            return $this;
        }
    
        public function getInterestDistance() :?int
        {
            return $this->interestDistance;
        }

        public function setInterestDistance(?int $interestDistance): self
        {
            $this->interestDistance = $interestDistance;
    
            return $this;
        }

        public function getInterestAge() :?int
        {
            return $this->interestAge;
        }

        public function setInterestAge(?int $interestAge): self
        {
            $this->interestAge = $interestAge;

            return $this;
        }

        public function getDescription() :?string
        {
            return $this->description;
        }

        public function setDescription(?string $description): self
        {
            $this->description = $description;
            
            return $this;
        }
   
        public function getPrincipalPicture() :?string
        {
            return $this->principalPicture;
        }
    
        public function setPrincipalPicture(?string $principalPicture): self
        {
            $this->principalPicture = $principalPicture;
            
            return $this;
        }
    
        public function getPassword() :?string
        {
            return '';
        }
    
        public function getSalt(): ?string
        {
            return '';
        }
    
        public function eraseCredentials()
        {
            // TODO: Implement eraseCredentials() method.
            return '';
        }
    
        /**
         * @return Collection|Message[]
         */
        public function getMessagesSender(): Collection
        {
            return $this->messagesSender;
        }
    
        public function addMessageSender(Message $message): self
        {
            if (!$this->messagesSender->contains($message)) {
                $this->messagesSender[] = $message;
                $message->setSender($this);
            }
        
            return $this;
        }
    
        public function removeMessageSender(Message $message): self
        {
            if ($this->messagesSender->contains($message)) {
                $this->messagesSender->removeElement($message);
                // set the owning side to null (unless already changed)
                if ($message->getSender() === $this) {
                    $message->setSender(null);
                }
            }
        
            return $this;
        }
    
        /**
         * @return float|null
         */
        public function getLat(): ?float
        {
            return $this->lat;
        }
    
        /**
         * @param float|null $lat
         */
        public function setLat(?float $lat): self
        {
            $this->lat = $lat;
    
            return $this;
        }
    
        /**
         * @return float|null
         */
        public function getLon(): ?float
        {
            return $this->lon;
        }
    
        /**
         * @param float|null $lon
         */
        public function setLon(?float $lon): self
        {
            $this->lon = $lon;
    
            return $this;
        }
    
        /**
         * @return Collection|User[]
         */
        public function getMyMatchs(): Collection
        {
            return $this->myMatchs;
        }
    
        public function addMyMatch(User $myMatch): self
        {
            if (!$this->myMatchs->contains($myMatch)) {
                $this->myMatchs[] = $myMatch;
            }
        
            return $this;
        }
    
        public function removeMyMatch(User $myMatch): self
        {
            if ($this->myMatchs->contains($myMatch)) {
                $this->myMatchs->removeElement($myMatch);
            }
        
            return $this;
        }
        
        /**
         * @return Collection|User[]
         */
        public function getMyUnWantedMatchs(): Collection
        {
            return $this->myUnWantedMatchs;
        }
    
        public function addMyUnWantedMatch(User $myUnWantedMatch): self
        {
            if (!$this->myUnWantedMatchs->contains($myUnWantedMatch)) {
                $this->myUnWantedMatchs[] = $myUnWantedMatch;
            }
        
            return $this;
        }
    
        public function removeMyUnWantedMatch(User $myUnWantedMatch): self
        {
            if ($this->myUnWantedMatchs->contains($myUnWantedMatch)) {
                $this->myUnWantedMatchs->removeElement($myUnWantedMatch);
            }
        
            return $this;
        }
    
        /**
         * @return Collection|Matchs[]
         */
        public function getMatchs(): Collection
        {
            return $this->matchs;
        }
    
        public function addMatch(Matchs $match): self
        {
            if (!$this->matchs->contains($match)) {
                $this->matchs[] = $match;
                $match->setUser($this);
            }
        
            return $this;
        }
    
        public function removeMatch(Matchs $match): self
        {
            if ($this->matchs->removeElement($match)) {
//                // set the owning side to null (unless already changed)
//                if ($match->getUser() === $this) {
//                    $match->setUser(null);
//                }
            }
        
            return $this;
        }
    
        /**
         * @return Collection|Matchs[]
         */
        public function getUserMatchs(): Collection
        {
            return $this->userMatchs;
        }
    
        public function addUserMatch(Matchs $userMatch): self
        {
            if (!$this->userMatchs->contains($userMatch)) {
                $this->userMatchs[] = $userMatch;
                $userMatch->setUser($this);
            }
        
            return $this;
        }
    
        public function removeUserMatch(Matchs $userMatch): self
        {
            if ($this->userMatchs->removeElement($userMatch)) {
//                // set the owning side to null (unless already changed)
//                if ($userMatch->getUser() === $this) {
//                    $userMatch->setUser(null);
//                }
            }
        
            return $this;
        }
    
        /**
         * @return bool|null
         */
        public function getHideAge(): ?bool
        {
            return $this->hideAge;
        }
    
        /**
         * @param bool|null $hideAge
         */
        public function setHideAge(?bool $hideAge): self
        {
            $this->hideAge = $hideAge;
    
            return $this;
        }
    
        /**
         * @return bool|null
         */
        public function getHideDistance(): ?bool
        {
            return $this->hideDistance;
        }
    
        /**
         * @param bool|null $hideDistance
         */
        public function setHideDistance(?bool $hideDistance): self
        {
            $this->hideDistance = $hideDistance;
    
            return $this;
        }
    
        /**
         * @return bool|null
         */
        public function getHideAccount(): ?bool
        {
            return $this->hideAccount;
        }
    
        /**
         * @param bool|null $hideAccount
         */
        public function setHideAccount(?bool $hideAccount): self
        {
            $this->hideAccount = $hideAccount;
    
            return $this;
        }
    
        /**
         * @return int|null
         */
        public function getSmoker(): ?int
        {
            return $this->smoker;
        }
    
        /**
         * @param int|null $smoker
         */
        public function setSmoker(?int $smoker): self
        {
            $this->smoker = $smoker;
    
            return $this;
        }
    
        /**
         * @return int|null
         */
        public function getChildren(): ?int
        {
            return $this->children;
        }
    
        /**
         * @param int|null $children
         */
        public function setChildren(?int $children): self
        {
            $this->children = $children;
    
            return $this;
        }
    
        /**
         * @return int|null
         */
        public function getInterestSmoker(): ?int
        {
            return $this->interestSmoker;
        }
    
        /**
         * @param int|null $interestSmoker
         */
        public function setInterestSmoker(?int $interestSmoker): self
        {
            $this->interestSmoker = $interestSmoker;
    
            return $this;
        }
    
        /**
         * @return int|null
         */
        public function getInterestChildren(): ?int
        {
            return $this->interestChildren;
        }
    
        /**
         * @param int|null $interestChildren
         */
        public function setInterestChildren(?int $interestChildren): self
        {
            $this->interestChildren = $interestChildren;
    
            return $this;
        }
    
        /**
         * @return string
         */
        public function getStripeCustomerId(): ?string
        {
            return $this->stripeCustomerId;
        }
    
        /**
         * @param mixed $stripeCustomerId
         */
        public function setStripeCustomerId(?string $stripeCustomerId): self
        {
            $this->stripeCustomerId = $stripeCustomerId;
            
            return $this;
        }
    
        /**
         * @return string|null
         */
        public function getStripePlan(): ?string
        {
            return $this->stripePlan;
        }
    
        /**
         * @param string|null $stripePlan
         */
        public function setStripePlan(?string $stripePlan): self
        {
            $this->stripePlan = $stripePlan;
    
            return $this;
        }
    
        /**
         * @return string|null
         */
        public function getStripeStatus(): ?string
        {
            return $this->stripeStatus;
        }
    
        /**
         * @param string|null $stripeStatus
         */
        public function setStripeStatus(?string $stripeStatus): self
        {
            $this->stripeStatus = $stripeStatus;
    
            return $this;
        }
    
        /**
         * @return int|null
         */
        public function getNumberSpam(): ?int
        {
            return $this->numberSpam;
        }
    
        /**
         * @param int|null $numberSpam
         */
        public function setNumberSpam(?int $numberSpam): self
        {
            $this->numberSpam = $numberSpam;
    
            return $this;
        }
    
        /**
         * @return \DateTime|null
         */
        public function getLastUpdateNumberSpam(): ?\DateTime
        {
            return $this->lastUpdateNumberSpam;
        }
    
        /**
         * @param \DateTime|null $lastUpdateNumberSpam
         */
        public function setLastUpdateNumberSpam(?\DateTime $lastUpdateNumberSpam): self
        {
            $this->lastUpdateNumberSpam = $lastUpdateNumberSpam;
            
            return $this;
        }

        /**
         * @return bool|null
         */
        public function getHasNotificationMatch(): ?bool
        {
            return $this->hasNotificationMatch;
        }

        /**
         * @param bool|null $hasNotificationMatch
         */
        public function setHasNotificationMatch(?bool $hasNotificationMatch): self
        {
            $this->hasNotificationMatch = $hasNotificationMatch;

            return $this;
        }

        /**
         * @return int|null
         */
        public function getInterestGenre(): ?int
        {
            return $this->interestGenre;
        }

        /**
         * @param int|null $interestGenre
         */
        public function setInterestGenre(?int $interestGenre): self
        {
            $this->interestGenre = $interestGenre;

            return $this;
        }

        /**
         * @return string|null
         */
        public function getCity(): ?string
        {
            return $this->city;
        }

        /**
         * @param string|null $city
         */
        public function setCity(?string $city): self
        {
            $this->city = $city;

            return $this;
        }

        /**
         * @return bool|null
         */
        public function getHideAccount24(): ?bool
        {
            return $this->hideAccount24;
        }

        /**
         * @param bool|null $hideAccount24
         */
        public function setHideAccount24(?bool $hideAccount24): self
        {
            $this->hideAccount24 = $hideAccount24;

            return $this;
        }

        /**
         * @return \DateTime|null
         */
        public function getHideAccountDate24(): ?\DateTime
        {
            return $this->hideAccountDate24;
        }

        /**
         * @param \DateTime|null $hideAccountDate24
         */
        public function setHideAccountDate24(?\DateTime $hideAccountDate24): self
        {
            $this->hideAccountDate24 = $hideAccountDate24;

            return $this;
        }
    }
