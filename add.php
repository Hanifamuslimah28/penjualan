<?php
session_start();
require_once 'config.php';

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
  header("location: login.php");
}

// Handle form submission
if (isset($_POST["submit"])) {
  $title = $_POST["title"];
  $price = $_POST["price"];
  $quantity = $_POST["quantity"];
  $description = $_POST["description"];
  $img = $_FILES["img"]["name"];

  // Move uploaded image to the imgs folder
  move_uploaded_file($_FILES["img"]["tmp_name"], "image/" . $img);

  // Insert the new product into the database
  $stmt = $pdo->prepare("INSERT INTO products (title, price, quantity, description, img) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$title, $price, $quantity, $description, $img]);

  // Redirect to the products page
  header("location: products.php");
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Add Product</title>
</head>

<body>
  <h1>Add Product</h1>
  <form action="" method="post" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input type="text" name="title" id="title" required><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" id="price" required><br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea><br>

    <label for="img">Image:</label>
    <input type="file" name="img" id="img" required><br>

    <input type="submit" name="submit" value="Add Product">
  </form>
</body>

</html>