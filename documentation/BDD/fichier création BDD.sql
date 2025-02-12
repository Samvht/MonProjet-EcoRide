-- Création de ma base de données EcoRide
create database EcoRide;

--Utilisation de la BDD
use EcoRide;

--Création table Utilisateur avec les différentes colonnes (id / pseudo/ email/ password/ telephone / dateNaissance, photo)
create table Utilisateur (id int not null primary key auto_increment, pseudo varchar(50), email varchar(50), password varchar(255), telephone varchar(50), dateNaissance varchar(50), photo varchar(255));

--Modification de la BDD pour que les caractères spéciaux soient bien pris en compte 
alter DATABASE EcoRide DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--Visiualisation de la table utilisateur crée
SHOW COLUMNS FROM utilisateur;

--Rajout de not null sur la colonne pseudo/email/password
alter table utilisateur MODIFY column pseudo VARCHAR(50) not null;
alter table utilisateur MODIFY column email VARCHAR(50) not null;
alter table utilisateur MODIFY column password VARCHAR(255) not null;

--Rajout d'une contrainte unique sur la colonne email dans la table utilisateur (pour qu'il n'y est pas de doublon d'adresse mail)
ALTER TABLE utilisateur ADD CONSTRAINT unique_email UNIQUE (email);

--Création table Voiture avec les colonnes (id/modele/ immat / energie/couleur/ date 1ère immat)
create table Voiture (voiture_id int not null primary key auto_increment, modele varchar(50), immatriculation varchar(50) not null, energie varchar(255) not null, couleur varchar(50), date_premiere_immatriculation varchar(50));

--Relier ma table Voiture et ma table Utilisateur (relation 1 à plusieurs). Pour cela rajout de l'id Utilisateur dans ma table Voiture
ALTER TABLE Voiture
ADD id INT NULL;
--puis création clé étrangère pour relier les 2
ALTER TABLE Voiture
ADD FOREIGN KEY (id) REFERENCES Utilisateur (id)
ON DELETE NO ACTION;

--Création de ma table Marque
create table Marque (marque_id int not null primary key auto_increment, libelle varchar(50));

--Association de ma table Voiture et de ma Table Marque (relation 1 à plusieurs, rajout de l'ID marque dans Voiture)
ALTER TABLE Voiture
ADD marque_id INT NULL;
ALTER TABLE Voiture
ADD FOREIGN KEY (marque_id) REFERENCES Marque (id)
ON DELETE NO ACTION;

--Création de ma table Covoiturage
create table Covoiturage (covoiturage_id int not null primary key auto_increment, date_depart varchar(50) Not null, heure_depart varchar(50) Not null, lieu_depart varchar(50) Not null, date_arrivee varchar(50) Not null, heure_arrivee varchar(50) Not null, lieu_arrivee varchar(50) Not null, statut varchar(50), nbre_place varchar(50) Not null, prix_personne varchar(50) Not null);

--Association de ma table Voiture et de ma Table Covoiturage (relation 1 à plusieurs, rajout de l'ID marque dans Covoiturage)
ALTER TABLE Covoiturage
ADD voiture_id INT NULL;
ALTER TABLE Covoiturage
ADD FOREIGN KEY (voiture_id) REFERENCES Voiture (id)
ON DELETE NO ACTION;

--Création de ma table de liaison entre Utilisateur et Covoiturage, et association de ces 2 tables
CREATE TABLE utilisateur_covoiturage (
id int NOT NULL,
covoiturage_id int NOT NULL,
FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE NO ACTION ON UPDATE CASCADE,
FOREIGN KEY (covoiturage_id) REFERENCES covoiturage (id) ON DELETE NO ACTION ON UPDATE CASCADE,
PRIMARY KEY (id, covoiturage_id)
);

--Création table Avis
create table Avis (avis_id int not null primary key auto_increment, note varchar(50) Not null, commentaire varchar(255), statut varchar(50));

--création table role
create table Role (role_id int not null primary key auto_increment, libelle varchar(50) not null);

--création table association utilisateur et avis
CREATE TABLE utilisateur_avis(
id int NOT NULL,
avis_id int not null,
FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE no action ON UPDATE cascade,
FOREIGN KEY (avis_id) REFERENCES avis (id) ON DELETE no action ON UPDATE cascade,
PRIMARY KEY (id, avis_id)
);

--création table association utilisateur et role
CREATE TABLE utilisateur_role(
id int NOT NULL,
role_id int not null,
FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE no action ON UPDATE cascade,
FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE no action ON UPDATE cascade,
PRIMARY KEY (id, role_id)
);

--Visualisation des index (ici table association utilisateur_covoiturage
show index from utilisateur_covoiturage;

--suppression clé primaire et nom index (ici table utilisateur_covoiturage)
alter table utilisateur_covoiturage add primary key (id, covoiturage_id);
alter table utilisateur_covoiturage add index idx_utilisateur_id (id);
alter table utilisateur_covoiturage add index idx_covoiturage_id (covoiturage_id);

--rajout clé primaire et index
alter table utilisateur_covoiturage add primary key (id, covoiturage_id);
alter table utilisateur_covoiturage add index idx_utilisateur_id (id);
alter table utilisateur_covoiturage add index idx_covoiturage_id (covoiturage_id);

--Modification de mon ID utilisateur car il faut mieux un UUID niveau sécurité
ALTER TABLE utilisateur CHANGE utilisateur_id utilisateur_id BINARY(16) DEFAULT (UUID_TO_BIN(UUID(), TRUE)) NOT NULL UNIQUE;

--création table configuration
CREATE TABLE configuration ( id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL UNIQUE );
--Insérer mes rôles Admin, employé et utilisateur classique à l'intérieur
INSERT INTO configuration (name) VALUES ('Administrateur'), ('Employé'), ('Utilisateur Classique');

--liaison table utilisateur et configuration, rajoute id condif dans utilisateur (relation 1 config = plusieurs utilisateurs)
ALTER TABLE Utilisateur
ADD id INT NULL;
--ajout clé étrangère pour liaison
ALTER TABLE Utilisateur
ADD FOREIGN KEY (id) REFERENCES Configuration (id)
ON DELETE NO ACTION;






