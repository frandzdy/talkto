<?php
    
    namespace App\Entity;
    
    use Doctrine\ORM\Mapping as ORM;

    /**
     * Génère un token pour plus de sécurité
     */
    trait TraitToken
    {
        /**
         * @ORM\Column(type="string", length=255)
         */
        private ?string $token;
    
    
        public function getToken(): ?string
        {
            return $this->token;
        }
        
        public function setToken(?string $token): self
        {
            $this->token = $token;
    
            return $this;
        }
        
        /**
         * @ORM\PrePersist
         * @ORM\PreUpdate
         */
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
