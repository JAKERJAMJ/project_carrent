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
    <title>จัดการแพ็คเกจท่องเที่ยว</title>
    <link rel="stylesheet" href="../styles/packet_manage.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
        <div class="manage-package">การจัดการแพ็คเกจ</div>
        <button onclick="showPopup()" id="uppacket" type="button" class="btn btn-success">เพิ่ม</button>
    </div>

    <div class="container">
        <div class="row view-package">
            <?php
            require '../conDB.php';
            $sql = "SELECT * FROM package ORDER BY package_id";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $package_status = $row['package_status'];
                if ($package_status === 'ใช้งาน') {
                    $status_color = 'green'; // หากสถานะเป็น 'ใช้งาน' ให้เป็นสีเขียว
                } elseif ($package_status === 'ยกเลิกการใช้งาน') {
                    $status_color = 'red'; // หากสถานะเป็น 'ยกเลิกการใช้งาน' ให้เป็นสีแดง
                }
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $row['package_picture'] ?>" class="card-img-top" alt="packet img">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['package_name'] ?></h5>
                            <p class="card-text">
                                ID: <?= $row['package_id'] ?><br>
                                ราคาของแพ็คเกจ: <?= $row['package_price'] ?><br>
                                สถานะ : <span style="color: <?= $status_color; ?>;"><?= $package_status ?></span>
                            </p>
                            <div class="btn-group">
                                <a href="package_detail.php?id=<?= $row['package_id'] ?>" class="btn btn-outline-success">รายละเอียด</a>
                                <button type="button" class="btn btn-outline-danger" onclick="deletePackage(<?= $row['package_id'] ?>)">ลบข้อมูล</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="package-container" id="add_packet">
        <div class="title-package">
            เพิ่มข้อมูลแพ็คเกจท่องเที่ยว
        </div>
        <div class="package-body">
            <form action="package_management.php" method="post" enctype="multipart/form-data">
                <div class="box">
                    <label for="package_name">ชื่อแพ็คเกจท่องเที่ยว</label><br>
                    <input class="form-control" type="text" id="package_name" name="package_name" placeholder="-- ชื่อแพ็คเกจท่องเที่ยว --">
                </div>
                <div class="box">
                    <label for="package_hotel">ชื่อที่พัก</label><br>
                    <input class="form-control" type="text" id="package_hotel" name="package_hotel" placeholder="-- ชื่อที่พัก--">
                </div>
                <div class="box">
                    <label for="package_price">ราคาของแพ็คเกจ</label><br>
                    <input class="form-control" type="text" name="package_price" placeholder="-- ราคาของแพ็คเกจ --">
                </div>
                <div class="box">
                    <label for="package_date">ระยะเวลา (จำนวนกี่วัน)</label><br>
                    <input class="form-control" type="text" id="package_date" name="package_date">
                </div>
                <div class="box">
                    <label for="package_detail">รายละเอียดแพ็คเกจ</label><br>
                    <textarea class="form-control" type="text" id="package_detail" name="package_detail"></textarea>
                </div>
                <div class="box">
                    <label for="package_picture">รูปภาพหลัก</label><br>
                    <input class="form-control" type="file" name="package_picture" accept="image/*" id="package_picture">
                    <img src="" id="image-preview" class="image-preview" alt="รูปภาพตัวอย่าง">
                </div>
                <div class="box-btn">
                    <button class="btn btn-success" type="submit" name="submit">บันทึก</button>
                    <button class="btn btn-danger" type="button" onclick="hidePopup()" id="close-package">Close</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        $package_name = $_POST['package_name'];
        $package_price = $_POST['package_price'];
        $package_date = $_POST['package_date'];
        $package_detail = $_POST['package_detail'];
        $package_hotel = $_POST['package_hotel'];

        $target_dir = "../img/packet/";

        function createNewFileName($originalFileName) {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "pack_" . rand(1000, 999999) . "." . $fileExtension;
            return $newFileName;
        }

        $package_picture = $target_dir . createNewFileName($_FILES["package_picture"]["name"]);
        move_uploaded_file($_FILES["package_picture"]["tmp_name"], $package_picture);

        $sql = "INSERT INTO package (package_name, package_hotel, package_picture, 
            package_price, package_date, package_detail) 
            VALUES ('$package_name', '$package_hotel', '$package_picture', '$package_price', '$package_date', '$package_detail')";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }

    if (isset($_POST['delete'])) {
        $package_id = $_POST['package_id'];

        $sql = "DELETE FROM package WHERE package_id = '$package_id'";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }
    ?>

    <script src="../script/packet_manage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        function deletePackage(packageId) {
            if (confirm('Are you sure you want to delete this package?')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = '';
                
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'package_id';
                hiddenField.value = packageId;
                form.appendChild(hiddenField);
                
                const deleteField = document.createElement('input');
                deleteField.type = 'hidden';
                deleteField.name = 'delete';
                deleteField.value = 'true';
                form.appendChild(deleteField);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>

</html>
