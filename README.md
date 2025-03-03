# Plateforme de Suivi du Championnat de Ligue 1 Sénégalaise

## Description

Cette plateforme permet de suivre en temps réel le championnat de Ligue 1 sénégalaise. Elle propose un classement mis à jour automatiquement, l'affichage des matchs récents et à venir, ainsi qu'une interface d'administration pour la gestion des matchs et des scores.

## Fonctionnalités Principales

### 1. Section Classement

- Affichage du classement avec :
  - Position dans le classement
  - Nom de l'équipe
  - Nombre de matchs joués
  - Nombre de victoires, nuls et défaites
  - Nombre de buts marqués et encaissés
  - Différence de buts
  - Nombre de points
- Mise à jour automatique du classement après saisie des scores des matchs.

### 2. Section Matchs

- Affichage des matchs récents avec :
  - Date et heure du match
  - Équipes concernées
  - Score final (si renseigné)
- Affichage des matchs à venir avec :
  - Date et heure prévue
  - Équipes concernées
  - Lieu du match
- Filtrage par équipe ou par date.

### 3. Administration (Accès Restreint)

- Interface sécurisée pour les administrateurs permettant :
  - L'ajout de nouveaux matchs avec date, heure et équipes concernées
  - La mise à jour des scores après un match
  - La modification ou suppression des matchs erronés

## Technologies Utilisées

- **Backend :** PHP (sans framework)
- **Base de données :** MySQL
- **Frontend :** HTML, CSS, JavaScript

## Installation

### Prérequis

- Serveur Apache avec support PHP
- MySQL ou MariaDB

### Configuration

1. **Cloner le projet**
   ```sh
   git clone https://github.com/votre-repo/ligue1-senegal.git
   cd ligue1-senegal
   ```
2. **Configurer la base de données**

   - Importer le fichier `database.sql` dans MySQL :

     ```sql
     CREATE DATABASE ligue1_senegal;
     USE ligue1_senegal;

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

     CREATE TABLE administrateurs (
         id INT AUTO_INCREMENT PRIMARY KEY,
         username VARCHAR(50) NOT NULL UNIQUE,
         password_hash VARCHAR(255) NOT NULL
     );
     ```

3. **Configurer la connexion à la base de données**

   - Modifier le fichier `config.php` avec vos informations MySQL :
     ```php
     <?php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'ligue1_senegal');
     define('DB_USER', 'votre_utilisateur');
     define('DB_PASSWORD', 'votre_mot_de_passe');
     ?>
     ```

4. **Lancer l'application**
   - Déplacer les fichiers vers votre serveur Apache et accéder à `http://localhost/ligue1-senegal`

## Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.
