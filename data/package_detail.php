<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

$package_id = $_GET['id'];

$sql = "SELECT * FROM package WHERE package_id = '$package_id'";
$result = mysqli_query($con, $sql);
$package = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $package_name = $_POST['package_name'];
    $package_price = $_POST['package_price'];
    $package_date = $_POST['package_date'];
    $package_detail = $_POST['package_detail'];
    $package_hotel = $_POST['package_hotel'];

    $update_sql = "UPDATE package SET package_name='$package_name', package_price='$package_price', package_date='$package_date', package_detail='$package_detail', package_hotel='$package_hotel' WHERE package_id='$package_id'";

    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('อัพเดตข้อมูลสำเร็จ'); window.location.href='package_detail.php?id=$package_id';</script>";
    } else {
        echo "Error: " . $update_sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST['add_tourist'])) {
    $tourist_name = $_POST['tourist_name'];
    $tourist_detail = $_POST['tourist_detail'];
    $tourist_link = $_POST['tourist_link'];

    // Handle file upload
    $target_dir = "../img/tourist/";
    $original_file_name = basename($_FILES["tourist_picture"]["name"]);
    $file_extension = pathinfo($original_file_name, PATHINFO_EXTENSION);
    $new_file_name = uniqid("tourist_", true) . "." . $file_extension;
    $tourist_picture = $target_dir . $new_file_name;

    if (move_uploaded_file($_FILES["tourist_picture"]["tmp_name"], $tourist_picture)) {
        $add_sql = "INSERT INTO tourist (package_id, tourist_name, tourist_picture, tourist_detail, tourist_link) VALUES ('$package_id', '$tourist_name', '$new_file_name', '$tourist_detail', '$tourist_link')";

        if (mysqli_query($con, $add_sql)) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ'); window.location.href='package_detail.php?id=$package_id';</script>";
        } else {
            echo "Error: " . $add_sql . "<br>" . mysqli_error($con);
        }
    } else {
        echo "Error uploading file.";
    }
}

$tourist_sql = "SELECT * FROM tourist WHERE package_id = '$package_id'";
$tourist_result = mysqli_query($con, $tourist_sql);

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดแพ็คเกจ</title>
    <link rel="stylesheet" href="../styles/packet_detail.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar bg-body-tertiary">
            <div class="container-fluid d-flex justify-content-between">
                <div>
                    <a class="navbar-brand" href="../admin.php">Admin Controller</a>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="package_management.php" class="btn btn-outline-dark">กลับ</a>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">อัพเดต</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTouristModal">เพิ่ม</button>
            </div>
        </div>
        <div class="title-detail">
            รายละเอียดแพ็คเกจ
        </div>
        <div class="img-container mb-3">
            <img src="<?= $package['package_picture'] ?>" alt="Package Image" class="img-fluid" style="width: 30%;">
        </div>
        <table class="table table-bordered">
            <tr>
                <th>ชื่อแพ็คเกจท่องเที่ยว</th>
                <td><?= $package['package_name'] ?></td>
            </tr>
            <tr>
                <th>ชื่อที่พัก</th>
                <td><?= $package['package_hotel'] ?></td>
            </tr>
            <tr>
                <th>ราคาของแพ็คเกจ</th>
                <td><?= $package['package_price'] ?></td>
            </tr>
            <tr>
                <th>ระยะเวลา (จำนวนกี่วัน)</th>
                <td><?= $package['package_date'] ?></td>
            </tr>
            <tr>
                <th>รายละเอียดของแพ็คเกจ</th>
                <td><?= $package['package_detail'] ?></td>
            </tr>
        </table>

        <div class="title-detail mt-5">
            สถานที่ท่องเที่ยว
        </div>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อสถานที่ท่องเที่ยว</th>
                    <th>รูปภาพ</th>
                    <th>รายละเอียด</th>
                    <th>ลิงค์สถานที่ท่องเที่ยว</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1; // Initialize counter
                while ($tourist = mysqli_fetch_assoc($tourist_result)) {
                ?>
                    <tr>
                        <td><?= $counter ?></td> <!-- Display counter -->
                        <td><?= $tourist['tourist_name'] ?></td>
                        <td>
                            <img src="../img/tourist/<?= $tourist['tourist_picture'] ?>" alt="Tourist Image" class="img-fluid" style="width: 100px;" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-image="../img/tourist/<?= $tourist['tourist_picture'] ?>">
                        </td>
                        <td><?= $tourist['tourist_detail'] ?></td>
                        <td><a href="<?= $tourist['tourist_link'] ?>" target="_blank">ลิงค์</a></td>
                    </tr>
                <?php
                    $counter++; // Increment counter
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">อัพเดตข้อมูลแพ็คเกจ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST" action="packet_detail.php?id=<?= $package_id ?>">
                        <div class="mb-3">
                            <label for="package_name" class="form-label">ชื่อแพ็คเกจท่องเที่ยว</label>
                            <input type="text" class="form-control" id="package_name" name="package_name" value="<?= $package['package_name'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="package_hotel" class="form-label">ชื่อที่พัก</label>
                            <input type="text" class="form-control" id="package_hotel" name="package_hotel" value="<?= $package['package_hotel'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="package_price" class="form-label">ราคาของแพ็คเกจ</label>
                            <input type="text" class="form-control" id="package_price" name="package_price" value="<?= $package['package_price'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="package_date" class="form-label">ระยะเวลา (จำนวนกี่วัน)</label>
                            <input type="text" class="form-control" id="package_date" name="package_date" value="<?= $package['package_date'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="package_detail" class="form-label">รายละเอียดของแพ็คเกจ</label>
                            <textarea class="form-control" id="package_detail" name="package_detail" value="<?= $package['package_detail'] ?>"></textarea>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">อัพเดต</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Tourist Modal -->
    <div class="modal fade" id="addTouristModal" tabindex="-1" aria-labelledby="addTouristModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTouristModalLabel">เพิ่มข้อมูลสถานที่ท่องเที่ยว</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addTouristForm" method="POST" action="packet_detail.php?id=<?= $package_id ?>" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="tourist_name" class="form-label">ชื่อสถานที่ท่องเที่ยว</label>
                            <input type="text" class="form-control" id="tourist_name" name="tourist_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="tourist_picture" class="form-label">รูปภาพสถานที่ท่องเที่ยว</label>
                            <input type="file" class="form-control" id="tourist_picture" name="tourist_picture" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="tourist_detail" class="form-label">รายละเอียดสถานที่ท่องเที่ยว</label>
                            <textarea class="form-control" id="tourist_detail" name="tourist_detail" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="tourist_link" class="form-label">ลิงค์สถานที่ท่องเที่ยว</label>
                            <input type="url" class="form-control" id="tourist_link" name="tourist_link">
                        </div>
                        <button type="submit" name="add_tourist" class="btn btn-success">เพิ่มข้อมูล</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">รูปภาพสถานที่ท่องเที่ยว</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" alt="Tourist Image">
                </div>
            </div>
        </div>
    </div>

    <script src="../scripts/packet_detail.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var imageUrl = button.getAttribute('data-bs-image');
                var modalImage = document.getElementById('modalImage');
                modalImage.src = imageUrl;
            });
        });
    </script>
</body>

</html>
