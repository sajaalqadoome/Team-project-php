
<?php
session_start();
require_once "./connect.php";
$db = new Database();
$conn = $db->conn;


/*add*/
if (isset($_POST['add_now'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity']; 

    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please Login First'); window.location.href='login.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];


    $check_user = $conn->query("SELECT user_id FROM users WHERE user_id = '$user_id'");
    if ($check_user->num_rows == 0) {
        session_destroy();
        echo "<script>alert('Session expired. Please login again.'); window.location.href='login.php';</script>";
        exit();
    }


    $sql_cart = "SELECT cart_id FROM carts WHERE user_id = '$user_id'";
    $result_cart = $conn->query($sql_cart); 

    if ($result_cart && $result_cart->num_rows > 0) {
        $row_cart = $result_cart->fetch_assoc();
        $cart_id = $row_cart['cart_id'];
    } else {
        $sql_create_cart = "INSERT INTO carts (user_id) VALUES ('$user_id')";
        if ($conn->query($sql_create_cart)) {
            $cart_id = $conn->insert_id;
        } else {
            die("Database Error: " . $conn->error);
        }
    }

    $sql_check_item = "SELECT * FROM cart_items WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    $result_item = $conn->query($sql_check_item);

    if ($result_item && $result_item->num_rows > 0) {
        $sql_action = "UPDATE cart_items SET quantity = quantity + $quantity 
                       WHERE cart_id = '$cart_id' AND product_id = '$product_id'";
    } else {
        $sql_action = "INSERT INTO cart_items (cart_id, product_id, quantity) 
                       VALUES ('$cart_id', '$product_id', '$quantity')";
    }

    if ($conn->query($sql_action)) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $product_id . "&added=success");
        exit();
    } else {
        die("Error: " . $conn->error);
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

<?php
$cart_count = 0; 
if (isset($_SESSION['user_id'])) {
    $u_id = $_SESSION['user_id'];
    
    $sql_count = "SELECT SUM(ci.quantity) as total_items 
                  FROM cart_items ci 
                  JOIN carts c ON ci.cart_id = c.cart_id 
                  WHERE c.user_id = '$u_id'";
                  
    $result_count = $conn->query($sql_count);
    
    if ($result_count && $row = $result_count->fetch_assoc()) {
        $cart_count = $row['total_items'] ? $row['total_items'] : 0;
    }
}
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
<style>

    .auth-link {
        font-size: 14px;
        font-weight: 500;
        color: var(--sonic-silver); 
        transition: color 0.3s ease;
        text-transform: uppercase;
    }

    .auth-link:hover {
        color: #ff8f9c; 
    }

    @media (max-width: 768px) {
        .logo-auth-group {
            flex-direction: column;
            gap: 5px;
        }
    }


</style>

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

<!--login & Reg-->  

<div class="container" style="display: flex; align-items: center; justify-content: space-between;">

            <div class="logo-auth-group" style="display: flex; align-items: center; gap: 20px;">

                <div class="auth-buttons" style="display: flex; gap: 10px;">
                    <a href="Login.php" class="auth-link">Login</a>
                    <span style="color: #ccc;">|</span>
                    <a href="SignUp.php" class="auth-link">Register</a>
                </div>
            </div>
<!--login & Reg-->  
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

<a href="./cart.php" class="action-btn" title="Shopping Cart">
    <ion-icon name="bag-handle-outline"></ion-icon>
    <span class="count"><?php echo $cart_count; ?></span>
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

    <div class="showcase" style="cursor: pointer;" onclick="window.location.href='./productDetails.php?id=<?php echo $id; ?>';">

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


      </div>

    <form method="POST" >
        <div class="showcase-controls" style="display: flex; gap: 8px; margin-top: 10px; align-items: center; justify-content: center;">
            
            <input type="number" name="quantity" value="1" min="1" 
                   style="width: 45px; border: 1px solid #eee; text-align: center; border-radius: 5px; height: 35px;">
            
            <input type="hidden" name="product_id" value="<?php echo $id; ?>"> 
        <button type="submit" name="add_now" class="add-cart-btn custom-add-btn" 
        style="background: #ff8f9c; color: white; border: none; padding: 0 15px; border-radius: 5px; height: 35px; cursor: pointer; font-size: 12px; font-weight: 600; flex-grow: 1;"
        data-id="<?php echo $id; ?>" 
        data-name="<?php echo $name; ?>" 
        data-price="<?php echo $price; ?>"
        data-image="<?php echo $imagePath; ?>">
    ADD TO CART
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


<script>
// دالة لتحديث عداد السلة في الهيدر فوراً
function updateCartIconCount() {
    const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    const countElement = document.querySelector('.header-user-actions .action-btn .count');
    
    if (!isLoggedIn && countElement) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        let total = cart.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        countElement.innerText = total;
    }
}

// تشغيل العداد عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', updateCartIconCount);

document.querySelectorAll('.custom-add-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        
        if (!isLoggedIn) {
            // 1. منع الفورم من الإرسال لصفحة Login
            e.preventDefault();

            // 2. جلب الكمية من حقل الـ input القريب
            const qtyInput = this.closest('.showcase-controls').querySelector('input[name="quantity"]');
            const qtyValue = qtyInput ? qtyInput.value : 1;

            // 3. تجهيز بيانات المنتج
            const product = {
                id: this.dataset.id,
                name: this.dataset.name,
                price: this.dataset.price,
                image: this.dataset.image,
                quantity: qtyValue
            };

            // 4. الحفظ في Local Storage
            let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
            let found = cart.find(item => item.id === product.id);
            if (found) {
                found.quantity = parseInt(found.quantity) + parseInt(product.quantity);
            } else {
                cart.push(product);
            }
            localStorage.setItem('guest_cart', JSON.stringify(cart));

            alert("Added to guest cart!");
            updateCartIconCount();
        }
        // ملاحظة: إذا كان مسجل دخول، لن يدخل هنا وسيعمل الـ PHP (POST) بشكل طبيعي
    });
});
</script>