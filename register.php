<?php
include 'config.php';

if(isset($_POST['submit'])) {
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $phone = mysqli_real_escape_string($conn, $_POST['phone']);
   $address = mysqli_real_escape_string($conn, $_POST['address']);
   $gender = mysqli_real_escape_string($conn, $_POST['gender']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

   $select = mysqli_query($conn, "SELECT * FROM `register_form` WHERE email = '$email'") or die('query failed');

   if(mysqli_num_rows($select) > 0) {
      $message[] = 'User already exists!';
   } else {
      if($pass != $cpass) {
         $message[] = 'Passwords do not match!';
      } else {
         mysqli_query($conn, "INSERT INTO `register_form` (name, email, phone, address, gender, password) 
                             VALUES ('$name', '$email', '$phone', '$address', '$gender', '$pass')") or die('query failed');
         $message[] = 'Registered successfully!';
         header('location:login.php');
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="header.css">
   <style>


.form-container {
   background-color: rgba(255, 255, 255, 0.9); /* Light background with some transparency */
   border-radius: 8px; /* Rounded corners */
   padding: 20px;
   width: 600px; /* Fixed width for form */
   margin: 50px auto; /* Center the form horizontally */
   margin-top: 130px; /* Add space above the form */
   box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2); /* Shadow effect */
}
.form-container h3 {
   text-align: center;
   margin-bottom: 20px;
   color: #333; /* Darker text color for the header */
}

.box {
   width: 100%; /* Full width inputs */
   padding: 12px; /* More padding for inputs */
   margin: 10px 0; /* Margin between inputs */
   border: 1px solid #ccc; /* Light border */
   border-radius: 4px; /* Rounded corners for inputs */
   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Input shadow */
   transition: border-color 0.3s; /* Smooth transition */
}

.box:focus {
   border-color: #007BFF; /* Change border color on focus */
   outline: none; /* Remove default outline */
}

.btn {
   background-color: #007BFF; /* Button color */
   color: white; /* Button text color */
   border: none; /* Remove border */
   padding: 12px; /* Padding */
   border-radius: 4px; /* Rounded corners for button */
   cursor: pointer; /* Pointer on hover */
   width: 100%; /* Full width button */
   transition: background-color 0.3s; /* Smooth transition */
}

.btn:hover {
         background: #004d40; /* Darker teal on hover */
         transform: translateY(-2px); /* Button lift effect */
      
}

p {
   text-align: center; /* Center the paragraph */
   color: #555; /* Lighter color for the text */
}

.message {
   background-color: #f8d7da; /* Light red background for messages */
   color: #721c24; /* Dark red text */
   border: 1px solid #f5c6cb; /* Border color */
   border-radius: 4px; /* Rounded corners */
   padding: 10px; /* Padding for messages */
   margin: 10px 0; /* Margin for messages */
   text-align: center; /* Center the message text */
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
      <h3>Register Now</h3>
      <input type="text" name="name" required placeholder="Enter username" class="box">
      <input type="email" name="email" required placeholder="Enter email" class="box">
      <input type="text" name="phone" required placeholder="Enter phone number" class="box">
      <input type="text" name="address" required placeholder="Enter address" class="box">
      <select name="gender" class="box">
         <option value="default">---SELECT GENDER---</option>
         <option value="Male">Male</option>
         <option value="Female">Female</option>
         <option value="Other">Other</option>
      </select>
      <input type="password" name="password" required placeholder="Enter password" class="box">
      <input type="password" name="cpassword" required placeholder="Confirm password" class="box">
      <input type="submit" name="submit" class="btn" value="Register now">
      <p>Already have an account? <a href="login.php" style="color: #00796b;">Login now</a></p>
   </form>
</div>

</body>
</html>
