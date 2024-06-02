<?php
session_start();
require 'conDB.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('ไม่พบข้อมูลรถยนต์ที่ต้องการ'); window.location.href='show_car.php';</script>";
    exit;
}

$car_id = $_GET['id'];

$sql = "SELECT * FROM car WHERE car_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    echo "<script>alert('ไม่พบข้อมูลรถยนต์ที่ต้องการ'); window.location.href='show_car.php';</script>";
    exit;
}

// ดึงข้อมูลรูปภาพเพิ่มเติมจากตาราง car_picture
$sql_pictures = "SELECT car_pic1, car_pic2, car_pic3 FROM car WHERE car_id = ?";
$stmt_pictures = $con->prepare($sql_pictures);
$stmt_pictures->bind_param("i", $car_id);
$stmt_pictures->execute();
$result_pictures = $stmt_pictures->get_result();
$pictures = $result_pictures->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดรถยนต์</title>
    <link rel="stylesheet" href="./styles/show_car_detail.css">
    <link rel="stylesheet" href="./styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <?php
    if (isset($_SESSION['MemberID'])) {
        require 'user_nav.php'; // Include user navigation if user is logged in
    } else {
        require 'nav.php'; // Include default navigation if user is not logged in
    }
    ?>
    <div class="container car-detail-container mt-5">
        <div class="car-detail">
            <div class="picture">
                <img src="<?= str_replace("../img/", "./img/", $car['main_picture']) ?>" class="pic-head" alt="Car Image">
            </div>
            <div class="picture-detail mt-3">
                <?php
                foreach (['picture1', 'picture2', 'picture3'] as $picture_field) {
                    if (!empty($pictures[$picture_field]) && $pictures[$picture_field] != '.') {
                        $picture_url = str_replace("../img/", "./img/", $pictures[$picture_field]);
                        echo "<img src='$picture_url' class='img-thumbnail' alt='Car Image' onclick='showModal(\"$picture_url\")'>";
                    }
                }
                ?>
            </div>
            <a href="show_car.php" class="btn btn-outline-secondary mt-3">กลับไปหน้ารายการรถยนต์</a>
        </div>
        <div class="detail">
            <div class="detail-title">
                <p><?= $car['car_name'] ?></p>
            </div>
            <div class="de">
            <p>ยี่ห้อรถ: <?= $car['car_brand'] ?><br>
            <br>
            ราคา: <?= $car['car_price'] ?> บาท<br>
            <br>
            รายละเอียด: <?= $car['car_detail'] ?></p>
            </div>
            <a href="rent_car.php?id=<?= $car['car_id'] ?>" class="btn btn-outline-warning">เช่ารถ</a>
        </div>
    </div>

    <!-- Modal for displaying the image -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <img id="modalImage" class="modal-img" src="" alt="Car Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script>
        function showModal(imageUrl) {
            const modalImage = document.getElementById('modalImage');
            modalImage.src = imageUrl;
            const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
            imageModal.show();
        }
    </script>
</body>

</html>
