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

// Handle form submission
if (isset($_POST["submit"])) {
  $title = $_POST["title"];
  $price = $_POST["price"];
  $quantity = $_POST["quantity"];
  $description = $_POST["description"];
  $img = $_FILES["img"]["name"];

  // Move uploaded image to the imgs folder
  move_uploaded_file($_FILES["img"]["tmp_name"], "imgage/" . $img);

  // Update the product in the database
  $stmt = $pdo->prepare("UPDATE products SET title = ?, price = ?, quantity = ?, description = ?, img = ? WHERE id = ?");
  $stmt->execute([$title, $price, $quantity, $description, $img, $id]);

  // Redirect to the products page
  header("location: products.php");
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Edit Product</title>
</head>

<body>
  <h1>Edit Product</h1>
  <form action="" method="post" enctype="multipart/form-data">
    <label for="name">Name:</label>
    <input type="text" name="title" id="title" value="<?php echo $product['title'] ?>" required><br>

    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" id="price" value="<?php echo $product['price'] ?>" required><br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="<?php echo $product['quantity'] ?>" required><br>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required><?php echo $product['description'] ?></textarea><br>

    <label for="img">Image:</label>
    <input type="file" name="img" id="img"><br>

    <input type="submit" name="submit" value="Update Product">
  </form>
</body>

</html>