<?php
require_once '../config.php';
session_start();

// Vérification de l'authentification
if (!isset($_SESSION['user'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Non authentifié']);
    exit;
}

// Vérification de l'expiration du jeton
if ($_SESSION['user']['expires'] <= time()) {
    // Implémentation du rafraîchissement du jeton si un refresh_token est disponible
    if (isset($_SESSION['user']['refresh_token'])) {
        // Code pour rafraîchir le jeton
        // ...
    } else {
        // Redirection vers la page de connexion si le jeton est expiré
        header('Location: /auth/login.php?provider=' . $_SESSION['user']['provider']);
        exit;
    }
}

// Récupération de données protégées
$data = [
    'user_id' => $_SESSION['user']['id'],
    'timestamp' => time(),
    'secret_data' => 'Ceci est une ressource protégée accessible uniquement aux utilisateurs authentifiés'
];

header('Content-Type: application/json');
echo json_encode($data);