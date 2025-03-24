<?php
session_start();
$registerMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    $nom = trim($_POST['nom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (!empty($nom) && !empty($email) && !empty($password)) {
        // Check for duplicate email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $registerMessage = "Email déjà utilisé.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nom, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
            if ($stmt->execute([$nom, $email, $hashedPassword])) {
                
                // Gérer le cookie si checkbox cochée
                if ($remember) {
                    setcookie('register_nom', $nom, time() + (86400 * 30), "/"); // 30 jours
                    setcookie('register_email', $email, time() + (86400 * 30), "/");
                } else {
                    setcookie('register_nom', '', time() - 3600, "/");
                    setcookie('register_email', '', time() - 3600, "/");
                }

                header('Location: login.php');
                exit;
            } else {
                $registerMessage = "Erreur lors de l'inscription.";
            }
        }
    } else {
        $registerMessage = "Tous les champs sont requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-4">Créer un compte</h3>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" placeholder="Votre nom" 
                            value="<?= isset($_COOKIE['register_nom']) ? htmlspecialchars($_COOKIE['register_nom']) : '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" placeholder="Votre email"
                            value="<?= isset($_COOKIE['register_email']) ? htmlspecialchars($_COOKIE['register_email']) : '' ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" name="password" placeholder="Votre mot de passe" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" 
                            <?= isset($_COOKIE['register_email']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="remember">Se souvenir de moi</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                    </form>

                    <?php if ($registerMessage): ?>
                        <p class="text-center text-danger mt-3"><?= htmlspecialchars($registerMessage) ?></p>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
