<?php
session_start();
require 'conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมืองเลยรถเช่า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/index.css">
    <link rel="stylesheet" href="./styles/style.css">
</head>

<body>
<?php
if (isset($_SESSION['MemberID'])) {
    require 'user_nav.php'; // Include user navigation if user is logged in
} else {
    require 'nav.php'; // Include default navigation if user is not logged in
}
?>
    <div class="banner-container">
        <img src="./img/banner.png" alt="เมืองเลยรถเช่า" class="banner">
    </div>
    <div class="title-container">
        <div class="title">
            <p>
                <span class="welcome">ยินดีต้อนรับเข้าสู่</span> เว็บไซต์หลักของบริษัทเมืองเลยรถเช่า ทางเราให้บริการรถเช่า ภายในจังหวัดเลย
                ราคามิตรภาพสมเหตุสมผล ด้วยรถยนต์สภาพดีมีประกันทุกคัน มีบริการ walk-in ในการเช่ารถที่เคาน์เตอร์สนามบินเลย
                บริการรถเช่าจังหวัดเลย ต้องเมืองเลยรถเช่า 
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>