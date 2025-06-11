<?php
session_start();
include 'config.php';

// Check if grand total is posted from cart
if (isset($_POST['grand_total'])) {
    $grand_total = $_POST['grand_total'];
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Fetch the user details from the database
    $user_query = mysqli_query($conn, "SELECT * FROM `register_form` WHERE id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($user_query) > 0) {
        $user_data = mysqli_fetch_assoc($user_query);
    } else {
        die('User not found.');
    }

    // Fetch the cart products from the database
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    $products = [];
    while ($row = mysqli_fetch_assoc($cart_query)) {
        $products[] = $row;
    }
} else {
    die('No total amount received.');
}

// Shop's Billing Address (hardcoded)
$shop_address = "XYZ Electronics, 123 Main Street, City, Country, 987654";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>

    <style>
        *{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins',sans-serif;

}
body{
    background-color: black;
    scroll-behavior: smooth;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

header {
    position: fixed;
    top: 0;
    width: 100%;
    height: 14vh; /* Fixed header height */
    z-index: 1000;
    background: rgba(0, 0, 0, 0.836); /* Semi-transparent background */
    backdrop-filter: blur(20px); /* Blur effect */
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    transition: background-color 0.3s ease, backdrop-filter 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.2); /* Border added */
    box-shadow: 0 0 14px 0 rgba(0, 0, 0, 0.5); /* Box shadow added */
}

header.scrolled {
    background: rgba(0, 0, 0, 0.185); /* Darker background when scrolling */
    backdrop-filter: blur(120px); /* Increase blur on scroll */
    box-shadow: 0 0 16px 20px rgba(0, 0, 0, 0.8); /* Stronger shadow on scroll */
}

.navbar {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: transparent;

}

.navbar #logo {
    margin-right: auto;
    position: relative;
    font-weight: 900;
    font-size: 2.5rem;
    left: 20px;
    color: white;
    cursor: default;
    background-color: transparent;
}
#logo:hover{
    color: red;
}

.navlist {
    display: flex;
    align-items: center;
    list-style: none;
    gap: 2rem;
    background-color: transparent;
}

.navlinks {
    color: #fff;
    font-weight: bolder;
    font-size: 1.4rem;
    list-style: none;
    text-decoration: none;
    background-color: transparent;
}

.navlinks:hover,
.navlinks.active {
    color: #ff000d;
    background-color: transparent;
}

.logo {
    width: 1.3rem;
    aspect-ratio: 1;
    background: #fff;
    padding: 3px;
    transform: rotate(45deg);
    border: 2px solid #fff; /* Optional border for logo */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Box shadow for logo */
}
.user-icon {
    width: 30px;
    height: 30px;
    margin-left: 10px;
    border-radius: 50%;
    cursor: pointer;
}

.user-icon:hover {
    scale: 1.05px;
}

   

