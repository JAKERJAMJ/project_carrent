<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจัดการคนขับ</title>
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
    <div class="top-button">
        <a href="data_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        <div class="manage-driver">การจัดการคนขับ</div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDriverModal">เพิ่ม</button>
    </div>

    <div class="container">
        <div class="row view-driver">
            <?php
            $sql = "SELECT * FROM driver ORDER BY driver_id";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $driver_status = $row['driver_status'];
                $status_color = $driver_status === 'ใช้งาน' ? 'green' : ($driver_status === 'ยกเลิกการใช้งาน' ? 'red' : '');
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $row['driver_picture'] ?>" class="card-img-top" alt="Driver Image" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['driver_name'] ?></h5>
                            <p class="card-text">
                                ID : <?= $row['driver_id'] ?><br>
                                เบอร์โทรศัพท์ : <?= $row['driver_phone'] ?><br>
                                สถานะ : <span style="color: <?= $status_color; ?>;"><?= $driver_status ?></span>
                            </p>
                            <button type="button" class="btn btn-outline-success" onclick="window.location.href='driver_detail.php?driver_id=<?= $row['driver_id'] ?>'">รายละเอียด</button>
                            <button type="button" class="btn btn-outline-danger" onclick="cancelDriver(<?= $row['driver_id'] ?>)">ยกเลิกการใช้งาน</button>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <!-- Add Driver Modal -->
    <div class="modal fade" id="addDriverModal" tabindex="-1" aria-labelledby="addDriverModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDriverModalLabel">เพิ่มข้อมูลคนขับรถ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="driver_manage.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="driver_name" class="form-label">ชื่อคนขับรถ</label>
                            <input type="text" class="form-control" id="driver_name" name="driver_name" placeholder="--- ชื่อคนขับรถ ---" required>
                        </div>
                        <div class="mb-3">
                            <label for="driver_phone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="driver_phone" name="driver_phone" placeholder="--- เบอร์โทรศัพท์ ---" oninput="formatPhoneNumber(this)" maxlength="10" required>
                        </div>
                        <div class="mb-3">
                            <label for="driver_detail" class="form-label">ประวัติย่อ</label>
                            <textarea class="form-control" id="driver_detail" name="driver_detail" rows="3" placeholder="--- ประวัติโดยย่อ ---" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="driver_picture" class="form-label">รูปภาพ</label>
                            <input type="file" class="form-control" id="driver_picture" name="driver_picture" accept="image/*" onchange="previewImage()" required>
                            <div class="preview-img mt-2">
                                <img src="" id="image-preview" class="image-preview img-fluid" alt="รูปภาพตัวอย่าง" style="display:none;">
                            </div>
                        </div>
                        <button type="submit" name="submit" id="submit" class="btn btn-outline-success w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

   <?php
   
   if (isset($_POST['submit'])) {
        $driver_name = $_POST['driver_name'];
        $driver_phone = $_POST['driver_phone'];
        $driver_detail = $_POST['driver_detail'];

        $target_dir = "../img/driver/";

        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "Driver_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }

        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $driver_picture = $target_dir . createNewFileName($_FILES["driver_picture"]["name"]);
        move_uploaded_file($_FILES["driver_picture"]["tmp_name"], $driver_picture);

        $sql = "INSERT INTO driver (driver_name, driver_phone, driver_detail, driver_picture, driver_status) 
                VALUES ('$driver_name', '$driver_phone', '$driver_detail', '$driver_picture' , 'ใช้งาน'); ";

        // Execute SQL Query
        if ($con->query($sql) === TRUE) {
            echo '<script>alert("เพิ่มคนขับสำเร็จ");window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }
   }
   ?>


    <script src="../script/driver_manage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        function previewImage() {
            const preview = document.getElementById('image-preview');
            const file = document.getElementById('driver_picture').files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = 'none';
            }
        }

        function cancelDriver(driverId) {
            if (confirm('คุณแน่ใจว่าต้องการยกเลิกการใช้งานคนขับนี้หรือไม่?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_driver.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('ยกเลิกการใช้งานคนขับสำเร็จ');
                        window.location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาดในการยกเลิกการใช้งาน');
                    }
                };
                xhr.send('driver_id=' + driverId);
            }
        }
    </script>
</body>

</html>
