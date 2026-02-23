<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222143922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comentario (id INT AUTO_INCREMENT NOT NULL, texto LONGTEXT NOT NULL, fecha_creacion DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, palabra_id INT NOT NULL, usuario_id INT NOT NULL, INDEX IDX_4B91E70228EA1B16 (palabra_id), INDEX IDX_4B91E702DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE mensaje (id INT AUTO_INCREMENT NOT NULL, contenido LONGTEXT DEFAULT NULL, fecha_envio DATETIME NOT NULL, leido TINYINT NOT NULL, remitente_id INT NOT NULL, destinatario_id INT NOT NULL, palabra_compartida_id INT DEFAULT NULL, INDEX IDX_9B631D011C3E945F (remitente_id), INDEX IDX_9B631D01B564FBC1 (destinatario_id), INDEX IDX_9B631D01B55CDF0B (palabra_compartida_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE palabra (id INT AUTO_INCREMENT NOT NULL, palabra VARCHAR(255) NOT NULL, definicion LONGTEXT NOT NULL, fecha_creacion DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, usuario_id INT NOT NULL, INDEX IDX_11B8C74DB38439E (usuario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reto_diario (id INT AUTO_INCREMENT NOT NULL, fecha DATE NOT NULL, palabra_id INT NOT NULL, INDEX IDX_3184F47528EA1B16 (palabra_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE seguimiento (id INT AUTO_INCREMENT NOT NULL, fecha_seguimiento DATETIME NOT NULL, seguidor_id INT NOT NULL, seguido_id INT NOT NULL, INDEX IDX_1B2181D2924E960 (seguidor_id), INDEX IDX_1B2181D3572B040 (seguido_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, fecha_registro DATETIME NOT NULL, biografia LONGTEXT DEFAULT NULL, foto_perfil VARCHAR(255) DEFAULT NULL, roles JSON DEFAULT NULL, is_blocked TINYINT DEFAULT 0, reset_token VARCHAR(100) DEFAULT NULL, reset_token_expires_at DATETIME DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2265B05DE7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE valoracion (id INT AUTO_INCREMENT NOT NULL, like_activa TINYINT NOT NULL, fecha_creacion DATETIME NOT NULL, usuario_id INT NOT NULL, palabra_id INT NOT NULL, INDEX IDX_6D3DE0F4DB38439E (usuario_id), INDEX IDX_6D3DE0F428EA1B16 (palabra_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E70228EA1B16 FOREIGN KEY (palabra_id) REFERENCES palabra (id)');
        $this->addSql('ALTER TABLE comentario ADD CONSTRAINT FK_4B91E702DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D011C3E945F FOREIGN KEY (remitente_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D01B564FBC1 FOREIGN KEY (destinatario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE mensaje ADD CONSTRAINT FK_9B631D01B55CDF0B FOREIGN KEY (palabra_compartida_id) REFERENCES palabra (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE palabra ADD CONSTRAINT FK_11B8C74DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE reto_diario ADD CONSTRAINT FK_3184F47528EA1B16 FOREIGN KEY (palabra_id) REFERENCES palabra (id)');
        $this->addSql('ALTER TABLE seguimiento ADD CONSTRAINT FK_1B2181D2924E960 FOREIGN KEY (seguidor_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE seguimiento ADD CONSTRAINT FK_1B2181D3572B040 FOREIGN KEY (seguido_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE valoracion ADD CONSTRAINT FK_6D3DE0F4DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE valoracion ADD CONSTRAINT FK_6D3DE0F428EA1B16 FOREIGN KEY (palabra_id) REFERENCES palabra (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E70228EA1B16');
        $this->addSql('ALTER TABLE comentario DROP FOREIGN KEY FK_4B91E702DB38439E');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D011C3E945F');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D01B564FBC1');
        $this->addSql('ALTER TABLE mensaje DROP FOREIGN KEY FK_9B631D01B55CDF0B');
        $this->addSql('ALTER TABLE palabra DROP FOREIGN KEY FK_11B8C74DB38439E');
        $this->addSql('ALTER TABLE reto_diario DROP FOREIGN KEY FK_3184F47528EA1B16');
        $this->addSql('ALTER TABLE seguimiento DROP FOREIGN KEY FK_1B2181D2924E960');
        $this->addSql('ALTER TABLE seguimiento DROP FOREIGN KEY FK_1B2181D3572B040');
        $this->addSql('ALTER TABLE valoracion DROP FOREIGN KEY FK_6D3DE0F4DB38439E');
        $this->addSql('ALTER TABLE valoracion DROP FOREIGN KEY FK_6D3DE0F428EA1B16');
        $this->addSql('DROP TABLE comentario');
        $this->addSql('DROP TABLE mensaje');
        $this->addSql('DROP TABLE palabra');
        $this->addSql('DROP TABLE reto_diario');
        $this->addSql('DROP TABLE seguimiento');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE valoracion');
    }
}
