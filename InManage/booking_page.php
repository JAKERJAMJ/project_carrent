<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';

if (isset($_GET['car_id']) && isset($_GET['start_date']) && isset($_GET['end_date']) && isset($_GET['member_id'])) {
    $carId = $_GET['car_id'];
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $memberID = $_GET['member_id'];

    // ค้นหาข้อมูลรถ
    $sql = "SELECT * FROM car WHERE car_id = '$carId'";
    $result = mysqli_query($con, $sql);
    $car = mysqli_fetch_assoc($result);

    // ค้นหาข้อมูลสมาชิก
    $sql = "SELECT * FROM member WHERE MemberID = '$memberID'";
    $result = mysqli_query($con, $sql);
    $member = mysqli_fetch_assoc($result);

    // คำนวณจำนวนวันที่เช่า
    $datetime1 = new DateTime($startDate);
    $datetime2 = new DateTime($endDate);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->days;

    // ตรวจสอบเงื่อนไขการนับวัน
    if ($days == 0) {
        $days = 1; // นับวันที่เช่าและคืนในวันเดียวกันเป็น 1 วัน
    } elseif ($days == 1) {
        $days = 1; // นับวันที่เช่าและคืนในวันถัดไปเป็น 1 วัน
    } else {
        $days += 1; // เพิ่มวันสุดท้ายที่ไม่ได้ถูกนับ
    }

    // คำนวณราคาเช่าทั้งหมด
    $totalRentalPrice = $days * $car['car_price'];
}
 // ดึงค่า enum ของ carrent_time
 $enumSql = "SHOW COLUMNS FROM carrent LIKE 'carrent_time'";
 $enumResult = mysqli_query($con, $enumSql);
 $enumRow = mysqli_fetch_assoc($enumResult);
 $enumList = str_replace("'", "", substr($enumRow['Type'], 5, (strlen($enumRow['Type'])-6)));
 $carRentTimes = explode(",", $enumList);

  // ดึงค่า enum ของ return_time
  $enumSql = "SHOW COLUMNS FROM carrent LIKE 'return_time'";
  $enumResult = mysqli_query($con, $enumSql);
  $enumRow = mysqli_fetch_assoc($enumResult);
  $enumList = str_replace("'", "", substr($enumRow['Type'], 5, (strlen($enumRow['Type'])-6)));
  $returnTimes = explode(",", $enumList);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช่ารถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/booking.css">
    <link rel="stylesheet" href="../styles/style.css">
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
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <a href="check_carrent.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="head-booking">
        <div class="car-name"><?= htmlspecialchars($car['car_name']) ?></div>
        <img src="<?= htmlspecialchars($car['car_picture1']) ?>" alt="รูปภาพรถ">
    </div>
    <div class="booking-body">
        <form action="" method="post">
            <div class="box">
                <label for="car_name">ชื่อรถ</label>
                <input class="form-control" type="text" name="car_name" id="car_name" value="<?= htmlspecialchars($car['car_name']) ?>" readonly>
                <input class="form-control" type="hidden" name="car_id" id="car_id" value="<?= htmlspecialchars($car['car_id']) ?>">
            </div>
            <div class="box">
                <label for='Membername'>ชื่อผู้เช่า</label><br>
                <input class="form-control" type='text' id='Membername' name='Membername' value='<?= htmlspecialchars($member['Membername'] . " " . $member['Memberlastname']) ?>' readonly><br>
                <input type='hidden' id='MemberID' name='MemberID' value='<?= htmlspecialchars($member['MemberID']) ?>'>
            </div>
            <div class="box">
                <label for="RentalDate">วันที่ต้องการเช่า:</label><br>
                <input class="form-control" type="date" id="RentalDate" name="RentalDate" value="<?= htmlspecialchars($startDate) ?>" readonly>
            </div>
            <div class="box">
                <label for="RentalTime">เวลาในการรับรถ (ตามไฟท์บินของท่าน):</label><br>
                <select class="form-select" id="RentalTime" name="RentalTime">
                <option selected>กรุณาเลือกเวลาในการรับรถ (ตามไฟท์บินของท่าน)</option>
                    <?php
                    foreach ($carRentTimes as $time) {
                        echo "<option value=\"" . htmlspecialchars($time) . "\">" . htmlspecialchars($time) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="box">
                <label for="ReturnDate">วันที่ส่งคืน:</label><br>
                <input class="form-control" type="date" id="ReturnDate" name="ReturnDate" value="<?= htmlspecialchars($endDate) ?>" readonly>
            </div>
            <div class="box">
                <label for="ReturnTime">เวลาในการส่งคืน (ตามไฟท์บินของท่าน):</label><br>
                <select class="form-select" id="ReturnTime" name="ReturnTime">
                <option selected>กรุณาเลือกเวลาในการส่งคืน (ตามไฟท์บินของท่าน)</option>
                    <?php
                    foreach ($returnTimes as $time) {
                        echo "<option value=\"" . htmlspecialchars($time) . "\">" . htmlspecialchars($time) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="box">
                <label for="RentalPrice">ราคาเช่าทั้งหมด:</label><br>
                <input class="form-control" type='text' id='RentalPrice' name='RentalPrice' value='<?= htmlspecialchars($totalRentalPrice) ?>' readonly>
            </div>
            <div class="box">
                <button class="btn btn-primary" type="submit" name="AddRent" id="AddRent">ยืนยันการเช่า</button>
            </div>
        </form>
    </div>

    <?php
    if (isset($_POST['AddRent'])) {
        $memberID = $_POST['MemberID'];
        $carID = $_POST['car_id'];
        $rentalDate = $_POST['RentalDate'];
        $rentalTime = $_POST['RentalTime'];
        $returnDate = $_POST['ReturnDate'];
        $returnTime = $_POST['ReturnTime'];
        $rentalPrice = $_POST['RentalPrice'];

        $sql = "INSERT INTO carrent (car_id, MemberID, type_rent, type_carrent, driver_status, driver_id, carrent_date, carrent_time, carrent_return, return_time, carrent_price, carrent_status_id) 
            VALUES ('$carID', '$memberID', 'เช่ารถหน้าร้าน', 'เช่ารถส่วนตัว', 'ไม่ต้องการคนขับ', '5', '$rentalDate', '$rentalTime', '$returnDate', '$returnTime', '$rentalPrice', '1')";
        if (mysqli_query($con, $sql)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
                </script>";
        } else {
            echo "<script>
                alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($con) . "');
              </script>";
        }
    }
    ?>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">การเช่าสำเร็จ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    เพิ่มข้อมูลเรียบร้อยแล้ว
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmButton">ตกลง</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../script/booking.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var confirmButton = document.getElementById('confirmButton');
            confirmButton.addEventListener('click', function() {
                window.location.href = 'manage_carrent.php';
            });

            var successModal = document.getElementById('successModal');
            successModal.addEventListener('hidden.bs.modal', function () {
                window.location.href = 'manage_carrent.php';
            });
        });
    </script>
</body>

</html>
