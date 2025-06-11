<?php
// Configuration
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'shop_db';

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total count of products
$count_sql = "SELECT COUNT(*) AS product_count FROM products";
$count_result = $conn->query($count_sql);

$product_count = 0;
if ($count_result->num_rows > 0) {
    $row = $count_result->fetch_assoc();
    $product_count = $row['product_count'];
}

// Fetch total count of registered users
$user_count_query = "SELECT COUNT(*) AS user_count FROM register_form";
$result = $conn->query($user_count_query);

$user_count = 0;
if ($result && $result->num_rows > 0) {
   $row = $result->fetch_assoc();
   $user_count = $row['user_count'];
}

// Fetch total count of payments
$payment_count_query = "SELECT COUNT(*) AS payment_count FROM payments";
$payment_result = $conn->query($payment_count_query);

$payment_count = 0;
if ($payment_result && $payment_result->num_rows > 0) {
    $row = $payment_result->fetch_assoc();
    $payment_count = $row['payment_count'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="header.css">
    <style>
        body {
            background-color: black;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            justify-content: space-around;
            margin: 80px 0;
            flex-wrap: wrap;
        }

        .dashboard-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
            margin-bottom: 20px;
            position: relative;
            transition: all 0.3s ease;
        }

        .dashboard-section:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        h2 {
            font-size: 1.5em;
            color: #333;
            margin-bottom: 10px;
        }

        .count {
            font-size: 4em;
            font-weight: bold;
            color: #27ae60;
        }

        p {
            font-size: 1.2em;
            color: #666;
        }

        .add-product-btn, .report-btn, .refresh-btn {
            display: inline-block;
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 15px;
            background-color: #4CAF50;
            color: #fff;
            transition: background-color 0.3s;
        }

        .add-product-btn:hover, .report-btn:hover, .refresh-btn:hover {
            background-color: #3e8e41;
        }

        .refresh-btn {
            background-color: #2980b9;
        }

        .refresh-btn:hover {
            background-color: #3498db;
        }

        /* Add button gradients */
        .add-product-btn, .refresh-btn, .report-btn {
            background-image: linear-gradient(to right, #4CAF50, #3e8e41);
        }

        .refresh-btn {
            background-image: linear-gradient(to right, #2980b9, #3498db);
        }

        .report-btn {
            background-color: #f39c12;
            background-image: linear-gradient(to right, #f39c12, #e67e22);
            text-decoration: none;
        }

        .report-btn:hover {
            background-color: #e67e22;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .dashboard-section {
                width: 90%;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<!-- Header Section -->
<header>
    <nav class="navbar">
        <div class="logo"></div>
        <h3 id="logo">PERFUME SHOP</h3>
        <ul class="navlist">
            <li><a href="home.html" class="navlinks">Home</a></li>
            <li><a href="men.html" class="navlinks">Men</a></li>
            <li><a href="women.html" class="navlinks">Women</a></li>
            <li><a href="brand.html" class="navlinks">Brands</a></li>
            <li><a href="login.php" class="navlinks active">Shop</a></li>
            <li><a href="about.html" class="navlinks">About</a></li>
            <li><a href="login.php" style="display: flex; justify-content: center; align-items: center;">
                <img src="image/user-profile.png" alt="User Icon" style="width: 50px; height: 50px; border-radius: 50%; cursor: pointer;">
            </a></li>
        </ul>
    </nav>
</header>

<h1 style="color: white; text-align: center;">Perfume Shop - Admin Dashboard</h1>

<!-- Dashboard Container -->
<div class="container">
    <!-- Product Count Section -->
    <div class="dashboard-section">
        <h2>Total Products</h2>
        <div class="count"><?php echo $product_count; ?></div>
        <p>Products available in the store</p>
        <a href="product_entry.php" class="add-product-btn" role="button">Add New Product</a>
    </div>

    <!-- User Count Section -->
    <div class="dashboard-section">
        <h2>Total Registered Users</h2>
        <div class="count"><?php echo $user_count; ?></div>
        <p>Registered users in the system</p>
        <button class="refresh-btn" onclick="location.reload();">Refresh Count</button>
    </div>

    <!-- Payments Count Section -->
    <div class="dashboard-section">
        <h2>Total Payments</h2>
        <div class="count"><?php echo $payment_count; ?></div>
        <p>Payments processed in the system</p>
    </div>

    <!-- Report Navigation Section -->
    <div class="dashboard-section">
        <h2>View Filters</h2>
        <p>Generate detailed filters</p>
        <a href="report.php" class="report-btn" role="button">Go to Filters</a>
    </div>
</div>

</body>
</html>
