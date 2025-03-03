<?php 
require_once __DIR__ . '/../config/config.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ligue 1 Sénégalaise</title>
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <h1>Ligue 1 Sénégalaise</h1>
            </div>
            <ul class="nav-links">
                <li><a href="<?= url('index.php') ?>">Accueil</a></li>
                <li><a href="<?= url('classement.php') ?>">Classement</a></li>
                <li><a href="<?= url('matchs.php') ?>">Matchs</a></li>
                <?php if(isset($_SESSION['admin_id'])): ?>
                    <li><a href="<?= url('admin/dashboard.php') ?>">Administration</a></li>
                    <li><a href="<?= url('admin/logout.php') ?>">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="<?= url('admin/login.php') ?>">Connexion Admin</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="container"> 