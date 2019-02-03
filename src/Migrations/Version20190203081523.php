<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190203081523 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attachment ADD attach_name VARCHAR(255) DEFAULT NULL, ADD attach_original_name VARCHAR(255) DEFAULT NULL, ADD attach_mime_type VARCHAR(255) DEFAULT NULL, ADD attach_size INT DEFAULT NULL, ADD attach_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', DROP path, CHANGE extension extension VARCHAR(10) NOT NULL, CHANGE size size INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE attachment ADD path VARCHAR(255) NOT NULL COLLATE utf8_general_ci, DROP attach_name, DROP attach_original_name, DROP attach_mime_type, DROP attach_size, DROP attach_dimensions, CHANGE extension extension VARCHAR(10) DEFAULT NULL COLLATE utf8_general_ci, CHANGE size size NUMERIC(10, 0) NOT NULL');
    }
}
