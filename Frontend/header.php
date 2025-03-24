<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Electro - Boutique Électronique</title>
    
    <!-- Liens CSS -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="css/slick.css" />
    <link type="text/css" rel="stylesheet" href="css/slick-theme.css" />
    <link type="text/css" rel="stylesheet" href="css/nouislider.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="css/style.css" />
</head>

<body>
    <!-- HEADER -->
    <header>
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-left">
                    <li><a href="#"><i class="fa fa-phone"></i> 33-800-22-22</a></li>
                    <li><a href="#"><i class="fa fa-envelope-o"></i> contact@electro.sn</a></li>
                    <li><a href="#"><i class="fa fa-map-marker"></i> Mermoz, Dakar</a></li>
                </ul>
                <ul class="header-links pull-right">
                    <li><a href="panier.php"><i class="fa fa-shopping-cart"></i> Panier 
                        <span class="cart-count"><?= count($_SESSION['cart'] ?? []) ?></span></a></li>
                </ul>
            </div>
        </div>

        <div id="header">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="header-logo">
                            <a href="index.php" class="logo">
                                <img src="./img/logo.png" alt="Logo Electro">
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="header-search">
                            <form action="store.php" method="GET">
                                <input class="input" name="search" placeholder="Rechercher...">
                                <button class="search-btn" type="submit">Rechercher</button>
                            </form>
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
                    <li><a href="store.php">Catégories</a></li>
                    <li><a href="store.php">Promotions</a></li>
                </ul>
            </div>
        </div>
    </nav>