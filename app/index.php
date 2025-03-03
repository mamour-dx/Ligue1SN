<?php
session_start();
require_once 'config/database.php';

// Récupérer les matchs récents
$stmt = $pdo->query("
    SELECT m.*, 
           e1.nom as equipe_domicile_nom, 
           e2.nom as equipe_exterieur_nom
    FROM matchs m
    JOIN equipes e1 ON m.equipe_domicile = e1.id
    JOIN equipes e2 ON m.equipe_exterieur = e2.id
    WHERE m.date_heure <= NOW()
    ORDER BY m.date_heure DESC
    LIMIT 5
");
$matchs_recents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les prochains matchs
$stmt = $pdo->query("
    SELECT m.*, 
           e1.nom as equipe_domicile_nom, 
           e2.nom as equipe_exterieur_nom
    FROM matchs m
    JOIN equipes e1 ON m.equipe_domicile = e1.id
    JOIN equipes e2 ON m.equipe_exterieur = e2.id
    WHERE m.date_heure > NOW()
    ORDER BY m.date_heure ASC
    LIMIT 5
");
$prochains_matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <section class="upcoming-matches">
        <h2>Prochains Matchs</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Domicile</th>
                    <th>Extérieur</th>
                    <th>Lieu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prochains_matchs as $match): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($match['date_heure'])) ?></td>
                    <td><?= htmlspecialchars($match['equipe_domicile_nom']) ?></td>
                    <td><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></td>
                    <td><?= htmlspecialchars($match['lieu']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="standings">
        <h2>Classement</h2>
        <table>
            <thead>
                <tr>
                    <th>Pos</th>
                    <th>Équipe</th>
                    <th>MJ</th>
                    <th>V</th>
                    <th>N</th>
                    <th>D</th>
                    <th>BP</th>
                    <th>BC</th>
                    <th>DB</th>
                    <th>Pts</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classement as $index => $equipe): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($equipe['nom']) ?></td>
                    <td><?= $equipe['matchs_joues'] ?></td>
                    <td><?= $equipe['victoires'] ?></td>
                    <td><?= $equipe['nuls'] ?></td>
                    <td><?= $equipe['defaites'] ?></td>
                    <td><?= $equipe['buts_marques'] ?></td>
                    <td><?= $equipe['buts_encaisses'] ?></td>
                    <td><?= $equipe['difference_buts'] ?></td>
                    <td><?= $equipe['points'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<?php include 'includes/footer.php'; ?> 