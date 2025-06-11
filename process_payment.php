<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $grand_total = $_POST['grand_total'];

    // Validate required fields
    if (empty($_POST['billing_address']) || empty($_POST['payment_method'])) {
        die('Billing address and payment method are required.');
    }

    // Sanitize user input
    $billing_address = mysqli_real_escape_string($conn, $_POST['billing_address']);
    $shipping_address = isset($_POST['shipping_address']) ? mysqli_real_escape_string($conn, $_POST['shipping_address']) : '';
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);

    // Retrieve product details from the cart
    $product_details = [];
    $cart_query = mysqli_query($conn, "SELECT name, price FROM `cart` WHERE user_id = '$user_id'") or die('Cart query failed');

    while ($row = mysqli_fetch_assoc($cart_query)) {
        $product_details[] = [
            'name' => $row['name'],
            'price' => $row['price'],
        ];
    }

    // If product details are retrieved, store them as JSON
    $products_json = json_encode($product_details);

    // Prepare to insert payment information based on selected method
    if ($payment_method == 'card') {
        // Validate card details
        if (empty($_POST['card_number']) || empty($_POST['expiry_date']) || empty($_POST['cvv'])) {
            die('All card details must be provided.');
        }
    
        $card_number = mysqli_real_escape_string($conn, $_POST['card_number']);
        $expiry_date = mysqli_real_escape_string($conn, $_POST['expiry_date']);
        $cvv = mysqli_real_escape_string($conn, $_POST['cvv']);
    
        // Insert query for card payment
        $query = "INSERT INTO `payments` (user_id, total_amount, payment_method, card_number, expiry_date, cvv, billing_address, shipping_address) 
                  VALUES ('$user_id', '$grand_total', '$payment_method', '$card_number', '$expiry_date', '$cvv', '$billing_address', '$shipping_address')";
    
    } elseif ($payment_method == 'gpay') {
        // Validate UPI ID
        if (empty($_POST['gpay_upi'])) {
            die('Please provide your UPI ID for GPay.');
        }
    
        $gpay_upi = mysqli_real_escape_string($conn, $_POST['gpay_upi']);
        // Insert query for GPay payment
        $query = "INSERT INTO `payments` (user_id, total_amount, payment_method, gpay_upi, billing_address, shipping_address) 
                  VALUES ('$user_id', '$grand_total', '$payment_method', '$gpay_upi', '$billing_address', '$shipping_address')";
    
    } else {
        // For Net Banking or other methods
        $query = "INSERT INTO `payments` (user_id, total_amount, payment_method, billing_address, shipping_address) 
                  VALUES ('$user_id', '$grand_total', '$payment_method', '$billing_address', '$shipping_address')";
    }

    // Execute the query and check for success
    if (mysqli_query($conn, $query)) {
        echo "<script>window.location.href='success.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
