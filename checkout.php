<?php
session_start();
require_once "connect.php";
$db = new Database();
$conn = $db->conn;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// جلب إجمالي السعر من السلة
$query = "SELECT SUM(p.price * ci.quantity) as total 
          FROM cart_items ci 
          JOIN products p ON ci.product_id = p.product_id 
          JOIN carts c ON ci.cart_id = c.cart_id 
          WHERE c.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$cart_total = $row['total'] ? $row['total'] : 0;

if (isset($_POST['place_order']) && $cart_total > 0) {
    $payment_method = $_POST['payment_method'];

    // 1. إضافة الطلب في جدول orders
    // الأعمدة المتوقعة: user_id, total_amount, status
    $order_sql = "INSERT INTO orders (user_id, total_amount, status) 
                  VALUES ('$user_id', '$cart_total', 'pending')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);

        // 2. إضافة الدفع في جدول payments (حسب الصورة التي أرسلتها)
        // الأعمدة: order_id, payment_method, payment_status, amount
        $payment_sql = "INSERT INTO payments (order_id, payment_method, payment_status, amount) 
                        VALUES ('$order_id', '$payment_method', 'completed', '$cart_total')";
        mysqli_query($conn, $payment_sql);

        // 3. نقل المنتجات لجدول order_items
        $move_items = "INSERT INTO order_items (order_id, product_id, quantity, price)
                       SELECT '$order_id', ci.product_id, ci.quantity, p.price
                       FROM cart_items ci
                       JOIN products p ON ci.product_id = p.product_id
                       JOIN carts c ON ci.cart_id = c.cart_id
                       WHERE c.user_id = '$user_id'";
        
        if (mysqli_query($conn, $move_items)) {
            // 4. تفريغ السلة
            $clear_cart = "DELETE ci FROM cart_items ci 
                           JOIN carts c ON ci.cart_id = c.cart_id 
                           WHERE c.user_id = '$user_id'";
            mysqli_query($conn, $clear_cart);

            echo "<script>alert('Order Placed Successfully!'); window.location.href='index.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="checkout-container">
    <h2>Review Your Order</h2>
    <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <p>Total Amount: <strong style="color: #ff8f9c; font-size: 20px;"><?php echo number_format($cart_total, 2); ?> JD</strong></p>
    </div>
    
    <form method="POST">
        <h3 style="font-size: 16px; margin-bottom: 10px;">Select Payment Method:</h3>
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 10px; cursor: pointer;">
                <input type="radio" name="payment_method" value="Cash" checked> 
                <i class="fas fa-money-bill-wave"></i> Cash on Delivery
            </label>
            <label style="display: block; cursor: pointer;">
                <input type="radio" name="payment_method" value="Credit Card"> 
                <i class="fas fa-credit-card"></i> Credit Card / Online
            </label>
        </div>
        
        <button type="submit" name="place_order" class="btn">Confirm & Pay</button>
        <a href="cart.php" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;">Return to Cart</a>
    </form>
</div>    

</body>
</html>