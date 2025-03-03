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

include '../includes/header.php';
?>

<div class="container">
    <h1>Tableau de bord administrateur</h1>

    <section class="admin-section">
        <h2>Ajouter un match</h2>
        <form method="POST" action="actions/ajouter_match.php" class="admin-form">
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

            <div class="form-group">
                <label for="date_heure">Date et heure</label>
                <input type="datetime-local" id="date_heure" name="date_heure" required>
            </div>

            <div class="form-group">
                <label for="lieu">Lieu</label>
                <input type="text" id="lieu" name="lieu" required>
            </div>

            <button type="submit" class="btn">Ajouter le match</button>
        </form>
    </section>

    <section class="admin-section">
        <h2>Matchs à venir</h2>
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
                            <td>
                                <a href="modifier_match.php?id=<?= $match['id'] ?>" class="btn">Modifier</a>
                                <a href="actions/supprimer_match.php?id=<?= $match['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="admin-section">
        <h2>Matchs terminés</h2>
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
                            <td>
                                <?php if ($match['score_domicile'] !== null): ?>
                                    <?= $match['score_domicile'] ?> - <?= $match['score_exterieur'] ?>
                                <?php else: ?>
                                    <a href="saisir_score.php?id=<?= $match['id'] ?>" class="btn">Saisir le score</a>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></td>
                            <td>
                                <?php if ($match['score_domicile'] === null): ?>
                                    <a href="modifier_match.php?id=<?= $match['id'] ?>" class="btn">Modifier</a>
                                <?php endif; ?>
                                <a href="actions/supprimer_match.php?id=<?= $match['id'] ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<style>
.admin-section {
    margin-bottom: 40px;
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.admin-form {
    max-width: 600px;
    margin: 0 auto;
}

.admin-section h2 {
    margin-bottom: 20px;
}

table {
    margin-top: 20px;
}
</style>

<?php include '../includes/footer.php'; ?> 