<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Validator\Constraints as Assert;

    /**
     * Gère les champs date de création et de mise à jour
     */
    trait TraitTimestamp
    {
        /**
         * @var \DateTime|null
         */
        #[ORM\Column()]
        #[Assert\DateTime()]
        private ?\DateTime $createdAt = null;
        /**
         * @var \DateTime|null
         */
        #[ORM\Column()]
        #[Assert\DateTime()]
        private ?\DateTime $updatedAt = null;

        /**
         * @return \DateTime|null
         */
        public function getCreatedAt(): ?\DateTime
        {
            return $this->createdAt;
        }

        /**
         * @param \DateTime|null $createdAt
         * @return User|Message|Notification|Product|Reservation|TraitTimestamp|Transaction
         */
        public function setCreatedAt(?\DateTime $createdAt): self
        {
            $this->createdAt = $createdAt;
            
            return $this;
        }

        /**
         * @return \DateTime|null
         */
        public function getUpdatedAt(): ?\DateTime
        {
            return $this->updatedAt;
        }

        /**
         * @param \DateTime|null $updatedAt
         * @return User|Message|Notification|Product|Reservation|TraitTimestamp|Transaction
         */
        public function setUpdatedAt(?\DateTime $updatedAt): self
        {
            $this->updatedAt = $updatedAt;
    
            return $this;
        }

        #[ORM\PrePersist]
        #[ORM\PreUpdate]
        public function updateDate() {
            if (!$this->getCreatedAt()) {
                $this->setCreatedAt(new \DateTime('now'));
            }
            $this->setUpdatedAt(new \DateTime('now'));
        }
    }
