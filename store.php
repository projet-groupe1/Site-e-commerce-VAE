<?php
session_start();

// Configuration PDO
$pdo = new PDO('mysql:host=localhost;dbname=electro', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Traitement des filtres
$whereClauses = [];
$params = [];

if (!empty($_GET['search'])) {
    $search = trim($_GET['search']);
    $whereClauses[] = "nom LIKE :search";
    $params[':search'] = "%$search%";
}

if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
    $params[':min_price'] = (float)$_GET['min_price'];
    $whereClauses[] = "prix >= :min_price";
}

if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
    $params[':max_price'] = (float)$_GET['max_price'];
    $whereClauses[] = "prix <= :max_price";
}

// Gestion du tri
$orderBy = "nom";
$orderDirection = "ASC";

if (!empty($_GET['sort'])) {
    if ($_GET['sort'] == "1") {
        $orderBy = "prix";
        $orderDirection = "ASC";
    } elseif ($_GET['sort'] == "2") {
        $orderBy = "prix";
        $orderDirection = "DESC";
    }
}

$sql = "SELECT id, nom, prix, image_path FROM produits" .
    (count($whereClauses) ? " WHERE " . implode(' AND ', $whereClauses) : '') .
    " ORDER BY $orderBy $orderDirection";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Electro - Boutique Électronique</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="css/slick.css" />
    <link type="text/css" rel="stylesheet" href="css/slick-theme.css" />
    <link type="text/css" rel="stylesheet" href="css/nouislider.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="css/style.css" />
    
    <style>
        body {
            padding-top: 12px;
        }
        .cart-icon i {
    font-size: 1.6em; /* Ajuste la taille ici selon tes préférences */
}

.product-img img {
    height: 200px;
    object-fit: contain; /* Ajuste l'image sans la déformer */
    width: 100%; /* L'image prend toute la largeur du conteneur */
}

.product-body h3 {
    white-space: nowrap; /* Empêche la coupure du texte */
    overflow: hidden; /* Cache le texte qui dépasse */
    text-overflow: ellipsis; /* Ajoute des "..." si nécessaire */
    padding: 12px;
}
        
        .store-filter {
            margin-bottom: 20px;
        }
        
        .no-products {
            text-align: center;
            padding: 20px;
            color: #e74c3c;
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header>
        <!-- Top Header -->
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="#"><i class="fa fa-phone"></i> 33-800-22-22</a></li>
                    <li><a href="#"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
                    <li><a href="#"><i class="fa fa-map-marker"></i> Mermoz, Dakar, Senegal</a></li>
                </ul>
                <ul class="header-links pull-right">
                    <li><a href="panier.php" class="cart-icon">
                        <i class="fa fa-shopping-cart"></i>
                        <span class="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span>
                    </a></li>
                </ul>
            </div>
        </div>
        
        <!-- Main Header -->
        <div id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="header-logo">
                            <a href="index.php" class="logo">
                                <img src="./img/logo.png" alt="">
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <form method="GET" class="header-search">
                            <input class="input" name="search" placeholder="Rechercher..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button class="search-btn" type="submit">Rechercher</button>
                        </form>
                    </div>
                    
                    <div class="col-md-3 clearfix">
                        <div class="header-ctn">
                            <!-- Panier -->
                            <div class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>Panier</span>
                                    <div class="qty"><?= count($_SESSION['cart'] ?? []) ?></div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- NAVIGATION -->
    <nav id="navigation">
        <div class="container">
            <div id="responsive-nav">
                <ul class="main-nav nav navbar-nav">
                    <li class="active"><a href="index.php">Accueil</a></li>
                    <li><a href="categorie.php">Catégories</a></li>
                    <li><a href="produit.php">Produits</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENU PRINCIPAL -->
    <div class="section">
        <div class="container">
            <div class="row">
                <!-- FILTRES -->
                <div id="aside" class="col-md-3">
                    <div class="aside">
                        <h3 class="aside-title">Filtres</h3>
                        <form method="GET">
                            <div class="price-filter">
                                <div class="input-number">
                                    <input type="number" name="min_price" placeholder="Prix min" 
                                           value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                                </div>
                                <span>-</span>
                                <div class="input-number">
                                    <input type="number" name="max_price" placeholder="Prix max" 
                                           value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Appliquer</button>
                        </form>
                    </div>
                </div>

                <!-- PRODUITS -->
                <div id="store" class="col-md-9">
                    <div class="store-filter clearfix">
                        <div class="store-sort">
                            <label>Trier par :
                                <select class="input-select">
                                    <option value="0">Pertinence</option>
                                    <option value="1">Prix croissant</option>
                                    <option value="2">Prix décroissant</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <?php if (empty($products)): ?>
                            <div class="no-products">Aucun produit trouvé avec ces critères</div>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <div class="col-md-4 col-xs-6">
                                    <div class="product">
                                        <div class="product-img">
                                            <img src="uploads/<?= htmlspecialchars($product['image_path']) ?>" 
                                                 alt="<?= htmlspecialchars($product['nom']) ?>">
                                        </div>
                                        <div class="product-body">
                                            <h3 class="product-name"><?= htmlspecialchars($product['nom']) ?></h3>
                                            <h4 class="product-price"><?= number_format($product['prix'], 2) ?> FCFA</h4>
                                            <div class="product-btns">
                                                <button class="add-to-cart" data-id="<?= $product['id'] ?>">
                                                    <i class="fa fa-shopping-cart"></i> Ajouter au panier
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
    // Gestion AJAX du panier
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
            const productId = button.dataset.id;
            
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                document.querySelectorAll('.cart-count, .qty').forEach(el => {
                    el.textContent = data.cart_count;
                });
            })
            .catch(error => console.error('Error:', error));
        });
    });
    </script>
</body>
</html>