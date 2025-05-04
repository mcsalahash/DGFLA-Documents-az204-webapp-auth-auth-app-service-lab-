<?php
require_once '../config.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: /auth/login.php?provider=microsoft');
    exit;
}

// Récupération des informations utilisateur
$user = $_SESSION['user'];

// Fonction pour récupérer les informations détaillées de l'utilisateur
// en fonction du fournisseur d'authentification
function getUserDetails($user) {
    $provider = $user['provider'];
    $token = $user['token'];
    
    switch ($provider) {
        case 'microsoft':
            // Appel à Microsoft Graph API pour obtenir plus d'informations
            $graphUrl = 'https://graph.microsoft.com/v1.0/me';
            
            $ch = curl_init($graphUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $userData = json_decode($response, true);
                
                // Enrichissement des données utilisateur avec les informations de Graph API
                return [
                    'id' => $user['id'],
                    'displayName' => $userData['displayName'] ?? $user['name'],
                    'email' => $userData['mail'] ?? $userData['userPrincipalName'] ?? $user['email'],
                    'jobTitle' => $userData['jobTitle'] ?? 'Non spécifié',
                    'department' => $userData['department'] ?? 'Non spécifié',
                    'officeLocation' => $userData['officeLocation'] ?? 'Non spécifié',
                    'provider' => $provider,
                    'token_expiry' => date('Y-m-d H:i:s', $user['expires'])
                ];
            }
            
            // En cas d'erreur, retourner les informations de base
            return [
                'id' => $user['id'],
                'displayName' => $user['name'],
                'email' => $user['email'],
                'provider' => $provider,
                'token_expiry' => date('Y-m-d H:i:s', $user['expires']),
                'error' => 'Impossible de récupérer les informations détaillées (Code: ' . $httpCode . ')'
            ];
            
        case 'google':
            // Appel à Google People API pour obtenir plus d'informations
            $peopleUrl = 'https://people.googleapis.com/v1/people/me?personFields=names,emailAddresses,organizations,locations';
            
            $ch = curl_init($peopleUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                $userData = json_decode($response, true);
                
                // Extraction des données depuis la réponse de l'API Google
                $name = $userData['names'][0]['displayName'] ?? $user['name'];
                $email = $userData['emailAddresses'][0]['value'] ?? $user['email'];
                $organization = $userData['organizations'][0]['name'] ?? 'Non spécifié';
                $title = $userData['organizations'][0]['title'] ?? 'Non spécifié';
                $location = $userData['locations'][0]['value'] ?? 'Non spécifié';
                
                return [
                    'id' => $user['id'],
                    'displayName' => $name,
                    'email' => $email,
                    'jobTitle' => $title,
                    'organization' => $organization,
                    'location' => $location,
                    'provider' => $provider,
                    'token_expiry' => date('Y-m-d H:i:s', $user['expires'])
                ];
            }
            
            // En cas d'erreur, retourner les informations de base
            return [
                'id' => $user['id'],
                'displayName' => $user['name'],
                'email' => $user['email'],
                'provider' => $provider,
                'token_expiry' => date('Y-m-d H:i:s', $user['expires']),
                'error' => 'Impossible de récupérer les informations détaillées (Code: ' . $httpCode . ')'
            ];
            
        case 'custom':
            // Pour l'authentification personnalisée, vous pourriez récupérer
            // les informations utilisateur depuis votre base de données
            
            // Exemple factice
            return [
                'id' => $user['id'],
                'displayName' => $user['name'],
                'email' => $user['email'],
                'jobTitle' => 'Développeur',
                'department' => 'IT',
                'customFields' => [
                    'lastLogin' => date('Y-m-d H:i:s'),
                    'accountType' => 'Standard'
                ],
                'provider' => $provider,
                'token_expiry' => date('Y-m-d H:i:s', $user['expires'])
            ];
            
        default:
            // Fournisseur non pris en charge
            return [
                'id' => $user['id'],
                'displayName' => $user['name'],
                'email' => $user['email'],
                'provider' => $provider,
                'token_expiry' => date('Y-m-d H:i:s', $user['expires']),
                'error' => 'Fournisseur non pris en charge pour les détails utilisateur'
            ];
    }
}

// Action à effectuer (par défaut : afficher les informations)
$action = $_GET['action'] ?? 'view';

switch ($action) {
    case 'view':
        // Récupération des détails utilisateur
        $userDetails = getUserDetails($user);
        
        // Affichage des informations utilisateur
        header('Content-Type: application/json');
        echo json_encode($userDetails, JSON_PRETTY_PRINT);
        break;
        
    case 'profile':
        // Récupération des détails utilisateur
        $userDetails = getUserDetails($user);
        
        // Inclusion de la vue du profil
        include '../views/profile.php';
        break;
        
    case 'check-token':
        // Vérification de la validité du jeton
        $isValid = $user['expires'] > time();
        $remainingTime = $isValid ? $user['expires'] - time() : 0;
        
        header('Content-Type: application/json');
        echo json_encode([
            'valid' => $isValid,
            'remaining_seconds' => $remainingTime,
            'expires' => date('Y-m-d H:i:s', $user['expires'])
        ]);
        break;
        
    case 'refresh-token':
        // Rafraîchissement du jeton si un refresh_token est disponible
        if (!isset($user['refresh_token'])) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Aucun refresh_token disponible']);
            exit;
        }
        
        $provider = $user['provider'];
        $providerConfig = $authProviders[$provider];
        $refreshToken = $user['refresh_token'];
        
        switch ($provider) {
            case 'microsoft':
                $tokenUrl = 'https://login.microsoftonline.com/' . $providerConfig['tenant'] . '/oauth2/v2.0/token';
                $postData = [
                    'client_id' => $providerConfig['client_id'],
                    'client_secret' => getenv('MICROSOFT_CLIENT_SECRET'),
                    'refresh_token' => $refreshToken,
                    'grant_type' => 'refresh_token',
                    'scope' => $providerConfig['scope']
                ];
                
                $ch = curl_init($tokenUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                
                $response = curl_exec($ch);
                curl_close($ch);
                
                if (!$response) {
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode(['error' => 'Échec de la récupération du jeton']);
                    exit;
                }
                
                $tokenData = json_decode($response, true);
                
                if (isset($tokenData['error'])) {
                    header('HTTP/1.1 401 Unauthorized');
                    echo json_encode(['error' => 'Erreur de jeton: ' . $tokenData['error_description']]);
                    exit;
                }
                
                // Mise à jour des informations utilisateur dans la session
                $_SESSION['user']['token'] = $tokenData['access_token'];
                $_SESSION['user']['refresh_token'] = $tokenData['refresh_token'] ?? $refreshToken;
                $_SESSION['user']['expires'] = time() + ($tokenData['expires_in'] ?? 3600);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'expires' => date('Y-m-d H:i:s', $_SESSION['user']['expires'])
                ]);
                break;
                
            // Implémentation pour d'autres fournisseurs...
                
            default:
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['error' => 'Fournisseur non pris en charge pour le rafraîchissement du jeton']);
                exit;
        }
        break;
        
    default:
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Action non valide']);
}