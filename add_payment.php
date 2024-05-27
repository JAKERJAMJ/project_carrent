<?php
session_start();
if (!isset($_SESSION['MemberID'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='./login.php';</script>";
    exit;
}

require './conDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rentID = $_POST['rent_id'];
    $paymentMethod = $_POST['payment_method'];
    $paymentDate = $_POST['payment_date'];
    $paymentTime = $_POST['payment_time'];
    $paymentSlip = "";

    if (isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["payment_slip"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $targetFilePath)) {
            $paymentSlip = $fileName;
        }
    }

    $sql = "INSERT INTO payment (carrent_id, payment_type, payment_date, payment_time, payment_slip, payment_status) VALUES (?, ?, ?, ?, ?, 'Pending')";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("issss", $rentID, $paymentMethod, $paymentDate, $paymentTime, $paymentSlip);
    if ($stmt->execute()) {
        echo "<script>alert('ชำระเงินสำเร็จ'); window.location.href='manage_carrent.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มการชำระเงิน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
</head>

<body>
    <?php
    if (isset($_SESSION['MemberID'])) {
        require 'user_nav.php'; // Include user navigation if user is logged in
    } else {
        require 'nav.php'; // Include default navigation if user is not logged in
    }
    ?>
    <div class="container mt-5">
        <h2>เพิ่มการชำระเงิน</h2>
        <form action="add_payment.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="rent_id" id="rent_id" value="<?= $rentID ?>">
            <div class="mb-3">
                <label for="payment_method" class="form-label">วิธีการชำระเงิน:</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="bank_transfer">โอนผ่านธนาคาร</option>
                    <option value="promptpay">PromptPay</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="payment_date" class="form-label">วันที่โอน:</label>
                <input type="date" class="form-control" name="payment_date" id="payment_date" required>
            </div>
            <div class="mb-3">
                <label for="payment_time" class="form-label">เวลาที่โอน:</label>
                <input type="time" class="form-control" name="payment_time" id="payment_time" required>
            </div>
            <div class="mb-3">
                <label for="payment_slip" class="form-label">หลักฐานการชำระเงิน:</label>
                <input type="file" class="form-control" name="payment_slip" id="payment_slip" required>
            </div>
            <button type="submit" class="btn btn-primary">ชำระเงิน</button>
        </form>
    </div>
</body>

</html>