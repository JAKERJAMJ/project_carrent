<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เกี่ยวกับเรา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/about_us.css">
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
    <div class="about-container">
        <img src="./img/about.png" alt="เกี่ยวกับ" class="about-img">
        <div class="image-text">เกี่ยวกับเรา</div>
    </div>
    <div class="about-body">
        <div class="img-owner">
            <img src="./img/owner.png" alt="เจ้าของ" class="owner-img">
        </div>
        <div class="contact-container">
            <div class="about-head">
                <div class="about-title">
                    บริษัท เมืองเลยรถเช่า จำกัด
                </div>
            </div>
            <div class="contact-in">
                <div class="contact">
                    <div class="box">
                        <a href="https://www.facebook.com/groups/7154894054597017/user/100063546818574/">
                            <img src="./img/facebook.png" alt="Facebook Logo" class="facebook-img">
                        </a>
                        <p class="text">เมืองเลยรถเช่าคุณกร</p>
                    </div>
                    <div class="box">
                        <img src="./img/line.png" alt="line Logo" class="line-img">
                        <p class="text">
                            089-4201166 (คุณปุ๊)<br>
                            089-7107768 (คุณกร)
                        </p>
                    </div>
                    <div class="box">
                        <img src="./img/phone.png" alt="phone Logo" class="phone-img">
                        <p class="text">
                            089-4201166 (คุณปุ๊)<br>
                            089-7107768 (คุณกร)
                        </p>
                    </div>
                </div>
                <div class="car-contact">
                    <img src="./img/about-car.png" alt="รถ" class="about-car-img">
                </div>
            </div>
        </div>
    </div>
    <div class="about-footer-container">
        <div class="marquee">
            <span>ขอบพระคุณทุกท่านที่มาใช้บริการเมืองเลยรถเช่า</span>
        </div>
    </div>
</body>

</html>