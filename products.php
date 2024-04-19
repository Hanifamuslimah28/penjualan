<?php
session_start();
require_once 'config.php';

$sql = "SELECT * FROM products";
$stmt = $pdo->prepare($sql);
$stmt->execute();

echo "<table>";
echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Action</th></tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["title"] . "</td>";
    echo "<td>" . $row["price"] . "</td>";
    echo "<td>" . $row["quantity"] . "</td>";
    echo "<td><a href='edit.php?id=" . $row["id"] . "'>Edit</a> | <a href='delete.php?id=" . $row["id"] . "'>Delete</a></td>";
    echo "</tr>";
}

echo "</table>";

echo "<a href='add.php'>Add New Product</a>";
