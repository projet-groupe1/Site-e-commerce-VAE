<?php 
session_start(); 
require 'db.php'; // Ce fichier doit initialiser la connexion PDO dans la variable $pdo

// ----------------- Gestion des Catégories ----------------- 

// Suppression d'une catégorie
if (isset($_GET['delete_cat'])) { 
    try { 
        $id = intval($_GET['delete_cat']); 
        // Vérifier si la catégorie existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) { 
            // Vérifier si les produits utilisent cette catégorie
            $stmtProd = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = ?");
            $stmtProd->execute([$id]);
            if ($stmtProd->fetchColumn() > 0) { 
                $_SESSION['message'] = "Impossible de supprimer cette catégorie car elle est utilisée par des produits."; 
            } else { 
                $stmtDelete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                $stmtDelete->execute([$id]);
                $_SESSION['message'] = "Catégorie supprimée avec succès."; 
            } 
        } else { 
            $_SESSION['message'] = "Catégorie introuvable."; 
        } 
    } catch (PDOException $e) { 
        $_SESSION['message'] = "Erreur lors de la suppression : " . $e->getMessage(); 
    } 
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit; 
}

// Ajout d'une catégorie
if (isset($_POST['ajt_cat'])) { 
    try { 
        $nom = trim($_POST['nom']); 
        $image = null; 
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { 
            $imageName = basename($_FILES['image']['name']); 
            $imagePath = 'uploads/' . $imageName; 
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) { 
                $image = $imagePath; 
            } else { 
                $_SESSION['message'] = "Erreur lors de l'upload de l'image."; 
            } 
        } 
        
        if ($nom) { 
            $stmt = $pdo->prepare("INSERT INTO categories (nom, image) VALUES (?, ?)");
            $stmt->execute([htmlspecialchars($nom), $image]); 
            $_SESSION['message'] = "Catégorie ajoutée avec succès."; 
        } else { 
            $_SESSION['message'] = "Le nom de la catégorie est obligatoire."; 
        } 
    } catch (PDOException $e) { 
        $_SESSION['message'] = "Erreur lors de l'ajout : " . $e->getMessage(); 
    } 
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit; 
}

// Modification d'une catégorie
if (isset($_POST['edit_cat'])) { 
    try { 
        $id = intval($_POST['id']); 
        $nom = trim($_POST['nom']); 
        $image = $_POST['image_actuelle']; 
        
        // Utiliser l'image actuelle si aucune nouvelle n'est uploadée
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) { 
            $imageName = basename($_FILES['image']['name']); 
            $imagePath = 'uploads/' . $imageName; 
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) { 
                $image = $imagePath; 
            } else { 
                $_SESSION['message'] = "Erreur lors de l'upload de l'image."; 
            } 
        } 
        
        if ($nom) { 
            $stmt = $pdo->prepare("UPDATE categories SET nom = ?, image = ? WHERE id = ?");
            $stmt->execute([htmlspecialchars($nom), $image, $id]); 
            $_SESSION['message'] = "Catégorie modifiée avec succès."; 
        } else { 
            $_SESSION['message'] = "Le nom de la catégorie est obligatoire."; 
        } 
    } catch (PDOException $e) { 
        $_SESSION['message'] = "Erreur lors de la modification : " . $e->getMessage(); 
    } 
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit; 
}

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC); 
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
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
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
        .content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"> Electro Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="#categories"><i class="fa fa-list"></i> Catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <ul class="list-unstyled">
                    <li><a href="pro.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="pro.php"><i class="fa fa-box"></i> Produits</a></li>
                    <li><a href="#categories"><i class="fa fa-list"></i> Catégories</a></li>
                    <li><a href="users.php"><i class="fa fa-users"></i> Utilisateurs</a></li>
                    <li><a href="#"><i class="fa fa-cogs"></i> Paramètres</a></li>
                </ul>
            </div>

            <div class="col-md-10 content">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info">
                        <?= $_SESSION['message'] ?>
                        <?php unset($_SESSION['message']); ?>
                    </div>
                <?php endif; ?>

                <div class="card" id="categories">
                    <div class="card-header">
                        <h3>Gestion des Catégories</h3>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addCatModal">
                            <i class="fa fa-plus"></i> Ajouter Catégorie
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Image</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($cat['id']) ?></td>
                                        <td><?= htmlspecialchars($cat['nom']) ?></td>
                                        <td>
                                            <?php if ($cat['image']): ?>
                                                <img src="<?= htmlspecialchars($cat['image']) ?>" alt="Image" width="50">
                                            <?php else: ?>
                                                Pas d'image
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?delete_cat=<?= $cat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette catégorie ?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCatModal"
                                                data-id="<?= $cat['id'] ?>"
                                                data-nom="<?= htmlspecialchars($cat['nom']) ?>"
                                                data-image="<?= htmlspecialchars($cat['image']) ?>">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div> <!-- fin col-md-10 -->
        </div> <!-- fin row -->
    </div> <!-- fin container-fluid -->

    <!-- Modal d'ajout de catégorie -->
    <div class="modal fade" id="addCatModal" tabindex="-1" aria-labelledby="addCatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCatModalLabel">Ajouter Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nom" class="form-control mb-2" placeholder="Nom de la catégorie" required>
                    <input type="file" name="image" class="form-control mb-2" accept="image/*">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="ajt_cat" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification de catégorie -->
    <div class="modal fade" id="editCatModal" tabindex="-1" aria-labelledby="editCatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" enctype="multipart/form-data" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCatModalLabel">Modifier Catégorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_cat_id">
                    <input type="text" name="nom" class="form-control mb-2" id="edit_cat_nom" placeholder="Nom de la catégorie" required>
                    <input type="file" name="image" class="form-control mb-2" accept="image/*">
                    <input type="hidden" name="current_image" id="edit_cat_current_image">
                    <img src="" alt="Current Image" id="edit_cat_image" width="100" class="mb-2">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="edit_cat" class="btn btn-primary">Modifier</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Remplir le modal d'édition avec les données de la catégorie
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#editCatModal"]');
            editButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const id = button.getAttribute('data-id');
                    const nom = button.getAttribute('data-nom');
                    const image = button.getAttribute('data-image');
                    
                    document.getElementById('edit_cat_id').value = id;
                    document.getElementById('edit_cat_nom').value = nom;
                    document.getElementById('edit_cat_current_image').value = image;
                    document.getElementById('edit_cat_image').src = image ? image : '';
                });
            });
        });
    </script>
</body>
</html>
