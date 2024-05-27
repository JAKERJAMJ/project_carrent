<?php
session_start();
require 'conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รถเช่าส่วนตัว</title>
    <link rel="stylesheet" href="./styles/show_car.css">
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
    <div class="head-show-car">
        <div class="title-show-car">
            รถยนต์ส่วนตัว
        </div>
    </div>
    <div class="search_date">
        <form action="" method="GET" class="d-flex justify-content-center">
            <div class="form-group mx-2">
                <label for="start_date">วันที่เริ่มเช่า:</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>" required>
            </div>
            <div class="form-group mx-2">
                <label for="end_date">วันที่สิ้นสุด:</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>" required>
            </div>
            <div class="form-group mx-2 align-self-end">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
                <button type="button" class="btn btn-danger" onclick="window.location.href='show_car.php'">ล้างค้นหา</button>
            </div>
        </form>
    </div>
    <div class="show-car">
        <div class="row view-car">
            <?php
            if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
                $start_date = $_GET['start_date'];
                $end_date = $_GET['end_date'];

                // Query to find available cars within the selected date range
                $sql = "SELECT * FROM car WHERE car_id NOT IN (
                            SELECT car_id FROM carrent 
                            WHERE ('$start_date' BETWEEN carrent_date AND carrent_return) 
                            OR ('$end_date' BETWEEN carrent_date AND carrent_return) 
                            OR (carrent_date BETWEEN '$start_date' AND '$end_date')
                            OR (carrent_return BETWEEN '$start_date' AND '$end_date')
                        )";
            } else {
                // Default query to show all cars
                $sql = "SELECT * FROM car ORDER BY car_id";
            }

            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $new_url = str_replace("../img/", "./img/", $row['car_picture1']);
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $new_url ?>" class="card-img-top" alt="Car Image" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['car_name'] ?></h5>
                            <p class="card-text">
                                ยี่ห้อรถ : <?= $row['car_brand'] ?><br>
                                ราคา : <?= $row['car_price'] ?> บาท <br>
                            </p>
                            <a href="show_car_detail.php?id=<?= $row['car_id'] ?>" class="btn btn-outline-success">รายละเอียด</a>
                            <button type="button" class="btn btn-outline-warning" onclick="window.location.href='check.php'">เช่ารถ</button>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
