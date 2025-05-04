<?php
require_once '../config.php';
session_start();

$provider = $_GET['provider'] ?? '';
$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';

// Vérification de l'état pour éviter les attaques CSRF
if (!isset($_SESSION['state']) || $state !== $_SESSION['state']) {
    handleError('État invalide - possible attaque CSRF', 400);
}

if (!isset($authProviders[$provider])) {
    handleError('Fournisseur d\'authentification non valide', 400);
}

$providerConfig = $authProviders[$provider];

// Échange du code d'autorisation contre un jeton d'accès
switch ($provider) {
    case 'microsoft':
        $tokenUrl = 'https://login.microsoftonline.com/' . $providerConfig['tenant'] . '/oauth2/v2.0/token';
        $postData = [
            'client_id' => $providerConfig['client_id'],
            'client_secret' => getenv('MICROSOFT_CLIENT_SECRET'), // Récupéré de Key Vault
            'code' => $code,
            'redirect_uri' => $providerConfig['redirect_uri'],
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if (!$response) {
            handleError('Échec de la récupération du jeton', 500);
        }
        
        $tokenData = json_decode($response, true);
        
        if (isset($tokenData['error'])) {
            handleError('Erreur de jeton: ' . $tokenData['error_description'], 401);
        }
        
        // Décodage du jeton ID pour obtenir les informations utilisateur
        $idToken = $tokenData['id_token'];
        $tokenParts = explode('.', $idToken);
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
        
        // Stockage des informations utilisateur dans la session
        $_SESSION['user'] = [
            'id' => $payload['oid'] ?? $payload['sub'],
            'name' => $payload['name'] ?? '',
            'email' => $payload['email'] ?? $payload['preferred_username'] ?? '',
            'provider' => 'microsoft',
            'token' => $tokenData['access_token'],
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'expires' => time() + ($tokenData['expires_in'] ?? 3600)
        ];
        
        // Redirection vers la page d'accueil
        header('Location: /');
        exit;
        
    case 'google':
        // Code similaire pour Google OAuth2
        // ...
        
    case 'custom':
        // Implémentation d'un système d'authentification personnalisé
        // ...
}