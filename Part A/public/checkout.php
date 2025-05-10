<?php 
    include "./components/connect.php";
    if(isset($_COOKIE["user_id"])) $user_id = $_COOKIE["user_id"];
    else setcookie("user_id", create_unique_id(), time() + 60 * 60 * 24 * 30);

    if(isset($_GET["get_id"])) $get_id = $_GET["get_id"];
    else $get_id = "";

    if(isset($_POST["place_order"])) {
        if(!isset($user_id)) {
            setcookie("user_id", create_unique_id(), time() + 60 * 60 * 24 * 30);
            $user_id = $_COOKIE["user_id"];
        }
        $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $number = filter_var($_POST["number"], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST["email"], FILTER_SANITIZE_STRING);
        $method = filter_var($_POST["method"], FILTER_SANITIZE_STRING);
        $address_type = filter_var($_POST["address_type"], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST["flat"]. ", ". $_POST["street"]. ", ". 
            $_POST["city"]. ", ". $_POST["country"]. ", ". 
            $_POST["pin_code"], FILTER_SANITIZE_STRING);

        $verify_cart = $connect -> prepare(
            "SELECT * FROM `cart` WHERE user_id = ?");
        $verify_cart -> execute([$user_id]);
        if(isset($_GET["get_id"])) {
            $get_product = $connect -> prepare(
                "SELECT * FROM `products` WHERE id = ? LIMIT 1");
            $get_product -> execute([$_GET["get_id"]]);
            if($get_product -> rowCount() > 0) {
                while($fetch_p = $get_product -> fetch(PDO::FETCH_ASSOC)) {
                    $insect_order = $connect ->prepare(
                        "INSERT INTO `orders` (id, user_id, name, number, email, 
                        address, address_type, method, product_id, price, qty) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insect_order -> execute([create_unique_id(), $user_id, $name, 
                        $number, $email, $address, $address_type, $method, 
                        $fetch_p["id"], $fetch_p["price"], 1]);
                    header("location: ./orders.php");
                }
            }
            else $warning_msg[] = "Something went wrong!";
        }
        elseif($verify_cart -> rowCount() > 0) {
            while($f_cart = $verify_cart -> fetch(PDO::FETCH_ASSOC)) {
                $get_price = $connect -> prepare(
                    "SELECT * FROM `products` WHERE id = ?");
                $get_price -> execute([$f_cart["product_id"]]);
                if($get_price -> rowCount() > 0) {
                    while($f_price = $get_price -> fetch(PDO::FETCH_ASSOC)) {
                        $insect_order = $connect ->prepare(
                            "INSERT INTO `orders` (id, user_id, name, number, email, 
                            address, address_type, method, product_id, price, qty) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $insect_order -> execute([create_unique_id(), 
                            $user_id, $name, $number, $email, $address, 
                            $address_type, $method, $f_cart["product_id"], 
                            $f_price["price"], $f_cart["qty"]]);
                        header("location: ./orders.php");
                    }
                    if($insect_order) {
                        $empty_cart = $connect -> prepare(
                            "DELETE FROM `cart` WHERE user_id =?");
                        $empty_cart -> execute([$user_id]);
                        header("location: ./orders.php");
                        exit;
                    }
                }
                else $warning_msg[] = "Something went wrong!";
            }
        }
        else $warning_msg[] = "Your cart is empty!";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Checkouts</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>
    <?php include "./components/header.php"; ?>
    <section class="checkout">
        <h1 class="heading">checkout summary</h1>
        <div class="row">
            <form action="" method="POST">
                <h3>billing details</h3>
                <div class="flex">
                    <div class="box">
                        <p>your name <span>*</span></p>
                        <input type="text" name="name" required class="input"
                            maxlength="50" placeholder="enter your name" />
                        <p>your email <span>*</span></p>
                        <input type="email" name="email" required class="input"
                            maxlength="50" placeholder="enter your email" />
                        <p>your number <span>*</span></p>
                        <input type="number" name="number" required class="input"
                            maxlength="10" max="9999999999" min="0" 
                            placeholder="enter your name" />
                        <p>payment method <span>*</span></p>
                        <select name="method" id="method" 
                            require class="input">
                            <option value="cason delivery">
                                cash on delivery</option>
                            <option value="net banking">
                                net banking</option>
                            <option value="cason delivery">
                                credit or debit card</option>
                            <option value="UPI of RuPay">
                                UPI of RuPay</option>
                        </select>
                        <p>address type <span>*</span></p>
                        <select name="address_type" id="address_type" 
                            require class="input">
                            <option value="home">home</option>
                            <option value="office">office</option>
                        </select>
                    </div>
                    <div class="box">
                        <p>address line 01 <span>*</span></p>
                        <input type="text" name="flat" 
                            required maxlength="50" class="input"
                            placeholder="e.g. flat no & building no." />
                        <p>address line 02 <span>*</span></p>
                        <input type="text" name="street" 
                            required maxlength="50" class="input"
                            placeholder="e.g. street name" />
                        <p>city name <span>*</span></p>
                        <input type="text" name="city" 
                            required maxlength="50" class="input"
                            placeholder="enter your city name" />
                        <p>country name <span>*</span></p>
                        <input type="text" name="country" 
                            required maxlength="50" class="input"
                            placeholder="enter your country name" />
                        <p>pin code <span>*</span></p>
                        <input type="number" name="pin_code" class="input"
                            maxlength="6" max="999999" min="0" required 
                            placeholder="enter your pin code" />
                    </div>
                </div>
                <input type="submit" class="btn"
                    value="place order" name="place_order" />
            </form>
            <div class="summary">
                <p class="title">total items :</p>
                <?php
                    $grand_total = 0;
                    if($get_id != "") {
                        $select_product = $connect -> prepare(
                            "SELECT * FROM `products` WHERE id = ?");
                        $select_product -> execute([$get_id]);
                        if($select_product -> rowCount() > 0) {
                            while($fetch_product = $select_product 
                                -> fetch(PDO::FETCH_ASSOC)) {
                                $grand_total = $fetch_product["price"];
                ?>
                <div class="flex">
                    <img src="./uploaded_files/<?= $fetch_product["image"]; ?>" 
                        alt="" />
                    <div>
                        <h3 class="name"><?= $fetch_product["name"]; ?></h3>
                        <p class="price">
                            <i class="fas fa-indian-rupee-sign"></i> 
                            <?= $fetch_product["price"]; ?> x 1
                        </p>
                    </div>
                </div>
                <?php
                            }
                        }
                        else echo "<p class=\"empty\">product was found!</p>";
                    }
                    else {
                        $select_cart = $connect -> prepare(
                            "SELECT * FROM `cart` WHERE user_id = ?");
                        $select_cart -> execute([$user_id]);
                        if($select_cart -> rowCount() > 0) {
                            while($fetch_cart = $select_cart 
                                -> fetch(PDO::FETCH_ASSOC)) {
                                $select_p = $connect -> prepare(
                                    "SELECT * FROM `products` WHERE id = ?");
                                $select_p -> execute([$fetch_cart["product_id"]]);
                                if($select_p -> rowCount() > 0) {
                                    while($f_product = $select_p 
                                        -> fetch(PDO::FETCH_ASSOC)) {
                                        $sub_total = ($f_product["price"] 
                                            * $fetch_cart["qty"]);
                                        $grand_total += $sub_total;
                ?>
                <div class="flex">
                    <img src="./uploaded_files/<?= $f_product["image"]; ?>" 
                        alt="" />
                    <div>
                        <h3 class="name"><?= $f_product["name"]; ?></h3>
                        <p class="price">
                            <i class="fas fa-indian-rupee-sign"></i> 
                            <?= $f_product["price"]; ?> x <?= $fetch_cart["qty"]; ?>
                        </p>
                    </div>
                </div>
                <?php
                                    }
                                }
                                else echo 
                                    "<p class=\"empty\">product was not found!</p>";
                            }
                        }
                        else echo "<p class=\"empty\">your cart is empty!</p>";
                    }
                ?>
                <p class="grand-total">
                    grand total : 
                    <span>
                        <i class="fas fa-indian-rupee-sign"></i>
                        <?= $grand_total; ?>
                    </span>
                </p>
            </div>
        </div>
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="./js/script.js"></script>
    <?php include "./components/alert.php"; ?>
</body>
</html>