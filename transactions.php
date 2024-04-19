<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["username"])) {
    header("location: login.php");
}

$username = $_SESSION["username"];

// Get the product ID from the URL
$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

// Get the product data from the database
$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$product_id]);
$product = $stmt->fetch();

// Check if the product exists
if (!$product) {
    echo "Product not found";
    exit;
}

// Display the form
echo "<h1>Add Transaction</h1>";
echo "<form action='' method='post'>";
echo "<label for='quantity'>Quantity:</label>";
echo "<input type='number' name='quantity' id='quantity' value='1' min='1' max='{$product['quantity']}'>";
echo "<input type='hidden' name='product_id' value='{$product['id']}'>";
echo "<input type='submit' value='Add Transaction'>";
echo "</form>";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the quantity from the form
    $quantity = $_POST["quantity"];

    // Check if the quantity is valid
    if ($quantity <= 0 || $quantity > $product["quantity"]) {
        echo "Invalid quantity";
        exit;
    }

    // Add the transaction to the database
    $stmt = $pdo->prepare('INSERT INTO transactions (user_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
    $stmt->execute([$user_id, $product_id, $quantity, $product["price"]]);

    // Decrease the product quantity
    $stmt = $pdo->prepare('UPDATE products SET quantity = quantity - ? WHERE id = ?');
    $stmt->execute([$quantity, $product_id]);

    // Redirect to the transactions page
    header("location: transactions.php");
    exit;
}

// The amounts of transactions to show on each page
$num_transactions_on_each_page = 4;

// The current page - in the URL, will appear as index.php?page=transactions&p=1, index.php?page=transactions&p=2, etc...
$current_page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1;

// Select transactions ordered by the date added
$stmt = $pdo->prepare("SELECT * FROM transactions INNER JOIN users ON transactions.user_id = users.id INNER JOIN products ON transactions.product_id = products.id ORDER BY transactions.date_added DESC LIMIT ?,?");

// bindValue will allow us to use an integer in the SQL statement, which we need to use for the LIMIT clause
$stmt->bindValue(1, ($current_page - 1) * $num_transactions_on_each_page, PDO::PARAM_INT);
$stmt->bindValue(2, $num_transactions_on_each_page, PDO::PARAM_INT);

$stmt->execute();

echo "<table>";
echo "<tr><th>ID</th><th>User</th><th>Product</th><th>Quantity</th><th>Price</th><th>Date Added</th></tr>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row["id"] . "</td>";
    echo "<td>" . $row["username"] . "</td>";
    echo "<td><a href='index.php?page=product&id=" . $row["product_id"] . "'>" . $row["product"] . "</a></td>";
    echo "<td>" . $row["quantity"] . "</td>";
    echo "<td>" . $row["price"] . "</td>";
    echo "<td>" . $row["date_added"] . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<a href='add.php'>Add New Product</a>";

// Pagination
$stmt = $pdo->prepare('SELECT COUNT(*) FROM transactions INNER JOIN users ON transactions.user_id = users.id INNER JOIN products ON transactions.product_id = products.id');
$stmt->execute();
$num_transactions = $stmt->fetchColumn();
$num_pages = ceil($num_transactions / $num_transactions_on_each_page);

echo "<div class='pagination'>";
for ($i = 1; $i <= $num_pages; $i++) {
    if ($i == $current_page) {
        echo "<span>$i</span>";
    } else {
        echo "<a href='transactions.php?page=transactions&p=$i'>$i</a>";
    }
}
echo "</div>";