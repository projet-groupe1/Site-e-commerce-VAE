<?php
session_start();
$loginMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'db.php';

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'email' => $user['email'],
                'role' => $user['role'],
                'created_at' => $user['created_at']
            ];

            // Si la checkbox est cochée, on garde l'email (PAS le mot de passe en clair !)
            if ($remember) {
                setcookie('email', $email, time() + (86400 * 30), "/"); // 30 jours
            } else {
                setcookie('email', '', time() - 3600, "/"); // Effacer cookie si décoché
            }

            header('Location: checkout.php');
            exit;
        } else {
            $loginMessage = "Email ou mot de passe incorrect.";
        }
    } else {
        $loginMessage = "Tous les champs sont requis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

    <div class="card shadow p-4" style="width: 350px;">
        <h2 class="text-center mb-3">Connexion</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" 
                       value="<?= isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : '' ?>" 
                       placeholder="Entrez votre email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Entrez votre mot de passe" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" 
                       <?= isset($_COOKIE['email']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="text-center mt-3">Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
        <?php if ($loginMessage): ?>
            <p class="text-center text-danger mt-2"><?= htmlspecialchars($loginMessage) ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
