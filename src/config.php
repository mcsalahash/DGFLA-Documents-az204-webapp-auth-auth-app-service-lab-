<?php
// Connexion à Key Vault pour récupérer les secrets en utilisant l'identité managée
$keyVaultUrl = getenv('KEY_VAULT_URL');
$clientId = null; // Utilisation de l'identité managée

// Configuration des fournisseurs d'authentification
$authProviders = [
    'microsoft' => [
        'client_id' => getenv('MICROSOFT_CLIENT_ID'),
        'tenant' => getenv('MICROSOFT_TENANT_ID'),
        'redirect_uri' => getenv('APP_URL') . '/src/auth/callback.php?provider=microsoft',
        'scope' => 'openid profile email'
    ],
    'google' => [
        'client_id' => getenv('GOOGLE_CLIENT_ID'),
        'redirect_uri' => getenv('APP_URL') . '/src/auth/callback.php?provider=google',
        'scope' => 'openid profile email'
    ],
    'custom' => [
        'token_endpoint' => getenv('CUSTOM_TOKEN_ENDPOINT'),
        'client_id' => getenv('CUSTOM_CLIENT_ID'),
        'scope' => 'api://custom-api/access'
    ]
];

// Configuration de l'API
$apiConfig = [
    'base_url' => getenv('API_BASE_URL'),
    'scope' => 'api://protected-api/access'
];

// Helper pour gérer les erreurs
function handleError($message, $code = 500) {
    $_SESSION['error'] = [
        'message' => $message,
        'code' => $code
    ];
    header('Location: /views/error.php');
    exit;
}