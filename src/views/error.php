<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - Authentification App Service</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h3>Erreur</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <h4>Une erreur est survenue</h4>
                                <p><?php echo htmlspecialchars($_SESSION['error']['message']); ?></p>
                                <p><strong>Code:</strong> <?php echo htmlspecialchars($_SESSION['error']['code']); ?></p>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <p>Une erreur inconnue est survenue.</p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-4">
                            <a href="/" class="btn btn-primary">Retourner à l'accueil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>