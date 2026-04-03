<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260203092045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE generation DROP FOREIGN KEY `FK_D3266C3BA76ED395`');
        $this->addSql('DROP INDEX IDX_D3266C3BA76ED395 ON generation');
        $this->addSql('ALTER TABLE generation ADD user_id_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('ALTER TABLE generation ADD CONSTRAINT FK_D3266C3B9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_D3266C3B9D86650F ON generation (user_id_id)');
        $this->addSql('ALTER TABLE generation_user_contact DROP FOREIGN KEY `FK_59D3984040C6E3A6`');
        $this->addSql('ALTER TABLE generation_user_contact DROP FOREIGN KEY `FK_59D39840553A6EC4`');
        $this->addSql('ALTER TABLE generation_user_contact MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE generation_user_contact DROP id, DROP PRIMARY KEY, ADD PRIMARY KEY (generation_id, user_contact_id)');
        $this->addSql('ALTER TABLE generation_user_contact ADD CONSTRAINT FK_59D3984040C6E3A6 FOREIGN KEY (user_contact_id) REFERENCES user_contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE generation_user_contact ADD CONSTRAINT FK_59D39840553A6EC4 FOREIGN KEY (generation_id) REFERENCES generation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plan ADD created_at DATETIME NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(255) DEFAULT NULL, CHANGE `limit` limit_generation INT NOT NULL');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY `FK_8D93D649E899029B`');
        $this->addSql('DROP INDEX IDX_8D93D649E899029B ON user');
        $this->addSql('ALTER TABLE user DROP plan_id, CHANGE dob dob DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE user_contact ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_contact ADD CONSTRAINT FK_146FF8329D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_146FF8329D86650F ON user_contact (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE generation DROP FOREIGN KEY FK_D3266C3B9D86650F');
        $this->addSql('DROP INDEX IDX_D3266C3B9D86650F ON generation');
        $this->addSql('ALTER TABLE generation ADD user_id INT NOT NULL, DROP user_id_id');
        $this->addSql('ALTER TABLE generation ADD CONSTRAINT `FK_D3266C3BA76ED395` FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D3266C3BA76ED395 ON generation (user_id)');
        $this->addSql('ALTER TABLE generation_user_contact DROP FOREIGN KEY FK_59D39840553A6EC4');
        $this->addSql('ALTER TABLE generation_user_contact DROP FOREIGN KEY FK_59D3984040C6E3A6');
        $this->addSql('ALTER TABLE generation_user_contact ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE generation_user_contact ADD CONSTRAINT `FK_59D39840553A6EC4` FOREIGN KEY (generation_id) REFERENCES generation (id)');
        $this->addSql('ALTER TABLE generation_user_contact ADD CONSTRAINT `FK_59D3984040C6E3A6` FOREIGN KEY (user_contact_id) REFERENCES user_contact (id)');
        $this->addSql('ALTER TABLE plan DROP created_at, CHANGE image image VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(255) NOT NULL, CHANGE limit_generation `limit` INT NOT NULL');
        $this->addSql('ALTER TABLE `user` ADD plan_id INT DEFAULT NULL, CHANGE dob dob DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT `FK_8D93D649E899029B` FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649E899029B ON `user` (plan_id)');
        $this->addSql('ALTER TABLE user_contact DROP FOREIGN KEY FK_146FF8329D86650F');
        $this->addSql('DROP INDEX IDX_146FF8329D86650F ON user_contact');
        $this->addSql('ALTER TABLE user_contact DROP user_id_id');
    }
}
