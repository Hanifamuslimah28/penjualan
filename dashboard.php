<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["username"])) {
    header("location: login.php");
}

$username = $_SESSION["username"];

echo "Welcome, $username!<br>";
echo "<a href='logout.php'>Logout</a>";

$num_products_on_each_page = 4;

$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT ?,?');

$stmt->bindValue(1, ($current_page - 1) * $num_products_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_products_on_each_page, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
foreach ($products as $product) {
    echo "<div class='product-card'>";
    echo "<img src='./image/$product[img]' alt='$product[title]'>";
    echo "<h3>$product[title]</h3>";
    echo "<p>$product[price]</p>";
    echo "<a href='product.php?id=$product[id]'>View Details</a>";
    echo "</div>";
}

