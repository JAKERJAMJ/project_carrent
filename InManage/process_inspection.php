<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';

$car_id = $carrent_id = $car_status = $fix_detail = $fix_date = $fix_return = $fix_price = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $car_id = $_POST['car_id'];
    $carrent_id = $_POST['carrent_id'];
    $car_status = $_POST['car_status'];
    $fix_detail = $_POST['fix_detail'] ?? '';
    $fix_date = $_POST['fix_date'] ?? '';
    $fix_return = $_POST['fix_return'] ?? '';
    $fix_price = $_POST['fix_price'] ?? '';

    if ($car_status == 'สภาพรถปกติ') {
        $sql = "INSERT INTO fix_car (fix_id, car_id, carrent_id, fix_detail, fix_date, fix_return, fix_price, fix_status) 
                VALUES (NULL, '$car_id', '$carrent_id', 'สภาพรถปกติ', NOW(), NOW(), 0, 'สภาพรถปกติ')";
    } elseif ($car_status == 'ต้องซ่อมแซม') {
        $sql = "INSERT INTO fix_car (fix_id, car_id, carrent_id, fix_detail, fix_date, fix_return, fix_price, fix_status) 
                VALUES (NULL, '$car_id', '$carrent_id', '$fix_detail', '$fix_date', '$fix_return', '$fix_price', 'ต้องส่งซ่อม')";
    }

    if (mysqli_query($con, $sql)) {
        $last_id = mysqli_insert_id($con);
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                    setTimeout(function() {
                        window.location.href = 'fix_car.php';
                    }, 5000);
                });
              </script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($con) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การตรวจสอบสภาพรถ - Processing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/inspect.css">
</head>

<body>
    <!-- Bootstrap modal for success message -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="head">ข้อมูลถูกเพิ่มไปยังฐานข้อมูลเรียบร้อยแล้ว<br>
                        <span style="color: <?= $car_status == 'ต้องซ่อมแซม' ? 'red' : 'green' ?>;">
                            <?= htmlspecialchars($car_status) ?>
                        </span>
                    </p>
                    <p><strong>รหัสรถ:</strong> <?= htmlspecialchars($car_id) ?></p>
                    <p><strong>รหัสการเช่ารถ:</strong> <?= htmlspecialchars($carrent_id) ?></p>
                    <?php if ($car_status == 'ต้องซ่อมแซม') : ?>
                        <p><strong>รายละเอียดการซ่อม:</strong> <?= htmlspecialchars($fix_detail) ?></p>
                        <p><strong>วันที่คาดว่าจะส่งซ่อม:</strong> <?= htmlspecialchars($fix_date) ?></p>
                        <p><strong>วันที่คาดว่าจะเสร็จ:</strong> <?= htmlspecialchars($fix_return) ?></p>
                        <p><strong>ราคาประมาณการซ่อม:</strong> <?= htmlspecialchars($fix_price) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>

<?php
mysqli_close($con);
?>