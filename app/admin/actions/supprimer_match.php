<?php
session_start();
require_once '../../config/database.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    try {
        // Supprimer le match
        $stmt = $pdo->prepare("DELETE FROM matchs WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Le match a été supprimé avec succès";
        } else {
            $_SESSION['error'] = "Match non trouvé";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la suppression du match: " . $e->getMessage();
    }
}

header('Location: ../dashboard.php'); 