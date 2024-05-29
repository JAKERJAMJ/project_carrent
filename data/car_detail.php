<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

$car_id = $_GET['id'];

// Fetch car details
$sql = "SELECT * FROM car WHERE car_id = '$car_id'";
$result = mysqli_query($con, $sql);
$car = mysqli_fetch_assoc($result);

$picture_sql = "SELECT * FROM car_picture WHERE car_id = '$car_id'";
$picture_result = mysqli_query($con, $picture_sql);
$pictures = mysqli_fetch_assoc($picture_result);

// Fetch rental details
$carrent_sql = "SELECT * FROM carrent WHERE car_id = '$car_id'";
$carrent_result = mysqli_query($con, $carrent_sql);
$carrent = mysqli_fetch_assoc($carrent_result);

// Calculate income
$income_sql = "SELECT 
    SUM(carrent_price) AS total_income, 
    DATE_FORMAT(carrent_date, '%Y-%m') AS rent_month 
FROM carrent 
WHERE car_id = '$car_id' 
GROUP BY rent_month";
$income_result = mysqli_query($con, $income_sql);

$monthly_income = [];
while ($row = mysqli_fetch_assoc($income_result)) {
    $monthly_income[] = $row;
}

// Filter daily income by selected month
$selected_month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');

$daily_income_sql = "SELECT 
    SUM(carrent.carrent_price) AS daily_income, 
    DATE(carrent.carrent_date) AS rent_day,
    carrent.carrent_id,
    member.Membername,
    member.Memberlastname
FROM carrent 
LEFT JOIN member ON carrent.MemberID = member.MemberID
WHERE car_id = '$car_id' AND DATE_FORMAT(carrent.carrent_date, '%Y-%m') = '$selected_month'
GROUP BY rent_day, carrent.carrent_id, member.Membername, member.Memberlastname";
$daily_income_result = mysqli_query($con, $daily_income_sql);

$daily_income = [];
while ($row = mysqli_fetch_assoc($daily_income_result)) {
    $daily_income[] = $row;
}

// Fetch repair history
$repair_sql = "SELECT * FROM fix_car WHERE car_id = '$car_id' ORDER BY fix_date DESC";
$repair_result = mysqli_query($con, $repair_sql);

$repair_history = [];
while ($row = mysqli_fetch_assoc($repair_result)) {
    $repair_history[] = $row;
}

if (isset($_POST['update'])) {
    $car_name = $_POST['car_name'];
    $car_brand = $_POST['car_brand'];
    $car_numplate = $_POST['car_numplate'];
    $car_vin = $_POST['car_vin'];
    $car_price = $_POST['car_price'];
    $car_detail = $_POST['car_detail'];

    $car_picture1 = $car['car_picture1'];

    if ($_FILES['car_picture1']['name']) {
        $target_dir = "../img/car/";
        $car_picture1 = $target_dir . createNewFileName($_FILES["car_picture1"]["name"]);
        move_uploaded_file($_FILES["car_picture1"]["tmp_name"], $car_picture1);
    }

    $update_sql = "UPDATE car SET car_name='$car_name', car_brand='$car_brand', car_numplate='$car_numplate', car_vin='$car_vin', car_price='$car_price', car_detail='$car_detail', car_picture1='$car_picture1' WHERE car_id='$car_id'";

    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('อัพเดตข้อมูลสำเร็จ'); window.location.href='car_detail.php?id=$car_id';</script>";
    } else {
        echo "Error: " . $update_sql . "<br>" . mysqli_error($con);
    }
}

