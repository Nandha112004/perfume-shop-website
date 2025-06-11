<?php
include 'config.php';
session_start();

if(isset($_POST['submit'])) {
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   // Check admin credentials
   if($email == 'admin@gmail.com' && $pass == md5('admin')) {
      $_SESSION['admin_id'] = 1;
      header('location:admin.php');
   } else {
      $select = mysqli_query($conn, "SELECT * FROM `register_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

      if(mysqli_num_rows($select) > 0) {
         $row = mysqli_fetch_assoc($select);
         $_SESSION['user_id'] = $row['id'];
         header('location:shop.php');
      } else {
         $message[] = 'Incorrect email or password!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="header.css">
   <style>
      body {
         font-family: 'Arial', sans-serif;
         
         display: flex;
         justify-content: center;
         align-items: center;
         height: 100vh;
         margin: 0;
         color: white;
      }

      
      .form-container {
         background: rgba(255, 255, 255, 0.9); /* White background with transparency */
         border-radius: 15px;
         padding: 40px;
         box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
         width: 600px; /* Fixed width */
      }

      .form-container h3 {
         margin-bottom: 20px;
         text-align: center;
         color: #00796b; /* Teal color */
      }
      .form-container p {
         color: black; /* Teal color */
      }

      .form-container input[type="email"],
      .form-container input[type="password"] {
         width: 100%;
         padding: 15px;
         margin-bottom: 20px;
         border: 2px solid #00796b;
         border-radius: 5px;
         font-size: 16px;
         transition: border-color 0.3s;
      }

      .form-container input[type="email"]:focus,
      .form-container input[type="password"]:focus {
         border-color: #26a69a; /* Lighter teal on focus */
         outline: none;
      }

      .form-container .btn {
         background: linear-gradient(135deg, #26a69a, #00796b); /* Gradient button */
         color: white;
         padding: 15px;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         font-size: 18px;
         transition: background 0.3s, transform 0.3s;
         width: 100%;
      }

      .form-container .btn:hover {
         background: #004d40; /* Darker teal on hover */
         transform: translateY(-2px); /* Button lift effect */
      }

      .message {
         background-color: #ffcccc; /* Light red background for messages */
         color: #d8000c; /* Dark red text */
         padding: 10px;
         border: 1px solid #d8000c;
         border-radius: 5px;
         margin-bottom: 20px;
         text-align: center;
      }

      @media (max-width: 480px) {
         .form-container {
            width: 90%; /* Responsive width */
         }
      }
   </style>
</head>
<body>

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

<?php
if(isset($message)) {
   foreach($message as $msg) {
      echo '<div class="message" onclick="this.remove();">'.$msg.'</div>';
   }
}
?>

<div class="form-container">
   <form action="" method="post">
      <h3>Login Now</h3>
      <input type="email" name="email" required placeholder="Enter email" class="box">
      <input type="password" name="password" required placeholder="Enter password" class="box">
      <input type="submit" name="submit" class="btn" value="Login now">
      <p>Don't have an account? <a href="register.php" style="color: #00796b;">Register now</a></p>
   </form>
</div>

</body>
</html>
