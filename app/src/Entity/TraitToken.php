<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;

    /**
     * Génère un token pour plus de sécurité
     */
    trait TraitToken
    {
        /**
         * @var string
         */
        #[ORM\Column(length: 255)]
        private string $token;

        /**
         * @return string
         */
        public function getToken(): string
        {
            return $this->token;
        }

        /**
         * @param string $token
         * @return TraitToken|Check|Claim|Discussion|Media|Message|Picture|Product|Reservation|Transaction|TransactionLine|User|Video
         */
        public function setToken(string $token): self
        {
            $this->token = $token;
    
            return $this;
        }
        
        /**
         * @ORM\PrePersist
         * @ORM\PreUpdate
         */
        #[ORM\PrePersist]
        #[ORM\PreUpdate]
        private function setTokenValue() :void
        {
            echo '<pre>';
            dump($this->token);
            echo '</pre>';
            echo 'Methode : '.__METHOD__.' Ligne : '.__LINE__;
            die;
            if (!$this->token) {
                $this->token = hash('sha256', random_bytes(32));
            }
        }
    }
