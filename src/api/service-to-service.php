<?php
require_once '../config.php';

// Utilisation de l'identité managée pour l'authentification service-à-service
function getManagedIdentityToken($resource) {
    $url = 'http://169.254.169.254/metadata/identity/oauth2/token';
    $params = [
        'api-version' => '2018-02-01',
        'resource' => $resource
    ];
    
    $ch = curl_init($url . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Metadata: true'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if (!$response) {
        return null;
    }
    
    $tokenData = json_decode($response, true);
    return $tokenData['access_token'] ?? null;
}

// Exemple d'appel à une API protégée en utilisant une identité managée
function callProtectedApi($endpoint) {
    $token = getManagedIdentityToken($apiConfig['scope']);
    
    if (!$token) {
        return ['error' => 'Échec de l\'obtention du jeton d\'identité managée'];
    }
    
    $ch = curl_init($apiConfig['base_url'] . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Exemple d'utilisation
$apiResponse = callProtectedApi('/api/data');
header('Content-Type: application/json');
echo json_encode($apiResponse);