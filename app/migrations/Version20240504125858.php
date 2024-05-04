<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240504125858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checkin (id INT AUTO_INCREMENT NOT NULL, transaction_line_id INT DEFAULT NULL, author_id INT DEFAULT NULL, type SMALLINT NOT NULL, status SMALLINT NOT NULL, comments LONGTEXT DEFAULT NULL, start_date DATE NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_E1631C913BD149A0 (transaction_line_id), INDEX IDX_E1631C91F675F31B (author_id), INDEX ecommerce_checkin (status, type, start_date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE checkin_picture (checkin_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_BA137FBAA85C7AA9 (checkin_id), INDEX IDX_BA137FBAEE45BDBF (picture_id), PRIMARY KEY(checkin_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE claim (id INT AUTO_INCREMENT NOT NULL, checkin_id INT DEFAULT NULL, status SMALLINT NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_A769DE27A85C7AA9 (checkin_id), INDEX ecommerce_claim (status, checkin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contributor (id INT AUTO_INCREMENT NOT NULL, fullname VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_DA6F9793E7927C74 (email), INDEX admin_contributor (fullname, email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, position SMALLINT NOT NULL, INDEX ecommerce_country (position, label, code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE home_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, reservation_id INT DEFAULT NULL, author_id INT DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_B6BD307FB83297E7 (reservation_id), INDEX IDX_B6BD307FF675F31B (author_id), INDEX ecommerce_message (author_id, created_at, reservation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mid (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, home_page_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_41AEF4CE4584665A (product_id), INDEX IDX_41AEF4CEB966A8BC (home_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, INDEX ecommerce_picture (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, short_description VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, title VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, caution DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, quantity_all_ready_reserved INT NOT NULL, category SMALLINT NOT NULL, number_view INT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_D34A04ADF675F31B (author_id), INDEX ecommerce_products (status, short_description, title, category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_picture (product_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_C70254394584665A (product_id), INDEX IDX_C7025439EE45BDBF (picture_id), PRIMARY KEY(product_id, picture_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, transaction_id INT DEFAULT NULL, author_id INT DEFAULT NULL, status SMALLINT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, INDEX IDX_42C849552FC0CB0F (transaction_id), INDEX IDX_42C84955F675F31B (author_id), INDEX ecommerce_reservation (author_id, created_at, token, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, author_id INT DEFAULT NULL, note INT NOT NULL, message VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, INDEX IDX_794381C64584665A (product_id), INDEX IDX_794381C6F675F31B (author_id), INDEX ecommerce_review (product_id, author_id, created_at, note), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slider (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, home_page_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_CFC710074584665A (product_id), INDEX IDX_CFC71007B966A8BC (home_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, status SMALLINT NOT NULL, reference VARCHAR(20) DEFAULT NULL, payment_intent_id VARCHAR(40) DEFAULT NULL, total_amount_ttc INT NOT NULL, total_amount_tva INT NOT NULL, total_fees INT NOT NULL, token VARCHAR(255) NOT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, INDEX IDX_723705D1F675F31B (author_id), INDEX ecommerce_transaction (author_id, created_at, token, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction_line (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, transaction_id INT DEFAULT NULL, quantity INT NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, amount_ttc INT NOT NULL, amount_tva INT NOT NULL, fees INT NOT NULL, status SMALLINT NOT NULL, transfert_id VARCHAR(255) DEFAULT NULL, cancel_transfert_id VARCHAR(255) DEFAULT NULL, caution_id VARCHAR(255) DEFAULT NULL, capture_caution_id VARCHAR(255) DEFAULT NULL, cancel_amount INT DEFAULT NULL, caution_amount VARCHAR(11) DEFAULT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_33578A574584665A (product_id), INDEX IDX_33578A572FC0CB0F (transaction_id), INDEX ecommerce_transaction_line (start_date, end_date, token, status), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE under_slider (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, home_page_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4E8B53434584665A (product_id), INDEX IDX_4E8B5343B966A8BC (home_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, country_id INT DEFAULT NULL, picture_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, role VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, genre SMALLINT NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, additional_address VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(5) NOT NULL, phone VARCHAR(20) NOT NULL, description VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, lat VARCHAR(15) DEFAULT NULL, lon VARCHAR(15) DEFAULT NULL, stripe_customer_id VARCHAR(40) DEFAULT NULL, stripe_account_id VARCHAR(40) DEFAULT NULL, is_stripe_account_active TINYINT(1) NOT NULL, is_guess TINYINT(1) NOT NULL, terms TINYINT(1) NOT NULL, last_date_connexion DATE DEFAULT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, token VARCHAR(255) NOT NULL, deleted_at DATE DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649F92F3E70 (country_id), UNIQUE INDEX UNIQ_8D93D649EE45BDBF (picture_id), INDEX ecommerce_user (email, firstname, lastname, created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE website_content (id INT AUTO_INCREMENT NOT NULL, picture_id INT DEFAULT NULL, home_page_id INT DEFAULT NULL, author_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, sub_title VARCHAR(200) NOT NULL, link SMALLINT NOT NULL, white_color TINYINT(1) DEFAULT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, UNIQUE INDEX UNIQ_52EB4C0DEE45BDBF (picture_id), INDEX IDX_52EB4C0DB966A8BC (home_page_id), INDEX IDX_52EB4C0DF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, user_id INT DEFAULT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, token VARCHAR(255) NOT NULL, INDEX IDX_9CE12A314584665A (product_id), INDEX IDX_9CE12A31A76ED395 (user_id), INDEX ecommerce_wishlists (product_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE checkin ADD CONSTRAINT FK_E1631C913BD149A0 FOREIGN KEY (transaction_line_id) REFERENCES transaction_line (id)');
        $this->addSql('ALTER TABLE checkin ADD CONSTRAINT FK_E1631C91F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE checkin_picture ADD CONSTRAINT FK_BA137FBAA85C7AA9 FOREIGN KEY (checkin_id) REFERENCES checkin (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE checkin_picture ADD CONSTRAINT FK_BA137FBAEE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE claim ADD CONSTRAINT FK_A769DE27A85C7AA9 FOREIGN KEY (checkin_id) REFERENCES checkin (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE mid ADD CONSTRAINT FK_41AEF4CE4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE mid ADD CONSTRAINT FK_41AEF4CEB966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_picture ADD CONSTRAINT FK_C70254394584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_picture ADD CONSTRAINT FK_C7025439EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849552FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC710074584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE slider ADD CONSTRAINT FK_CFC71007B966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction_line ADD CONSTRAINT FK_33578A574584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE transaction_line ADD CONSTRAINT FK_33578A572FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id)');
        $this->addSql('ALTER TABLE under_slider ADD CONSTRAINT FK_4E8B53434584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE under_slider ADD CONSTRAINT FK_4E8B5343B966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE website_content ADD CONSTRAINT FK_52EB4C0DEE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('ALTER TABLE website_content ADD CONSTRAINT FK_52EB4C0DB966A8BC FOREIGN KEY (home_page_id) REFERENCES home_page (id)');
        $this->addSql('ALTER TABLE website_content ADD CONSTRAINT FK_52EB4C0DF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A314584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE checkin DROP FOREIGN KEY FK_E1631C913BD149A0');
        $this->addSql('ALTER TABLE checkin DROP FOREIGN KEY FK_E1631C91F675F31B');
        $this->addSql('ALTER TABLE checkin_picture DROP FOREIGN KEY FK_BA137FBAA85C7AA9');
        $this->addSql('ALTER TABLE checkin_picture DROP FOREIGN KEY FK_BA137FBAEE45BDBF');
        $this->addSql('ALTER TABLE claim DROP FOREIGN KEY FK_A769DE27A85C7AA9');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FB83297E7');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF675F31B');
        $this->addSql('ALTER TABLE mid DROP FOREIGN KEY FK_41AEF4CE4584665A');
        $this->addSql('ALTER TABLE mid DROP FOREIGN KEY FK_41AEF4CEB966A8BC');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF675F31B');
        $this->addSql('ALTER TABLE product_picture DROP FOREIGN KEY FK_C70254394584665A');
        $this->addSql('ALTER TABLE product_picture DROP FOREIGN KEY FK_C7025439EE45BDBF');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849552FC0CB0F');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955F675F31B');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C64584665A');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC710074584665A');
        $this->addSql('ALTER TABLE slider DROP FOREIGN KEY FK_CFC71007B966A8BC');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F675F31B');
        $this->addSql('ALTER TABLE transaction_line DROP FOREIGN KEY FK_33578A574584665A');
        $this->addSql('ALTER TABLE transaction_line DROP FOREIGN KEY FK_33578A572FC0CB0F');
        $this->addSql('ALTER TABLE under_slider DROP FOREIGN KEY FK_4E8B53434584665A');
        $this->addSql('ALTER TABLE under_slider DROP FOREIGN KEY FK_4E8B5343B966A8BC');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649F92F3E70');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649EE45BDBF');
        $this->addSql('ALTER TABLE website_content DROP FOREIGN KEY FK_52EB4C0DEE45BDBF');
        $this->addSql('ALTER TABLE website_content DROP FOREIGN KEY FK_52EB4C0DB966A8BC');
        $this->addSql('ALTER TABLE website_content DROP FOREIGN KEY FK_52EB4C0DF675F31B');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A314584665A');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A76ED395');
        $this->addSql('DROP TABLE checkin');
        $this->addSql('DROP TABLE checkin_picture');
        $this->addSql('DROP TABLE claim');
        $this->addSql('DROP TABLE contributor');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE home_page');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE mid');
        $this->addSql('DROP TABLE picture');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_picture');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE slider');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE transaction_line');
        $this->addSql('DROP TABLE under_slider');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE website_content');
        $this->addSql('DROP TABLE wishlist');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
