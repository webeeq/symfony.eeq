<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404193058 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE city (id SMALLINT AUTO_INCREMENT NOT NULL, province_id SMALLINT DEFAULT NULL, active TINYINT(1) NOT NULL, name VARCHAR(30) NOT NULL, INDEX IDX_2D5B0234E946114A (province_id), INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE province (id SMALLINT AUTO_INCREMENT NOT NULL, active TINYINT(1) NOT NULL, name VARCHAR(30) NOT NULL, INDEX name (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, active TINYINT(1) NOT NULL, visible TINYINT(1) NOT NULL, name VARCHAR(180) NOT NULL, url VARCHAR(180) NOT NULL, ip_added VARCHAR(15) NOT NULL, date_added DATETIME NOT NULL, ip_updated VARCHAR(15) NOT NULL, date_updated DATETIME NOT NULL, INDEX IDX_694309E4A76ED395 (user_id), INDEX active (active), INDEX visible (visible), INDEX date_added (date_added), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, province_id SMALLINT DEFAULT NULL, city_id SMALLINT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', name VARCHAR(30) NOT NULL, surname VARCHAR(50) NOT NULL, url VARCHAR(180) NOT NULL, phone VARCHAR(20) NOT NULL, street VARCHAR(60) NOT NULL, postcode VARCHAR(6) NOT NULL, description TEXT NOT NULL, `show` INT NOT NULL, ip_added VARCHAR(15) NOT NULL, date_added DATETIME NOT NULL, ip_updated VARCHAR(15) NOT NULL, date_updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), INDEX IDX_8D93D649E946114A (province_id), INDEX IDX_8D93D6498BAC62AF (city_id), INDEX `show` (`show`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E4A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6498BAC62AF');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234E946114A');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649E946114A');
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E4A76ED395');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE `user`');
    }
}
