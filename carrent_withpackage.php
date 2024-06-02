<?php
session_start();

if (!isset($_SESSION['MemberID'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='./login.php';</script>";
    exit;
}

require './conDB.php';

if (!isset($_GET['car_id']) || !isset($_GET['package_id']) || !isset($_GET['start_date']) || !isset($_GET['end_date'])) {
    die("Missing required parameters.");
}

$carId = $_GET['car_id'];
$packageId = $_GET['package_id'];
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$memberID = $_SESSION['MemberID'];

// Fetch car details
$sql = "SELECT * FROM car WHERE car_id = $carId";
$result = mysqli_query($con, $sql);
if (!$result) {
    die("Error in SQL query: " . mysqli_error($con));
}
$car = mysqli_fetch_assoc($result);
if (!$car) {
    die("Car not found.");
}

// Fetch package details
$sql = "SELECT * FROM package WHERE package_id = $packageId";
$result = mysqli_query($con, $sql);
if (!$result) {
    die("Error in SQL query: " . mysqli_error($con));
}
$package = mysqli_fetch_assoc($result);
if (!$package) {
    die("Package not found.");
}

// Fetch member details
$sql = "SELECT * FROM member WHERE MemberID = $memberID";
$result = mysqli_query($con, $sql);
if (!$result) {
    die("Error in SQL query: " . mysqli_error($con));
}
$member = mysqli_fetch_assoc($result);

// Calculate rental days
$datetime1 = new DateTime($startDate);
$datetime2 = new DateTime($endDate);
$interval = $datetime1->diff($datetime2);
$rentalDays = $interval->days + 1;

// Calculate prices
$carRentalPrice = $car['car_price'];
$carRentalTotalPrice = $carRentalPrice * $rentalDays;
$packagePrice = $package['package_price'];
$totalRentalPrice = $carRentalTotalPrice + $packagePrice;
$driverDailyWage = 300; // Daily wage for the driver

// Fetch car rental times
$enumSql = "SHOW COLUMNS FROM carrent LIKE 'carrent_time'";
$enumResult = mysqli_query($con, $enumSql);
$enumRow = mysqli_fetch_assoc($enumResult);
$enumList = str_replace("'", "", substr($enumRow['Type'], 5, (strlen($enumRow['Type']) - 6)));
$carRentTimes = explode(",", $enumList);

// Fetch car return times
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
    <title>เช่ารถพร้อมแพ็คเกจ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/rent_car_with_package.css">
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
    <a href="check_availability.php?package_id=<?= htmlspecialchars($packageId) ?>" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="booking-out-header">
        <div class="booking-out-car-name"><?= htmlspecialchars($car['car_name']) ?></div>
        <img src="<?= str_replace("../img/", "./img/", $car['main_picture']) ?>" alt="รูปภาพรถ" class="booking-out-car-image">
    </div>
    <div class="booking-out-body">
        <form id="rentalForm" action="" method="post" onsubmit="return validateForm()">
            <div class="booking-out-box">
                <label for="car_name">ชื่อรถ</label>
                <input class="form-control" type="text" name="car_name" id="car_name" value="<?= htmlspecialchars($car['car_name']) ?>" readonly>
                <input class="form-control" type="hidden" name="car_id" id="car_id" value="<?= htmlspecialchars($car['car_id']) ?>">
            </div>
            <div class="booking-out-box">
                <label for="package_name">ชื่อแพ็คเกจ</label>
                <input class="form-control" type="text" name="package_name" id="package_name" value="<?= htmlspecialchars($package['package_name']) ?>" readonly>
                <input class="form-control" type="hidden" name="package_id" id="package_id" value="<?= htmlspecialchars($package['package_id']) ?>">
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
            <div class="booking-out-box">
                <label for="CarRentalPrice">ราคารถเช่าต่อวัน:</label><br>
                <input class="form-control" type='text' id='CarRentalPrice' name='CarRentalPrice' value='<?= htmlspecialchars($carRentalPrice) ?>' readonly>
            </div>
            <div class="booking-out-box">
                <label for="CarRentalTotalPrice">ราคารถเช่าทั้งหมด:</label><br>
                <input class="form-control" type='text' id='CarRentalTotalPrice' name='CarRentalTotalPrice' value='<?= htmlspecialchars($carRentalTotalPrice) ?>' readonly>
            </div>
            <div class="booking-out-box">
                <label for="PackagePrice">ราคาแพ็คเกจ:</label><br>
                <input class="form-control" type='text' id='PackagePrice' name='PackagePrice' value='<?= htmlspecialchars($packagePrice) ?>' readonly>
            </div>
            <div class="booking-out-box">
                <label for="RentalPrice">ราคาเช่าทั้งหมด:</label><br>
                <input class="form-control" type='text' id='RentalPrice' name='RentalPrice' value='<?= htmlspecialchars($totalRentalPrice) ?>' readonly>
                <input type='hidden' id='original_price' value='<?= htmlspecialchars($totalRentalPrice) ?>'>
                <input type='hidden' id='driver_daily_wage' value='<?= $driverDailyWage ?>'>
                <input type='hidden' id='rental_days' value='<?= $rentalDays ?>'>
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
        $packageID = $_POST['package_id'];
        $rentalDate = $_POST['RentalDate'];
        $rentalTime = $_POST['RentalTime'];
        $returnDate = $_POST['ReturnDate'];
        $returnTime = $_POST['ReturnTime'];
        $driverStatus = $_POST['driver_status'];
        $driverID = "0"; // Temporary driver ID
        $rentalPrice = $_POST['RentalPrice'];

        // Insert data into carrent table
        $sql = "INSERT INTO carrent (car_id, MemberID, type_rent, type_carrent, package_id, driver_status, driver_id, carrent_date, carrent_time, carrent_return, return_time, carrent_price, carrent_status)
                VALUES ('$carID', '$memberID', 'เช่ารถแบบออนไลน์', 'เช่ารถพร้อมแพ็คเกจ', '$packageID', '$driverStatus', '$driverID', '$rentalDate', '$rentalTime', '$returnDate', '$returnTime', '$rentalPrice', 'กำลังดำเนินการเช่า')";

        if (mysqli_query($con, $sql)) {
            $rentID = mysqli_insert_id($con);
            echo "<script>
            alert('จองรถสำเร็จ กรุณาชำระเงิน');
            window.location.href = 'payment.php?rent_id=$rentID';
                </script>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var driverStatus = document.getElementById('driver_status');
            var originalPrice = parseFloat(document.getElementById('original_price').value);
            var rentalPriceField = document.getElementById('RentalPrice');
            var driverDailyWage = parseFloat(document.getElementById('driver_daily_wage').value);
            var rentalDays = parseInt(document.getElementById('rental_days').value);
            var totalDriverCost = rentalDays * driverDailyWage;

            driverStatus.addEventListener('change', function() {
                if (driverStatus.value === 'ต้องการคนขับ') {
                    rentalPriceField.value = (originalPrice + totalDriverCost).toFixed(2);
                } else {
                    rentalPriceField.value = originalPrice.toFixed(2);
                }
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
