<?php
$dbname = 'register';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=$dbname;", $username, $password);
    // $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // die("Connection failed: " . $e->getMessage());
}
?>
