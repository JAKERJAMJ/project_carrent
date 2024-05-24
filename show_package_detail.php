<?php
session_start();
require 'conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดแพ็คเกจท่องเที่ยว</title>
    <link rel="stylesheet" href="./styles/show_package_detail.css">
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
        <a href="show_packet.php" class="button">ย้อนกลับ</a>
        </div>
        <div class="head-show-package-detail">
        <div class="title-show-package-detail">
            รายละเอียดแพ็คเกจท่องเที่ยว
        </div>
    </div>
    </div>
    
    <div class="detail">
        <?php
        $ids = $_GET['id'];
        $sql = "SELECT * FROM packet WHERE packet_id = '$ids'";
        $result = mysqli_query($con, $sql);

        if (!$result) {
            die("Error in SQL query: " . mysqli_error($con));
        }

        $row = mysqli_fetch_array($result);

        if ($row) {
            $new_url = str_replace("../img/", "./img/", $row['packet_main_picture']);
        ?>
        <div class="body-container">
        <div class="image-container">
            <img src="<?= htmlspecialchars($new_url) ?>" alt="รูปภาพแพ็คเกจ" class="img">
        </div>
        <div class="detail-info">
            <div class="up-info">
                <div class="packet-name">ชื่อแพ็คเกจ : <?= htmlspecialchars($row['packet_name']) ?></div>
                <div class="main-tourist">สถานที่ท่องเที่ยวหลัก : <?= htmlspecialchars($row['packet_main_tourist']) ?></div>
            </div>
            <div class="down-info">
                <div class="start-tourist">วันที่เริ่ม:  <?= date('d/m/', strtotime($row['start_tourist'])) . (date('Y', strtotime($row['start_tourist'])) + 543) ?></div>
                <div class="end-tourist">วันที่สิ้นสุด: <?= date('d/m/', strtotime($row['end_tourist'])) . (date('Y', strtotime($row['end_tourist'])) + 543) ?></div>
                <div class="packet-price">ราคา <?= htmlspecialchars($row['packet_price']) ?> บาท</div>
            </div>
        </div>
        <?php
        } else {
            echo "<p>ไม่พบข้อมูลแพ็คเกจ</p>";
        }
        ?>
    </div>
    </div>
    <div class="package-rent">
        <div class="button">
        <a href="member_rent_packet.php" class="button">เช่าแพ็คเกจ</a>
        </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>