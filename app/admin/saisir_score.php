<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$match = null;
$error = '';
$success = '';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Récupérer les informations du match
    $stmt = $pdo->prepare("
        SELECT m.*, 
               e1.nom as equipe_domicile_nom, 
               e2.nom as equipe_exterieur_nom
        FROM matchs m
        JOIN equipes e1 ON m.equipe_domicile = e1.id
        JOIN equipes e2 ON m.equipe_exterieur = e2.id
        WHERE m.id = ?
    ");
    $stmt->execute([$id]);
    $match = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['match_id'] ?? 0;
    $score_domicile = $_POST['score_domicile'] ?? '';
    $score_exterieur = $_POST['score_exterieur'] ?? '';

    if (!is_numeric($score_domicile) || !is_numeric($score_exterieur) || $score_domicile < 0 || $score_exterieur < 0) {
        $error = "Les scores doivent être des nombres positifs";
    } else {
        try {
            $pdo->beginTransaction();

            // Mettre à jour le score du match
            $stmt = $pdo->prepare("
                UPDATE matchs 
                SET score_domicile = ?, score_exterieur = ?
                WHERE id = ?
            ");
            $stmt->execute([$score_domicile, $score_exterieur, $id]);

            // Récupérer les IDs des équipes
            $stmt = $pdo->prepare("SELECT equipe_domicile, equipe_exterieur FROM matchs WHERE id = ?");
            $stmt->execute([$id]);
            $match_info = $stmt->fetch(PDO::FETCH_ASSOC);

            // Mettre à jour les statistiques des équipes
            $equipes = [
                $match_info['equipe_domicile'] => $score_domicile,
                $match_info['equipe_exterieur'] => $score_exterieur
            ];

            foreach ($equipes as $equipe_id => $buts_marques) {
                $autre_equipe_id = ($equipe_id == $match_info['equipe_domicile']) ? $match_info['equipe_exterieur'] : $match_info['equipe_domicile'];
                $buts_encaisses = ($equipe_id == $match_info['equipe_domicile']) ? $score_exterieur : $score_domicile;

                // Déterminer le résultat
                $victoire = 0;
                $nul = 0;
                $defaite = 0;
                $points = 0;

                if ($buts_marques > $buts_encaisses) {
                    $victoire = 1;
                    $points = 3;
                } elseif ($buts_marques == $buts_encaisses) {
                    $nul = 1;
                    $points = 1;
                } else {
                    $defaite = 1;
                }

                // Mettre à jour les statistiques
                $stmt = $pdo->prepare("
                    UPDATE equipes 
                    SET matchs_joues = matchs_joues + 1,
                        victoires = victoires + ?,
                        nuls = nuls + ?,
                        defaites = defaites + ?,
                        buts_marques = buts_marques + ?,
                        buts_encaisses = buts_encaisses + ?,
                        difference_buts = buts_marques - buts_encaisses,
                        points = points + ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $victoire,
                    $nul,
                    $defaite,
                    $buts_marques,
                    $buts_encaisses,
                    $points,
                    $equipe_id
                ]);
            }

            $pdo->commit();
            $success = "Le score a été enregistré et les statistiques ont été mises à jour";
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Erreur lors de la mise à jour: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="score-form">
        <h2>Saisir le score du match</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($match): ?>
            <form method="POST" action="" class="admin-form">
                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                <div class="match-info">
                    <p>Date: <?= date('d/m/Y H:i', strtotime($match['date_heure'])) ?></p>
                    <p>Lieu: <?= htmlspecialchars($match['lieu']) ?></p>
                </div>

                <div class="score-inputs">
                    <div class="team-score">
                        <label><?= htmlspecialchars($match['equipe_domicile_nom']) ?></label>
                        <input type="number" name="score_domicile" min="0" required>
                    </div>

                    <div class="score-separator">-</div>

                    <div class="team-score">
                        <label><?= htmlspecialchars($match['equipe_exterieur_nom']) ?></label>
                        <input type="number" name="score_exterieur" min="0" required>
                    </div>
                </div>

                <button type="submit" class="btn">Enregistrer le score</button>
            </form>
        <?php else: ?>
            <p>Match non trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.score-form {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background: white;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.match-info {
    margin-bottom: 20px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.score-inputs {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.team-score {
    text-align: center;
}

.team-score label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

.team-score input {
    width: 80px;
    text-align: center;
    font-size: 1.2em;
}

.score-separator {
    font-size: 1.5em;
    font-weight: bold;
}
</style>

<?php include '../includes/footer.php'; ?> 