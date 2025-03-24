<?php
session_start();

// Vérification si la requête est bien une méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données envoyées en JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérification si le format des données est correct
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Données JSON mal formées.']);
        http_response_code(400); // Bad Request
        exit;
    }

    // Vérification si l'identifiant du produit est valide
    if (!isset($data['product_id']) || !is_numeric($data['product_id']) || $data['product_id'] <= 0) {
        echo json_encode(['error' => 'Identifiant du produit invalide ou manquant.']);
        http_response_code(400); // Bad Request
        exit;
    }

    $productId = (int)$data['product_id'];


    // Initialisation du panier si ce n'est pas déjà fait
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Ajouter la quantité si l'article existe déjà dans le panier
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += 1; // Incrémente la quantité
    } else {
        $_SESSION['cart'][$productId] = 1; // Ajoute un nouvel article avec quantité 1
    }

    // Retourner la réponse avec le total des articles dans le panier
    echo json_encode([
        'cart_count' => array_sum($_SESSION['cart']) // Total des articles dans le panier
    ]);
    exit;
} else {
    echo json_encode(['error' => 'Méthode de requête non autorisée.']);
    http_response_code(405); // Method Not Allowed
    exit;
}
?>
