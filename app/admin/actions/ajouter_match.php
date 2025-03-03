<?php
session_start();
require_once '../../config/database.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipe_domicile = $_POST['equipe_domicile'] ?? '';
    $equipe_exterieur = $_POST['equipe_exterieur'] ?? '';
    $date_heure = $_POST['date_heure'] ?? '';
    $lieu = $_POST['lieu'] ?? '';

    // Validation
    if (empty($equipe_domicile) || empty($equipe_exterieur) || empty($date_heure) || empty($lieu)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires";
        header('Location: ../dashboard.php');
        exit;
    }

    if ($equipe_domicile === $equipe_exterieur) {
        $_SESSION['error'] = "Les équipes doivent être différentes";
        header('Location: ../dashboard.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO matchs (equipe_domicile, equipe_exterieur, date_heure, lieu)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $equipe_domicile,
            $equipe_exterieur,
            $date_heure,
            $lieu
        ]);

        $_SESSION['success'] = "Le match a été ajouté avec succès";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur lors de l'ajout du match: " . $e->getMessage();
    }
}

header('Location: ../dashboard.php'); 