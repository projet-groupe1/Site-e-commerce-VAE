<?php
session_start();
require 'db.php';

// Configuration
$limit = 12; // Produits par page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
$categorie_id = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    // Récupération des catégories
    $stmt_cat = $pdo->query("SELECT * FROM categories");
    $categories = $stmt_cat->fetchAll();

    // Récupération des produits si catégorie sélectionnée
    $produits = [];
    $total_pages = 0;

    if ($categorie_id) {
        // Requête paginée avec paramètres typés
        $stmt = $pdo->prepare("
            SELECT p.* 
            FROM produits p
            INNER JOIN produit_categorie pc ON p.id = pc.produit_id
            WHERE pc.categorie_id = :categorie_id
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':categorie_id', $categorie_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $produits = $stmt->fetchAll();

        // Calcul du nombre total de pages
        $stmt_total = $pdo->prepare("
            SELECT COUNT(*) 
            FROM produit_categorie 
            WHERE categorie_id = :categorie_id
        ");
        $stmt_total->bindValue(':categorie_id', $categorie_id, PDO::PARAM_INT);
        $stmt_total->execute();
        $total_produits = $stmt_total->fetchColumn();
        $total_pages = ceil($total_produits / $limit);
    }

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Récupération nom catégorie
$current_categorie = '';
if ($categorie_id) {
    $stmt = $pdo->prepare("SELECT nom FROM categories WHERE id = ?");
    $stmt->execute([$categorie_id]);
    $current_categorie = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Electro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-img-top {
            height: 200px;
            object-fit: cover
        }

        .category-link.active {
            font-weight: bold;
            background: #f8f9fa
        }

        .pagination .page-item.active .page-link {
            background: #0d6efd;
            border-color: #0d6efd
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'header.php'; ?>

    <div class="container py-5">
        <div class="row g-4">
            <!-- Sidebar Catégories -->
            <div class="col-lg-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Catégories</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($categories as $cat): ?>
                            <a href="categorie.php?id=<?= htmlspecialchars($cat['id']) ?>"
                                class="list-group-item list-group-item-action <?= $cat['id'] == $categorie_id ? 'active' : '' ?>">
                                <?= htmlspecialchars($cat['nom']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-9">
                <?php if ($categorie_id): ?>
                    <h2 class="mb-4"><?= htmlspecialchars($current_categorie) ?></h2>

                    <?php if (count($produits) > 0): ?>
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <?php foreach ($produits as $produit): ?>
                                <div class="col">
                                    <div class="card h-100 shadow-sm">
                                        <img src="uploads/<?= htmlspecialchars($produit['image']) ?>" class="card-img-top"
                                            alt="<?= htmlspecialchars($produit['nom']) ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($produit['nom']) ?></h5>
                                            <p class="text-success fw-bold"><?= number_format($produit['prix'], 0, ',', ' ') ?> FCFA
                                            </p>
                                            <a href="produit.php?id=<?= htmlspecialchars($produit['id']) ?>"
                                                class="btn btn-outline-primary">
                                                Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-5">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="categorie.php?id=<?= htmlspecialchars($categorie_id) ?>&page=1">Première</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="categorie.php?id=<?= htmlspecialchars($categorie_id) ?>&page=<?= $page - 1 ?>">Précédent</a>
                                        </li>
                                    <?php endif; ?>

                                    <li class="page-item active">
                                        <span class="page-link">Page <?= $page ?></span>
                                    </li>

                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="categorie.php?id=<?= htmlspecialchars($categorie_id) ?>&page=<?= $page + 1 ?>">Suivant</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="categorie.php?id=<?= htmlspecialchars($categorie_id) ?>&page=<?= $total_pages ?>">Dernière</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="alert alert-warning">Aucun produit dans cette catégorie</div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="alert alert-info">
                        Sélectionnez une catégorie pour afficher les produits
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>