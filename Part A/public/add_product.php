<?php 
    include "./components/connect.php";
    if(isset($_COOKIE["user_id"])) $user_id = $_COOKIE["user_id"];
    else setcookie("user_id", create_unique_id(), time() + 60 * 60 * 24 * 30);

    if(isset($_POST["add_product"])) {
        $id = create_unique_id();
        $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
        $price = filter_var($_POST["price"], FILTER_SANITIZE_STRING);
        $image = filter_var($_FILES["image"]["name"], FILTER_SANITIZE_STRING);
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $rename = $id . "." . $ext;
        $image_tmp_name = $_FILES["image"]["tmp_name"];
        $image_folder = "uploaded_files/" . $rename;

        if($_FILES["image"]["size"] > 2000000) {
            $warning_msg[] = "Image size is too large!";
        }
        else {
            $insert_product = $connect -> prepare(
                "INSERT INTO `products`(id, name, price, image) VALUES(?, ?, ?, ?)"
            ) -> execute([$id, $name, $price, $rename]);
            $success_msg[] = "Product uploaded!";
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Product</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>
    <?php include "./components/header.php"; ?>
    <section class="product-form">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>product details</h3>
            <p>product name <span>*</span></p>
            <input type="text" name="name" required maxlength="50" 
                placeholder="enter product name" class="box" />
            <p>product price <span>*</span></p>
            <input type="number" name="price" required 
                maxlength="10" max="9999999999" min="0" 
                placeholder="enter product price" class="box" />
            <p>product image <span>*</span></p>
            <input type="file" name="image" required 
                accept="image/*" class="box" />
            <input type="submit" value="add product" 
                name="add_product" class="btn" />
        </form>
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script src="./js/script.js"></script>
    <?php include "./components/alert.php"; ?>
</body>
</html>