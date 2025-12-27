
<?php
session_start();
require_once "./connect.php";
$db = new Database();
$conn = $db->conn;


/*add*/
if (isset($_POST['add_now'])) {
if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please Login First'); window.location.href='login.php';</script>";
        exit();
    }

    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id'];

    $sql_cart = "SELECT cart_id FROM carts WHERE user_id = '$user_id'";
    $result_cart = mysqli_query($conn, $sql_cart);

    if (mysqli_num_rows($result_cart) > 0) {
        $row_cart = mysqli_fetch_assoc($result_cart);
        $cart_id = $row_cart['cart_id'];
    } else {
        $sql_create_cart = "INSERT INTO carts (user_id) VALUES ('$user_id')";
        mysqli_query($conn, $sql_create_cart);
        $cart_id = mysqli_insert_id($conn);
    }

    $sql_check_item = "SELECT * FROM cart_items WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    $result_item = mysqli_query($conn, $sql_check_item);

    if (mysqli_num_rows($result_item) > 0) {
        $sql_action = "UPDATE cart_items SET quantity = quantity + $quantity 
                       WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    } else {
        $sql_action = "INSERT INTO cart_items (cart_id, product_id, quantity) 
                       VALUES ('$cart_id', '$product_id', '$quantity')";
    }

    if (mysqli_query($conn, $sql_action)) {

header("Location: " . $_SERVER['PHP_SELF'] . "?added=success");
      exit();
    }
}
/*add*/



/*search*/
if (isset($_GET['search-btn']) && !empty($_GET['search'])) {
    $search_input = $_GET['search'];
    $sql = "SELECT product_id, name, price, image FROM products WHERE status = 'active' AND name LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%$search_input%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT product_id, name, price, image FROM products WHERE status = 'active'";
    $result = $conn->query($sql);
}
/*search*/

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anon - eCommerce Website</title>

  <!--
    - favicon
  -->
  <link rel="shortcut icon" href="./assets/images/logo/favicon.ico" type="image/x-icon">

  <!--
    - custom css link
  -->
  <link rel="stylesheet" href="./assets/css/style-prefix.css">

  <!--
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
    rel="stylesheet">

</head>

<body>


  <div class="overlay" data-overlay></div>

  <!--
    - MODAL
  -->





    </div>

  </div>

<?php if (isset($_GET['added']) && $_GET['added'] == 'success'): ?>
    <script>
        alert("Success: Product added to cart!");
        window.history.replaceState({}, document.title, window.location.pathname);
    </script>
<?php endif; ?>

  <!--
    - HEADER
  -->

  <header>


    <div class="header-main">

      <div class="container">

        <a href="#" class="header-logo">
          <img src="./assets/images/logo/logo.svg" alt="Anon's logo" width="120" height="36">
        </a>
   
<!--Search-->
<form method="get">
    <div class="header-search-container">
        <input type="search" name="search" class="search-field" placeholder="Enter your product name..." required>
        <button type="submit" name="search-btn" class="search-btn">
            <ion-icon name="search-outline"></ion-icon>
        </button>
    </div>
</form>
<!--Search-->
        <div class="header-user-actions">
<a href="./edit_profile.php" class="action-btn" title="Personal information">
    <ion-icon name="person-outline"></ion-icon>
  </a>

          <button class="action-btn">
            <ion-icon name="heart-outline"></ion-icon>
            <span class="count">0</span>
          </button>
<a href="./cart.php" class="action-btn" title="Shopping Cart">
    <ion-icon name="bag-handle-outline"></ion-icon>
    <span class="count">0</span>
  </a>

<a href="./LandingPage.php" class="action-btn" title="Logout">
  <ion-icon name="log-out-outline"></ion-icon>
</a>


        </div>

      </div>

    </div>


    <div class="mobile-bottom-navigation">



      <button class="action-btn">
        <ion-icon name="bag-handle-outline"></ion-icon>
      </button>

      <button class="action-btn">
        <a href="./edit_profile.php" class="action-btn" title="Personal information">
        <ion-icon name="person-outline"></ion-icon>
      </button>


      
      <button class="action-btn">
        <ion-icon name="heart-outline">
          
        </ion-icon>
      </button>

<a href="./LandingPage.php" class="action-btn" title="Logout">
  <ion-icon name="log-out-outline"></ion-icon>
