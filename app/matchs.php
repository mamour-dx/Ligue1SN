<?php
session_start();
require_once 'config/database.php';

// Récupérer toutes les équipes pour le filtre
$stmt = $pdo->query("SELECT id, nom FROM equipes ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Construire la requête de base
$sql = "
    SELECT m.*, 
           e1.nom as equipe_domicile_nom, 
           e2.nom as equipe_exterieur_nom
    FROM matchs m
    JOIN equipes e1 ON m.equipe_domicile = e1.id
    JOIN equipes e2 ON m.equipe_exterieur = e2.id
    WHERE 1=1
";

$params = [];

// Appliquer les filtres
if (isset($_GET['equipe']) && !empty($_GET['equipe'])) {
    $sql .= " AND (m.equipe_domicile = ? OR m.equipe_exterieur = ?)";
    $params[] = $_GET['equipe'];
    $params[] = $_GET['equipe'];
}

if (isset($_GET['date']) && !empty($_GET['date'])) {
    $sql .= " AND DATE(m.date_heure) = ?";
    $params[] = $_GET['date'];
}

$sql .= " ORDER BY m.date_heure DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container">
    <h1>Matchs</h1>

    <div class="filters">
        <form method="GET" action="" class="filter-form">
            <div class="form-group">
                <label for="equipe">Équipe</label>
                <select name="equipe" id="equipe">
                    <option value="">Toutes les équipes</option>
                    <?php foreach ($equipes as $equipe): ?>
                        <option value="<?= $equipe['id'] ?>" <?= isset($_GET['equipe']) && $_GET['equipe'] == $equipe['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($equipe['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" id="date" name="date" value="<?= $_GET['date'] ?? '' ?>">
            </div>

            <button type="submit" class="btn">Filtrer</button>
            <a href="matchs.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>

    <div class="matches-list">
        <?php if (empty($matchs)): ?>
            <p>Aucun match trouvé.</p>
        <?php else: ?>
            <?php
            $current_date = null;
            foreach ($matchs as $match):
                $match_date = date('Y-m-d', strtotime($match['date_heure']));
                if ($match_date !== $current_date):
                    if ($current_date !== null) echo '</div>';
                    $current_date = $match_date;
            ?>
                <h3 class="date-header"><?= date('d/m/Y', strtotime($match_date)) ?></h3>
                <div class="matches-group">
            <?php endif; ?>
                
                <div class="match-card">
                    <div class="match-time"><?= date('H:i', strtotime($match['date_heure'])) ?></div>
                    <div class="match-teams">
                        <div class="team home"><?= htmlspecialchars($match['equipe_domicile_nom']) ?></div>
                        <div class="score">
                            <?php if ($match['score_domicile'] !== null): ?>
                                <?= $match['score_domicile'] ?> - <?= $match['score_exterieur'] ?>
                            <?php else: ?>
                                vs
                            <?php endif; ?>
                        </div>
                        <div class="team away"><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></div>
                    </div>
                    <div class="match-venue"><?= htmlspecialchars($match['lieu']) ?></div>
                </div>
            <?php endforeach; ?>
            <?php if (!empty($matchs)): ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.filters {
    margin-bottom: 30px;
    padding: 20px;
    background: white;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.filter-form {
    display: flex;
    gap: 20px;
    align-items: flex-end;
}

.date-header {
    margin: 30px 0 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #2c3e50;
    color: #2c3e50;
}

.matches-group {
    display: grid;
    gap: 15px;
}

.match-card {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 20px;
}

.match-time {
    font-weight: bold;
    color: #2c3e50;
}

.match-teams {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 15px;
    align-items: center;
    text-align: center;
}

.team {
    font-weight: 500;
}

.score {
    font-weight: bold;
    font-size: 1.2em;
    padding: 5px 10px;
    background: #f8f9fa;
    border-radius: 4px;
    min-width: 80px;
}

.match-venue {
    color: #666;
    font-size: 0.9em;
}

.btn-secondary {
    background-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

@media (max-width: 768px) {
    .filter-form {
        flex-direction: column;
        gap: 10px;
    }

    .match-card {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 10px;
    }

    .match-teams {
        order: -1;
    }
}
</style>

<?php include 'includes/footer.php'; ?> 