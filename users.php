<?php
session_start();
include 'db.php'; // Connexion à la base de données

// ----------------- Gestion des Utilisateurs -----------------
// Suppression d'un utilisateur
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        // Supprimer l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['message'] = "Utilisateur supprimé avec succès.";
    } else {
        $_SESSION['message'] = "Utilisateur introuvable.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ----------------- Modification d'un utilisateur -----------------
if (isset($_POST['userId']) && isset($_POST['nom']) && isset($_POST['email'])) {
    $userId = intval($_POST['userId']);
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE users SET nom = ?, email = ? WHERE id = ?");
    $stmt->execute([$nom, $email, $userId]);
    $_SESSION['message'] = "Utilisateur mis à jour avec succès.";
    echo json_encode(['success' => true]);
    exit;
}

// ----------------- Récupération des Utilisateurs -----------------
$query = "
    SELECT u.id, u.nom, u.email, u.created_at, 
           SUM(cp.quantite * cp.prix) AS total_commandes
    FROM users u
    LEFT JOIN commandes c ON u.id = c.user_id
    LEFT JOIN commande_produits cp ON c.id = cp.commande_id
    GROUP BY u.id
";
$users = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
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

        /* Sidebar */
        .sidebar {
            background-color: #343a40;
            color: #fff;
            padding-top: 20px;
            height: 100vh;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            padding: 10px 20px;
            font-size: 16px;
        }

        .sidebar a:hover {
            background-color: #555;
        }

        .sidebar .nav-item .nav-link {
            color: #ddd !important;
        }

        /* Tableau des utilisateurs */
        .table th {
            background-color: #343a40;
            color: #fff;
        }

        .table td {
            background-color: #ffffff;
        }

        /* Modal */
        .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <!-- Navigation principale -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Electro Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="pro.php">Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="Cat.php">Catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Utilisateurs</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <ul class="list-unstyled">
                    <li><a href="#dashboard"><i class="fa fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="pro.php"><i class="fa fa-box"></i> Produits</a></li>
                    <li><a href="Cat.php"><i class="fa fa-list"></i> Catégories</a></li>
                    <li><a href="#"><i class="fa fa-users"></i> Utilisateurs</a></li>
                    <li><a href="#"><i class="fa fa-cogs"></i> Paramètres</a></li>
                    <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=electro">Admin</a>                </li>

                </ul>
            </div>

            <!-- Contenu principal -->
            <div class="col-md-10 content">
                <h1 class="mb-4">Liste des Utilisateurs</h1>

                <!-- Message de session -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-info">
                        <?= $_SESSION['message'] ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <!-- Tableau des utilisateurs -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Utilisateurs Inscrits</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                    <th>Total des commandes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['id']) ?></td>
                                        <td><?= htmlspecialchars($user['nom']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td><?= number_format($user['total_commandes'], 2, ',', ' ') ?> FCFA</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal" onclick="editUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['nom']) ?>', '<?= htmlspecialchars($user['email']) ?>')">✏️</button>
                                            <a href="?delete_user=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">🗑️</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Formulaire de modification d'utilisateur -->
            <form id="editUserForm" method="POST">
              <input type="hidden" id="userId" name="userId" value="">
              <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <button type="submit" class="btn btn-primary">Modifier</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour gérer l'édition via AJAX -->
    <script>
        // Fonction pour remplir le modal avec les informations de l'utilisateur à modifier
        function editUser(id, nom, email) {
            document.getElementById('userId').value = id;
            document.getElementById('nom').value = nom;
            document.getElementById('email').value = email;
        }

        // Soumettre le formulaire via AJAX (pour ne pas recharger la page)
        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(this);
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload(); // Rafraîchir la page pour voir les changements
                }
            })
            .catch(error => console.error('Erreur:', error));
        });
    </script>
</body>
</html>
