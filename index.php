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
    <div id="carouselExampleIndicators" class="carousel slide custom-carousel" data-bs-ride="carousel" data-bs-interval="4000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./img/slide1.png" class="d-block custom-slide" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="./img/slide2.png" class="d-block custom-slide" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="./img/slide3.png" class="d-block custom-slide" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev custom-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next custom-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    <div class="footer-index-container">
        <div class="marquee">
            <span>สวัสดีทุกท่านที่มาใช้บริการเมืองเลยรถเช่าและแพ็คเกจท่องเที่ยว</span>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>


</html>