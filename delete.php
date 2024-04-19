<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["username"])) {
  header("location: login.php");
}

$username = $_SESSION["username"];

// Get the product data from the database
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

// Delete the product from the database
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

// Redirect to the products page
header("location: products.php");
exit;
