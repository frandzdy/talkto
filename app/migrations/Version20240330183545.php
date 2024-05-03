<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330183545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkin CHANGE start_date start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL, CHANGE deleted_at deleted_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL');
        $this->addSql('ALTER TABLE review CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL');
        $this->addSql('ALTER TABLE transaction_line CHANGE end_date end_date DATE NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE last_date_connexion last_date_connexion DATE DEFAULT NULL, CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL, CHANGE deleted_at deleted_at DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE website_content CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL');
        $this->addSql('ALTER TABLE wishlist CHANGE created_at created_at DATE NOT NULL, CHANGE updated_at updated_at DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishlist CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE website_content CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE checkin CHANGE start_date start_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transaction_line CHANGE end_date end_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE transaction CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE last_date_connexion last_date_connexion DATETIME DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE reservation CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE review CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
