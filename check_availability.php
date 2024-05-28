<?php
session_start();
require 'conDB.php';

if (!isset($_GET['package_id'])) {
    die("Package ID not specified.");
}

$package_id = $_GET['package_id'];
$sql = "SELECT * FROM package WHERE package_id = $package_id";
$result = mysqli_query($con, $sql);

if (!$result) {
    die("Error in SQL query: " . mysqli_error($con));
}

$package = mysqli_fetch_assoc($result);
if (!$package) {
    die("Package not found.");
}

function convertToThaiDate($date) {
    $thaiMonths = array(
        "01" => "มกราคม", "02" => "กุมภาพันธ์", "03" => "มีนาคม", "04" => "เมษายน",
        "05" => "พฤษภาคม", "06" => "มิถุนายน", "07" => "กรกฎาคม", "08" => "สิงหาคม",
        "09" => "กันยายน", "10" => "ตุลาคม", "11" => "พฤศจิกายน", "12" => "ธันวาคม"
    );
    $year = date("Y", strtotime($date)) + 543;
    $month = date("m", strtotime($date));
    $day = date("d", strtotime($date));

    return $day . " " . $thaiMonths[$month] . " " . $year;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบวันว่างรถเช่า</title>
    <link rel="stylesheet" href="./styles/check_availability.css">
    <link rel="stylesheet" href="./styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <?php
    if (isset($_SESSION['MemberID'])) {
        require 'user_nav.php';
    } else {
        require 'nav.php';
    }
    ?>
    <div class="container">
        <div class="head-check-availability">
            <div class="title-check-availability">
                ตรวจสอบวันว่างรถเช่า
            </div>
        </div>
        <div class="package-info">
            <h3><?= htmlspecialchars($package['package_name']) ?></h3>
            <p>ระยะเวลา: <?= htmlspecialchars($package['package_date']) ?> วัน</p>
            <p>ราคา: <?= htmlspecialchars($package['package_price']) ?> บาท</p>
        </div>
        <div class="search_date">
            <form action="check_availability.php" method="GET" class="d-flex justify-content-center">
                <input type="hidden" name="package_id" value="<?= htmlspecialchars($package_id) ?>">
                <div class="form-group mx-2">
                    <label for="start_date">วันที่เริ่มเช่า:</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>" required>
                </div>
                <div class="form-group mx-2 align-self-end">
                    <button type="submit" class="btn btn-primary">ตรวจสอบ</button>
                </div>
            </form>
        </div>
        <div class="show-availability">
            <?php
            if (isset($_GET['start_date'])) {
                $start_date = $_GET['start_date'];
                $package_days = $package['package_date'];
                $end_date = date('Y-m-d', strtotime($start_date . " + $package_days days"));

                // Query to find cars and their availability status within the selected date range
                $sql = "SELECT car.*, carrent.carrent_date, carrent.carrent_return FROM car 
                        LEFT JOIN carrent ON car.car_id = carrent.car_id 
                        AND (
                            ('$start_date' BETWEEN carrent.carrent_date AND carrent.carrent_return) 
                            OR ('$end_date' BETWEEN carrent.carrent_date AND carrent.carrent_return) 
                            OR (carrent.carrent_date BETWEEN '$start_date' AND '$end_date')
                            OR (carrent.carrent_return BETWEEN '$start_date' AND '$end_date')
                        )
                        ORDER BY car.car_id";
                $result = mysqli_query($con, $sql);

                if ($result) {
                    echo '<div class="row view-car">';
                    while ($row = mysqli_fetch_array($result)) {
                        $new_url = str_replace("../img/", "./img/", $row['car_picture1']);
                        ?>
                        <div class="col-md-3 mb-4">
                            <div class="card">
                                <img src="<?= $new_url ?>" class="card-img-top" alt="Car Image" style="width: 100%; height: 250px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title text-success"><?= htmlspecialchars($row['car_name']) ?></h5>
                                    <p class="card-text">
                                        ยี่ห้อรถ : <?= htmlspecialchars($row['car_brand']) ?><br>
                                        ราคา : <?= htmlspecialchars($row['car_price']) ?> บาท <br>
                                    </p>
                                    <?php
                                    if ($row['carrent_date']) {
                                        // Car is booked within the selected date range
                                        $carrent_date_thai = convertToThaiDate($row['carrent_date']);
                                        $carrent_return_thai = convertToThaiDate($row['carrent_return']);
                                        echo "<p class='text-danger'>ไม่ว่าง</p>";
                                        echo "<p>ช่วงเวลาที่ถูกจอง: " . htmlspecialchars($carrent_date_thai) . " ถึง " . htmlspecialchars($carrent_return_thai) . "</p>";
                                    } else {
                                        // Car is available within the selected date range
                                        echo "<a href='carrent_withpackage.php?car_id=" . htmlspecialchars($row['car_id']) . "&package_id=" . htmlspecialchars($package_id) . "&start_date=" . htmlspecialchars($start_date) . "&end_date=" . htmlspecialchars($end_date) . "' class='btn btn-outline-success'>เช่ารถ</a>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    echo '</div>';
                } else {
                    echo '<p>ไม่พบรถที่ว่างในช่วงวันที่ที่เลือก</p>';
                }
            }
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
