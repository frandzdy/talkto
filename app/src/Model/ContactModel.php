<?php
    
    namespace App\Model;
    
    use App\Entity\ContactSubject;
    use Symfony\Component\Validator\Constraints as Assert;

    class ContactModel
    {
        use NotificationModelTrait;
    
        /**
         * Sujet du formulaire de contactez-nous
         *
         * @Assert\NotBlank(message="Information requise.")
         */
        private int $subject;
    
        /**
         * Retourne le sujet
         */
        public function getSubject(): ?string
        {
            return ContactSubject::getLabel($this->subject);
        }
    
        /**
         * set le sujet
         */
        public function setSubject(?string $subject): self
        {
            $this->subject = $subject;
        
            return $this;
        }
    }
