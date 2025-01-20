<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250120135735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, cursus_id_id INT NOT NULL, date_obtained DATETIME NOT NULL, INDEX IDX_6C3C6D759D86650F (user_id_id), INDEX IDX_6C3C6D75ED70AAB9 (cursus_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE progression (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, cursus_id_id INT DEFAULT NULL, chapter INT DEFAULT NULL, percentage DOUBLE PRECISION DEFAULT NULL, INDEX IDX_D5B250739D86650F (user_id_id), INDEX IDX_D5B25073ED70AAB9 (cursus_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D759D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75ED70AAB9 FOREIGN KEY (cursus_id_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE progression ADD CONSTRAINT FK_D5B250739D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE progression ADD CONSTRAINT FK_D5B25073ED70AAB9 FOREIGN KEY (cursus_id_id) REFERENCES cursus (id)');
        $this->addSql('ALTER TABLE cursus DROP INDEX UNIQ_255A0C39777D11E, ADD INDEX IDX_255A0C39777D11E (category_id_id)');
        $this->addSql('ALTER TABLE order_detail DROP quantity');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D759D86650F');
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75ED70AAB9');
        $this->addSql('ALTER TABLE progression DROP FOREIGN KEY FK_D5B250739D86650F');
        $this->addSql('ALTER TABLE progression DROP FOREIGN KEY FK_D5B25073ED70AAB9');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE progression');
        $this->addSql('ALTER TABLE cursus DROP INDEX IDX_255A0C39777D11E, ADD UNIQUE INDEX UNIQ_255A0C39777D11E (category_id_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE order_detail ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
