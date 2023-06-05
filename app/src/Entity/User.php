<?php

namespace App\Entity;

use App\Enum\Civility;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AssertQualifelec;

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
    const ROLE_SELLER = 'ROLE_SELLER';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * L'e-mail de l'utilisateur
     *
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Information requise.")
     * @Assert\Email(message="Format E-mail incorrect.")
     * @Assert\NoSuspiciousCharacters(
     *     restrictionLevel="805306368",
     *     restrictionLevelMessage="Information erronée.",
     *     hiddenOverlayMessage="Information erronée.",
     *     invisibleMessage="Information invisible dectectée.",
     *     mixedNumbersMessage="Information erronée."
     * )
     */
    private ?string $email = null;

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
    private ?string $lastname = null;

    /**
     * Le prénom de l'utilisateur
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $firstname = null;

    /**
     * Le genre de l'utilisateur
     *
     * @ORM\Column(type="smallint", nullable=false, enumType=Civility::class)
     * @Assert\NotBlank(message="Information requise.")
     */
    private Civility $genre;

    /**
     * Ville de l'utilisateur
     * @ORM\Column(type="text", length= 255, nullable=false)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $city = null;

    /**
     * Le genre de l'utilisateur
     *
     * @ORM\Column(type="text", length= 255, nullable=false)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $address = null;

    /**
     * Le genre de l'utilisateur
     *
     * @ORM\Column(type="text", length= 255, nullable=true)
     */
    private ?string $additionalAddress = null;

    /**
     * Le genre de l'utilisateur
     *
     * @ORM\Column(type="text", length=5, nullable=false)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $zipCode = null;

    /**
     * Le genre de l'utilisateur
     *
     * @ORM\Column(type="text", length=20, nullable=false)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $phone = null;

    /**
     * Pays
     *
     * @ORM\ManyToOne(targetEntity=Country::class)
     * @Assert\Valid()
     */
    private ?Country $country = null;

    /**
     * Toutes les photos de l'utilisateur
     *
     * @ORM\OneToOne(targetEntity=Picture::class, orphanRemoval=true, cascade={"persist", "remove"})
     */
    private ?Picture $picture;

    /**
     * A propos de l'utilisateur [SELLER]
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     *  Mot de passe
     *
     * @ORM\Column(type="string", length=255)
     */
    private ?string $password;

    /**
     * Mot de passe en clair de l'utilisateur
     * @AssertQualifelec\PasswordRequirements()
     */
    private ?string $plainPassword = null;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $stripeCustomerId = null;

    # $stripeAccountId [SELLER]
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */    private ?string $stripeAccountId = null;

    # si le compte est actif [SELLER]
    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $isStripeAccountActive = false;

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


    public function getGenre(): Civility
    {
        return $this->genre;
    }

    public function setGenre(Civility $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Picture|null
     */
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

    public function eraseCredentials(): string
    {
        // TODO: Implement eraseCredentials() method.
        return '';
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
     * @return string|null
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
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddress(): ?string
    {
        return $this->additionalAddress;
    }

    /**
     * @param string|null $additionalAddress
     */
    public function setAdditionalAddress(?string $additionalAddress): self
    {
        $this->additionalAddress = $additionalAddress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     */
    public function setZipCode(?string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     */
    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullAddress(): ?string
    {
        return vsprintf(
            '%s, %d %s, %s',
            [
                $this->getAddress(),
                $this->getZipCode(),
                $this->getCity(),
                $this->getCountry(),
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getStripeAccountId(): ?string
    {
        return $this->stripeAccountId;
    }

    /**
     * @param mixed $stripeAccountId
     */
    public function setStripeAccountId(?string $stripeAccountId): self
    {
        $this->stripeAccountId = $stripeAccountId;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsStripeAccountActive(): ?bool
    {
        return $this->isStripeAccountActive;
    }

    /**
     * @param mixed $stripeAccountActive
     */
    public function setIsStripeAccountActive(?bool $stripeAccountActive): self
    {
        $this->isStripeAccountActive = $stripeAccountActive;

        return $this;
    }
}
