<?php
session_start();
require_once 'config/database.php';

// Récupérer les matchs récents et prochains en une seule requête
$stmt = $pdo->query("
    (SELECT m.*, 
            e1.nom as equipe_domicile_nom, 
            e2.nom as equipe_exterieur_nom,
            'recent' as type
     FROM matchs m
     JOIN equipes e1 ON m.equipe_domicile = e1.id
     JOIN equipes e2 ON m.equipe_exterieur = e2.id
     WHERE m.date_heure <= NOW()
     ORDER BY m.date_heure DESC
     LIMIT 5)
    UNION ALL
    (SELECT m.*, 
            e1.nom as equipe_domicile_nom, 
            e2.nom as equipe_exterieur_nom,
            'prochain' as type
     FROM matchs m
     JOIN equipes e1 ON m.equipe_domicile = e1.id
     JOIN equipes e2 ON m.equipe_exterieur = e2.id
     WHERE m.date_heure > NOW()
     ORDER BY m.date_heure ASC
     LIMIT 5)
");
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$matchs_recents = array_filter($matchs, fn($m) => $m['type'] === 'recent');
$prochains_matchs = array_filter($matchs, fn($m) => $m['type'] === 'prochain');

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
    <section class="recent-matches">
        <h2>Matchs Récents</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Domicile</th>
                    <th>Score</th>
                    <th>Extérieur</th>
                    <th>Lieu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matchs_recents as $match): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($match['date_heure'])) ?></td>
                    <td><?= htmlspecialchars($match['equipe_domicile_nom']) ?></td>
                    <td>
                        <?php if ($match['score_domicile'] !== null): ?>
                            <?= $match['score_domicile'] ?> - <?= $match['score_exterieur'] ?>
                        <?php else: ?>
                            vs
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></td>
                    <td><?= htmlspecialchars($match['lieu']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="home-section">
        <div class="section-header">
            <i class="fas fa-calendar-alt"></i>
            <h2>Prochains Matchs</h2>
        </div>
        
        <div class="matches-grid">
            <?php foreach ($prochains_matchs as $match): ?>
                <div class="match-card">
                    <div class="match-date">
                        <i class="far fa-clock"></i>
                        <?= date('d/m/Y H:i', strtotime($match['date_heure'])) ?>
                    </div>
                    <div class="match-teams">
                        <div class="team">
                            <div class="team-name"><?= htmlspecialchars($match['equipe_domicile_nom']) ?></div>
                            <div>Domicile</div>
                        </div>
                        <div class="versus">VS</div>
                        <div class="team">
                            <div class="team-name"><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></div>
                            <div>Extérieur</div>
                        </div>
                    </div>
                    <div class="match-venue">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($match['lieu']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="standings">
        <div class="standings-title">
            <i class="fas fa-trophy"></i>
            <h2>Classement</h2>
        </div>
        <div class="standings-table">
            <table>
                <thead>
                    <tr>
                        <th>Pos</th>
                        <th class="team-name">Équipe</th>
                        <th>MJ</th>
                        <th>V</th>
                        <th>N</th>
                        <th>D</th>
                        <th>BP</th>
                        <th>BC</th>
                        <th>Diff</th>
                        <th class="points">Pts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classement as $index => $equipe): ?>
                        <tr>
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
    </section>
</div>

<?php include 'includes/footer.php'; ?> 