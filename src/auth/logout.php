<?php
require_once '../config.php';
session_start();

// Vérification si l'utilisateur est connecté
if (isset($_SESSION['user'])) {
    $provider = $_SESSION['user']['provider'] ?? '';
    $token = $_SESSION['user']['token'] ?? '';
    
    // Révocation du jeton selon le fournisseur (optionnel)
    switch ($provider) {
        case 'microsoft':
            // La révocation de jeton n'est pas nécessaire pour Microsoft
            // Les jetons expirent naturellement et la session côté serveur est détruite
            break;
            
        case 'google':
            // Révocation du jeton Google (optionnel)
            if ($token) {
                $revokeUrl = 'https://accounts.google.com/o/oauth2/revoke?token=' . urlencode($token);
                
                $ch = curl_init($revokeUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_exec($ch);
                curl_close($ch);
            }
            break;
            
        case 'custom':
            // Pour une authentification personnalisée, vous pourriez implémenter
            // une logique de révocation de jeton ou de déconnexion spécifique
            // Par exemple, ajouter le jeton à une liste de tokens révoqués
            break;
    }
    
    // Nettoyage de la session
    unset($_SESSION['user']);
    
    // Si vous voulez détruire complètement la session
    session_destroy();
    
    // Suppression du cookie de session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}

// Redirection vers la page d'accueil
header('Location: /');
exit;