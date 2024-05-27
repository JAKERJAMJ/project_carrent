<?php
session_start();

if (!isset($_SESSION['MemberID'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='./login.php';</script>";
    exit;
}

require './conDB.php';

if (isset($_GET['car_id']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $carId = $_GET['car_id'];
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
    $memberID = $_SESSION['MemberID'];

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
    $driverDailyWage = 300; // ค่าจ้างรายวันของคนขับ
}

// ดึงค่า enum ของ carrent_time
$enumSql = "SHOW COLUMNS FROM carrent LIKE 'carrent_time'";
$enumResult = mysqli_query($con, $enumSql);
$enumRow = mysqli_fetch_assoc($enumResult);
$enumList = str_replace("'", "", substr($enumRow['Type'], 5, (strlen($enumRow['Type']) - 6)));
$carRentTimes = explode(",", $enumList);

// ดึงค่า enum ของ return_time
$enumSql = "SHOW COLUMNS FROM carrent LIKE 'return_time'";
$enumResult = mysqli_query($con, $enumSql);
$enumRow = mysqli_fetch_assoc($enumResult);
$enumList = str_replace("'", "", substr($enumRow['Type'], 5, (strlen($enumRow['Type']) - 6)));
$returnTimes = explode(",", $enumList);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช่ารถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/booking_out.css">
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
    <a href="check.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="booking-out-header">
        <div class="booking-out-car-name"><?= htmlspecialchars($car['car_name']) ?></div>
        <img src="<?= str_replace("../img/", "./img/", $car['car_picture1']) ?>" alt="รูปภาพรถ" class="booking-out-car-image">
    </div>
    <div class="booking-out-body">
        <form id="rentalForm" action="" method="post" onsubmit="return validateForm()">
            <div class="booking-out-box">
                <label for="car_name">ชื่อรถ</label>
                <input class="form-control" type="text" name="car_name" id="car_name" value="<?= htmlspecialchars($car['car_name']) ?>" readonly>
                <input class="form-control" type="hidden" name="car_id" id="car_id" value="<?= htmlspecialchars($car['car_id']) ?>">
            </div>
            <div class="booking-out-box">
                <label for='Membername'>ชื่อผู้เช่า</label><br>
                <input class="form-control" type='text' id='Membername' name='Membername' value='<?= htmlspecialchars($member['Membername'] . " " . $member['Memberlastname']) ?>' readonly><br>
                <input type='hidden' id='MemberID' name='MemberID' value='<?= htmlspecialchars($member['MemberID']) ?>'>
            </div>
            <div class="booking-out-box">
                <label for="RentalDate">วันที่ต้องการเช่า:</label><br>
                <input class="form-control" type="date" id="RentalDate" name="RentalDate" value="<?= htmlspecialchars($startDate) ?>" readonly>
            </div>
            <div class="booking-out-box">
                <label for="RentalTime">เวลาในการรับรถ (ตามไฟท์บินของท่าน):</label><br>
                <select class="form-select" id="RentalTime" name="RentalTime" required>
                    <option value="">กรุณาเลือกเวลาในการรับรถ (ตามไฟท์บินของท่าน)</option>
                    <?php
                    foreach ($carRentTimes as $time) {
                        echo "<option value=\"" . htmlspecialchars($time) . "\">" . htmlspecialchars($time) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="booking-out-box">
                <label for="ReturnDate">วันที่ส่งคืน:</label><br>
                <input class="form-control" type="date" id="ReturnDate" name="ReturnDate" value="<?= htmlspecialchars($endDate) ?>" readonly>
            </div>
            <div class="booking-out-box">
                <label for="ReturnTime">เวลาในการส่งคืน (ตามไฟท์บินของท่าน):</label><br>
                <select class="form-select" id="ReturnTime" name="ReturnTime" required>
                    <option value="">กรุณาเลือกเวลาในการส่งคืน (ตามไฟท์บินของท่าน)</option>
                    <?php
                    foreach ($returnTimes as $time) {
                        echo "<option value=\"" . htmlspecialchars($time) . "\">" . htmlspecialchars($time) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="booking-out-box">
                <label for="driver_status">ต้องการคนขับหรือไม่:</label><br>
                <select class="form-select" id="driver_status" name="driver_status" required>
                    <option value="ไม่ต้องการคนขับ">ไม่ต้องการคนขับ</option>
                    <option value="ต้องการคนขับ">ต้องการคนขับ</option>
                </select>
            </div>
            <div class="booking-out-box" id="driverSelectBox" style="display: none;">
                <label for="driver_id">เลือกคนขับ:</label><br>
                <select class="form-select" id="driver_id" name="driver_id">
                    <option value="">เลือกคนขับ</option>
                    <?php
                    $sql_drivers = "SELECT driver_id, driver_name FROM driver";
                    $result_drivers = mysqli_query($con, $sql_drivers);
                    while ($row_driver = mysqli_fetch_assoc($result_drivers)) {
                        echo "<option value=\"" . htmlspecialchars($row_driver['driver_id']) . "\">" . htmlspecialchars($row_driver['driver_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="booking-out-box">
                <label for="RentalPrice">ราคาเช่าทั้งหมด:</label><br>
                <input class="form-control" type='text' id='RentalPrice' name='RentalPrice' value='<?= htmlspecialchars($totalRentalPrice) ?>' readonly>
                <input type='hidden' id='original_price' value='<?= htmlspecialchars($totalRentalPrice) ?>'>
                <input type='hidden' id='driver_daily_wage' value='<?= $driverDailyWage ?>'>
                <input type='hidden' id='rental_days' value='<?= $days ?>'>
            </div>
            <div class="booking-out-box">
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
        $driverStatus = $_POST['driver_status'];
        $driverID = ($driverStatus === 'ไม่ต้องการคนขับ') ? '5' : $_POST['driver_id'];
        $rentalPrice = $_POST['RentalPrice'];

        $stmt = $con->prepare("INSERT INTO carrent (car_id, MemberID, type_rent, type_carrent, driver_status, driver_id, carrent_date, carrent_time, carrent_return, return_time, carrent_price, carrent_status_id) VALUES (?, ?, 'เช่ารถแบบออนไลน์', 'เช่ารถส่วนตัว', ?, ?, ?, ?, ?, ?, ?, '1')");
        $stmt->bind_param("iisssssss", $carID, $memberID, $driverStatus, $driverID, $rentalDate, $rentalTime, $returnDate, $returnTime, $rentalPrice);

        if ($stmt->execute()) {
            $rentID = $stmt->insert_id;
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                });
                </script>";
        } else {
            echo "<script>
                alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $stmt->error . "');
              </script>";
        }

        $stmt->close();
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

    <script src="./script/booking.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var driverStatus = document.getElementById('driver_status');
            var driverSelectBox = document.getElementById('driverSelectBox');
            var originalPrice = parseFloat(document.getElementById('original_price').value);
            var rentalPriceField = document.getElementById('RentalPrice');
            var driverDailyWage = parseFloat(document.getElementById('driver_daily_wage').value);
            var rentalDays = parseInt(document.getElementById('rental_days').value);
            var totalDriverCost = rentalDays * driverDailyWage;

            driverStatus.addEventListener('change', function() {
                if (driverStatus.value === 'ต้องการคนขับ') {
                    driverSelectBox.style.display = 'block';
                    rentalPriceField.value = (originalPrice + totalDriverCost).toFixed(2);
                } else {
                    driverSelectBox.style.display = 'none';
                    rentalPriceField.value = originalPrice.toFixed(2);
                }
            });

            var confirmButton = document.getElementById('confirmButton');
            confirmButton.addEventListener('click', function() {
                window.location.href = 'payment.php?rent_id=<?= $rentID ?>';
            });

            var successModal = document.getElementById('successModal');
            successModal.addEventListener('hidden.bs.modal', function() {
                window.location.href = 'payment.php?rent_id=<?= $rentID ?>';
            });
        });

        function validateForm() {
            var rentalTime = document.getElementById('RentalTime').value;
            var returnTime = document.getElementById('ReturnTime').value;

            if (!rentalTime || !returnTime) {
                alert('กรุณาระบุเวลาในการรับรถและคืนรถ');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>
