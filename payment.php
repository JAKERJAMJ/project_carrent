<?php
session_start();

if (!isset($_SESSION['MemberID'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='./login.php';</script>";
    exit;
}

if (!isset($_GET['rent_id'])) {
    echo "<script>alert('ไม่พบข้อมูลการเช่า'); window.location.href='./index.php';</script>";
    exit;
}

$rentID = $_GET['rent_id'];

require './conDB.php';

// ดึงข้อมูลการเช่า
$sql = "SELECT * FROM carrent WHERE carrent_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $rentID);
$stmt->execute();
$result = $stmt->get_result();
$rent = $result->fetch_assoc();

if (!$rent) {
    echo "<script>alert('ไม่พบข้อมูลการเช่า'); window.location.href='./index.php';</script>";
    exit;
}

// ดึงข้อมูลรถ
$sql = "SELECT * FROM car WHERE car_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $rent['car_id']);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/payment.css">
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
    <a href="add_payment.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="payment-header">
        <div class="payment-car-name"><?= htmlspecialchars($car['car_name']) ?></div>
        <img src="<?= str_replace("../img/", "./img/", $car['car_picture1']) ?>" alt="รูปภาพรถ" class="payment-car-image">
    </div>
    <div class="payment-body">
        <form action="payment.php?rent_id=<?= htmlspecialchars($rentID) ?>" method="post" enctype="multipart/form-data">
            <div class="payment-box">
                <label for="rental_price">ราคาเช่าทั้งหมด:</label>
                <input class="form-control" type="text" name="rental_price" id="rental_price" value="<?= htmlspecialchars($rent['carrent_price']) ?>" readonly>
            </div>
            <div class="payment-box">
                <label for="payment_method">วิธีการชำระเงิน:</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="" disabled selected>เลือกวิธีการชำระเงิน</option>
                    <option value="bank_transfer">โอนผ่านธนาคาร</option>
                    <option value="promptpay">PromptPay</option>
                </select>
            </div>
            <div class="bank" style="display: none;">
                <div class="bank-number">
                    <img src="./img/kbank.webp" alt="">
                    <div class="bank-text">
                        เลขบัญชี 065-8-75611-7 กสิกรไทย<br>
                        ชื่อบัญชี ธนวรรณ คัมภ์บุญยอ
                    </div>
                </div>
                <div class="bank-slip">
                    <div class="box">
                        <label for="payment_date">วันที่โอน</label>
                        <input class="form-control" type="date" name="payment_date" id="payment_date" required>
                    </div>
                    <div class="box">
                        <label for="payment_time">เวลาที่โอน</label>
                        <input class="form-control" type="time" name="payment_time" id="payment_time" required>
                    </div>
                    <div class="box">
                        <label for="payment_slip">หลักฐานการชำระเงิน</label>
                        <input class="form-control" type="file" name="payment_slip" id="payment_slip" required>
                    </div>
                </div>
            </div>
            <div class="qr-pay" style="display: none;">
                <div class="payment-logo">
                    <img src="./img/PromptPay-logo.png" alt="">
                </div>
                <div class="qr">
                    <?php
                    require_once './conDB.php';
                    require_once("./lib/PromptPayQR.php");

                    $PromptPayQR = new PromptPayQR(); // new object
                    $PromptPayQR->size = 2; // Set QR code size to 2
                    $PromptPayQR->id = '0610299843'; // PromptPay ID
                    $PromptPayQR->amount = htmlspecialchars($rent['carrent_price']); // Set amount from car rent price
                    echo '<img src="' . $PromptPayQR->generate() . '">';
                    ?>
                </div>
                <div class="bank-slip">
                    <div class="box">
                        <label for="payment_date">วันที่โอน</label>
                        <input class="form-control" type="date" name="payment_date" id="payment_date" required>
                    </div>
                    <div class="box">
                        <label for="payment_time">เวลาที่โอน</label>
                        <input class="form-control" type="time" name="payment_time" id="payment_time" required>
                    </div>
                    <div class="box">
                        <label for="payment_slip">หลักฐานการชำระเงิน</label>
                        <input class="form-control" type="file" name="payment_slip" id="payment_slip" required>
                    </div>
                </div>
            </div>
            <input type="hidden" name="rent_id" value="<?= $rentID ?>">
            <div class="payment-box">
                <button class="btn btn-primary" type="submit" name="processPayment" id="processPayment">ชำระเงิน</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('payment_method').addEventListener('change', function() {
            var bankSection = document.querySelector('.bank');
            var qrSection = document.querySelector('.qr-pay');
            var paymentMethod = this.value;

            if (paymentMethod === 'bank_transfer') {
                bankSection.style.display = 'block';
                qrSection.style.display = 'none';
            } else if (paymentMethod === 'promptpay') {
                bankSection.style.display = 'none';
                qrSection.style.display = 'block';
            } else {
                bankSection.style.display = 'none';
                qrSection.style.display = 'none';
            }
        });
    </script>
</body>

</html>