if (isset($_POST['add_pictures'])) {
    $target_dir = "../img/car/";

    $picture1 = $target_dir . createNewFileName($_FILES["picture1"]["name"]);
    $picture2 = $target_dir . createNewFileName($_FILES["picture2"]["name"]);
    $picture3 = $target_dir . createNewFileName($_FILES["picture3"]["name"]);

    move_uploaded_file($_FILES["picture1"]["tmp_name"], $picture1);
    move_uploaded_file($_FILES["picture2"]["tmp_name"], $picture2);
    move_uploaded_file($_FILES["picture3"]["tmp_name"], $picture3);

    if ($pictures) {
        $picture_update_sql = "UPDATE car_picture SET picture1='$picture1', picture2='$picture2', picture3='$picture3' WHERE car_id='$car_id'";
    } else {
        $picture_update_sql = "INSERT INTO car_picture (car_id, picture1, picture2, picture3) VALUES ('$car_id', '$picture1', '$picture2', '$picture3')";
    }

    if (mysqli_query($con, $picture_update_sql)) {
        echo "<script>alert('เพิ่มรูปภาพสำเร็จ'); window.location.href='car_detail.php?id=$car_id';</script>";
    } else {
        echo "Error: " . $picture_update_sql . "<br>" . mysqli_error($con);
    }
}

function createNewFileName($originalFileName)
{
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $newFileName = "Car_" . uniqid() . "." . $fileExtension;
    return $newFileName;
}

