<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification App Service - Demo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Démonstration d'authentification Azure App Service</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($isLoggedIn): ?>
                            <div class="alert alert-success">
                                <p>Bonjour, <?php echo htmlspecialchars($_SESSION['user']['name']); ?> (<?php echo htmlspecialchars($_SESSION['user']['email']); ?>)</p>
                                <p>Vous êtes connecté via: <?php echo htmlspecialchars(ucfirst($_SESSION['user']['provider'])); ?></p>
                            </div>
                            
                            <div class="mb-4">
                                <a href="/auth/user.php?action=profile" class="btn btn-primary me-2">Voir mon profil</a>
                                <a href="/api/protected-resource.php" class="btn btn-info me-2">Accéder à la ressource protégée</a>
                                <a href="/auth/logout.php" class="btn btn-danger">Déconnexion</a>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <p>Vous n'êtes pas connecté. Choisissez une méthode d'authentification:</p>
                            </div>
                            
                            <div class="row justify-content-center mb-4">
                                <div class="col-md-6">
                                    <div class="d-grid gap-2">
                                        <a href="/auth/login.php?provider=microsoft" class="btn btn-outline-primary mb-2">
                                            Se connecter avec Microsoft
                                        </a>
                                        <a href="/auth/login.php?provider=google" class="btn btn-outline-danger mb-2">
                                            Se connecter avec Google
                                        </a>
                                        <a href="/auth/login.php?provider=custom" class="btn btn-outline-dark">
                                            Authentification personnalisée
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="mt-4">
                            <h4>Points clés de cette démonstration</h4>
                            <ul>
                                <li>Authentification avec plusieurs fournisseurs d'identité</li>
                                <li>Gestion sécurisée des sessions et des jetons</li>
                                <li>Protection CSRF avec des jetons d'état</li>
                                <li>Accès à des API protégées avec authentification</li>
                                <li>Utilisation d'identités managées pour l'authentification service-à-service</li>
                                <li>Stockage sécurisé des secrets avec Azure Key Vault</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>