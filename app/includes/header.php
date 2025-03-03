<?php 
require_once __DIR__ . '/../config/config.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ligue 1 Sénégalaise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f6f8;
            min-height: 100vh;
        }

        /* Header et Navigation */
        header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-links a:hover {
            background-color: rgba(255,255,255,0.1);
        }

        /* Container principal */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 20px;
        }

        /* Formulaires */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        /* Boutons */
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        /* Alertes */
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .nav-links {
                flex-direction: column;
                gap: 0.5rem;
            }

            .nav-links a {
                display: block;
            }
        }

        /* Styles pour la page d'accueil */
        .home-section {
            margin-bottom: 3rem;
        }

        .section-header {
            color: #1e293b;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-header i {
            color: #3b82f6;
        }

        /* Styles pour les prochains matchs */
        .matches-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .match-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .match-date {
            color: #64748b;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .match-teams {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .team {
            text-align: center;
            flex: 1;
        }

        .team-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .versus {
            font-weight: 600;
            color: #94a3b8;
            padding: 0 1rem;
        }

        .match-venue {
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        /* Styles pour le classement */
        .standings-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
    </style>
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