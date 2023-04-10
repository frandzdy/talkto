<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    const ROLE_SELLER = 'ROLE_SELLER';

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
     * Le prénom de l'utilisateur
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $firstname;

    # Le genre de l'utilisateur
    #[ORM\Column(type: "smallint", nullable: false)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?int $genre;

    # Adresse de l'utilisateur
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    private ?string $address;

    # Code postal de l'utilisateur
    #[ORM\Column(type: "string", length: 5, nullable: false)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $zipCode;

    # Ville de l'utilisateur
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $city;

    # Code postal de l'utilisateur
    #[ORM\Column(type: "string", length: 20, nullable: false)]
    #[Assert\NotBlank(message: "Information requise.")]
    private ?string $phone;

    # Code postal de l'utilisateur
    #[ORM\Column(type: "string", length: 255, nullable: false)]
    ##[Assert\NotBlank(message:"Information requise.")]
    private ?string $country;

    # Toutes les photos de l'utilisateur
    #[ORM\OneToOne(targetEntity: "App\Entity\Picture", cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Assert\All(
        new Assert\File(
            maxSize: 5 * 1024 * 1024,
            mimeTypes: ['image/*'],
            maxSizeMessage: 'Photo trop lourde.',
            mimeTypesMessage: 'Format image uniquement autorisé.'
        )
    )]
    #[Assert\Count(min: 1, max: 1, minMessage: "Information requise.")]
    private ?Picture $picture;

    /**
     * À propos de l'utilisateur
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="Information requise.")
     */
    private ?string $description;

    /**
     * A propos de l'utilisateur
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private ?string $password;

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

    /**
     * @return string|null
     */
    public function getFullname(): ?string
    {
        return $this->lastname . ' ' . $this->firstname;
    }

    /**
     * @return int|null
     */
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

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return string|null
     */
    public function setPassword(?string $password): ?string
    {
        return $this->password = $password;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return '';
    }

    /**
     * @return string
     */
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
    public function setAddress(?string $address): void
    {
        $this->address = $address;
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
     * @return $this
     */
    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     * @return $this
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
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
     * @return $this
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
