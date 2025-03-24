<?php
// Dans le script de commande
$to = "client@example.com";
$subject = "Confirmation de commande";
$message = "Merci pour votre achat de ".$total." FCFA";
$headers = "From: noreply@ecommerce.com";

if(mail($to, $subject, $message, $headers)) {
    echo json_encode(['success' => true]);
}
?>