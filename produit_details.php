<?php
session_start();

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=electro', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Vérifier si l'ID du produit est passé dans l'URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Requête pour récupérer les détails du produit
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch();

// Si le produit n'existe pas
if (!$product) {
    echo "Produit non trouvé.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Détails - <?= htmlspecialchars($product['nom']) ?></title>
    <!-- Ajouter des liens vers des styles CSS si nécessaire -->
</head>
<body>
    <h1><?= htmlspecialchars($product['nom']) ?></h1>
    <img src="uploads/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['nom']) ?>" style="max-width: 300px;">
    <p><strong>Prix : </strong><?= number_format($product['prix'], 2) ?> FCFA</p>
    <p><strong>Description : </strong><?= nl2br(htmlspecialchars($product['description'])) ?></p>

    <!-- Ajout au panier -->
    <button class="add-to-cart" data-id="<?= $product['id'] ?>">Ajouter au panier</button>

    <!-- Autres informations si nécessaire -->

    <script>
        // Ajouter au panier via AJAX
        document.querySelector('.add-to-cart').addEventListener('click', function() {
            const productId = this.dataset.id;

            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                // Mise à jour du panier
                alert('Produit ajouté au panier !');
            })
            .catch(error => console.error('Erreur:', error));
        });
    </script>
</body>
</html>
