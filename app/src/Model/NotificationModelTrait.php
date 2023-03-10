<?php
    
    namespace App\Model;
    
    use Doctrine\Common\Collections\ArrayCollection;
    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use Symfony\Component\Validator\Constraints as Assert;
    
    trait NotificationModelTrait
    {
        /**
         * Prénom du formulaire de contactez-nous
         * @Assert\NotBlank(message="Information requise.")
         * @Assert\Length(max="100")
         */
        private ?string $firstname;
        
        /**
         * Nom du formulaire de contactez-nous
         *
         * @Assert\NotBlank(message="Information requise.")
         * @Assert\Length(max="100")
         */
        private ?string $lastname;
        
        /**
         * E-mail du formulaire de contactez-nous
         *
         * @Assert\NotBlank(message="Information requise.")
         * @Assert\Email(message="Email incorrect")
         * @Assert\Length(max=255)
         */
        private ?string $email;
        
        /**
         * Message du formulaire de contactez-nous
         *
         * @Assert\NotBlank(message="Information requise.")
         */
        private ?string $message;
        
        /**
         * Retourne le prénom du contact
         */
        public function getFirstname(): ?string
        {
            return $this->firstname;
        }
        
        /**
         * Set le prénom du contact
         */
        public function setFirstname(?string $firstname): self
        {
            $this->firstname = $firstname;
            
            return $this;
        }
        
        /**
         * Retourne le nom du contact
         */
        public function getLastname(): ?string
        {
            return $this->lastname;
        }
        
        /**
         * set le nom du contact
         */
        public function setLastname(?string $lastname): self
        {
            $this->lastname = $lastname;
            
            return $this;
        }
        
        /**
         * Retourne l'e-mail du contact
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }
        
        /**
         * Set l'e-mail du contact
         */
        public function setEmail(?string $email): self
        {
            $this->email = $email;
            
            return $this;
        }
        
        /**
         * Retourne le message
         */
        public function getMessage(): ?string
        {
            return $this->message;
        }
        
        /**
         * Set le message
         */
        public function setMessage(?string $message): self
        {
            $this->message = $message;
            
            return $this;
        }
    }
