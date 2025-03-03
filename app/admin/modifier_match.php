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

// Récupérer toutes les équipes
$stmt = $pdo->query("SELECT id, nom FROM equipes ORDER BY nom");
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    $equipe_domicile = $_POST['equipe_domicile'] ?? '';
    $equipe_exterieur = $_POST['equipe_exterieur'] ?? '';
    $date_heure = $_POST['date_heure'] ?? '';
    $lieu = $_POST['lieu'] ?? '';

    // Validation
    if (empty($equipe_domicile) || empty($equipe_exterieur) || empty($date_heure) || empty($lieu)) {
        $error = "Tous les champs sont obligatoires";
    } elseif ($equipe_domicile === $equipe_exterieur) {
        $error = "Les équipes doivent être différentes";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE matchs 
                SET equipe_domicile = ?,
                    equipe_exterieur = ?,
                    date_heure = ?,
                    lieu = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $equipe_domicile,
                $equipe_exterieur,
                $date_heure,
                $lieu,
                $id
            ]);

            $success = "Le match a été modifié avec succès";
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification du match: " . $e->getMessage();
        }
    }
}

include '../includes/header.php';
?>

<div class="container">
    <div class="edit-form">
        <h2>Modifier le match</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($match): ?>
            <form method="POST" action="" class="admin-form">
                <input type="hidden" name="match_id" value="<?= $match['id'] ?>">

                <div class="form-group">
                    <label for="equipe_domicile">Équipe domicile</label>
                    <select name="equipe_domicile" id="equipe_domicile" required>
                        <?php foreach ($equipes as $equipe): ?>
                            <option value="<?= $equipe['id'] ?>" <?= $equipe['id'] == $match['equipe_domicile'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($equipe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="equipe_exterieur">Équipe extérieur</label>
                    <select name="equipe_exterieur" id="equipe_exterieur" required>
                        <?php foreach ($equipes as $equipe): ?>
                            <option value="<?= $equipe['id'] ?>" <?= $equipe['id'] == $match['equipe_exterieur'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($equipe['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_heure">Date et heure</label>
                    <input type="datetime-local" id="date_heure" name="date_heure" 
                           value="<?= date('Y-m-d\TH:i', strtotime($match['date_heure'])) ?>" required>
                </div>

                <div class="form-group">
                    <label for="lieu">Lieu</label>
                    <input type="text" id="lieu" name="lieu" value="<?= htmlspecialchars($match['lieu']) ?>" required>
                </div>

                <button type="submit" class="btn">Enregistrer les modifications</button>
            </form>
        <?php else: ?>
            <p>Match non trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.edit-form {
    max-width: 600px;
    margin: 50px auto;
    padding: 20px;
    background: white;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>

<?php include '../includes/footer.php'; ?> 