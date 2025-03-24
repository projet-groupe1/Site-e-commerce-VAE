<?php
session_start();

// Si un panier existe, on le vide
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
}

// Simulation d'envoi d'email de confirmation
if (isset($_SESSION['user_email']) && filter_var($_SESSION['user_email'], FILTER_VALIDATE_EMAIL)) {
    $to = htmlspecialchars($_SESSION['user_email']); // L'email de l'utilisateur
    $subject = "Confirmation de commande - Electro";
    $message = "Merci pour votre commande !\nVotre commande a bien été reçue et sera traitée sous peu.";
    $headers = "From: ndeye@electro.com\r\n";
    $headers .= "Reply-To: ndeye@electro.com\r\n";
    
    // Envoi de l'email
    if(mail($to, $subject, $message, $headers)) {
        echo "<p>L'email de confirmation a été envoyé avec succès.</p>";
    } else {
        echo "<p>Erreur : l'email n'a pas pu être envoyé.</p>";
    }
}

// Affichage du message de confirmation
echo "<h2>Merci pour votre commande !</h2>";
echo "<p>Un email de confirmation vous a été envoyé.</p>";

// Redirection vers la page d'accueil après 3 secondes
header("Refresh: 3; url=index.php");
exit;
?>