</a>

    </div>


  </header>





  <!--
    - MAIN
  -->

  <main>

    <!--
      - BANNER
    -->

    <div class="banner">

      <div class="container">

        <div class="slider-container has-scrollbar">

          <div class="slider-item">

            <img src="./assets/images/banner-1.jpg" alt="women's latest fashion sale" class="banner-img">

            <div class="banner-content">

              <p class="banner-subtitle">Trending item</p>

              <h2 class="banner-title">Women's latest fashion sale</h2>

              <p class="banner-text">
                starting at &dollar; <b>20</b>.00
              </p>

              <a href="#" class="banner-btn">Shop now</a>

            </div>

          </div>

          <div class="slider-item">

            <img src="./assets/images/banner-2.jpg" alt="modern sunglasses" class="banner-img">

            <div class="banner-content">

              <p class="banner-subtitle">Trending accessories</p>

              <h2 class="banner-title">Modern sunglasses</h2>

              <p class="banner-text">
                starting at &dollar; <b>15</b>.00
              </p>

              <a href="#" class="banner-btn">Shop now</a>

            </div>

          </div>

          <div class="slider-item">

            <img src="./assets/images/banner-3.jpg" alt="new fashion summer sale" class="banner-img">

            <div class="banner-content">

              <p class="banner-subtitle">Sale Offer</p>

              <h2 class="banner-title">New fashion summer sale</h2>

              <p class="banner-text">
                starting at &dollar; <b>29</b>.99
              </p>

              <a href="#" class="banner-btn">Shop now</a>

            </div>

          </div>

        </div>

      </div>

    </div>






    <!--
      - PRODUCT
    -->

    <div class="product-container">

      <div class="container">


          <!--
            - PRODUCT GRID
          -->

          <div class="product-main">

            <h2 class="title">New Products</h2>
<!--Product in DB-->
<div class="product-grid">

  <?php
  if ($result && $result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
          $id    = $row['product_id'];
          $name  = $row['name'];
          $price = $row['price'];
          $imagePath = $row['image']; 
  ?>

      <div class="showcase">

        <div class="showcase-banner">
          <img src="./<?php echo $imagePath; ?>" alt="<?php echo $name; ?>" width="300" class="product-img default">
          <img src="./<?php echo $imagePath; ?>" alt="<?php echo $name; ?>" width="300" class="product-img hover">
          
        </div>

    <div class="showcase-content">
    <a href="detalis.php?id=<?php echo $id; ?>">
        <h3 class="showcase-title"><?php echo $name; ?></h3>
    </a>

    <div class="price-box">
        <p class="price">$<?php echo $price; ?></p>

<a href="./productDetails.php?id=<?php echo $row['product_id']; ?>" class="btn-action">
    <ion-icon name="eye-outline"></ion-icon>
    <span>View Details</span>
</a>
      </div>

    <form method="POST" >
        <div class="showcase-controls" style="display: flex; gap: 8px; margin-top: 10px; align-items: center; justify-content: center;">
            
            <input type="number" name="quantity" value="1" min="1" 
                   style="width: 45px; border: 1px solid #eee; text-align: center; border-radius: 5px; height: 35px;">
            
            <input type="hidden" name="product_id" value="<?php echo $id; ?>"> 
            
            <button type="submit" name="add_now" class="add-cart-btn" 
                    style="background: #ff8f9c; color: white; border: none; padding: 0 15px; border-radius: 5px; height: 35px; cursor: pointer; font-size: 12px; font-weight: 600; flex-grow: 1;">
              ADD TO CART
            </button>
              <button type="submit" name="add_now" class="add-cart-btn" 
                    style="background: #ff8f9c; color: white; border: none; padding: 0 15px; border-radius: 5px; height: 35px; cursor: pointer; font-size: 12px; font-weight: 600; flex-grow: 1;">
              ADD TO Fav
            </button> 
        </div>
    </form>
</div>  

      </div>

  <?php 
      }
  } else {
      echo "<p>No products found.</p>";
  }
  ?>

</div>

<!--Product in DB-->


          </div>

       <!--
            - PRODUCT GRID
          -->         
        </div>

      </div>

    </div>

    <div>

  

    </div>


  </main>





  <!--
    - FOOTER
  -->

  <footer>

    <div class="footer-category">

      <div class="container">

        <h2 class="footer-category-title">Brand directory</h2>

        <div class="footer-category-box">

          <h3 class="category-box-title">Fashion :</h3>

          <a href="#" class="footer-category-link">T-shirt</a>
          <a href="#" class="footer-category-link">Shirts</a>
          <a href="#" class="footer-category-link">shorts & jeans</a>
          <a href="#" class="footer-category-link">jacket</a>
          <a href="#" class="footer-category-link">dress & frock</a>
          <a href="#" class="footer-category-link">innerwear</a>
          <a href="#" class="footer-category-link">hosiery</a>

        </div>

        <div class="footer-category-box">
          <h3 class="category-box-title">footwear :</h3>
        
          <a href="#" class="footer-category-link">sport</a>
          <a href="#" class="footer-category-link">formal</a>
          <a href="#" class="footer-category-link">Boots</a>
          <a href="#" class="footer-category-link">casual</a>
          <a href="#" class="footer-category-link">cowboy shoes</a>
          <a href="#" class="footer-category-link">safety shoes</a>
          <a href="#" class="footer-category-link">Party wear shoes</a>
          <a href="#" class="footer-category-link">Branded</a>
          <a href="#" class="footer-category-link">Firstcopy</a>
          <a href="#" class="footer-category-link">Long shoes</a>
        </div>





    </div>


    <div class="footer-bottom">

      <div class="container">

        <img src="./assets/images/payment.png" alt="payment method" class="payment-img">

        <p class="copyright">
          Copyright &copy; <a href="#">Anon</a> all rights reserved.
        </p>

      </div>

    </div>

  </footer>






  <!--
    - custom js link
  -->
  <script src="./assets/js/script.js"></script>

  <!--
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

</html>