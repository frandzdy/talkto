<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230623214421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `check` (id INT AUTO_INCREMENT NOT NULL, transaction_line_id INT DEFAULT NULL, status SMALLINT NOT NULL, start_date DATETIME NOT NULL, token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_3C8EAC133BD149A0 (transaction_line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE claim (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contributor (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DA6F9793E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, caution DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, quantity_all_ready_reserved INT NOT NULL, category SMALLINT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D34A04ADF675F31B (author_id), INDEX ecommerce_products (status, description, title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_picture (product_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_C70254394584665A (product_id), INDEX IDX_C7025439EE45BDBF (picture_id), PRIMARY KEY(product_id, picture_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, transaction_id INT DEFAULT NULL, author_id INT DEFAULT NULL, status SMALLINT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_42C849552FC0CB0F (transaction_id), INDEX IDX_42C84955F675F31B (author_id), INDEX ecommerce_reservation (author_id, created_at, token, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, status SMALLINT NOT NULL, reference VARCHAR(20) NOT NULL, payment_intent_id VARCHAR(40) NOT NULL, total_amount_ttc INT NOT NULL, total_amount_tva INT NOT NULL, total_fees INT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_723705D1F675F31B (author_id), INDEX ecommerce_transaction (author_id, created_at, token, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_line (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, quantity INT NOT NULL, start_date DATE NOT NULL, end_date DATETIME NOT NULL, amount_ttc INT NOT NULL, amount_tva INT NOT NULL, fees INT NOT NULL, status SMALLINT NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_33578A574584665A (product_id), INDEX IDX_33578A572FC0CB0F (transaction_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, picture_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, genre SMALLINT NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, additional_address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(5) NOT NULL, phone VARCHAR(20) NOT NULL, description LONGTEXT DEFAULT NULL, password VARCHAR(255) NOT NULL, lat VARCHAR(15) NOT NULL, lon VARCHAR(15) NOT NULL, stripe_customer_id VARCHAR(40) NOT NULL, stripe_account_id VARCHAR(40) NOT NULL, is_stripe_account_active TINYINT(1) NOT NULL, is_guess TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, token VARCHAR(255) NOT NULL, intro_menu TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F92F3E70 (country_id), UNIQUE INDEX UNIQ_8D93D649EE45BDBF (picture_id), INDEX ecommerce_user (email, firstname, lastname, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `check` ADD CONSTRAINT FK_3C8EAC133BD149A0 FOREIGN KEY (transaction_line_id) REFERENCES transaction_line (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_picture ADD CONSTRAINT FK_C70254394584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_picture ADD CONSTRAINT FK_C7025439EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction_line ADD CONSTRAINT FK_33578A574584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE transaction_line ADD CONSTRAINT FK_33578A572FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `check` DROP FOREIGN KEY FK_3C8EAC133BD149A0');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF675F31B');
        $this->addSql('ALTER TABLE product_picture DROP FOREIGN KEY FK_C70254394584665A');
        $this->addSql('ALTER TABLE product_picture DROP FOREIGN KEY FK_C7025439EE45BDBF');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552FC0CB0F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955F675F31B');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F675F31B');
        $this->addSql('ALTER TABLE transaction_line DROP FOREIGN KEY FK_33578A574584665A');
        $this->addSql('ALTER TABLE transaction_line DROP FOREIGN KEY FK_33578A572FC0CB0F');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('DROP TABLE `check`');
        $this->addSql('DROP TABLE claim');
        $this->addSql('DROP TABLE contributor');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_picture');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transaction_line');
        $this->addSql('DROP TABLE user');
    }
}
