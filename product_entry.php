<?php
// Database configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'shop_db';

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize message variables
$message = "";
$message_type = "";

// Upload product image and add product
if (isset($_POST['submit'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];
    $product_brand = $_POST['product_brand'];
    $product_image = $_FILES['product_image'];

    // Validate product image
    $max_file_size = 7 * 1024 * 1024; // 7 MB in bytes
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if ($product_image['size'] > 0 && $product_image['size'] <= $max_file_size && in_array($product_image['type'], $allowed_types)) {
        $image_path = "pro_images/" . basename($product_image['name']);
        if (move_uploaded_file($product_image['tmp_name'], $image_path)) {
            // Insert product into the database
            $stmt = $conn->prepare("INSERT INTO products (name, price, image, category, brand) VALUES (:name, :price, :image, :category, :brand)");
            $stmt->execute([
                ':name' => $product_name,
                ':price' => $product_price,
                ':image' => $product_image['name'],
                ':category' => $product_category,
                ':brand' => $product_brand,
            ]);
            $message = "New product added successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to upload image!";
            $message_type = "error";
        }
    } else {
        $message = "Invalid image type or size!";
        $message_type = "error";
    }
}

// Update product
if (isset($_POST['update'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_category = $_POST['product_category'];
    $product_brand = $_POST['product_brand'];

    // Prepare SQL update statement
    $stmt = $conn->prepare("UPDATE products SET name = :name, price = :price, category = :category, brand = :brand WHERE id = :id");
    $stmt->execute([
        ':name' => $product_name,
        ':price' => $product_price,
        ':category' => $product_category,
        ':brand' => $product_brand,
        ':id' => $product_id,
    ]);

    // Check if a new image is uploaded
    if (!empty($_FILES['product_image']['name'])) {
        $product_image = $_FILES['product_image'];
        $max_file_size = 7 * 1024 * 1024; // 7 MB in bytes
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
        // Check if the file size is within the limit and if the file type is allowed
        if ($product_image['size'] > 0 && $product_image['size'] <= $max_file_size && in_array($product_image['type'], $allowed_types)) {
            $image_path = "pro_images/" . basename($product_image['name']);
            if (move_uploaded_file($product_image['tmp_name'], $image_path)) {
                // Update product with new image
                $stmt = $conn->prepare("UPDATE products SET image = :image WHERE id = :id");
                $stmt->execute([
                    ':image' => $product_image['name'],
                    ':id' => $product_id,
                ]);
                $message = "Product updated successfully!";
                $message_type = "success";
            } else {
                $message = "Failed to upload image!";
                $message_type = "error";
            }
        } else {
            $message = "Invalid image type or size!";
            $message_type = "error";
        }
    } else {
        $message = "Product updated successfully without changing the image!";
        $message_type = "success";
    }
}

// Delete product
if (isset($_POST['delete'])) {
    $product_id = $_POST['product_id'];

    // Get product image path to delete from server
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product && file_exists("pro_images/" . $product['image'])) {
        unlink("pro_images/" . $product['image']);  // Delete image from server
    }

    // Delete the product
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    if ($stmt->execute([':id' => $product_id])) {
        $message = "Product deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting product!";
        $message_type = "error";
    }
}

// Fetch products for display
$result = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Entry</title>
    <link rel="stylesheet" href="header.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        h2 {
            color: white;
            margin-top: 110px;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            background: #fff;
            margin: 20px auto;
            padding: 20px;
            margin-top: 80px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: calc(100% - 22px);
            height: 45px;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"] {
            width: 100%;
            height: 45px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgba(0, 0, 0, 0.5); /* Black w/ opacity */
}
.modal form {
            background: #fff;
            margin: 20px auto;
            padding: 20px;
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
        }

        .modal-content h2{
            color: red;
            margin-top: 80px;
            text-align: center;
            margin-bottom: 20px;
        }
.modal-content {
    background-color: #fefefe;
    margin: 9% auto; /* 15% from the top and centered */
    padding: 5px;
    margin-top: 10px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
    max-width: 500px; /* Max width for the modal */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Box shadow */
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

        .product-list {
    max-width: 800px; /* Set a max width for better layout */
    margin: 0 auto; /* Center the product list */
    padding: 20px;
}

.product-item {
    display: flex; /* Use flexbox for layout */
    align-items: center; /* Center items vertically */
    justify-content: space-between; /* Space between image/info and buttons */
    padding: 15px; /* Spacing around the product item */
    border-radius: 8px; /* Rounded corners */
    background-color: #f9f9f9; /* Light background for contrast */
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
    margin-bottom: 15px; /* Space between products */
    transition: transform 0.2s; /* Smooth hover effect */
}

.product-item:hover {
    transform: scale(1.02); /* Scale up slightly on hover */
}

.product-item img {
    max-width: 100px; /* Limit image size */
    max-height: 100px; /* Limit image size */
    border-radius: 5px; /* Rounded corners for the image */
    margin-right: 15px; /* Space between image and text */
}

.product-item div {
    flex-grow: 1; /* Allow this div to grow */
    margin-right: 20px; /* Space between text and buttons */
}

.product-item strong {
    font-size: 1.2em; /* Slightly larger font for the product name */
    color: #333; /* Darker color for better visibility */
}

.product-item p {
    display: inline; /* Display price inline */
    margin: 0 5px 0 0; /* Spacing for inline text */
}

.button-container {
    display: flex;
    background: transparent;
    flex-direction: column; /* Align buttons vertically */
    align-items: center; /* Center buttons horizontally */
    justify-content: space-between; /* Space between buttons */
    height: 100%; /* Take full height of product item */
}
.button-container form {
            margin: 0 ;
            padding: 0;
            background: none;
}
.delete-button, .update-button {
    border: none;
    color: white;
    padding: 10px; /* Same padding for uniform button size */
    text-align: center;
    text-decoration: none;
    display: inline-flex; /* Use flexbox for alignment */
    justify-content: center; /* Center text inside buttons */
    align-items: center; /* Center text vertically */
    font-size: 16px; /* Font size */
    margin: 5px 0; /* Space between buttons */
    cursor: pointer;
    border-radius: 5px; /* Rounded corners */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
    width: 100px; /* Fixed width for uniformity */
}

.button-container form .delete-button {
    background-color: #d9534f; /* Red for delete */
}

.button-container .update-button {
    background-color: #28a745; /* Different color for update button */
}

.button-container form .delete-button:hover {
    background-color: red; /* Darker red on hover */
}

.button-container .update-button:hover {
    background-color: green; /* Darker blue on hover */
}
    </style>
</head>
<body style="background: black;">
<header>
   <nav class="navbar">
   <div class="logo"></div>
      <h3 id="logo">PERFUME SHOP</h3>
      <ul class="navlist">
         <li><a href="home.html" class="navlinks">Home</a></li>
         <li><a href="men.html" class="navlinks">Men</a></li>
         <li><a href="women.html" class="navlinks">Women</a></li>
         <li><a href="brand.html" class="navlinks">Brands</a></li>
         <li><a href="login.php" class="navlinks">Shop</a></li>
         <li><a href="about.html" class="navlinks">About</a></li>
         <li>
            <a href="login.php" class="navlinks active">
               <img src="image/user-profile.png" alt="User Icon" style="width: 50px; height: 50px; border-radius: 50%;">
            </a>
         </li>
      </ul>
   </nav>
</header>




    <?php if (!empty($message)): ?>
        <div class="message <?= $message_type; ?>">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h2>Product Entry</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required>

        <label for="product_price">Product Price:</label>
        <input type="number" id="product_price" name="product_price" required>

        <label for="product_category">Product Category:</label>
        <select id="product_category" name="product_category" required>
            <option value="">---SELECT GENDER---</option>
            <option value="male">MALE</option>
            <option value="female">FEMALE</option>
            <!-- Add more categories as needed -->
        </select>

        <label for="product_brand">Product Brand:</label>
        <input type="text" id="product_brand" name="product_brand" required>

        <label for="product_image">Product Image:</label>
        <input type="file" id="product_image" name="product_image" accept="image/*" required>

        <input type="submit" name="submit" value="Add Product">
    </form>

    <div class="product-list">
    <h3>Product List</h3>
    <?php foreach ($result as $product): ?>
        <div class="product-item">
            <img src="pro_images/<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
            <div>
                <strong><?= htmlspecialchars($product['name']); ?></strong><br>
                <p style="display:inline;">Price: $<?= htmlspecialchars($product['price']); ?></p><br>
                <p style="display:inline; margin-left: 20px;">Category: <?= htmlspecialchars($product['category']); ?></p><br>
                <p style="display:inline; margin-left: 20px;">Brand: <?= htmlspecialchars($product['brand']); ?></p><br>
            </div>
            
    
            <div class="button-container">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                    <input type="submit" name="delete" value="Delete" class="delete-button" onclick="return confirm('Are you sure you want to delete this product?');">
                </form>
                <button class="update-button" onclick="openModal(<?= $product['id']; ?>, '<?= htmlspecialchars($product['name']); ?>', <?= $product['price']; ?>, '<?= htmlspecialchars($product['category']); ?>', '<?= htmlspecialchars($product['brand']); ?>', '<?= htmlspecialchars($product['image']); ?>')">Update</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Update Modal -->
<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Update Product</h2>
        <form id="updateForm" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="update_product_id" required>
            <label for="update_product_name">Product Name:</label>
            <input type="text" id="update_product_name" name="product_name" required>

            <label for="update_product_price">Product Price:</label>
            <input type="number" id="update_product_price" name="product_price" required>

            <label for="update_product_category">Product Category:</label>
            <select id="update_product_category" name="product_category" required>
                <option value="">---SELECT CATEGORY---</option>
                <option value="male">MALE</option>
                <option value="female">FEMALE</option>
                <!-- Add more categories as needed -->
            </select>

            <label for="update_product_brand">Product Brand:</label>
            <input type="text" id="update_product_brand" name="product_brand" required>

            <label for="update_product_image">Product Image:</label>
            <input type="file" id="update_product_image" name="product_image" accept="image/*">

            <input type="submit" name="update" value="Update Product">
        </form>
    </div>
</div>


    <script>
      function openModal(id, name, price, category, brand, image) {
    document.getElementById('update_product_id').value = id;
    document.getElementById('update_product_name').value = name;
    document.getElementById('update_product_price').value = price;
    document.getElementById('update_product_category').value = category;
    document.getElementById('update_product_brand').value = brand;

    const modal = document.getElementById('updateModal');
    modal.style.display = "block";
}

function closeModal() {
    const modal = document.getElementById('updateModal');
    modal.style.display = "none";
}
    </script>
</body>
</html>
