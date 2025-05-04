<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur - Authentification App Service</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Profil Utilisateur</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($userDetails['error'])): ?>
                            <div class="alert alert-warning">
                                <?php echo htmlspecialchars($userDetails['error']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 150px; height: 150px; margin: 0 auto; font-size: 48px;">
                                    <?php echo substr(htmlspecialchars($userDetails['displayName'] ?? $userDetails['name'] ?? 'U'), 0, 1); ?>
                                </div>
                                
                                <h4 class="mt-3"><?php echo htmlspecialchars($userDetails['displayName'] ?? $userDetails['name'] ?? 'Utilisateur'); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($userDetails['email'] ?? ''); ?></p>
                                <span class="badge bg-<?php echo isset($userDetails['provider']) ? ($userDetails['provider'] === 'microsoft' ? 'primary' : ($userDetails['provider'] === 'google' ? 'danger' : 'dark')) : 'secondary'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($userDetails['provider'] ?? 'inconnu')); ?>
                                </span>
                            </div>
                            
                            <div class="col-md-8">
                                <table class="table table-striped">
                                    <tbody>
                                        <?php 
                                        // Affichage des détails utilisateur dans un tableau
                                        foreach ($userDetails as $key => $value): 
                                            // On exclut certaines clés pour éviter les doublons
                                            if (!in_array($key, ['id', 'displayName', 'name', 'email', 'provider', 'error'])): 
                                        ?>
                                            <tr>
                                                <th><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                                <td>
                                                    <?php if (is_array($value)): ?>
                                                        <ul class="mb-0">
                                                            <?php foreach ($value as $subKey => $subValue): ?>
                                                                <li><strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $subKey))); ?>:</strong> <?php echo htmlspecialchars($subValue); ?></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($value); ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary">Retour à l'accueil</a>
                            <a href="/auth/logout.php" class="btn btn-danger">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>