<?php
require 'db.php';

// Configuration de la pagination
$perPage = 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$page = max(1, $page); // Si le numéro de page est inférieur à 1, on le définit sur 1.
$offset = ($page - 1) * $perPage;

// Récupération des produits
$stmt = $pdo->prepare("SELECT produits.*, categories.nom AS category_nom FROM produits JOIN categories ON produits.categorie_id = categories.id ORDER BY created_at DESC LIMIT :offset, :perPage");
$stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
$stmt->bindValue(':perPage', (int) $perPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Calcul du nombre total de produits
$stmtCount = $pdo->query("SELECT COUNT(*) FROM produits");
$totalProducts = $stmtCount->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Récupération des catégories pour le formulaire
$categoriesStmt = $pdo->query("SELECT * FROM categories");
$categories = $categoriesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos produits</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .product-card {
            transition: transform 0.3s;
            margin-bottom: 30px;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-img {
            height: 200px;
            object-fit: contain;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Section: Affichage des produits -->
        <h1 class="mb-4">Nos produits</h1>
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Aucun produit disponible pour le moment</div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card product-card">
                            <img src="uploads/<?= htmlspecialchars($product['image_path']) ?>" class="card-img-top product-img" alt="<?= htmlspecialchars($product['nom']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['nom']) ?></h5>
                                <p class="text-muted"><?= htmlspecialchars($product['category_nom']) ?></p>
                                <p class="card-text"><?= nl2br(htmlspecialchars(mb_strimwidth($product['description'], 0, 60, '...'))) ?></p>
                                <h4 class="text-primary"><?= number_format($product['prix'], 0, ',', ' ') ?> FCFA</h4>
                                <a href="produit_details.php?id=<?= $product['id'] ?>" class="btn btn-primary w-100">Voir le produit</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>

        <hr>

    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>vente d'appareil electronique</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />

	<!-- Slick -->
	<link type="text/css" rel="stylesheet" href="css/slick.css" />
	<link type="text/css" rel="stylesheet" href="css/slick-theme.css" />

	<!-- nouislider -->
	<link type="text/css" rel="stylesheet" href="css/nouislider.min.css" />

	<!-- Font Awesome Icon -->
	<link rel="stylesheet" href="css/font-awesome.min.css">

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="css/style.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

	<style>
		/* FIXER LES ÉLÉMENTS EN HAUT */
		#top-header {
			position: fixed;
			top: 0;
			width: 100%;
			height: 40px;
			/* À mesurer précisément */
			z-index: 1000;
			background: #333;
			color: white;
		}

		#header {
			position: fixed;
			top: 40px;
			/* Hauteur de #top-header */
			width: 100%;
			z-index: 999;
		}

		#navigation {
			position: fixed;
			top: 150px;
			/* 40px (#top-header) + 90px (#header) */
			width: 100%;
			z-index: 998;
		}

		/* CORRIGER LE DÉBORDEMENT */
		body {
			padding-top: 220px;
			/* 40px + 90px + 50px + 40px (marge) */
		}

		.section {
			margin-top: 0;
			/* Réinitialiser si nécessaire */
		}
	</style>



</head>

