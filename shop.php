<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if(isset($_GET['logout'])){
   unset($user_id);
   session_destroy();
   header('location:login.php');
}

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];
   $product_category = $_POST['product_category']; // Added Category
   $product_brand = $_POST['product_brand']; // Added Brand Name

   $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($select_cart) > 0){
      $message[] = 'product already added to cart!';
   } else {
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, image, quantity, category, brand) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity', '$product_category', '$product_brand')") or die('query failed');
      $message[] = 'product added to cart!';
   }
}

if(isset($_POST['update_cart'])){
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($conn, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'cart quantity updated successfully!';
}

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
   header('locatshop.php');
}

if(isset($_GET['delete_all'])){
   mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:shop.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shopping Cart</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="header.css">

</head>
<body style="background-color: black;">

<!-- Header Section -->
<header  bg-dark-black >
        <nav class="navbar">
            <div class="logo"></div>
            <h3 id="logo">PERFUME SHOP</h3>
            <ul class="navlist">
                <li>
                    <a href="home.html" class="navlinks">Home</a>
                </li>
                <li>
                    <a href="men.html" class="navlinks ">Men</a>
                </li>
                <li>
                    <a href="women.html" class="navlinks">Women</a>
                </li>
                <li>
                    <a href="brand.html" class="navlinks">Brands</a>
                </li>
                <li>
                    <a href="login.php" class="navlinks active">Shop</a>
                </li>
                <li>
                    <a href="about.html" class="navlinks">About</a>
                </li>
                <li>
                    <a href="login.php" style="display: flex; justify-content: center; align-items: center;">
                        <img src="image/user-profile.png" alt="User Icon" style="width: 50px; height: 50px; border-radius: 50%; cursor: pointer;">
                    </a>
                </li>
            </ul>
        </nav>
    </header>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>

<div class="container">

<div class="user-profile">
   <?php
      $select_user = mysqli_query($conn, "SELECT * FROM `register_form` WHERE id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_user) > 0){
         $fetch_user = mysqli_fetch_assoc($select_user);
      }
   ?>
   <p>Username: <span><?php echo $fetch_user['name']; ?></span></p>
   <p>Email: <span><?php echo $fetch_user['email']; ?></span></p>
   <div class="flex">
      <a href="login.php" class="btn">Login</a>
      <a href="register.php" class="option-btn">Register</a>
      <a href="shop.php?logout=<?php echo $user_id; ?>" onclick="return confirm('Are your sure you want to logout?');" class="delete-btn">Logout</a>
   </div>
</div>

<div class="products">

   <h1 class="heading" style="color: var(--white)">Latest Products</h1>

   <div class="box-container">

   <?php
      $select_product = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
      if(mysqli_num_rows($select_product) > 0){
         while($fetch_product = mysqli_fetch_assoc($select_product)){
   ?>
      <form method="post" class="box" action="">
         <img src="pro_images/<?php echo $fetch_product['image']; ?>" width="270px" height="350px">
         <div class="name"><?php echo $fetch_product['name']; ?></div>
         <div class="price">₹<?php echo $fetch_product['price']; ?>/-</div>
         <input type="number" min="1" name="product_quantity" value="1">
         <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
         <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
         <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">

         <!-- New Category  -->
         <div class="category"><?php echo $fetch_product['category']; ?></div>
         <input type="hidden" name="product_category" value="<?php echo $fetch_product['category']; ?>">
         

         <!-- New Brand Input -->
         <div class="brand"><?php echo $fetch_product['brand']; ?></div>
         <input type="hidden" name="product_brand" value="<?php echo $fetch_product['brand']; ?>">

         <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
      </form>
   <?php
      }
   }
   ?>

   </div>

</div>

<!-- Shopping Cart -->
<div class="shopping-cart">
   <h1 class="heading" style="color: var(--white)">Shopping Cart</h1>
   <table>
      <thead>
         <th>Image</th>
         <th>Name</th>
         <th>Price</th>
         <th>Category</th> <!-- Added Category Column -->
         <th>Brand</th> <!-- Added Brand Column -->
         <th>Quantity</th>
         <th>Total Price</th>
         <th>Action</th>
      </thead>
      <tbody>
      <?php
         $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
         $grand_total = 0;
         if(mysqli_num_rows($cart_query) > 0){
            while($fetch_cart = mysqli_fetch_assoc($cart_query)){
      ?>
         <tr>
            <td><img src="pro_images/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
            <td><?php echo $fetch_cart['name']; ?></td>
            <td>₹<?php echo $fetch_cart['price']; ?>/-</td>
            <td><?php echo $fetch_cart['category']; ?></td> <!-- Display Category -->
            <td><?php echo $fetch_cart['brand']; ?></td> <!-- Display Brand -->
            <td>
               <form action="" method="post">
                  <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                  <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                  <input type="submit" name="update_cart" value="Update" class="option-btn">
               </form>
            </td>
            <td>₹<?php echo $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']); ?>/-</td>
            <td><a href="shop.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('Remove item from cart?');">Remove</a></td>
         </tr>
      <?php
         $grand_total += $sub_total;
            }
         }else{
            echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="8">No item added</td></tr>';
         }
      ?>
      <tr class="table-bottom">
         <td colspan="6">Grand Total :</td>
         <td>₹<?php echo $grand_total; ?>/-</td>
         <td><a href="shop.php?delete_all" onclick="return confirm('Delete all from cart?');" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">Delete All</a></td>
      </tr>
   </tbody>
   </table>

      <div class="cart-btn">  
      <!-- Proceed to Checkout Form -->
      <form action="payment.php" method="post">
         <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
         <button type="submit" class="btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">Proceed to Checkout</button>
      </form>
   </div>

</div>

</div>

</body>
</html>
