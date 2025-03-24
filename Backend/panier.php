<?php
session_start();

// Gestion des actions du panier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['quantity'] as $productId => $quantity) {
            // Valider la quantité et la mettre à jour dans la session
            if (is_numeric($quantity) && $quantity > 0) {
                $_SESSION['cart'][$productId] = (int)$quantity;
            } else {
                unset($_SESSION['cart'][$productId]); // Supprimer si la quantité est invalide
            }
        }
    } elseif (isset($_POST['remove']) && isset($_POST['product_id'])) {
        // Supprimer un produit du panier
        $productId = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$productId]);
    }
}

// Récupération des produits du panier
$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = rtrim(str_repeat('?,', count($productIds)), ',');

    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($productIds);

    while ($product = $stmt->fetch()) {
        $quantity = $_SESSION['cart'][$product['id']];
        $price = $product['prix'];
        $subtotal = $price * $quantity;

        $cartItems[] = [
            'id' => $product['id'],
            'nom' => $product['nom'],
            'prix' => $price,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'image_path' => $product['image_path']
        ];

        $total += $subtotal;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Electro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <?php if (file_exists('header.php')): ?>
        <?php include 'header.php'; ?>
    <?php else: ?>
        <div class="alert alert-danger">Le fichier header.php est introuvable.</div>
    <?php endif; ?>

    <div class="container mt-5">
        <h2 class="mb-4">Votre Panier</h2>

        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">Votre panier est vide</div>
            <a href="store.php" class="btn btn-primary">Continuer vos achats</a>
        <?php else: ?>
            <form method="post">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="row mb-4" data-product-id="<?= $item['id'] ?>">
                                        <div class="col-md-3">
                                            <img src="uploads/<?= htmlspecialchars($item['image_path']) ?>" 
                                                 alt="<?= htmlspecialchars($item['nom']) ?>" 
                                                 class="img-fluid">
                                        </div>
                                        <div class="col-md-6">
                                            <h5><?= htmlspecialchars($item['nom']) ?></h5>
                                            <p class="text-muted"><?= number_format($item['prix'], 2) ?> FCFA</p>
                                            <button type="button" 
                                                    class="btn btn-danger btn-sm remove-item" 
                                                    data-id="<?= $item['id'] ?>">
                                                Supprimer
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" 
                                                   name="quantity[<?= $item['id'] ?>]" 
                                                   value="<?= $item['quantity'] ?>" 
                                                   min="1" 
                                                   class="form-control">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Récapitulatif</h5>
                                <dl class="row">
                                    <dt class="col-6">Total:</dt>
                                    <dd class="col-6 text-right"><?= number_format($total, 2) ?> FCFA</dd>
                                </dl>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" name="update" class="btn btn-secondary">
                                        Actualiser le panier
                                    </button>
                                    <a href="login.php" class="btn btn-primary">
                                        Commander
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Suppression d'article via AJAX
        $('.remove-item').click(function() {
            const productId = $(this).data('id');
            const itemRow = $(this).closest('.row.mb-4');
            
            $.ajax({
                url: 'panier.php',
                method: 'POST',
                data: {
                    remove: true,
                    product_id: productId
                },
                success: function() {
                    itemRow.remove();
                    location.reload(); // Rafraîchir pour mettre à jour les totaux
                },
                error: function() {
                    alert("Une erreur est survenue lors de la suppression de l'article.");
                }
            });
        });
    });
    </script>
</body>
</html>
