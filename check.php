<?php
session_start();
require 'conDB.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็ควันว่าง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/check.css">
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

    <a href="show_car.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="check-container">
        <div class="check" id="CheckDate">
            <div class="check-title">
                เช็ควันที่ว่างของรถ
            </div>
            <div class="check-form" id="CheckFormDate">
                <form action="check.php" method="post">
                    <div class="box">
                        <label for="carrent_date">วันที่เช่า</label>
                        <input class="form-control" type="date" name="carrent_date" id="carrent_date" required>
                    </div>
                    <div class="box">
                        <label for="carrent_return">วันที่คืน</label>
                        <input class="form-control" type="date" name="carrent_return" id="carrent_return" required>
                    </div>
                    <button type="submit" name="CheckDate" id="CheckDate" class="btn btn-primary">ค้นหา</button>
                </form>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['CheckDate'])) {
        $startDate = $_POST['carrent_date'];
        $endDate = $_POST['carrent_return'];

        $sql = "SELECT car.car_id, car.car_name, car.car_picture1, car.car_brand, car.car_price,
                   (CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM carrent 
                            WHERE carrent.car_id = car.car_id 
                            AND ((carrent_date <= ? AND carrent_return >= ?)
                                 OR (carrent_date <= ? AND carrent_return >= ?)
                                 OR (carrent_date >= ? AND carrent_return <= ?))
                        ) THEN 'ไม่ว่าง'
                        ELSE 'ว่าง'
                    END) AS availability
                FROM car";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssss", $startDate, $startDate, $endDate, $endDate, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();

        $startDateThai = date('d/m/Y', strtotime($startDate));
        $endDateThai = date('d/m/Y', strtotime($endDate));

        echo '<div class="selected-dates text-center">';
        echo '<p>วันที่เช่า: ' . $startDateThai . '</p>';
        echo '<p>วันที่คืน: ' . $endDateThai . '</p>';
        echo '</div>';

        if ($result->num_rows > 0) {
            echo '<div class="table-view-datecheck">';
            echo '<div class="row view-car">';
            while ($car = $result->fetch_assoc()) {
                echo '<div class="col-md-3 mb-4">';
                echo '<div class="card">';
                echo '<img src="' . str_replace("../img/", "./img/", $car['car_picture1']) . '" class="card-img-top" alt="Car Image" style="width: 100%; height: 250px; object-fit: cover;">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-success">' . htmlspecialchars($car['car_name']) . '</h5>';
                echo '<p class="card-text">';
                echo 'ยี่ห้อรถ: ' . htmlspecialchars($car['car_brand']) . '<br>';
                echo 'ราคา: ' . htmlspecialchars($car['car_price']) . ' บาท <br>';
                echo '</p>';
                if ($car['availability'] === 'ว่าง') {
                    echo '<button class="btn btn-outline-warning" onclick="checkUserLogin(' . $car['car_id'] . ', \'' . $startDate . '\', \'' . $endDate . '\')">จองรถ</button>';
                } else {
                    echo '<button type="button" class="btn btn-danger" disabled>ไม่สามารถจองได้</button>';
                }
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning text-center">ไม่พบรถในช่วงวันที่เลือก</div>';
        }
    }
    ?>

    <script src="../script/out_check.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">แจ้งเตือน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                กรุณาลงชื่อเข้าใช้หรือสมัครสมาชิกเพื่อทำการจองรถ
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <a href="login.php" class="btn btn-primary">เข้าสู่ระบบ</a>
                <a href="register.php" class="btn btn-primary">สมัครสมาชิก</a>
            </div>
        </div>
    </div>
</div>

<script>
    function checkUserLogin(car_id, startDate, endDate) {
        <?php if (!isset($_SESSION['MemberID'])) : ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        <?php else : ?>
            window.location.href = 'booking_out.php?car_id=' + car_id + '&start_date=' + startDate + '&end_date=' + endDate;
        <?php endif; ?>
    }
</script>

</html>
