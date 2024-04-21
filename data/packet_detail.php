<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    // แสดง alert และ redirect ไปยังหน้า login.php
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit; // จบการทำงานของสคริปต์
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดแพ็คเกจ</title>
    <link rel="stylesheet" href="../styles/packet_detail.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <header>
        <nav class="navbar bg-body-tertiary">
            <div class="container-fluid d-flex justify-content-between">
                <div>
                    <a class="navbar-brand" href="../admin.php">Admin Controller</a>
                </div>
                <div class="dropdown">
                    <button class="btn btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"> <!-- เพิ่ม class dropdown-menu-end เพื่อจัดให้ dropdown อยู่ด้านขวาของ Navbar -->
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="head-detail">
        <div class="back">
            <a href="packet_management.php"><button type="button" class="btn btn-outline-dark">กลับ</button></a>
        </div>
        <div class="info-head">
            รายละเอียดแพ็คเกจ
        </div>
        <div class="spacer"></div>
    </div>
    <div class="btn-edit">
        <button type="button" class="btn-add-tourist">เพิ่มสถานที่ท่องเที่ยว</button>
        <button type="button" class="btn-edit-packet">แก้ไข</button>
    </div>

    <div class="detail">
        <?php
        $ids = $_GET['id'];
        $sql = "SELECT * FROM packet WHERE packet_id = '$ids' ";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($result);
        ?>
        <div class="main-picture-pack">
            <img src="<?= $row['packet_main_picture'] ?>" alt="">
            <button type="button" class="editpicture">แก้ไขรูปภาพ</button>
        </div>
        <div class="detail-info">
            <div class="up-info">
                <div class="packet-name">ชื่อแพ็คเกจ : <?= $row['packet_name'] ?></div>
                <div class="id-packet">ID : <?= $row['packet_id'] ?></div>
                <div class="main-tourist">สถานที่ท่องเที่ยวหลัก : <?= $row['packet_main_tourist'] ?></div>
            </div>
            <div class="down-info">
                <div class="start-tourist"><?= date('d/m/', strtotime($row['start_tourist'])) . (date('Y', strtotime($row['start_tourist'])) + 543) ?></div>
                <div class="end-tourist"><?= date('d/m/', strtotime($row['end_tourist'])) . (date('Y', strtotime($row['end_tourist'])) + 543) ?></div>
                <div class="packet-price">ราคา <?= $row['packet_price'] ?> บาท</div>
            </div>
        </div>
    </div>
    <div class="add-tourist" id="add-tourist">
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="title-add-tourist">
                เพิ่มสถานที่ท่องเที่ยว
            </div>
            <div class="tourist-1">
                <div class="box">
                    <label for="place1" class="place-label">สถานที่ท่องเที่ยว 1</label><br>
                    <input type="text" name="tourist1" id="tourist1" class="place-input"><br>
                    <label for="image">รูปภาพสถานที่ท่องเที่ยว </label><br>
                    <input type="file" name="image1" id="image1"><br>
                    <textarea name="รายละเอียด" id=""></textarea>
                </div>
            </div>
            <div class="tourist-2">
                <div class="box">
                    <label for="place1" class="place-label">สถานที่ท่องเที่ยว 2</label><br>
                    <input type="text" name="tourist1" id="tourist1" class="place-input"><br>
                    <label for="image">รูปภาพสถานที่ท่องเที่ยว</label><br>
                    <input type="file" name="image1" id="image1"><br>
                    <textarea name="รายละเอียด" id=""></textarea>
                </div>
            </div>
            <div class="tourist-3">
                <div class="box">
                    <label for="place1" class="place-label">สถานที่ท่องเที่ยว 3</label><br>
                    <input type="text" name="tourist1" id="tourist1" class="place-input"><br>
                    <label for="image">รูปภาพสถานที่ท่องเที่ยว</label><br>
                    <input type="file" name="image1" id="image1"><br>
                    <textarea name="รายละเอียด" id=""></textarea>ฃ
                </div>
            </div>

            <!-- ส่วนอื่น ๆ ของฟอร์ม -->
            <input type="submit" value="Submit">
        </form>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>