<body>
	<!-- HEADER -->
	<header>
		<!-- TOP HEADER -->
		<div id="top-header">
			<div class="container">
				<ul class="header-links pull-left">
					<li><a href="#"><i class="fa fa-phone"></i> 33-800-22-22</a></li>
					<li><a href="#"><i class="fa fa-envelope-o"></i> email@email.com</a></li>
					<li><a href="#"><i class="fa fa-map-marker"></i> Mermoz, Dakar, Senegal</a></li>
				</ul>
				<ul class="header-links pull-right">
					<li><a href="#"><i class="fa fa-dollar"></i> FCFA</a></li>
					<li><a href="checkout.php"><i class="fa fa-user-o"></i> Mon Compte</a></li>
				</ul>
			</div>
		</div>
		<!-- /TOP HEADER -->

		<!-- MAIN HEADER -->
		<div id="header">
			<!-- container -->
			<div class="container">
				<!-- row -->
				<div class="row">
					<!-- LOGO -->
					<div class="col-md-3">
						<div class="header-logo">
							<a href="index.php" class="logo">
								<img src="./img/logo.png" alt="">
							</a>
						</div>
					</div>
					<!-- /LOGO -->

					<!-- SEARCH BAR -->
					<div class="col-md-6">
						<div class="header-search">
							<form>
								<select class="input-select">
									<option value="0">les Categories</option>
									<option value="1">Categorie 1</option>
									<option value="1">Categorie 2</option>
								</select>
								<input class="input" placeholder="Rechercher Ici">
								<button class="search-btn">Rechercher</button>
							</form>
						</div>
					</div>
					<!-- /SEARCH BAR -->

					<!-- ACCOUNT -->
					<div class="col-md-3 clearfix">
						<div class="header-ctn">
							<!-- Wishlist -->
							<div>
								<a href="#">
									<i class="fa fa-heart-o"></i>
									<span>Votre liste de souhait</span>
									<div class="qty">2</div>
								</a>
							</div>
							<!-- /Wishlist -->

							<!-- Cart -->
							<div class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<i class="fa fa-shopping-cart"></i>
									<span>Panier</span>
									<div class="qty">3</div>
								</a>
								<div class="cart-dropdown">
									<div class="cart-list">
										<div class="product-widget">
											<div class="product-img">
												<img src="./img/product01.png" alt="">
											</div>
											<div class="product-body">
												<h3 class="product-name"><a href="#">Ordinateur</a></h3>
												<h4 class="product-price"><span class="qty">1x</span>280.000 FCFA</h4>
											</div>
											<button class="delete"><i class="fa fa-close"></i></button>
										</div>

										<div class="product-widget">
											<div class="product-img">
												<img src="./img/product02.png" alt="">
											</div>
											<div class="product-body">
												<h3 class="product-name"><a href="#">Casque</a></h3>
												<h4 class="product-price"><span class="qty">1x</span>80.000 FCFA</h4>
											</div>
											<button class="delete"><i class="fa fa-close"></i></button>
										</div>
									</div>
									<div class="cart-summary">
										<small>3 articles selectionnées</small>
										<h5>SOUS-TOTAL: 360.000 FCFA</h5>
									</div>
									<div class="cart-btns">
										<a href="panier.php">Voir le panier</a>
										<a href="panier.php">Verifier <i class="fa fa-arrow-circle-right"></i></a>
									</div>
								</div>
							</div>
							<!-- /Cart -->
							<div class="cart-dropdown">
								<div class="cart-list">
									<div class="product-widget">
										<div class="product-img">
											<img src="./img/product01.png" alt="">
										</div>
										<div class="product-body">
											<h3 class="product-name"><a href="#">Ordinateur</a></h3>
											<h4 class="product-price"><span class="qty">1x</span>280.000 FCFA</h4>
										</div>
										<button class="delete"><i class="fa fa-close"></i></button>
									</div>

									<div class="product-widget">
										<div class="product-img">
											<img src="./img/product02.png" alt="">
										</div>
										<div class="product-body">
											<h3 class="product-name"><a href="#">Casque</a></h3>
											<h4 class="product-price"><span class="qty">1x</span>80.000 FCFA</h4>
										</div>
										<button class="delete"><i class="fa fa-close"></i></button>
									</div>
								</div>
								<div class="cart-summary">
									<small>3 articles selectionnées</small>
									<h5>SOUS-TOTAL: 360.000 FCFA</h5>
								</div>
								<div class="cart-btns">
									<a href="panier.php">Voir le panier</a>
									<a href="panier.php">Verifier <i class="fa fa-arrow-circle-right"></i></a>
								</div>
							</div>
						</div>
						<!-- /Cart -->


						<!-- Menu Toogle -->
						<div class="menu-toggle">
							<a href="#">
								<i class="fa fa-bars"></i>
								<span>Menu</span>
							</a>
						</div>
						<!-- /Menu Toogle -->
					</div>
				</div>
				<!-- /ACCOUNT -->
			</div>
			<!-- row -->
		</div>
		<!-- container -->
		</div>
		<!-- /MAIN HEADER -->
	</header>
	<!-- /HEADER -->

	<!-- NAVIGATION -->
	<nav id="navigation">
		<!-- container -->
		<div class="container">
			<!-- responsive-nav -->
			<div id="responsive-nav">
				<!-- NAV -->
				<ul class="main-nav nav navbar-nav">
					<li class="active"><a href="index.php">Acceuil</a></li>
					<li><a href="store.php">Offres exceptionnelles</a></li>
					<li><a href="store.php">Categories</a></li>
					<li><a href="produit.php">ordinateurs portables</a></li>
					<li><a href="store.php">Smartphones</a></li>
					<li><a href="store.php">Cameras</a></li>
					<li><a href="store.php">Accessoires</a></li>
				</ul>
				<!-- /NAV -->
			</div>
			<!-- /responsive-nav -->
		</div>
		<!-- /container -->
	</nav>
	<!-- /NAVIGATION -->

	<!-- BREADCRUMB -->
	<div id="breadcrumb" class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<div class="col-md-12">
					<ul class="breadcrumb-tree">
						<li><a href="produit.php">ACCEUIL</a></li>
						<li><a href="store.php">TOUTES LES CATEGORIES</a></li>
						<li><a href="store.php">ACCESSOIRES</a></li>
						<li><a href="store.php">CASQUE AUDIO</a></li>
						<li class="active">TELEPHONE</li>
					</ul>
				</div>
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</div>
	<!-- /BREADCRUMB -->

	<!-- SECTION -->
	<div class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<!-- Product main img -->
				<div class="col-md-5 col-md-push-2">
					<div id="product-main-img">
						<div class="product-preview">
							<img src="./img/product01.png" alt="">
						</div>

						<div class="product-preview">
							<img src="./img/product03.png" alt="">
						</div>

						<div class="product-preview">
							<img src="./img/product06.png" alt="">
						</div>

						<div class="product-preview">
							<img src="./img/product08.png" alt="">
						</div>
					</div>
				</div>
				<!-- /Product main img -->

				<!-- Product thumb imgs -->
				<div class="col-md-2  col-md-pull-5">
					<div id="product-imgs">
						<div class="product-preview">
							<img src="./img/product01.png" alt="">
						</div>

						<div class="product-preview">
							<img src="./img/product03.png" alt="">
						</div>

						<div class="product-preview">
							<img src="./img/product06.png" alt="">
						</div>

						<div class="product-preview">
							<img src="./img/product08.png" alt="">
						</div>
					</div>
				</div>
				<!-- /Product thumb imgs -->

				<!-- Product details -->
				<div class="col-md-5">
					<div class="product-details">
						<h2 class="product-name">Ordinateur</h2>
						<div>
							<div class="product-rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star-o"></i>
							</div>
							<a class="review-link" href="#">Votre Avis</a>
						</div>
						<div>
							<h3 class="product-price">380.000 FCFA <del class="product-old-price">290.000 FCFA</del>
							</h3>
							<span class="product-available">In Stock</span>
						</div>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
							labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco
							laboris nisi ut aliquip ex ea commodo consequat.</p>

						<div class="product-options">
							<label>
								Taille
								<select class="input-select">
									<option value="0">X</option>
								</select>
							</label>
							<label>
								Couleur
								<select class="input-select">
									<option value="0">Red</option>
								</select>
							</label>
						</div>

						<div class="add-to-cart">
							<div class="qty-label">
								Quantité
								<div class="input-number">
									<input type="number">
									<span class="qty-up">+</span>
									<span class="qty-down">-</span>
								</div>
							</div>
							<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> AJOUTER AU
								PANIER</button>
						</div>

						<ul class="product-btns">
							<li><a href="#"><i class="fa fa-heart-o"></i> Liste de souhait</a></li>
							<li><a href="#"><i class="fa fa-exchange"></i> Ajouter pour commander</a></li>
						</ul>

						<ul class="product-links">
							<li>CATEGORIES:</li>
							<li><a href="#">Casque Audio</a></li>
							<li><a href="#">Accessoires</a></li>
						</ul>

						<ul class="product-links">
							<li>Partager:</li>
							<li><a href="#"><i class="fa fa-facebook"></i></a></li>
							<li><a href="#"><i class="fa fa-twitter"></i></a></li>
							<li><a href="#"><i class="fa fa-google-plus"></i></a></li>
							<li><a href="#"><i class="fa fa-envelope"></i></a></li>
						</ul>

					</div>
				</div>
				<!-- /Product details -->

				<!-- Product tab -->
				<div class="col-md-12">
					<div id="product-tab">
						<!-- product tab nav -->
						<ul class="tab-nav">
							<li class="active"><a data-toggle="tab" href="#tab1">Description</a></li>
							<li><a data-toggle="tab" href="#tab2">Details</a></li>
							<li><a data-toggle="tab" href="#tab3">Avis (3)</a></li>
						</ul>
						<!-- /product tab nav -->

						<!-- product tab content -->
						<div class="tab-content">
							<!-- tab1  -->
							<div id="tab1" class="tab-pane fade in active">
								<div class="row">
									<div class="col-md-12">
										<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
											tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
											quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
											consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
											cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
											non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
										</p>
									</div>
								</div>
							</div>
							<!-- /tab1  -->

							<!-- tab2  -->
							<div id="tab2" class="tab-pane fade in">
								<div class="row">
									<div class="col-md-12">
										<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
											tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
											quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
											consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
											cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
											non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
										</p>
									</div>
								</div>
							</div>
							<!-- /tab2  -->

							<!-- tab3  -->
							<div id="tab3" class="tab-pane fade in">
								<div class="row">
									<!-- Rating -->
									<div class="col-md-3">
										<div id="rating">
											<div class="rating-avg">
												<span>4.5</span>
												<div class="rating-stars">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star-o"></i>
												</div>
											</div>
											<ul class="rating">
												<li>
													<div class="rating-stars">
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
													</div>
													<div class="rating-progress">
														<div style="width: 80%;"></div>
													</div>
													<span class="sum">3</span>
												</li>
												<li>
													<div class="rating-stars">
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star-o"></i>
													</div>
													<div class="rating-progress">
														<div style="width: 60%;"></div>
													</div>
													<span class="sum">2</span>
												</li>
												<li>
													<div class="rating-stars">
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star-o"></i>
														<i class="fa fa-star-o"></i>
													</div>
													<div class="rating-progress">
														<div></div>
													</div>
													<span class="sum">0</span>
												</li>
												<li>
													<div class="rating-stars">
														<i class="fa fa-star"></i>
														<i class="fa fa-star"></i>
														<i class="fa fa-star-o"></i>
														<i class="fa fa-star-o"></i>
														<i class="fa fa-star-o"></i>
													</div>
													<div class="rating-progress">
														<div></div>
													</div>
													<span class="sum">0</span>
												</li>
												<li>
													<div class="rating-stars">
														<i class="fa fa-star"></i>
														<i class="fa fa-star-o"></i>
														<i class="fa fa-star-o"></i>
														<i class="fa fa-star-o"></i>
														<i class="fa fa-star-o"></i>
													</div>
													<div class="rating-progress">
														<div></div>
													</div>
													<span class="sum">0</span>
												</li>
											</ul>
										</div>
									</div>
									<!-- /Rating -->

									<!-- Reviews -->
									<div class="col-md-6">
										<div id="reviews">
											<ul class="reviews">
												<li>
													<div class="review-heading">
														<h5 class="name">Marie</h5>
														<p class="date">27 DEC 2018, 8:0 PM</p>
														<div class="review-rating">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o empty"></i>
														</div>
													</div>
													<div class="review-body">
														<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
															do eiusmod tempor incididunt ut labore et dolore magna
															aliqua</p>
													</div>
												</li>
												<li>
													<div class="review-heading">
														<h5 class="name">Moussa</h5>
														<p class="date">27 DEC 2018, 8:0 PM</p>
														<div class="review-rating">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o empty"></i>
														</div>
													</div>
													<div class="review-body">
														<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
															do eiusmod tempor incididunt ut labore et dolore magna
															aliqua</p>
													</div>
												</li>
												<li>
													<div class="review-heading">
														<h5 class="name">Ibrahima</h5>
														<p class="date">27 DEC 2018, 8:0 PM</p>
														<div class="review-rating">
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star"></i>
															<i class="fa fa-star-o empty"></i>
														</div>
													</div>
													<div class="review-body">
														<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
															do eiusmod tempor incididunt ut labore et dolore magna
															aliqua</p>
													</div>
												</li>
											</ul>
											<ul class="reviews-pagination">
												<li class="active">1</li>
												<li><a href="#">2</a></li>
												<li><a href="#">3</a></li>
												<li><a href="#">4</a></li>
												<li><a href="#"><i class="fa fa-angle-right"></i></a></li>
											</ul>
										</div>
									</div>
									<!-- /Reviews -->

									<!-- Review Form -->
									<div class="col-md-3">
										<div id="review-form">
											<form class="review-form">
												<input class="input" type="text" placeholder="Votre Nom">
												<input class="input" type="email" placeholder="Votre Email">
												<textarea class="input" placeholder="votre avis"></textarea>
												<div class="input-rating">
													<span>Votre note: </span>
													<div class="stars">
														<input id="star5" name="rating" value="5" type="radio"><label
															for="star5"></label>
														<input id="star4" name="rating" value="4" type="radio"><label
															for="star4"></label>
														<input id="star3" name="rating" value="3" type="radio"><label
															for="star3"></label>
														<input id="star2" name="rating" value="2" type="radio"><label
															for="star2"></label>
														<input id="star1" name="rating" value="1" type="radio"><label
															for="star1"></label>
													</div>
												</div>
												<button class="primary-btn">Valider</button>
											</form>
										</div>
									</div>
									<!-- /Review Form -->
								</div>
							</div>
							<!-- /tab3  -->
						</div>
						<!-- /product tab content  -->
					</div>
				</div>
				<!-- /product tab -->
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</div>
	<!-- /SECTION -->

	<!-- Section -->
	<div class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">

				<div class="col-md-12">
					<div class="section-title text-center">
						<h3 class="title">Produits connexes</h3>
					</div>
				</div>

				<!-- product -->
				<div class="col-md-3 col-xs-6">
					<div class="product">
						<div class="product-img">
							<img src="./img/product01.png" alt="">
							<div class="product-label">
								<span class="sale">-30%</span>
							</div>
						</div>
						<div class="product-body">
							<p class="product-category">Categorie</p>
							<h3 class="product-name"><a href="store.php">Ordinateur</a></h3>
							<h4 class="product-price">280.000 FCFA <del class="product-old-price">160.000
									FCFA</del></h4>
							<div class="product-rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star-o"></i>
							</div>
							<div class="product-btns">
								<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span
										class="tooltipp">Ajouter a la liste de souhait</span></button>
								<button class="add-to-compare"><i class="fa fa-exchange"></i><span
										class="tooltipp">Akouter pour comparer</span></button>
								<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">aperçu
										rapide</span></button>
							</div>
						</div>
						<div class="add-to-cart">
							<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> AJOUTER
								AU PANIER</button>
						</div>
					</div>
				</div>
				<!-- /product -->

				<!-- product -->
				<div class="col-md-3 col-xs-6">
					<div class="product">
						<div class="product-img">
							<img src="./img/product02.png" alt="">
							<div class="product-label">
								<span class="new">NOUVEAU</span>
							</div>
						</div>
						<div class="product-body">
							<p class="product-category">Categorie</p>
							<h3 class="product-name"><a href="store.php">Casque</a></h3>
							<h4 class="product-price">80.000 FCFA <del class="product-old-price">60.000
									FCFA</del></h4>
							<div class="product-rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star-o"></i>
							</div>
							<div class="product-btns">
								<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span
										class="tooltipp">Ajouter a la liste de souhait</span></button>
								<button class="add-to-compare"><i class="fa fa-exchange"></i><span
										class="tooltipp">Akouter pour comparer</span></button>
								<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">aperçu
										rapide</span></button>
							</div>
						</div>
						<div class="add-to-cart">
							<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> AJOUTER
								AU PANIER</button>
						</div>
					</div>
				</div>
				<!-- /product -->

				<div class="clearfix visible-sm visible-xs"></div>

				<!-- product -->
				<div class="col-md-3 col-xs-6">
					<div class="product">
						<div class="product-img">
							<img src="./img/product03.png" alt="">
						</div>
						<div class="product-body">
							<p class="product-category">Categorie</p>
							<h3 class="product-name"><a href="store.php">Ordinateur</a></h3>
							<h4 class="product-price">280.000 FCFA <del class="product-old-price">160.000
									FCFA</del></h4>
							<div class="product-rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star-o"></i>
							</div>
							<div class="product-btns">
								<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span
										class="tooltipp">Ajouter a la liste de souhait</span></button>
								<button class="add-to-compare"><i class="fa fa-exchange"></i><span
										class="tooltipp">Akouter pour comparer</span></button>
								<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">aperçu
										rapide</span></button>
							</div>
						</div>
						<div class="add-to-cart">
							<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> AJOUTER
								AU PANIER</button>
						</div>
					</div>
				</div>
				<!-- /product -->

				<!-- product -->
				<div class="col-md-3 col-xs-6">
					<div class="product">
						<div class="product-img">
							<img src="./img/product04.png" alt="">
						</div>
						<div class="product-body">
							<p class="product-category">Categorie</p>
							<h3 class="product-name"><a href="store.php">Tablette</a></h3>
							<h4 class="product-price">180.000 FCFA <del class="product-old-price">160.000
									FCFA</del></h4>
							<div class="product-rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star-o"></i>
							</div>
							<div class="product-btns">
								<button class="add-to-wishlist"><i class="fa fa-heart-o"></i><span
										class="tooltipp">Ajouter a la liste de souhait</span></button>
								<button class="add-to-compare"><i class="fa fa-exchange"></i><span
										class="tooltipp">Akouter pour comparer</span></button>
								<button class="quick-view"><i class="fa fa-eye"></i><span class="tooltipp">aperçu
										rapide</span></button>
							</div>
						</div>
						<div class="add-to-cart">
							<button class="add-to-cart-btn"><i class="fa fa-shopping-cart"></i> AJOUTER
								AU PANIER</button>
						</div>
					</div>
				</div>
				<!-- /product -->

			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</div>
	<!-- /Section -->

	<!-- NEWSLETTER -->
	<div id="newsletter" class="section">
		<!-- container -->
		<div class="container">
			<!-- row -->
			<div class="row">
				<div class="col-md-12">
					<div class="newsletter">
						<p>abonnez-vous à notre <strong>NEWSLETTER</strong></p>
						<form>
							<input class="input" type="email" placeholder="Entrer votre Email">
							<button class="newsletter-btn"><i class="fa fa-envelope"></i> s'abonner
							</button>
						</form>
						<ul class="newsletter-follow">
							<li>
								<a href="#"><i class="fa fa-facebook"></i></a>
							</li>
							<li>
								<a href="#"><i class="fa fa-twitter"></i></a>
							</li>
							<li>
								<a href="#"><i class="fa fa-instagram"></i></a>
							</li>
							<li>
								<a href="#"><i class="fa fa-pinterest"></i></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</div>
	<!-- /NEWSLETTER -->

	<!-- FOOTER -->
	<?php
	include "footer.php";
	?>
	<!-- /FOOTER -->

	<!-- jQuery Plugins -->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/slick.min.js"></script>
	<script src="js/nouislider.min.js"></script>
	<script src="js/jquery.zoom.min.js"></script>
	<script src="js/main.js"></script>



</body>

</html>