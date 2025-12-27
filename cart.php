<?php
session_start();
require_once "connect.php"; // تأكد من مسار ملف الاتصال
$db = new Database();
$conn = $db->conn;

// 1. التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. معالجة حذف منتج إذا تم الضغط على زر الحذف
if (isset($_GET['remove'])) {
    $item_id = $_GET['remove'];
    $delete_query = "DELETE FROM cart_items WHERE cart_item_id = '$item_id'";
    mysqli_query($conn, $delete_query);
    header("Location: cart.php"); // إعادة تحميل الصفحة لتحديث البيانات
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="./assets/css/style-prefix.css"> <style>
        .cart-container { padding: 30px; max-width: 1000px; margin: auto; }
        .cart-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .cart-table th { background: #ff8f9c; color: white; padding: 15px; text-align: center; }
        .cart-table td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; vertical-align: middle; }
        .product-img { width: 80px; border-radius: 5px; }
        .btn-remove { color: #eb4d4b; text-decoration: none; font-weight: bold; font-size: 14px; }
        .btn-remove:hover { text-decoration: underline; }
        .total-section { margin-top: 20px; text-align: right; font-size: 20px; font-weight: 600; }
        .checkout-btn { background: #333; color: white; padding: 10px 25px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 15px; }
    </style>
</head>
<body>

<div class="cart-container">
    <h2 class="title" style="margin-bottom: 20px;">Shopping Cart</h2>

    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // استعلام سحب البيانات بالربط بين الجداول
            $query = "SELECT ci.cart_item_id, ci.quantity, p.name, p.price, p.image 
                      FROM cart_items ci 
                      JOIN products p ON ci.product_id = p.product_id 
                      JOIN carts c ON ci.cart_id = c.cart_id 
                      WHERE c.user_id = '$user_id'";

            $result = mysqli_query($conn, $query);
            $grand_total = 0;

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $subtotal = $row['price'] * $row['quantity'];
                    $grand_total += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo $row['image']; ?>" class="product-img"><br>
                            <strong><?php echo $row['name']; ?></strong>
                        </td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>$<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="cart.php?remove=<?php echo $row['cart_item_id']; ?>" 
                               class="btn-remove" onclick="return confirm('Are you sure?')">Remove</a>
                        </td>
                    </tr>
                <?php
                }
            } else {
                echo "<tr><td colspan='5'>Your cart is empty! <a href='index.php'>Go Shopping</a></td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php if ($grand_total > 0): ?>
    <div class="total-section">
        <p>Total: $<?php echo number_format($grand_total, 2); ?></p>
           <a href="index.php" class="checkout-btn">Back</a>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
      
    </div>
    <?php endif; ?>
</div>

</body>
</html>