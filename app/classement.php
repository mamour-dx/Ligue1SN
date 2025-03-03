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

<div class="standings-page">
    <div class="standings-header">
        <div class="section-header">
            <i class="fas fa-trophy"></i>
            <h2>Classement Ligue 1 Sénégalaise</h2>
        </div>
        <div class="standings-legend">
            <div class="legend-item">
                <span class="legend-color champions"></span>
                <span>Qualification Ligue des Champions</span>
            </div>
            <div class="legend-item">
                <span class="legend-color relegation"></span>
                <span>Relégation</span>
            </div>
        </div>
    </div>

    <div class="standings-table">
        <table>
            <thead>
                <tr>
                    <th class="position-header">Pos</th>
                    <th class="team-header">Équipe</th>
                    <th>MJ</th>
                    <th>V</th>
                    <th>N</th>
                    <th>D</th>
                    <th>BP</th>
                    <th>BC</th>
                    <th>Diff</th>
                    <th class="points-header">Pts</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classement as $index => $equipe): ?>
                    <tr class="<?= $index < 2 ? 'champions-row' : ($index >= count($classement)-2 ? 'relegation-row' : '') ?>">
                        <td class="position"><?= $index + 1 ?></td>
                        <td class="team-name">
                            <?= htmlspecialchars($equipe['nom']) ?>
                        </td>
                        <td class="stats"><?= $equipe['matchs_joues'] ?></td>
                        <td class="stats"><?= $equipe['victoires'] ?></td>
                        <td class="stats"><?= $equipe['nuls'] ?></td>
                        <td class="stats"><?= $equipe['defaites'] ?></td>
                        <td class="stats"><?= $equipe['buts_marques'] ?></td>
                        <td class="stats"><?= $equipe['buts_encaisses'] ?></td>
                        <td class="stats <?= $equipe['difference_buts'] > 0 ? 'goal-diff-positive' : ($equipe['difference_buts'] < 0 ? 'goal-diff-negative' : '') ?>">
                            <?= $equipe['difference_buts'] > 0 ? '+' : '' ?><?= $equipe['difference_buts'] ?>
                        </td>
                        <td class="points"><?= $equipe['points'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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