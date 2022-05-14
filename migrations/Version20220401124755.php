<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220401124755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_programme ADD jaime INT DEFAULT NULL, ADD jaimepas INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit CHANGE jaime jaime INT NOT NULL, CHANGE jaimepas jaimepas INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_programme DROP jaime, DROP jaimepas');
        $this->addSql('ALTER TABLE produit CHANGE jaime jaime INT DEFAULT NULL, CHANGE jaimepas jaimepas INT DEFAULT NULL');
    }
}
