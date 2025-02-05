<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250204185245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7540AEF4B9');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F340AEF4B9');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F4640AEF4B9');
        $this->addSql('ALTER TABLE progression DROP FOREIGN KEY FK_D5B2507340AEF4B9');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(55) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_169E6FB912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE cursus DROP FOREIGN KEY FK_255A0C312469DE2');
        $this->addSql('DROP TABLE cursus');
        $this->addSql('DROP INDEX IDX_6C3C6D7540AEF4B9 ON certification');
        $this->addSql('ALTER TABLE certification CHANGE cursus_id course_id INT NOT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D75591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D75591CC992 ON certification (course_id)');
        $this->addSql('DROP INDEX IDX_F87474F340AEF4B9 ON lesson');
        $this->addSql('ALTER TABLE lesson CHANGE cursus_id course_id INT NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_F87474F3591CC992 ON lesson (course_id)');
        $this->addSql('DROP INDEX IDX_ED896F4640AEF4B9 ON order_detail');
        $this->addSql('ALTER TABLE order_detail CHANGE cursus_id course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F46591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_ED896F46591CC992 ON order_detail (course_id)');
        $this->addSql('DROP INDEX IDX_D5B2507340AEF4B9 ON progression');
        $this->addSql('ALTER TABLE progression CHANGE percentage percentage DOUBLE PRECISION DEFAULT NULL, CHANGE cursus_id course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE progression ADD CONSTRAINT FK_D5B25073591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_D5B25073591CC992 ON progression (course_id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D75591CC992');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3591CC992');
        $this->addSql('ALTER TABLE order_detail DROP FOREIGN KEY FK_ED896F46591CC992');
        $this->addSql('ALTER TABLE progression DROP FOREIGN KEY FK_D5B25073591CC992');
        $this->addSql('CREATE TABLE cursus (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(55) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, price INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_255A0C312469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cursus ADD CONSTRAINT FK_255A0C312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB912469DE2');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP INDEX IDX_6C3C6D75591CC992 ON certification');
        $this->addSql('ALTER TABLE certification CHANGE course_id cursus_id INT NOT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7540AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D7540AEF4B9 ON certification (cursus_id)');
        $this->addSql('DROP INDEX IDX_F87474F3591CC992 ON lesson');
        $this->addSql('ALTER TABLE lesson CHANGE course_id cursus_id INT NOT NULL');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F340AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('CREATE INDEX IDX_F87474F340AEF4B9 ON lesson (cursus_id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP INDEX IDX_ED896F46591CC992 ON order_detail');
        $this->addSql('ALTER TABLE order_detail CHANGE course_id cursus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_detail ADD CONSTRAINT FK_ED896F4640AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('CREATE INDEX IDX_ED896F4640AEF4B9 ON order_detail (cursus_id)');
        $this->addSql('DROP INDEX IDX_D5B25073591CC992 ON progression');
        $this->addSql('ALTER TABLE progression CHANGE percentage percentage DOUBLE PRECISION DEFAULT \'NULL\', CHANGE course_id cursus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE progression ADD CONSTRAINT FK_D5B2507340AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('CREATE INDEX IDX_D5B2507340AEF4B9 ON progression (cursus_id)');
        $this->addSql('ALTER TABLE `user` CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
