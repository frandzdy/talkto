<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240310091044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE home_page_slider DROP FOREIGN KEY FK_1DDDDB544584665A');
        $this->addSql('ALTER TABLE home_page_slider DROP FOREIGN KEY FK_1DDDDB54B966A8BC');
        $this->addSql('ALTER TABLE home_page_mid DROP FOREIGN KEY FK_E8140552B966A8BC');
        $this->addSql('ALTER TABLE home_page_mid DROP FOREIGN KEY FK_E81405524584665A');
        $this->addSql('ALTER TABLE home_page_under_slider DROP FOREIGN KEY FK_EBFDA675B966A8BC');
        $this->addSql('ALTER TABLE home_page_under_slider DROP FOREIGN KEY FK_EBFDA6754584665A');
        $this->addSql('DROP TABLE home_page_slider');
        $this->addSql('DROP TABLE home_page_mid');
        $this->addSql('DROP TABLE home_page_under_slider');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE home_page_slider (home_page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_1DDDDB54B966A8BC (home_page_id), INDEX IDX_1DDDDB544584665A (product_id), PRIMARY KEY(home_page_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE home_page_mid (home_page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_E8140552B966A8BC (home_page_id), INDEX IDX_E81405524584665A (product_id), PRIMARY KEY(home_page_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE home_page_under_slider (home_page_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_EBFDA675B966A8BC (home_page_id), INDEX IDX_EBFDA6754584665A (product_id), PRIMARY KEY(home_page_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE home_page_slider ADD CONSTRAINT FK_1DDDDB544584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE home_page_slider ADD CONSTRAINT FK_1DDDDB54B966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE home_page_mid ADD CONSTRAINT FK_E8140552B966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE home_page_mid ADD CONSTRAINT FK_E81405524584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE home_page_under_slider ADD CONSTRAINT FK_EBFDA675B966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE home_page_under_slider ADD CONSTRAINT FK_EBFDA6754584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }
}
