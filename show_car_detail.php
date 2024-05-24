<?php
session_start();
require 'conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดรถเช่าส่วนตัว</title>
    <link rel="stylesheet" href="./styles/show_car_detail.css">
    <link rel="stylesheet" href="./styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <?php
    if (isset($_SESSION['MemberID'])) {
        require 'user_nav.php'; // Include user navigation if user is logged in
    } else {
        require 'nav.php'; // Include default navigation if user is not logged in
    }
    ?>
    <div class="head-body">
    <div class="button">
        <a href="show_car.php" class="button">ย้อนกลับ</a>
    </div>
    <div class="head-show-car-detail">
        <div class="title-show-car-detail">
            รายละเอียดรถยนต์
        </div>
    </div>
</div>
    <div class="detail">
    <?php
       $ids = $_GET['id'];
       $sql = "SELECT * FROM car WHERE car_id = '$ids'";
       $result = mysqli_query($con, $sql);

       if (!$result) {
           die("Error in SQL query: " . mysqli_error($con));
       }

       $row = mysqli_fetch_array($result);

       if ($row) {
           $new_url = str_replace("../img/", "./img/", $row['car_picture1']);
        ?>
        <div class="body-container">
            <div class="image-container">
            <img src="<?= htmlspecialchars($new_url) ?>" alt="รูปภาพรถ" class="img">
        </div>
        <div class="detail-info">
            <div class="info">
            <div class="car_name">ชื่อรถ : <?= htmlspecialchars($row['car_name']) ?></div>
            <div class="car_band">ยี่ห้อรถ : <?= htmlspecialchars($row['car_brand']) ?></div>
            <div class="car_price">ราคาเช่า : <?= htmlspecialchars($row['car_price']) ?></div>
            </div>
            <div class="car-rent">
            <div class="button">
        <a href="member_rent_car.php" class="button">เช่ารถ</a>
        </div>
        </div>
        </div>
        <?php 
        }
        ?>
        </div>
      
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>