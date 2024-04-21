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
    <title>การจัดการคนขับ</title>
    <link rel="stylesheet" href="../styles/driver_manage.css">
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
    <div class="top-button">
        <a href="data_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        <div class="manage-driver">การจัดการคนขับ</div>
        <button onclick="AddDriver()" id="adddriver" type="button" class="btn btn-success">เพิ่ม</button>
    </div>

    <div class="container">
        <div class="row view-driver">
            <?php
            require '../conDB.php';
            $sql = "SELECT * FROM driver ORDER BY driver_id";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $row['driver_picture'] ?>" class="card-img-top" alt="DriverImage" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['driver_name'] ?></h5>
                            <p class="card-text">
                                ID : <?= $row['driver_id'] ?><br>
                                เบอร์โทรศัพท์ : <?= $row['driver_phone'] ?><br>
                            </p>
                            <button type="button" class="btn btn-outline-success">รายละเอียด</button>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteCar(<?= $row['car_id'] ?>)">ยกเลิกการใช้งาน</button>
                        </div>

                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="add-driver" id="AddDriver">
        <form action="driver_manage.php" method="post" enctype="multipart/form-data">
            <div class="title-add-driver">
                <u>เพิ่มข้อมูลคนขับรถ</u>
            </div>
            <div class="box">
                <label for="driver_name">ชื่อคนขับรถ</label><br>
                <input type="text" name="driver_name" placeholder="--- ชื่อคนขับรถ ---">
            </div>
            <div class="box">
                <label for="driver_phone">เบอร์โทรศัพท์</label><br>
                <input type="text" name="driver_phone" placeholder="--- เบอร์โทรศัพท์ ---" oninput="formatPhoneNumber(this)" maxlength="10">
            </div>
            <div class="box">
                <label for="driver_detail">ประวัติย่อ</label><br>
                <textarea name="driver_detail" class="detailbox" placeholder="--- ประวัติโดยย่อ ---"></textarea>
            </div>
            <div class="box">
                <label for="driver_picture">รูปภาพ</label><br>
                <input type="file" name="driver_picture" accept="img/" id="driver_picture" onchange="previewImage()">
                <div class="preview-img">
                    <img src="" id="image-preview" class="image-preview" alt="รูปภาพตัวอย่าง" style="display:none;">
                </div>
            </div>
            <input type="submit" name="submit" value="Submit" id="submit" class="btn btn-outline-success">
            <button onclick="hideAddDriver()" id="close-car" class="btn btn-outline-danger">Close</button>
        </form>
    </div>
    <?php
    require '../conDB.php';
    if (isset($_POST['submit'])) {
        $driver_name = $_POST['driver_name'];
        $driver_phone = $_POST['driver_phone'];
        $driver_detail = $_POST['driver_detail'];

        // ตรวจสอบว่ามีไฟล์รูปภาพถูกอัพโหลดมาหรือไม่
        if ($_FILES['driver_picture']['name'] != "") {
            $target_dir = "../img/driver/";
            $target_file = $target_dir . basename($_FILES['driver_picture']['name']);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $newFileName = "driver_" . uniqid() . "." . $imageFileType;
            $target_path = $target_dir . $newFileName;

            // เช็คว่าไฟล์ที่อัปโหลดเป็นไฟล์รูปภาพหรือไม่
            $check = getimagesize($_FILES["driver_picture"]["tmp_name"]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["driver_picture"]["tmp_name"], $target_path)) {
                    // เพิ่มข้อมูลลงในฐานข้อมูล
                    $sql = "INSERT INTO driver (driver_name, driver_phone, driver_detail, driver_picture)
                            VALUES ('$driver_name', '$driver_phone', '$driver_detail', '$target_path')";
                    if ($con->query($sql) === TRUE) {
                        echo '<script>alert("เพิ่มข้อมูลเรียบร้อยแล้ว"); window.location.href = window.location.href;</script>';
                    } else {
                        echo "Error: " . $sql . "<br>" . $con->error;
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            } else {
                echo "File is not an image.";
            }
        } else {
            echo "Please select an image file.";
        }
    }

    $con->close();
    ?>

    <script src="../script/driver_manage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>