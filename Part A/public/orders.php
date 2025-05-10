<?php 
    include "./components/connect.php";
    if(isset($_COOKIE["user_id"])) $user_id = $_COOKIE["user_id"];
    else setcookie("user_id", create_unique_id(), time() + 60 * 60 * 24 * 30);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orders</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>
    <?php include "./components/header.php"; ?>
    <section class="orders">
        <h1 class="heading">my Orders</h1>
        <div class="box-container">
            <?php
                $select_orders = $connect->prepare(
                    "SELECT * FROM `orders` WHERE user_id = ? ORDER BY date DESC");
                $select_orders->execute([$user_id]);
                // 添加调试输出
                error_log("SQL查询: ".$select_orders->queryString);
                error_log("参数: ".$user_id);
                if($select_orders -> rowCount() > 0) {
                    while($fetch_order = $select_orders 
                        -> fetch(PDO::FETCH_ASSOC)) {
                        $select_products = $connect -> prepare(
                            "SELECT * FROM `products` WHERE id = ?");
                        $select_products -> execute([$fetch_order["product_id"]]);
                        if($select_products -> rowCount() > 0) {
                            while($fetch_product = $select_products
                                -> fetch(PDO::FETCH_ASSOC)) {
            ?>
            <a href="./view_order.php?get_id=<?= $fetch_order["id"]; ?>" 
                class="box" <?php if($fetch_order["status"] == "cancelled")
                    echo "style=\"border-color: var(--red);\"" ?>>
                <p class="date">
                    <i class="fas fa-calendar"></i> 
                    <?= $fetch_order["date"]; ?>
                </p>
                <img src="./uploaded_files/<?= $fetch_product["image"]; ?>" 
                    alt="product" class="image" />
                <h3 class="name"><?= $fetch_product["name"]; ?></h3>
                <p class="price">
                    <i class="fas fa-indian-rupee-sign"></i> 
                    <?= $fetch_order["price"]; ?> x 
                    <?= $fetch_order["qty"]; ?>
                </p>
                <p class="status" style="color:<?php
                        $status = isset($fetch_order["status"]) ? $fetch_order["status"] : 'processing';
                        if($status == "cancelled") echo "red";
                        elseif($status == "delivered") echo "green";
                        else echo "orange";?>"
                    ><?= $status; ?></p>
            </a>
            <?php
                            }
                        }
                    }
                }
                else echo "<p class=\"empty\">orders not found!</p>";
            ?>
        </div>
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="./js/script.js"></script>
    <?php include "./components/alert.php"; ?>
</body>
</html>