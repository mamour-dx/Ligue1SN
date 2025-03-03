<?php
session_start();
require_once '../config/database.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
    } else {
        try {
            // Vérifier si le nom d'utilisateur existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM administrateurs WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Ce nom d'utilisateur existe déjà";
            } else {
                // Créer le nouvel administrateur avec mot de passe en clair
                $stmt = $pdo->prepare("INSERT INTO administrateurs (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $password]);
                $success = "L'administrateur a été créé avec succès";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la création de l'administrateur: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un administrateur - Ligue 1 Sénégalaise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<div class="admin-container">
    <div class="create-admin-form">
        <div class="form-header">
            <h2>Créer un nouvel administrateur</h2>
            <a href="dashboard.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-create">
                <i class="fas fa-user-plus"></i>
                Créer l'administrateur
            </button>
        </form>
    </div>
</div>

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    min-height: 100vh;
}

.admin-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.create-admin-form {
    width: 100%;
    max-width: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    padding: 2rem;
}

.form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.form-header h2 {
    color: #2c3e50;
    margin: 0;
    font-size: 1.8rem;
}

.back-link {
    color: #3498db;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.back-link:hover {
    color: #2980b9;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 500;
}

.input-group {
    position: relative;
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.input-group:focus-within {
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.input-group i {
    padding: 0 1rem;
    color: #7f8c8d;
}

.input-group input {
    flex: 1;
    padding: 0.8rem;
    border: none;
    background: none;
    outline: none;
    font-size: 1rem;
}

.btn-create {
    width: 100%;
    padding: 0.8rem;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-create:hover {
    background: #2980b9;
}

.alert {
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background-color: #dcfce7;
    border: 1px solid #bbf7d0;
    color: #16a34a;
}

.alert-danger {
    background-color: #fee2e2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

.alert i {
    font-size: 1.2rem;
}

@media (max-width: 480px) {
    .create-admin-form {
        padding: 1.5rem;
    }

    .form-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .form-header h2 {
        font-size: 1.5rem;
    }
}
</style>

</body>
</html> 