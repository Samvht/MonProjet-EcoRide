<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218111054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs 
        $this->addSql('ALTER TABLE covoiturage CHANGE nbre_place nbre_place INT NOT NULL'); 
        $this->addSql('ALTER TABLE covoiturage CHANGE prix_personne prix_personne DOUBLE PRECISION NOT NULL'); 
        $this->addSql('ALTER TABLE covoiturage CHANGE voiture_id voiture_id INT NOT NULL');
        $columnExists = $schema->getTable('covoiturage')->hasColumn('covoiturage_id'); 
            if (!$columnExists) { 
                $this->addSql('ALTER TABLE covoiturage ADD covoiturage_id INT AUTO_INCREMENT PRIMARY KEY'); 
        }
        
        $this->addSql('ALTER TABLE marque MODIFY id INT;'); 
        $this->addSql('ALTER TABLE marque CHANGE id marque_id INT;'); 
        $this->addSql('ALTER TABLE marque MODIFY marque_id INT AUTO_INCREMENT;');
        $this->addSql('ALTER TABLE marque DROP COLUMN relation');
        
        $this->addSql('ALTER TABLE role MODIFY role_id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON role');
        $this->addSql('ALTER TABLE role CHANGE role_id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE role ADD PRIMARY KEY (id)');

        
        $this->addSql('ALTER TABLE utilisateur_covoiturage CHANGE id utilisateur_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_DC21931A6B3CA4B ON utilisateur_covoiturage (utilisateur_id)');
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP INDEX PRIMARY');
        $this->addSql('ALTER TABLE utilisateur_covoiturage ADD PRIMARY KEY (utilisateur_id, covoiturage_id)');
        $this->addSql('CREATE INDEX IDX_DC21931A62671590 ON utilisateur_covoiturage (covoiturage_id)');

        $this->addSql('ALTER TABLE utilisateur_avis DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE utilisateur_avis DROP INDEX IF EXISTS IDX_4610C7CA1B7E2029'); 
        $this->addSql('ALTER TABLE utilisateur_avis DROP INDEX IF EXISTS IDX_4610C7CA197E709F'); 
        $this->addSql('ALTER TABLE utilisateur_avis CHANGE id utilisateur_id INT NOT NULL'); 
        $this->addSql('ALTER TABLE utilisateur_avis CHANGE avis_id avis_id INT NOT NULL'); 
        $this->addSql('CREATE INDEX IDX_4610C7CA1B7E2029 ON utilisateur_avis (utilisateur_id)'); 
        $this->addSql('CREATE INDEX IDX_4610C7CA197E709F ON utilisateur_avis (avis_id)'); 
        $this->addSql('ALTER TABLE utilisateur_avis ADD PRIMARY KEY (utilisateur_id, avis_id)');
        
        $this->addSql('ALTER TABLE utilisateur_role ADD utilisateur_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_9EE8E650FB88E14F ON utilisateur_role (utilisateur_id)');
        $this->addSql('ALTER TABLE utilisateur_role ADD PRIMARY KEY (utilisateur_id, role_id)');
        $this->addSql('ALTER TABLE utilisateur_role RENAME INDEX idx_role_id TO IDX_9EE8E650D60322AC');
        
        $this->addSql('ALTER TABLE voiture CHANGE id voiture_id INT AUTO_INCREMENT PRIMARY KEY'); 
        $this->addSql('ALTER TABLE voiture CHANGE energie energie VARCHAR(50) NOT NULL'); 
        $this->addSql('ALTER TABLE voiture CHANGE marque_id marque_id INT NOT NULL'); 
        $this->addSql('CREATE INDEX IDX_1234C7CA1B7E2029 ON voiture (utilisateur_id)');
        $this->addSql('ALTER TABLE voiture RENAME INDEX marque_id TO IDX_E9E2810F4827B9B2');

        $this->addSql('ALTER TABLE utilisateur CHANGE id utilisateur_id INT AUTO_INCREMENT PRIMARY KEY');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE role CHANGE id role_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE INDEX `primary` ON role (role_id)');
 
        $this->addSql('ALTER TABLE covoiturage DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE covoiturage DROP COLUMN covoiturage_id'); 
        $this->addSql('ALTER TABLE covoiturage CHANGE nbre_place nbre_place INT'); 
        $this->addSql('ALTER TABLE covoiturage CHANGE prix_personne prix_personne DOUBLE'); 
        $this->addSql('ALTER TABLE covoiturage CHANGE voiture_id voiture_id INT');
         

        $this->addSql('ALTER TABLE voiture DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE voiture CHANGE voiture_id id INT'); 
        $this->addSql('ALTER TABLE voiture CHANGE energie energie VARCHAR(50)'); 
        $this->addSql('ALTER TABLE voiture CHANGE marque_id marque_id INT'); 
        $this->addSql('ALTER TABLE voiture RENAME INDEX IDX_E9E2810F4827B9B2 TO marque_id');

        $this->addSql('ALTER TABLE utilisateur_role DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE utilisateur_role RENAME INDEX IDX_9EE8E650D60322AC TO idx_role_id'); 
        $this->addSql('DROP INDEX IDX_9EE8E650FB88E14F ON utilisateur_role'); 
        $this->addSql('ALTER TABLE utilisateur_role DROP COLUMN utilisateur_id');
        $this->addSql('ALTER TABLE utilisateur_role CHANGE utilisateur_id utilisateur_id INT NOT NULL');

        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP INDEX IDX_DC21931A6B3CA4B'); 
        $this->addSql('ALTER TABLE utilisateur_covoiturage DROP INDEX IDX_DC21931A62671590'); 
        $this->addSql('ALTER TABLE utilisateur_covoiturage CHANGE utilisateur_id utilisateur_id  INT NOT NULL');

        $this->addSql('ALTER TABLE utilisateur_avis DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE utilisateur_avis DROP INDEX IF EXISTS IDX_4610C7CA1B7E2029'); 
        $this->addSql('ALTER TABLE utilisateur_avis DROP INDEX IF EXISTS IDX_4610C7CA197E709F'); 
        $this->addSql('ALTER TABLE utilisateur_avis CHANGE avis_id id INT NOT NULL'); 
        $this->addSql('ALTER TABLE utilisateur_avis CHANGE utilisateur_id utilisateur_id INT NOT NULL');

        $this->addSql('ALTER TABLE marque DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE marque MODIFY marque_id INT;'); 
        $this->addSql('ALTER TABLE marque CHANGE marque_id id INT;'); 
        $this->addSql('ALTER TABLE marque MODIFY id INT AUTO_INCREMENT PRIMARY KEY;');
        $this->addSql('ALTER TABLE marque ADD relation VARCHAR(50) NOT NULL');

        $this->addSql('ALTER TABLE utilisateur DROP PRIMARY KEY'); 
        $this->addSql('ALTER TABLE utilisateur CHANGE utilisateur_id id INT AUTO_INCREMENT PRIMARY KEY');
    }
}