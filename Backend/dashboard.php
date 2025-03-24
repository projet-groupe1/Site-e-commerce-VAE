<?php
session_start();
include 'db.php'; // Ce fichier doit initialiser la connexion PDO dans la variable $pdo

// ----------------- Gestion des Catégories -----------------

// Suppression d'une catégorie (vérifie d'abord qu'aucun produit ne l'utilise)
if (isset($_GET['delete_cat'])) {
    $id = intval($_GET['delete_cat']);
    // Vérifier si la catégorie existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        // Vérifier si des produits utilisent cette catégorie
        $stmtProd = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = ?");
        $stmtProd->execute([$id]);
        if ($stmtProd->fetchColumn() > 0) {
            $_SESSION['message'] = "Impossible de supprimer cette catégorie car elle est utilisée par des produits.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['message'] = "Catégorie supprimée avec succès.";
        }
    } else {
        $_SESSION['message'] = "Catégorie introuvable.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ajout d'une catégorie
if (isset($_POST['ajt_cat'])) {
    $nom = trim($_POST['nom']);
    if ($nom) {
        $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        $stmt->execute([$nom]);
        $_SESSION['message'] = "Catégorie ajoutée avec succès.";
    } else {
        $_SESSION['message'] = "Le nom de la catégorie est obligatoire.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Modification d'une catégorie
if (isset($_POST['edit_cat'])) {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    if ($nom) {
        $stmt = $pdo->prepare("UPDATE categories SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $id]);
        $_SESSION['message'] = "Catégorie modifiée avec succès.";
    } else {
        $_SESSION['message'] = "Le nom de la catégorie est obligatoire.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ----------------- Gestion des Produits -----------------

// Suppression d'un produit
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Vérifier si le produit existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        $stmt = $pdo->prepare("DELETE FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Produit supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Produit introuvable.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Ajout d'un produit
if (isset($_POST['ajt_prod'])) {
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $categorie_id = intval($_POST['categorie_id']);

    // Vérifier que tous les champs sont remplis et qu'un fichier a bien été envoyé
    if ($nom && $description && $prix && $categorie_id && isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, categorie_id, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $description, $prix, $categorie_id, $imageName]);
            $_SESSION['message'] = "Produit ajouté avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors du téléchargement de l'image.";
        }
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Modification d'un produit
if (isset($_POST['edit_prod'])) {
    $id = intval($_POST['id']);
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $prix = floatval($_POST['prix']);
    $categorie_id = intval($_POST['categorie_id']);

    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $imageName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $_SESSION['message'] = "Erreur lors du téléchargement de l'image.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    }

    // Mise à jour du produit dans la base de données
    if ($imageName) {
        $stmt = $pdo->prepare("UPDATE produits SET nom = ?, description = ?, prix = ?, categorie_id = ?, image_path = ? WHERE id = ?");
        $stmt->execute([$nom, $description, $prix, $categorie_id, $imageName, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE produits SET nom = ?, description = ?, prix = ?, categorie_id = ? WHERE id = ?");
        $stmt->execute([$nom, $description, $prix, $categorie_id, $id]);
    }

    $_SESSION['message'] = "Produit modifié avec succès.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Récupération des catégories et des produits
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$produits = $pdo->query("SELECT produits.*, categories.nom AS categorie_nom FROM produits 
                         LEFT JOIN categories ON produits.categorie_id = categories.id")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Gestion des Produits & Catégories</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        /* Barre de navigation */
        .navbar {
            background-color: #343a40;
        }

        .navbar-brand,
        .nav-link {
            color: #fff !important;
        }

        /* Sidebar */
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
        }

        .sidebar a {
            color: #adb5bd;
            padding: 15px;
            display: block;
        }

        .sidebar a:hover {
            color: #fff;
            background-color: #495057;
            text-decoration: none;
        }

        /* Contenu principal */
        .content {
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    
    <!-- Navigation principale -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> Electro Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="store.php">Produits</a></li>
                    <!-- Lien modifié pour accéder à la section catégories -->
                    <li class="nav-item"><a class="nav-link" href="#categories"><i class="fa fa-list"></i>
                            Catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteneur principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <ul class="list-unstyled">
                    <li><a href="pro.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#produits"><i class="fa fa-box"></i> Produits</a></li>
                    <!-- Lien vers la section catégories -->
                    <li><a href="Cat.php"><i class="fa fa-list"></i> Catégories</a></li>
                    <li><a href="users.php"><i class="fa fa-users"></i> Utilisateurs</a></li>
                    <li><a href="#"><i class="fa fa-cogs"></i> Paramètres</a>
<a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=electro">Admin</a>                </li>
                
                </ul>
            </div>

            <!-- Contenu principal -->
            <div class="col-md-10 content">

                <!-- Gestion des Produits -->
                <div class="card">
                    <div class="card-header">
                        <h3>Gestion des Produits</h3>
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-info mt-2"><?= $_SESSION['message'] ?></div>
                            <?php unset($_SESSION['message']); ?>
                        <?php endif; ?>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fa fa-plus"></i> Ajouter Produit
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Prix (€)</th>
                                    <th>Image</th>
                                    <th>Catégorie</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produits as $prod): ?>
                                    <tr>
                                        <td><?= $prod['id'] ?></td>
                                        <td><?= htmlspecialchars($prod['nom']) ?></td>
                                        <td><?= htmlspecialchars($prod['description']) ?></td>
                                        <td><?= number_format($prod['prix'], 2) ?></td>
                                        <td>
                                            <?php if ($prod['image_path']): ?>
                                                <img src="uploads/<?= htmlspecialchars($prod['image_path']) ?>" width="50">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($prod['categorie_nom']) ?></td>
                                        <td>
                                            <a href="?delete=<?= $prod['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce produit ?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal"
                                                data-id="<?= $prod['id'] ?>"
                                                data-nom="<?= htmlspecialchars($prod['nom']) ?>"
                                                data-description="<?= htmlspecialchars($prod['description']) ?>"
                                                data-prix="<?= $prod['prix'] ?>"
                                                data-categorie="<?= $prod['categorie_id'] ?>"
                                                data-image="<?= $prod['image_path'] ?>">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

    <!-- Modal d'ajout de produit -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Ajouter Produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nom" class="form-control mb-2" placeholder="Nom" required>
                    <textarea name="description" class="form-control mb-2" placeholder="Description" required></textarea>
                    <input type="number" step="0.01" name="prix" class="form-control mb-2" placeholder="Prix (€)" required>
                    <select name="categorie_id" class="form-control mb-2" required>
                        <option value="">-- Catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="file" name="image" class="form-control mb-2" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="ajt_prod" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification de produit -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Modifier Produit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <input type="text" name="nom" id="edit_nom" class="form-control mb-2" placeholder="Nom" required>
                    <textarea name="description" id="edit_description" class="form-control mb-2" placeholder="Description" required></textarea>
                    <input type="number" step="0.01" name="prix" id="edit_prix" class="form-control mb-2" placeholder="Prix (€)" required>
                    <select name="categorie_id" id="edit_categorie_id" class="form-control mb-2" required>
                        <option value="">-- Catégorie --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="file" name="image" id="edit_image" class="form-control mb-2">
                    <div id="edit_image_preview" class="mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_prod" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Remplir le modal de modification du produit avec les données sélectionnées
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var nom = button.getAttribute('data-nom');
            var description = button.getAttribute('data-description');
            var prix = button.getAttribute('data-prix');
            var categorie = button.getAttribute('data-categorie');
            var image = button.getAttribute('data-image');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nom').value = nom;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_prix').value = prix;
            document.getElementById('edit_categorie_id').value = categorie;
            if (image) {
                document.getElementById('edit_image_preview').innerHTML = '<img src="uploads/' + image + '" width="100">';
            } else {
                document.getElementById('edit_image_preview').innerHTML = '';
            }
        });

</script>
</body>
</html>