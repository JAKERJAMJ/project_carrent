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
    <title>จัดการรถ</title>
    <link rel="stylesheet" href="../styles/car_manage.css">
    <link rel="stylesheet" href="../styles/style.css">
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
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="top-button">
        <a href="data_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        <div class="manage-car">การจัดการรถ</div>
        <button onclick="showPopup()" id="upcar" type="button" class="btn btn-success">เพิ่ม</button>
    </div>
    <div class="container">
        <div class="row view-car">
            <?php
            $sql = "SELECT * FROM car ORDER BY car_id";
            $result = mysqli_query($con, $sql);


            while ($row = mysqli_fetch_array($result)) {
                $car_status = $row['car_status'];
                if ($car_status === 'ใช้งาน') {
                    $status_color = 'green'; // หากสถานะเป็น 'ใช้งาน' ให้เป็นสีเขียว
                } elseif ($car_status === 'ยกเลิกการใช้งาน') {
                    $status_color = 'red'; // หากสถานะเป็น 'ยกเลิกการใช้งาน' ให้เป็นสีแดง
                }
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $row['main_picture'] ?>" class="card-img-top" alt="Car Image" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['car_name'] ?></h5>
                            <p class="card-text">
                                ยี่ห้อรถ : <?= $row['car_brand'] ?><br>
                                ราคา : <?= $row['car_price'] ?> บาท<br>
                                สถานะ : <span style="color: <?= $status_color; ?>;"><?= $car_status ?></span>
                            </p>
                            <a href="car_detail.php?id=<?= $row['car_id'] ?>" class="btn btn-outline-success">รายละเอียด</a>
                            <button type="button" class="btn btn-outline-danger" onclick="updateCarStatus(<?= $row['car_id'] ?>)">ยกเลิกการใช้งาน</button>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <!-- popup container-box => add_car -->
    <div class="container-box" id="add_car">
        <form action="car_management.php" method="post" enctype="multipart/form-data">
            <div class="title-box"><u>เพิ่มข้อมูลรถ</u></div>
            <div class="box">
                <label for="car_name">ชื่อรถ</label><br>
                <input type="text" name="car_name" placeholder="-- ชื่อรถ --">
            </div>
            <div class="box">
                <label for="car_brand">ยี่ห้อ</label><br>
                <input type="text" name="car_brand" placeholder="-- ยี่ห้อรถ --">
            </div>
            <div class="box">
                <label for="car_numplate">ป้ายทะเบียนรถ</label><br>
                <input type="text" name="car_numplate" placeholder="-- ป้ายทะเบียนรถ --">
            </div>
            <div class="box">
                <label for="car_vin">เลขตัวถัง</label><br>
                <input type="text" name="car_vin" placeholder="-- เลขตัวถัง --">
            </div>
            <div class="box">
                <label for="car_price">ราคาเช่า</label><br>
                <input type="text" name="car_price" placeholder="-- ราคาเช่า --">
            </div>
            <div class="box">
                <label for="car_detail">รายละเอียด</label><br>
                <textarea name="car_detail" class="detailbox" placeholder="-- กรุณากรอกรายละเอียด --"></textarea>
            </div>
            <div class="box">
                <label for="main_picture">รูปภาพ</label><br>
                <input type="file" name="main_picture" accept="img/" id="main_picture" onchange="previewImage()">
                <div class="preview-img">
                    <img src="" id="image-preview" class="image-preview" alt="รูปภาพตัวอย่าง" style="display:none;">
                </div>
            </div>
            <input type="submit" name="submit" value="Submit" id="submit">
            <button type="button" onclick="hidePopup()" id="close-car">Close</button>
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $car_name = $_POST['car_name'];
        $car_brand = $_POST['car_brand'];
        $car_numplate = $_POST['car_numplate'];
        $car_vin = $_POST['car_vin'];
        $car_price = $_POST['car_price'];
        $car_detail = $_POST['car_detail'];

        $target_dir = "../img/car/";

        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "Car_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }

        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $main_picture = $target_dir . createNewFileName($_FILES["main_picture"]["name"]);
        move_uploaded_file($_FILES["main_picture"]["tmp_name"], $main_picture);

        // SQL Query
        $sql = "INSERT INTO car (car_name, car_brand, 
                car_numplate, car_vin, car_price, car_detail, 
                main_picture, car_pic1, car_pic2, car_pic3, car_status) 
                VALUES ('$car_name', '$car_brand', '$car_numplate', 
                '$car_vin', '$car_price', '$car_detail', '$main_picture', '', '', '', 'ใช้งาน')";

        // Execute SQL Query
        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }

    ?>
    <script src="../script/car_manage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>