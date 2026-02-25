<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225115453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intento_wordle (id INT AUTO_INCREMENT NOT NULL, historial JSON NOT NULL, completado TINYINT NOT NULL, fecha DATETIME NOT NULL, usuario_id INT NOT NULL, reto_id INT NOT NULL, INDEX IDX_C8E0B590DB38439E (usuario_id), INDEX IDX_C8E0B59048B53DED (reto_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE intento_wordle ADD CONSTRAINT FK_C8E0B590DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE intento_wordle ADD CONSTRAINT FK_C8E0B59048B53DED FOREIGN KEY (reto_id) REFERENCES reto_diario (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05D3A909126 ON usuario (nombre)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE intento_wordle DROP FOREIGN KEY FK_C8E0B590DB38439E');
        $this->addSql('ALTER TABLE intento_wordle DROP FOREIGN KEY FK_C8E0B59048B53DED');
        $this->addSql('DROP TABLE intento_wordle');
        $this->addSql('DROP INDEX UNIQ_2265B05D3A909126 ON usuario');
    }
}
