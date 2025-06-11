<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #26a69a, #00796b); /* Gradient background */
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        p {
            font-size: 24px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }

        a {
            background-color: #ffffff; /* White background for button */
            color: #00796b; /* Text color matching the gradient */
            padding: 15px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 20px;
            transition: background-color 0.3s, transform 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        a:hover {
            background-color: #e0e0e0; /* Light gray on hover */
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <h1>Payment Successful!</h1>
    <p>Thank you for your payment. Your order will be processed soon.</p>
    <a href="shop.php">Return to Home</a>
</body>
</html>
