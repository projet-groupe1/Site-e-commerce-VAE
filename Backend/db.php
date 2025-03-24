<?php
$host = 'localhost';
$dbname = 'electro'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->query('SELECT 1');
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
