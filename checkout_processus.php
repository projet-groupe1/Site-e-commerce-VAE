<?php
session_start();
require 'db.php';
include "header.php";

// Vérification du panier
if (empty($_SESSION['cart'])) {
    header("Location: panier.php");
    exit;
}

// Configuration PDO avec try/catch
try {
    $pdo = new PDO('mysql:host=localhost;dbname=electro;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des prix depuis la base
$productIds = array_keys($_SESSION['cart']);
if (count($productIds) > 0) {
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    $stmt = $pdo->prepare("SELECT id, nom, prix FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $products[$row['id']] = [
            'nom' => $row['nom'],
            'prix' => $row['prix']
        ];
    }
} else {
    $products = [];
}

// Calcul du total
$total = 0;
foreach ($_SESSION['cart'] as $productId => $quantity) {
    if (isset($products[$productId])) {
        $total += $products[$productId]['prix'] * $quantity;
    }
}

// Traitement de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['valider_commande'])) {
    try {
        $pdo->beginTransaction();

        // Insertion de la commande sans l'user_id
        $stmt = $pdo->prepare("INSERT INTO commandes (total, statut) VALUES (?, 'En préparation')");
        $stmt->execute([$total]);
        $orderId = $pdo->lastInsertId();

        // Insertion des articles dans la commande
        $stmtItems = $pdo->prepare("INSERT INTO details_commande (commande_id, produit_id, quantite, total) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            if (isset($products[$productId])) {
                $totalItem = $products[$productId]['prix'] * $quantity;
                $stmtItems->execute([$orderId, $productId, $quantity, $totalItem]);
            }
        }

        // Commit de la transaction
        $pdo->commit();

        // Sauvegarde de l'ID de la commande dans la session pour confirmation
        $_SESSION['order_id'] = $orderId;

        // Vidage du panier
        unset($_SESSION['cart']);

        // Redirection vers la page de confirmation
        header("Location: confirmation.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erreur lors de la commande : " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation de commande</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Récapitulatif de commande</h2>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $productId => $quantity): ?>
                    <?php if (isset($products[$productId])): ?>
                        <tr>
                            <td><?= htmlspecialchars($products[$productId]['nom']) ?></td>
                            <td><?= (int)$quantity ?></td>
                            <td><?= number_format($products[$productId]['prix'], 0, ',', ' ') ?> FCFA</td>
                            <td><?= number_format($products[$productId]['prix'] * $quantity, 0, ',', ' ') ?> FCFA</td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="alert alert-info text-right h4">
            Total général : <?= number_format($total, 0, ',', ' ') ?> FCFA
        </div>

        <form method="post">
            <div class="text-center">
                <a href="panier.php" class="btn btn-secondary mr-2">Retour au panier</a>
                <button type="submit" name="valider_commande" class="btn btn-success btn-lg">
                    Confirmer la commande
                </button>
            </div>
        </form>
    </div>
</body>
</html>
