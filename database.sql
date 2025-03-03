-- Création de la base de données
CREATE DATABASE IF NOT EXISTS ligue1_senegal;
USE ligue1_senegal;

-- Table des équipes
CREATE TABLE equipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    matchs_joues INT DEFAULT 0,
    victoires INT DEFAULT 0,
    nuls INT DEFAULT 0,
    defaites INT DEFAULT 0,
    buts_marques INT DEFAULT 0,
    buts_encaisses INT DEFAULT 0,
    difference_buts INT DEFAULT 0,
    points INT DEFAULT 0
);

-- Table des matchs
CREATE TABLE matchs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipe_domicile INT NOT NULL,
    equipe_exterieur INT NOT NULL,
    date_heure DATETIME NOT NULL,
    lieu VARCHAR(255),
    score_domicile INT DEFAULT NULL,
    score_exterieur INT DEFAULT NULL,
    FOREIGN KEY (equipe_domicile) REFERENCES equipes(id),
    FOREIGN KEY (equipe_exterieur) REFERENCES equipes(id)
);

-- Table des administrateurs
CREATE TABLE administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insertion d'un administrateur par défaut avec mot de passe en clair
INSERT INTO administrateurs (username, password) 
VALUES ('admin', 'admin123');

-- Insertion de quelques équipes de la Ligue 1 sénégalaise
INSERT INTO equipes (nom) VALUES 
('Génération Foot'),
('Casa Sports'),
('Jaraaf'),
('Teungueth FC'),
('AS Pikine'),
('Diambars'),
('US Gorée'),
('CNEPS Excellence'),
('AS Douanes'),
('Dakar Sacré-Cœur'); 