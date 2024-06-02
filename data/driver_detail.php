<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

if (!isset($_GET['driver_id'])) {
    echo "<script>alert('ไม่พบข้อมูลคนขับ'); window.location.href='driver_manage.php';</script>";
    exit;
}

$driver_id = $_GET['driver_id'];
$sql = "SELECT * FROM driver WHERE driver_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('ไม่พบข้อมูลคนขับ'); window.location.href='driver_manage.php';</script>";
    exit;
}

$driver = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $driver_name = $_POST['driver_name'];
    $driver_phone = $_POST['driver_phone'];
    $driver_detail = $_POST['driver_detail'];
    $driver_picture = $driver['driver_picture'];

    $target_dir = "../img/driver/";

    function createNewFileName($originalFileName)
    {
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $newFileName = "Driver_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
        return $newFileName;
    }

    // ตรวจสอบว่ามีการอัพโหลดไฟล์ใหม่หรือไม่
    if (isset($_FILES["driver_picture"]["name"]) && $_FILES["driver_picture"]["name"] != "") {
        $driver_picture = $target_dir . createNewFileName($_FILES["driver_picture"]["name"]);
        move_uploaded_file($_FILES["driver_picture"]["tmp_name"], $driver_picture);
    }

    $update_sql = "UPDATE driver SET driver_name = ?, driver_phone = ?, driver_detail = ?, driver_picture = ? WHERE driver_id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->bind_param("sssii", $driver_name, $driver_phone, $driver_detail, $driver_picture, $driver_id);

    if ($update_stmt->execute()) {
        echo "<script>alert('แก้ไขข้อมูลสำเร็จ'); window.location.href='driver_detail.php?driver_id=$driver_id';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการแก้ไขข้อมูล');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคนขับ</title>
    <link rel="stylesheet" href="../styles/driver_manage.css">
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <img src="<?= $driver['driver_picture'] ?>" class="img-fluid" alt="Driver Image" style="width: 100%; height: auto;">
            </div>
            <div class="col-md-8">
                <h2 class="text-success"><?= $driver['driver_name'] ?></h2>
                <p>ID : <?= $driver['driver_id'] ?></p>
                <p>เบอร์โทรศัพท์ : <?= $driver['driver_phone'] ?></p>
                <p>สถานะ : <?= $driver['driver_status'] ?></p>
                <p>ประวัติ : <?= nl2br($driver['driver_detail']) ?></p>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal">แก้ไขข้อมูล</button>
                <a href="driver_manage.php" class="btn btn-outline-dark">กลับ</a>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">แก้ไขข้อมูลคนขับ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="driver_name" class="form-label">ชื่อคนขับ</label>
                            <input type="text" class="form-control" id="driver_name" name="driver_name" value="<?= $driver['driver_name'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="driver_phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="driver_phone" name="driver_phone" value="<?= $driver['driver_phone'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="driver_detail" class="form-label">ประวัติ</label>
                            <textarea class="form-control" id="driver_detail" name="driver_detail" rows="3" required><?= $driver['driver_detail'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="driver_picture" class="form-label">รูปภาพ</label>
                            <input type="file" class="form-control" id="driver_picture" name="driver_picture">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>