function thai_date($date)
{
    $months = [
        "01" => "มกราคม",
        "02" => "กุมภาพันธ์",
        "03" => "มีนาคม",
        "04" => "เมษายน",
        "05" => "พฤษภาคม",
        "06" => "มิถุนายน",
        "07" => "กรกฎาคม",
        "08" => "สิงหาคม",
        "09" => "กันยายน",
        "10" => "ตุลาคม",
        "11" => "พฤศจิกายน",
        "12" => "ธันวาคม"
    ];

    $year = date('Y', strtotime($date)) + 543;
    $month = date('m', strtotime($date));
    $day = date('d', strtotime($date));

    return $day . ' ' . $months[$month] . ' ' . $year;
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดรถ</title>
    <link rel="stylesheet" href="../styles/car_detail.css">
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
            <a href="car_management.php" class="btn btn-outline-dark">กลับ</a>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">อัพเดต</button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPicturesModal">เพิ่มรูปภาพ</button>
            </div>
        </div>
        <div class="text-center">
            <div class="title-detail mb-3">
                รายละเอียดรถ
            </div>
            <div class="img-container mb-3">
                <img src="<?= $car['car_picture1'] ?>" alt="Car Image" class="img-fluid" style="width: 30%;">
            </div>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>ชื่อรถ</th>
                <td><?= $car['car_name'] ?></td>
            </tr>
            <tr>
                <th>ยี่ห้อ</th>
                <td><?= $car['car_brand'] ?></td>
            </tr>
            <tr>
                <th>ป้ายทะเบียนรถ</th>
                <td><?= $car['car_numplate'] ?></td>
            </tr>
            <tr>
                <th>เลขตัวถัง</th>
                <td><?= $car['car_vin'] ?></td>
            </tr>
            <tr>
                <th>ราคาเช่า</th>
                <td><?= $car['car_price'] ?></td>
            </tr>
            <tr>
                <th>รายละเอียดของรถ</th>
                <td><?= $car['car_detail'] ?></td>
            </tr>
        </table>
    </div>

    <div class="sum-car">
        <div class="title-sum">
            รายได้สุทธิ
        </div>
        <div class="table-view-sum">
            <h5>รายได้ต่อเดือน</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>เดือน</th>
                        <th>รายได้ (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($monthly_income as $income) : ?>
                        <tr>
                            <td><?= thai_date($income['rent_month']) ?></td>
                            <td><?= number_format($income['total_income'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h5>รายได้ต่อวัน</h5>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>รหัสการเช่า</th>
                        <th>ชื่อผู้เช่า</th>
                        <th>รายได้ (บาท)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daily_income as $income) : ?>
                        <tr>
                            <td><?= thai_date($income['rent_day']) ?></td>
                            <td><?= htmlspecialchars($income['carrent_id']) ?></td>
                            <td><?= htmlspecialchars($income['Membername'] . ' ' . htmlspecialchars($income['Memberlastname'])) ?></td>
                            <td><?= number_format($income['daily_income'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fix-car">
        <div class="fix-title">
            ประวัติการซ่อม
        </div>
        <div class="fix-body">
            <?php if (count($repair_history) > 0) : ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>วันที่ซ่อม</th>
                            <th>รายละเอียดการซ่อม</th>
                            <th>ค่าใช้จ่าย (บาท)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($repair_history as $repair) : ?>
                            <tr>
                                <td><?= thai_date($repair['fix_date']) ?></td>
                                <td><?= htmlspecialchars($repair['fix_detail']) ?></td>
                                <td><?= number_format($repair['fix_price']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>ไม่มีประวัติการซ่อมสำหรับรถคันนี้</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="picture-detail">
        <?php if ($pictures) : ?>
            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="img-container mb-3">
                        <img src="<?= $pictures['picture1'] ?>" alt="Additional Picture 1" class="img-fluid" data-bs-toggle="modal" data-bs-target="#pictureModal" data-bs-picture="<?= $pictures['picture1'] ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="img-container mb-3">
                        <img src="<?= $pictures['picture2'] ?>" alt="Additional Picture 2" class="img-fluid" data-bs-toggle="modal" data-bs-target="#pictureModal" data-bs-picture="<?= $pictures['picture2'] ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="img-container mb-3">
                        <img src="<?= $pictures['picture3'] ?>" alt="Additional Picture 3" class="img-fluid" data-bs-toggle="modal" data-bs-target="#pictureModal" data-bs-picture="<?= $pictures['picture3'] ?>">
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">อัพเดตข้อมูลรถ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateForm" method="POST" action="car_detail.php?id=<?= $car_id ?>" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="car_name" class="form-label">ชื่อรถ</label>
                            <input type="text" class="form-control" id="car_name" name="car_name" value="<?= $car['car_name'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="car_brand" class="form-label">ยี่ห้อ</label>
                            <input type="text" class="form-control" id="car_brand" name="car_brand" value="<?= $car['car_brand'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="car_numplate" class="form-label">ป้ายทะเบียนรถ</label>
                            <input type="text" class="form-control" id="car_numplate" name="car_numplate" value="<?= $car['car_numplate'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="car_vin" class="form-label">เลขตัวถัง</label>
                            <input type="text" class="form-control" id="car_vin" name="car_vin" value="<?= $car['car_vin'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="car_price" class="form-label">ราคาเช่า</label>
                            <input type="text" class="form-control" id="car_price" name="car_price" value="<?= $car['car_price'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="car_detail" class="form-label">รายละเอียดของรถ</label>
                            <textarea class="form-control" id="car_detail" name="car_detail" rows="3"><?= $car['car_detail'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="car_picture1" class="form-label">รูปภาพ</label>
                            <input type="file" class="form-control" id="car_picture1" name="car_picture1" accept="image/*">
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">อัพเดต</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Pictures Modal -->
    <div class="modal fade" id="addPicturesModal" tabindex="-1" aria-labelledby="addPicturesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPicturesModalLabel">เพิ่มรูปภาพเพิ่มเติม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addPicturesForm" method="POST" action="car_detail.php?id=<?= $car_id ?>" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="picture1" class="form-label">รูปภาพเพิ่มเติม 1</label>
                            <input type="file" class="form-control" id="picture1" name="picture1" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="picture2" class="form-label">รูปภาพเพิ่มเติม 2</label>
                            <input type="file" class="form-control" id="picture2" name="picture2" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="picture3" class="form-label">รูปภาพเพิ่มเติม 3</label>
                            <input type="file" class="form-control" id="picture3" name="picture3" accept="image/*" required>
                        </div>
                        <button type="submit" name="add_pictures" class="btn btn-success">เพิ่มรูปภาพ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Picture Modal -->
    <div class="modal fade" id="pictureModal" tabindex="-1" aria-labelledby="pictureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pictureModalLabel">ดูรูปภาพ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Enlarged Picture" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="../scripts/car_detail.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(element) {
            element.addEventListener('click', function() {
                var imgSrc = element.getAttribute('data-bs-picture');
                var modalImg = document.getElementById('modalImage');
                modalImg.setAttribute('src', imgSrc);
            });
        });
    </script>
</body>

</html>
