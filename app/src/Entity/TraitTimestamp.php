<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;

    /**
     *
     */
    trait TraitTimestamp
    {
        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private \DateTime $createdAt;
    
        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private \DateTime $updatedAt;
    
        /**
         * @return \DateTime
         */
        public function getCreatedAt(): ?\DateTime
        {
            return $this->createdAt;
        }
    
        /**
         * @param \DateTime $createdAt
         */
        public function setCreatedAt(?\DateTime $createdAt): self
        {
            $this->createdAt = $createdAt;
            
            return $this;
        }
    
        /**
         * @return \DateTime
         */
        public function getUpdatedAt(): ?\DateTime
        {
            return $this->updatedAt;
        }

        /**
         * @param \DateTime|null $updatedAt
         * @return User|Message|Notification|TraitTimestamp
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
