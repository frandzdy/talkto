<?php

namespace App\Entity;

use App\Enum\Civility;
use App\Repository\UserRepository;
use App\Validator\Constraints as AssertRented;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table()]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Index(columns: ['email', 'firstname', 'lastname', 'created_at'], name: 'ecommerce_user')]
#[UniqueEntity(fields: ['email'], message: 'E-mail déjà enregistré.')]
#[ORM\HasLifecycleCallbacks()]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TraitTimestamp;
    use TraitToken;
    use TraitDeletable;

    final public const ROLE_USER = 'ROLE_USER';

    // customer
    final public const ROLE_GUESS = 'ROLE_GUESS';

    // guess
    final public const ROLE_SELLER = 'ROLE_SELLER';

    // lessor
    final public const ROLE_SUPPORT = 'ROLE_SUPPORT'; // support

    #[Assert\Image(
        maxSize: '10M',
        mimeTypes: ['image/jpg', 'image/jpeg'],
        detectCorrupted: true,
        maxSizeMessage: 'Document trop lourd. (10Mo)',
        mimeTypesMessage: 'Format image uniquement autorisé. (JPG)',
        corruptedMessage: 'Fichier corrompue'
    )]
    #[Ignore()]
    public ?string $uploadPicture = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * L'e-mail de l'utilisateur.
     */
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Information requise.')]
    #[Assert\Email(message: 'Format E-mail incorrect.')]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'Information erronée.',
        invisibleMessage: 'Information erronée.',
        mixedNumbersMessage: 'Information erronée.',
        hiddenOverlayMessage: 'Information erronée.',
        restrictionLevel: Assert\NoSuspiciousCharacters::RESTRICTION_LEVEL_HIGH,
    )]
    private ?string $email = null;

    /**
     * Ses différents role dans l'application pour défaut ROLE_USER.
     */
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $role = null;

    /**
     * le nom de famille de l'utilisateur.
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Information requise.")
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private ?string $lastname = null;

    /**
     * Le prénom de l'utilisateur.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private ?string $firstname = null;

    /**
     * Le genre de l'utilisateur.
     */
    #[ORM\Column(type: 'smallint', enumType: Civility::class)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private Civility $genre;

    /**
     * Ville de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private string $city;

    /**
     * Le genre de l'utilisateur.
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private string $address;

    /**
     * Le genre de l'utilisateur.
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $additionalAddress = null;

    /**
     * Le genre de l'utilisateur.
     */
    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private string $zipCode;

    /**
     * Le genre de l'utilisateur.
     */
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Information requise.')]
    private string $phone;

    /**
     * Pays.
     */
    #[ORM\ManyToOne(targetEntity: Country::class)]
    private Country $country;

    /**
     * La photo de l'utilisateur.
     */
    #[ORM\OneToOne(targetEntity: Picture::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Picture $picture = null;

    /**
     * A propos de l'utilisateur [SELLER].
     */
    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    /**
     *  Mot de passe.
     */
    #[ORM\Column(length: 255)]
    private string $password;

    /**
     * Mot de passe en clair de l'utilisateur.
     */
    #[AssertRented\PasswordRequirements()]
    #[Assert\Email(message: 'Format E-mail incorrect.')]
    #[Assert\NoSuspiciousCharacters(
        restrictionLevelMessage: 'Information erronée.',
        invisibleMessage: 'Information erronée.',
        mixedNumbersMessage: 'Information erronée.',
        hiddenOverlayMessage: 'Information erronée.',
        restrictionLevel: Assert\NoSuspiciousCharacters::RESTRICTION_LEVEL_HIGH,
    )]
    private ?string $plainPassword = null;

    /**
     * Latitude de la position de l'utilisateur.
     */
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $lat = null;

    /**
     * Longitude de la position de l'utilisateur.
     */
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $lon = null;

    /**
     * Stripe Customer Id [SELLER, GUESS].
     */
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $stripeCustomerId = null;

    // $stripeAccountId [SELLER]
    /**
     * Stripe Account Id [SELLER].
     */
    #[ORM\Column(length: 40, nullable: true)]
    private ?string $stripeAccountId = null;

    /**
     * Si le compte est actif [SELLER].
     */
    #[ORM\Column()]
    private ?bool $isStripeAccountActive = false;

    #[ORM\Column()]
    private ?bool $isGuess = false;

    /**
     * Terms de l'application.
     */
    #[ORM\Column()]
    #[Assert\NotBlank(message: 'Information requise.')]
    private bool $terms;

    /**
     * Dernière date de connexion.
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTime $lastDateConnexion = null;

    public function getFullname(): ?string
    {
        return sprintf('%s %s', strtoupper($this->lastname), $this->firstname);
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

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return $this->getRole() ? [$this->getRole()] : [];
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

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

    public function getGenre(): Civility
    {
        return $this->genre;
    }

    public function setGenre(Civility $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): ?string
    {
        return $this->password = $password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getSalt(): ?string
    {
        return '';
    }

    public function eraseCredentials(): void {}

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getStripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function setStripeCustomerId(?string $stripeCustomerId): self
    {
        $this->stripeCustomerId = $stripeCustomerId;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAdditionalAddress(): ?string
    {
        return $this->additionalAddress;
    }

    public function setAdditionalAddress(?string $additionalAddress): self
    {
        $this->additionalAddress = $additionalAddress;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFullAddress(): ?string
    {
        return vsprintf(
            '%s, %d %s, %s',
            [
                $this->getAddress(),
                $this->getZipCode(),
                $this->getCity(),
                $this->getCountry()?->getLabel(),
            ]
        );
    }

    public function getStripeAccountId(): ?string
    {
        return $this->stripeAccountId;
    }

    public function setStripeAccountId(?string $stripeAccountId): self
    {
        $this->stripeAccountId = $stripeAccountId;

        return $this;
    }

    public function getIsStripeAccountActive(): ?bool
    {
        return $this->isStripeAccountActive;
    }

    public function setIsStripeAccountActive(?bool $stripeAccountActive): self
    {
        $this->isStripeAccountActive = $stripeAccountActive;

        return $this;
    }

    public function getIsGuess(): ?bool
    {
        return $this->isGuess;
    }

    public function setIsGuess(?bool $isGuess): self
    {
        $this->isGuess = $isGuess;

        return $this;
    }

    /**
     * Retourne les rôles disponibles.
     */
    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_USER => 'Locataire',
            self::ROLE_GUESS => 'Invité(e)',
            self::ROLE_SELLER => 'Bailleur',
        ];
    }

    /**
     * Retourne le label du rôle de l'utilisateur.
     */
    public function getRoleAsLabel(): string
    {
        $availableRoles = self::getAvailableRoles();

        return $availableRoles[$this->getRoles()[0]] ?? '';
    }

    public function getLastDateConnexion(): ?\DateTime
    {
        return $this->lastDateConnexion;
    }

    public function setLastDateConnexion(?\DateTime $lastDateConnexion = null): self
    {
        $this->lastDateConnexion = $lastDateConnexion;

        return $this;
    }

    public function getTerms(): bool
    {
        return $this->terms;
    }

    public function setTerms(bool $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

    public function serialize(): void
    {
        // TODO: Implement serialize() method.
    }

    public function unserialize(string $data): void
    {
        // TODO: Implement unserialize() method.
    }
}
