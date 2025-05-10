<?php 
    include "./components/connect.php";
    if(isset($_COOKIE["user_id"])) $user_id = $_COOKIE["user_id"];
    else setcookie("user_id", create_unique_id(), time() + 60 * 60 * 24 * 30);

    if(isset($_POST["update_cart"])) {
        $cart_id = filter_var($_POST["cart_id"], FILTER_SANITIZE_STRING);
        $qty = filter_var($_POST["qty"], FILTER_SANITIZE_STRING);
        
        $update_cart = $connect -> prepare(
            "UPDATE `cart` SET qty = ? WHERE id = ?");
        $update_cart -> execute([$qty, $cart_id]);
        $success_msg[] = "Cart quantity updated!";
    }
    if(isset($_POST["delete_cart"])) {
        $cart_id = filter_var($_POST["cart_id"], FILTER_SANITIZE_STRING);
        
        $verify_delete_item = $connect -> prepare(
            "SELECT * FROM `cart` WHERE id = ?");
        $verify_delete_item -> execute([$cart_id]);
        if($verify_delete_item -> rowCount() > 0){
            $delete_item = $connect -> prepare(
                "DELETE FROM `cart` WHERE id = ?");
            $delete_item -> execute([$cart_id]);
            $success_msg[] = "Cart item removed!";
        }
        else $warning_msg[] = "Cart item already deleted!";
    }
    if(isset($_POST["empty_cart"])) {
        $verify_empty_cart = $connect -> prepare(
            "SELECT * FROM `cart` WHERE user_id = ?");
        $verify_empty_cart -> execute([$user_id]);
        if($verify_empty_cart -> rowCount() > 0){
            $empty_cart = $connect -> prepare(
                "DELETE FROM `cart` WHERE user_id = ?");
            $empty_cart -> execute([$user_id]);
            $success_msg[] = "Removed all from cart!";
        }
        else $warning_msg[] = "Already removed all!";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>
    <?php include "./components/header.php"; ?>
    <section class="products">
        <h1 class="heading">shopping cart</h1>
        <div class="box-container">
            <?php
                $grand_total = 0;
                $select_cart = $connect -> prepare(
                    "SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart -> execute([$user_id]);
                if($select_cart -> rowCount() > 0){
                    while($fetch_cart = $select_cart 
                        -> fetch((PDO::FETCH_ASSOC))) {
                        $select_products = $connect -> prepare(
                            "SELECT * FROM `products` WHERE id = ?");
                        $select_products -> execute([$fetch_cart["product_id"]]);
                        if($select_products -> rowCount() > 0) {
                            while($fetch_product = $select_products 
                                -> fetch((PDO::FETCH_ASSOC))) {
            ?>
            <form action="" method="POST" class="box">
                <input type="hidden" name="cart_id" 
                    value="<?= $fetch_cart["id"] ?>" />
                <img src="uploaded_files/<?= $fetch_product["image"]; ?>" 
                    alt="" class="image" />
                <h3 class="name"><?= $fetch_product["name"]; ?></h3>
                <div class="flex">
                    <p class="price">
                        <i class="fas fa-indian-rupee-sign"></i>
                        <?= $fetch_product["price"]; ?>
                    </p>
                    <input type="number" name="qty" maxlength="2" 
                        min="1" max="99" value="<?= $fetch_cart["qty"]; ?>" 
                            required class="qty" />
                    <button type="submit" class="fas fa-edit" name="update_cart">
                    </button>
                </div>
                <p class="sub-total">sub total : <span>
                    <i class="fas fa-indian-rupee-sign"></i>
                    <?= $sub_total = (
                        $fetch_product["price"] * $fetch_cart["qty"]); ?>
                </span></p>
                <input type="submit" value="delete item" 
                    class="delete-btn" name="delete_item" 
                    onclick="return confirm('delete this item?');" />
            </form>
            <?php
                                $grand_total += $sub_total;
                            }
                        }
                        else echo "<p class=\"empty\">no products found!</p>";
                    }
                }
                else echo "<p class=\"empty\">shopping cart is empty!</p>";
            ?>
        </div>
        <?php if($grand_total != 0) { ?>
        <div class="cart-total">
            <p>Grand Total : <span>
                <i class="fas fa-indian-rupee-sign"></i><?= $grand_total; ?>
            </span></p>
            <a href="checkout.php" class="btn">proceed to checkout</a>
            <form action="" method="POST">
                <input type="submit" class="delete-btn" 
                    value="empty cart" name="empty_cart" 
                    onclick="return confirm('empty your cart?');" />
            </form>
        </div>
        <?php } ?>
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="./js/script.js"></script>
    <?php include "./components/alert.php"; ?>
</body>
</html>