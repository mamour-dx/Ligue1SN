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
                        <td class="team-name"><?= htmlspecialchars($equipe['nom']) ?></td>
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

<?php include 'includes/footer.php'; ?> 