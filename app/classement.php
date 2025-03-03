<?php
session_start();
require_once 'config/database.php';

// Récupérer le classement
$stmt = $pdo->query("
    SELECT *
    FROM equipes
    ORDER BY points DESC, difference_buts DESC, buts_marques DESC
");
$classement = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container">
    <h1>Classement</h1>

    <div class="standings-container">
        <table class="standings-table">
            <thead>
                <tr>
                    <th class="position">#</th>
                    <th class="team-name">Équipe</th>
                    <th>MJ</th>
                    <th>V</th>
                    <th>N</th>
                    <th>D</th>
                    <th>BP</th>
                    <th>BC</th>
                    <th>DB</th>
                    <th class="points">Pts</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classement as $index => $equipe): ?>
                    <tr class="<?= $index < 3 ? 'top-three' : '' ?>">
                        <td class="position"><?= $index + 1 ?></td>
                        <td class="team-name"><?= htmlspecialchars($equipe['nom']) ?></td>
                        <td><?= $equipe['matchs_joues'] ?></td>
                        <td><?= $equipe['victoires'] ?></td>
                        <td><?= $equipe['nuls'] ?></td>
                        <td><?= $equipe['defaites'] ?></td>
                        <td><?= $equipe['buts_marques'] ?></td>
                        <td><?= $equipe['buts_encaisses'] ?></td>
                        <td class="<?= $equipe['difference_buts'] >= 0 ? 'positive' : 'negative' ?>">
                            <?= sprintf('%+d', $equipe['difference_buts']) ?>
                        </td>
                        <td class="points"><?= $equipe['points'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="legend">
            <h3>Légende</h3>
            <div class="legend-item qualification">
                <span class="legend-color"></span>
                <span>Qualification pour la phase finale</span>
            </div>
        </div>
    </div>
</div>

<style>
.standings-container {
    background: white;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-top: 20px;
}

.standings-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.standings-table th,
.standings-table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

.standings-table th {
    background-color: #2c3e50;
    color: white;
    font-weight: bold;
}

.standings-table .team-name {
    text-align: left;
    padding-left: 20px;
}

.standings-table .position {
    font-weight: bold;
    width: 40px;
}

.standings-table .points {
    font-weight: bold;
    background-color: #f8f9fa;
}

.top-three {
    background-color: #e8f5e9;
}

.positive {
    color: #28a745;
}

.negative {
    color: #dc3545;
}

.legend {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.legend h3 {
    margin-bottom: 10px;
    color: #2c3e50;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.legend-color {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    border-radius: 3px;
}

.qualification .legend-color {
    background-color: #e8f5e9;
}

@media (max-width: 768px) {
    .standings-table {
        font-size: 14px;
    }

    .standings-table th,
    .standings-table td {
        padding: 8px 4px;
    }

    .team-name {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}
</style>

<?php include 'includes/footer.php'; ?> 