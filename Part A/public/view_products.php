<?php 
    include "./components/connect.php";
    if(isset($_COOKIE["user_id"])) $user_id = $_COOKIE["user_id"];
    else setcookie("user_id", create_unique_id(), time() + 60 * 60 * 24 * 30);

    if(isset($_POST["add_to_cart"])) {
        $id = create_unique_id();
        $product_id = filter_var($_POST["product_id"], FILTER_SANITIZE_STRING);
        $qty = filter_var($_POST["qty"], FILTER_SANITIZE_STRING);

        $verify_cart = $connect -> prepare(
            "SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
        $verify_cart -> execute([$user_id, $product_id]);
        $max_cart_items = $connect -> prepare(
            "SELECT * FROM `cart` WHERE user_id = ?");
        $max_cart_items -> execute([$user_id]);
        if($verify_cart -> rowCount() > 0) {
            $warning_msg[] = "Already added to cart!";
        }
        elseif($max_cart_items -> rowCount() == 10) {
            $warning_msg[] = "Cart is full!";
        }
        else {
            $select_p = $connect -> prepare(
                "SELECT * FROM `products` WHERE id = ? LIMIT 1");
            $select_p -> execute([$product_id]);
            $fetch_p = $select_p -> fetch(PDO::FETCH_ASSOC);
            $insert_cart = $connect -> prepare(
                "INSERT INTO `cart`(id, user_id, product_id, price, qty) VALUES(?, ?, ?, ?, ?)");
            $insert_cart -> execute(
                [$id, $user_id, $product_id, $fetch_p["price"], $qty]);
            $success_msg[] = "Added to Cart!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Products</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>
    <?php include "./components/header.php"; ?>
    <section class="products">
        <h1 class="heading">all products</h1>
        <div class="box-container">
            <?php
                $select_products = $connect -> prepare("SELECT * FROM `products`");
                $select_products -> execute();
                if($select_products -> rowCount() > 0){
                    while($fetch_product = $select_products 
                        -> fetch((PDO::FETCH_ASSOC))) {
            ?>
            <form action="" method="POST" class="box">
                <input type="hidden" name="product_id" 
                    value="<?= $fetch_product["id"] ?>" />
                <img src="uploaded_files/<?= $fetch_product["image"]; ?>" 
                    alt="" class="image" />
                <h3 class="name"><?= $fetch_product["name"]; ?></h3>
                <div class="flex">
                    <p class="price">
                        <i class="fas fa-indian-rupee-sign"></i>
                        <?= $fetch_product["price"]; ?>
                    </p>
                    <input type="number" name="qty" maxlength="2" 
                        min="1" max="99" value="1" required class="qty" />
                </div>
                <a href="./checkout.php?get_id=<?= $fetch_product["id"]; ?>" 
                    class="delete-btn">buy now</a>
                <input type="submit" value="add to cart" 
                    name="add_to_cart" class="btn" />
            </form>
            <?php
                    }
                }
                else echo "<p class=\"empty\">no products found!</p>";
            ?>
        </div>
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="./js/script.js"></script>
    <?php include "./components/alert.php"; ?>
</body>
</html>