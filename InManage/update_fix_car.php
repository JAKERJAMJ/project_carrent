<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';

$update_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_button']) && $_POST['submit_button'] == 'update') {
    // รับข้อมูลจากฟอร์ม
    $fix_id = $_POST['fix_id'];
    $fix_detail = $_POST['fix_detail'];
    $fix_date = $_POST['fix_date'];
    $fix_return = $_POST['fix_return'];
    $fix_price = $_POST['fix_price'];
    $fix_status = 'ซ่อมเสร็จสิ้น'; // ปรับปรุงสถานะเป็น 'ซ่อมเสร็จสิ้น'

    // อัปเดตฐานข้อมูล
    $sql = "UPDATE fix_car SET 
            fix_detail = '$fix_detail',
            fix_date = '$fix_date',
            fix_return = '$fix_return',
            fix_price = '$fix_price',
            fix_status = '$fix_status'
            WHERE fix_id = $fix_id";

    if (mysqli_query($con, $sql)) {
        $update_success = true;
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Fix Car</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <?php if ($update_success) : ?>
        <!-- Bootstrap modal for success message -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Success</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="success" style="text-align: center; color:green;">อัปเดตข้อมูลเรียบร้อยแล้ว<p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($update_success) : ?>
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                setTimeout(function() {
                    window.location.href = 'fix_car_success.php';
                }, 3000); // แสดง modal 3 วินาทีก่อนเปลี่ยนหน้า
            <?php endif; ?>
        });
    </script>
</body>

</html>
