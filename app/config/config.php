<?php
// Définir le chemin de base de l'application
define('BASE_PATH', '');  // On enlève /app car il fait partie du chemin réel

// Fonction pour générer les URLs
function url($path) {
    // Récupérer le dossier de base de l'application
    $basedir = dirname($_SERVER['SCRIPT_NAME']);
    $basedir = rtrim($basedir, '/');
    
    // Construire l'URL complète
    return $basedir . '/' . ltrim($path, '/');
} 