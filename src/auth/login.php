<?php
require_once '../config.php';
session_start();

$provider = $_GET['provider'] ?? '';

if ($provider === 'microsoft') {
    // Génération d'un état pour éviter les attaques CSRF
    $_SESSION['state'] = bin2hex(random_bytes(16));
    
    // Valeurs codées en dur pour le test
    $tenant = '808ef6d5-5728-459b-800c-968535a011a1';
    $clientId = '3cc14f09-1cd6-4717-981f-3482bc2fea35';
    $redirectUri = 'https://authentiks.azurewebsites.net/src/auth/callback.php?provider=microsoft';
    $scope = 'openid profile email';
    
    // Construction de l'URL d'autorisation
    $authUrl = 'https://login.microsoftonline.com/' . $tenant . '/oauth2/v2.0/authorize';
    $authUrl .= '?client_id=' . urlencode($clientId);
    $authUrl .= '&response_type=code';
    $authUrl .= '&redirect_uri=' . urlencode($redirectUri);
    $authUrl .= '&scope=' . urlencode($scope);
    $authUrl .= '&state=' . urlencode($_SESSION['state']);
    
    // Redirection vers la page d'authentification Microsoft
    header('Location: ' . $authUrl);
    exit;
} else {
    echo "Fournisseur d'authentification non valide ou non spécifié";
}
?>