body {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.payment-details {
    background-color: #ffffff;
    margin-top: 660px;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0px 12px 30px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 500px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.payment-details:hover {
    transform: translateY(-10px);
    box-shadow: 0px 16px 40px rgba(0, 0, 0, 0.2);
}

h1, h3 {
    text-align: center;
    color: #00796b; /* Modern teal color */
    font-family: 'Arial', sans-serif;
    margin-bottom: 20px;
}

p, label {
    font-size: 16px;
    color: #333333; /* Darker color for readability */
    margin-bottom: 10px;
}

input[type="text"], 
input[type="email"], 
input[type="number"], 
textarea {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 8px;
    border: 1px solid #bbb;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

input[type="text"]:focus, 
input[type="email"]:focus, 
input[type="number"]:focus, 
textarea:focus {
    border: 1px solid #26a69a;
    outline: none;
    box-shadow: 0 0 10px rgba(38, 166, 154, 0.3); /* Soft shadow in teal */
}

.payment-methods {
    margin: 20px 0;
}

.payment-methods label {
    display: block;
    margin-bottom: 15px;
    font-size: 16px;
    cursor: pointer;
}

.payment-methods input[type="radio"] {
    margin-right: 10px;
}

.extra-payment-info {
    display: none;
}

button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #26a69a, #00796b); /* Gradient teal for modern look */
    color: #ffffff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 18px;
    margin-top: 25px;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

button:hover {
    background-color: #004d40; /* Darker teal on hover */
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
}

.payment-details form {
    text-align: center;
}

.read-only {
    background-color: #f1f3f4;
    cursor: not-allowed;
}

@media (max-width: 480px) {
    .payment-details {
        padding: 25px;
        max-width: 90%;
    }

    button {
        font-size: 16px;
    }
}
table {
    width: 100%;
    border-collapse: collapse; /* Removes space between table cells */
    margin-bottom: 20px; /* Space below the table */
}

th, td {
    padding: 12px; /* Space within cells */
    text-align: left; /* Align text to the left */
    border-bottom: 1px solid #ddd; /* Border below each row */
}

th {
    background-color: #00796b; /* Teal background for header */
    color: #ffffff; /* White text for header */
    font-weight: bold; /* Bold text for headers */
}

tr:hover {
    background-color: #e0f2f1; /* Light teal background on hover */
}

/* Button Styling for Mobile */
@media (max-width: 600px) {
    button {
        padding: 12px; /* Smaller padding on mobile */
        font-size: 16px; /* Smaller font size on mobile */
    }
}

/* Responsive Form Elements */
@media (max-width: 600px) {
    input[type="text"], 
    input[type="email"], 
    input[type="number"], 
    textarea {
        padding: 10px; /* Smaller padding for inputs on mobile */
    }

    .payment-details {
        padding: 20px; /* Less padding on mobile */
    }
}

/* Hover Effect for Inputs */
input[type="text"]:hover, 
input[type="email"]:hover, 
input[type="number"]:hover, 
textarea:hover {
    border: 1px solid #00796b; /* Change border color on hover */
}

/* Loading State */
.loading {
    position: fixed; /* Fixed position to cover the screen */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8); /* White background with transparency */
    display: flex; /* Centering the loader */
    align-items: center; /* Vertical alignment */
    justify-content: center; /* Horizontal alignment */
    z-index: 9999; /* On top of other elements */
}

/* Loader Animation */
.loader {
    border: 4px solid #f3f3f3; /* Light gray background */
    border-top: 4px solid #00796b; /* Teal color */
    border-radius: 50%; /* Circular shape */
    width: 40px; /* Loader size */
    height: 40px; /* Loader size */
    animation: spin 1s linear infinite; /* Spin animation */
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

    </style>
    <script>
        function showExtraPaymentInfo(paymentMethod) {
            var extraInfoSections = document.querySelectorAll('.extra-payment-info');
            extraInfoSections.forEach(section => section.style.display = 'none');

            if (paymentMethod === 'card') {
                document.getElementById('card-info').style.display = 'block';
            } else if (paymentMethod === 'gpay') {
                document.getElementById('gpay-info').style.display = 'block';
            }
        }
    </script>
</head>
<body style="background-color: black;">

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
         <li>
            <a href="login.php" class="navlinks active">
               <img src="image/user-profile.png" alt="User Icon" style="width: 50px; height: 50px; border-radius: 50%;">
            </a>
         </li>
      </ul>
   </nav>
</header>


<div class="payment-details">
    <h1>Payment Details</h1>

    <!-- Displaying User Information -->
    <p><strong>User:</strong> <?php echo $user_data['name']; ?></p>
    <p><strong>Email:</strong> <?php echo $user_data['email']; ?></p>
    <p><strong>Phone:</strong> <?php echo $user_data['phone']; ?></p>

    <!-- Displaying Total Amount to Pay -->
    <p><strong>Total Amount to Pay:</strong> ₹<?php echo $grand_total; ?>/-</p>

    <!-- Displaying Product Details -->
    <h3>Product Details</h3>
    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_quantity = 0;
            foreach ($products as $product) {
                echo '<tr>';
                echo '<td>' . $product['name'] . '</td>';
                echo '<td>' . $product['quantity'] . '</td>';
                echo '<td>₹' . $product['price'] . '</td>';
                echo '</tr>';
                $total_quantity += $product['quantity'];
            }
            ?>
        </tbody>
    </table>

    <p><strong>Total Products:</strong> <?php echo $total_quantity; ?></p>

    <form action="process_payment.php" method="POST">
        <!-- Shop's Billing Address (read-only) -->
        <h3>Billing Information</h3>
        <label for="billing_address">Billing Address (Our Shop):</label>
        <textarea name="billing_address" class="read-only" readonly><?php echo $shop_address; ?></textarea>

        <!-- Pre-filling User Shipping Address -->
        <label for="shipping_address">Shipping Address (Your Address):</label>
        <textarea name="shipping_address"><?php echo $user_data['address']; ?></textarea>

        <!-- Payment Methods -->
        <div class="payment-methods">
            <h3>Select Payment Method</h3>
            <label>
                <input type="radio" name="payment_method" value="gpay" required onclick="showExtraPaymentInfo('gpay')">
                GPay
            </label>
            <label>
                <input type="radio" name="payment_method" value="card" required onclick="showExtraPaymentInfo('card')">
                Credit/Debit Card
            </label>
            <label>
                <input type="radio" name="payment_method" value="net_banking" required onclick="showExtraPaymentInfo('net_banking')">
                Net Banking
            </label>
        </div>

        <!-- Additional Payment Info for Credit Card -->
        <div id="card-info" class="extra-payment-info">
            <h3>Card Information</h3>
            <label for="card_number">Card Number:</label>
            <input type="number" name="card_number" placeholder="XXXX-XXXX-XXXX-XXXX">
            <label for="expiry_date">Expiry Date:</label>
            <input type="text" name="expiry_date" placeholder="MM/YY">
            <label for="cvv">CVV:</label>
            <input type="number" name="cvv" placeholder="123">
        </div>

        <!-- Additional Payment Info for GPay -->
        <div id="gpay-info" class="extra-payment-info">
            <h3>GPay Information</h3>
            <label for="gpay_upi">UPI ID:</label>
            <input type="text" name="gpay_upi" placeholder="yourname@upi">
        </div>

        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">

        <button type="submit">Proceed with Payment</button>
    </form>
</div>
