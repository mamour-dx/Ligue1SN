<?php
// Définir le chemin de base de l'application
define('BASE_PATH', '/ligue1SN/app');  // Ajustez ceci selon votre configuration

// Fonction pour générer les URLs
function url($path) {
    // Construire l'URL complète
    return BASE_PATH . '/' . ltrim($path, '/');
} 