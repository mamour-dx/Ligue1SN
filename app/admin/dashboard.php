<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Récupérer toutes les équipes pour les formulaires
$stmt = $pdo->query("SELECT id, nom FROM equipes ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les matchs
$stmt = $pdo->query("
    SELECT m.*, 
           e1.nom as equipe_domicile_nom, 
           e2.nom as equipe_exterieur_nom
    FROM matchs m
    JOIN equipes e1 ON m.equipe_domicile = e1.id
    JOIN equipes e2 ON m.equipe_exterieur = e2.id
    ORDER BY m.date_heure DESC
");
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="admin-dashboard">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-tachometer-alt"></i> Administration</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="../index.php" class="nav-link">
                <i class="fas fa-home"></i> Retour au site
            </a>
            <a href="creer_admin.php" class="nav-link">
                <i class="fas fa-user-plus"></i> Créer un administrateur
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Tableau de bord</h1>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <section class="admin-section">
            <div class="section-header">
                <h2><i class="fas fa-plus-circle"></i> Ajouter un match</h2>
            </div>
            <form method="POST" action="actions/ajouter_match.php" class="admin-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="equipe_domicile">Équipe domicile</label>
                        <select name="equipe_domicile" id="equipe_domicile" required>
                            <option value="">Sélectionner une équipe</option>
                            <?php foreach ($equipes as $equipe): ?>
                                <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="equipe_exterieur">Équipe extérieur</label>
                        <select name="equipe_exterieur" id="equipe_exterieur" required>
                            <option value="">Sélectionner une équipe</option>
                            <?php foreach ($equipes as $equipe): ?>
                                <option value="<?= $equipe['id'] ?>"><?= htmlspecialchars($equipe['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_heure">Date et heure</label>
                        <input type="datetime-local" id="date_heure" name="date_heure" required>
                    </div>

                    <div class="form-group">
                        <label for="lieu">Lieu</label>
                        <input type="text" id="lieu" name="lieu" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter le match
                </button>
            </form>
        </section>

        <section class="admin-section">
            <div class="section-header">
                <h2><i class="fas fa-calendar-alt"></i> Matchs à venir</h2>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Domicile</th>
                            <th>Extérieur</th>
                            <th>Lieu</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matchs as $match): ?>
                            <?php if (strtotime($match['date_heure']) > time()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($match['date_heure'])) ?></td>
                                    <td><?= htmlspecialchars($match['equipe_domicile_nom']) ?></td>
                                    <td><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></td>
                                    <td><?= htmlspecialchars($match['lieu']) ?></td>
                                    <td class="actions">
                                        <a href="modifier_match.php?id=<?= $match['id'] ?>" class="btn btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="actions/supprimer_match.php?id=<?= $match['id'] ?>" 
                                           class="btn btn-delete"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-section">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> Matchs terminés</h2>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Domicile</th>
                            <th>Score</th>
                            <th>Extérieur</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matchs as $match): ?>
                            <?php if (strtotime($match['date_heure']) <= time()): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($match['date_heure'])) ?></td>
                                    <td><?= htmlspecialchars($match['equipe_domicile_nom']) ?></td>
                                    <td class="score">
                                        <?php if ($match['score_domicile'] !== null): ?>
                                            <?= $match['score_domicile'] ?> - <?= $match['score_exterieur'] ?>
                                        <?php else: ?>
                                            <a href="saisir_score.php?id=<?= $match['id'] ?>" class="btn btn-score">
                                                <i class="fas fa-plus"></i> Saisir le score
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></td>
                                    <td class="actions">
                                        <?php if ($match['score_domicile'] === null): ?>
                                            <a href="modifier_match.php?id=<?= $match['id'] ?>" class="btn btn-edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="actions/supprimer_match.php?id=<?= $match['id'] ?>" 
                                           class="btn btn-delete"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<style>
/* Reset et styles de base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f6f9;
    color: #333;
}

/* Layout */
.admin-dashboard {
    display: flex;
    min-height: 100vh;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    padding: 1rem;
}

.sidebar-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar-header h2 {
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-nav {
    margin-top: 2rem;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1rem;
    color: white;
    text-decoration: none;
    transition: background-color 0.3s;
    border-radius: 4px;
}

.nav-link:hover {
    background-color: rgba(255,255,255,0.1);
}

/* Main Content */
.main-content {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
}

.content-header {
    margin-bottom: 2rem;
}

.content-header h1 {
    font-size: 1.8rem;
    color: #2c3e50;
}

/* Sections */
.admin-section {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.section-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.section-header h2 {
    font-size: 1.2rem;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Forms */
.admin-form {
    max-width: 800px;
}

.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    flex: 1;
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

/* Tables */
.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
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

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #3498db;
    color: white;
}

.btn-primary:hover {
    background-color: #2980b9;
}

.btn-edit {
    background-color: #f39c12;
    color: white;
    padding: 0.5rem;
}

.btn-delete {
    background-color: #e74c3c;
    color: white;
    padding: 0.5rem;
}

.btn-score {
    background-color: #2ecc71;
    color: white;
    padding: 0.5rem 1rem;
}

.actions {
    display: flex;
    gap: 0.5rem;
}

/* Alerts */
.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

/* Responsive Design */
@media (max-width: 768px) {
    .admin-dashboard {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
    }

    .form-row {
        flex-direction: column;
    }

    .actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

</body>
</html> 