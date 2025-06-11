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

// Fetch product data based on category or brand (AJAX request handler)
if (isset($_POST['category'])) {
    $category = $_POST['category'];

    // Query products based on category
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = :category");
    $stmt->execute(['category' => $category]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the product data as JSON
    echo json_encode($products);
    exit();
}

if (isset($_POST['brand'])) {
    $brand = $_POST['brand'];

    // Query products based on brand
    $stmt = $conn->prepare("SELECT * FROM products WHERE brand = :brand");
    $stmt->execute(['brand' => $brand]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the product data as JSON
    echo json_encode($products);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Search by Category and Brand</title>
    <style>
        /* General styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            width: 100%;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        select, input[type="text"] {
            display: block;
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            transition: border-color 0.3s;
        }

        select:focus, input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .product-card {
            width: 45%;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .product-card img {
            width: 100%;
            height: auto;
            max-width: 150px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .product-card h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .product-card p {
            font-size: 16px;
            color: #777;
            margin-bottom: 5px;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .product-card img:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .product-card {
                width: 100%;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <h1>Search Products by Category or Brand</h1>

    <!-- Category Selection -->
    <select id="category" onchange="fetchCategoryProducts()">
        <option value="">Select Category</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
    </select>

    <!-- Brand Text Input -->
    <input type="text" id="brand" placeholder="Enter Brand Name" oninput="fetchBrandProducts()">

    <!-- Product List -->
    <div id="product-list" class="product-list"></div>
</div>

<script>
    // Fetch products based on selected category
    function fetchCategoryProducts() {
        let category = document.getElementById('category').value;
        let productList = document.getElementById('product-list');
        productList.innerHTML = '';

        if (category !== '') {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (this.status === 200) {
                    let products = JSON.parse(this.responseText);
                    products.forEach(function (product) {
                        let productDiv = document.createElement('div');
                        productDiv.classList.add('product-card');
                        productDiv.innerHTML = `
                            <img src="pro_images/${product.image}" alt="${product.name}">
                            <h2>${product.name}</h2>
                            <p>Brand: ${product.brand}</p>
                            <p>Category: ${product.category}</p>
                            <p>Price: $${product.price}</p>
                        `;
                        productList.appendChild(productDiv);
                    });
                }
            };
            xhr.send('category=' + category);
        }
    }

    // Fetch products based on entered brand name
    function fetchBrandProducts() {
        let brand = document.getElementById('brand').value.trim();
        let productList = document.getElementById('product-list');
        productList.innerHTML = '';

        if (brand !== '') {
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (this.status === 200) {
                    let products = JSON.parse(this.responseText);
                    products.forEach(function (product) {
                        let productDiv = document.createElement('div');
                        productDiv.classList.add('product-card');
                        productDiv.innerHTML = `
                            <img src="pro_images/${product.image}" alt="${product.name}">
                            <h2>${product.name}</h2>
                            <p>Brand: ${product.brand}</p>
                            <p>Category: ${product.category}</p>
                            <p>Price: $${product.price}</p>
                        `;
                        productList.appendChild(productDiv);
                    });
                }
            };
            xhr.send('brand=' + brand);
        }
    }
</script>

</body>
</html>
