<?php
require_once '../config.php';
session_start();

$provider = $_GET['provider'] ?? '';

if (!isset($authProviders[$provider])) {
    handleError('Fournisseur d\'authentification non valide', 400);
}

$providerConfig = $authProviders[$provider];

switch ($provider) {
    case 'microsoft':
        // Génération d'un état pour éviter les attaques CSRF
        $_SESSION['state'] = bin2hex(random_bytes(16));
        
        // Construction de l'URL d'autorisation
        $authUrl = 'https://login.microsoftonline.com/' . $providerConfig['tenant'] . '/oauth2/v2.0/authorize';
        $authUrl .= '?client_id=' . urlencode($providerConfig['client_id']);
        $authUrl .= '&response_type=code';
        $authUrl .= '&redirect_uri=' . urlencode($providerConfig['redirect_uri']);
        $authUrl .= '&scope=' . urlencode($providerConfig['scope']);
        $authUrl .= '&state=' . urlencode($_SESSION['state']);
        
        // Redirection vers la page d'authentification Microsoft
        header('Location: ' . $authUrl);
        exit;
        
    case 'google':
        // Code similaire pour Google OAuth2
        $_SESSION['state'] = bin2hex(random_bytes(16));
        
        $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth';
        $authUrl .= '?client_id=' . urlencode($providerConfig['client_id']);
        $authUrl .= '&response_type=code';
        $authUrl .= '&redirect_uri=' . urlencode($providerConfig['redirect_uri']);
        $authUrl .= '&scope=' . urlencode($providerConfig['scope']);
        $authUrl .= '&state=' . urlencode($_SESSION['state']);
        
        header('Location: ' . $authUrl);
        exit;
        
    case 'custom':
        // Exemple d'authentification personnalisée
        // Implémentation d'un système basé sur un nom d'utilisateur/mot de passe
        include '../views/custom-login.php';
        exit;
}
?>