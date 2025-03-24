<?php
session_start();

// Génération du token CSRF si inexistant
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérification du panier
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: panier.php');
    exit();
}

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=electro', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Récupération des produits du panier
$cartItems = [];
$total = 0;

$productIds = array_keys($_SESSION['cart']);

// Sécurité : si le panier est vide
if (empty($productIds)) {
    header('Location: panier.php');
    exit();
}

// Préparation dynamique
$placeholders = rtrim(str_repeat('?,', count($productIds)), ',');
try {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($productIds);

    // Construction des items
    while ($product = $stmt->fetch()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['prix'] * $quantity;
        $total += $subtotal;
        $cartItems[] = $product;
    }
} catch (PDOException $e) {
    die("Erreur lors de la récupération des produits : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Checkout - Electro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4">Finalisation de la commande</h2>

        <form action="checkout_processus.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="row">
                <div class="col-md-7">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="card-title">Informations personnelles</h4>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Nom</label>
                                    <input type="text" name="nom" class="form-control" required
                                        value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Prénom</label>
                                    <input type="text" name="prenom" class="form-control" required
                                        value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" required
                                    value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                            </div>

                            <h4 class="card-title mt-5">Adresse de livraison</h4>

                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" name="adresse" class="form-control" required
                                    value="<?= htmlspecialchars($_POST['adresse'] ?? '') ?>">
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Ville</label>
                                    <input type="text" name="ville" class="form-control" required
                                        value="<?= htmlspecialchars($_POST['ville'] ?? '') ?>">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Pays</label>
                                    <select name="pays" class="form-control" required>
                                        <option value="0" <?= (isset($_POST['pays']) && $_POST['pays'] == "0") ? 'selected' : '' ?>>Sénégal</option>
                                        <option value="1" <?= (isset($_POST['pays']) && $_POST['pays'] == "1") ? 'selected' : '' ?>>France</option>
                                        <option value="2" <?= (isset($_POST['pays']) && $_POST['pays'] == "2") ? 'selected' : '' ?>>Allemagne</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Code postal</label>
                                    <input type="text" name="code_postal" class="form-control"
                                        value="<?= htmlspecialchars($_POST['code_postal'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="create-account">
                                <label class="form-check-label" for="create-account">
                                    Créer un compte
                                </label>
                            </div>

                            <div id="password-section" class="mt-3" style="display:none;">
                                <div class="form-group">
                                    <label>Créer un mot de passe</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Récapitulatif</h4>

                            <ul class="list-group list-group-flush">
                                <?php foreach ($cartItems as $item): ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><?= htmlspecialchars($item['nom']) ?> x<?= $_SESSION['cart'][$item['id']] ?></span>
                                        <span><?= number_format($item['prix'] * $_SESSION['cart'][$item['id']], 2) ?> FCFA</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="mt-4">
                                <div class="d-flex justify-content-between">
                                    <h5>Total:</h5>
                                    <h5><?= number_format($total, 2) ?> FCFA</h5>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h5>Moyen de paiement</h5>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment" id="wave" value="wave" required>
                                    <label class="form-check-label" for="wave">
                                        Wave
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment" id="card" value="card">
                                    <label class="form-check-label" for="card">
                                        Orange Money
                                    </label>
                                </div>
                            </div>

                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    J'accepte les <a href="#">conditions générales</a>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt-4">
                                Confirmer la commande
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#create-account').change(function () {
                if (this.checked) {
                    $('#password-section').show();
                    $('input[name="password"]').prop('required', true);
                } else {
                    $('#password-section').hide();
                    $('input[name="password"]').prop('required', false);
                }
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>