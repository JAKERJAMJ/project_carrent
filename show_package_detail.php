<?php
session_start();
require 'conDB.php';

$package_id = $_GET['id'];
$sql = "SELECT * FROM package WHERE package_id = '$package_id'";
$result = mysqli_query($con, $sql);
$package = mysqli_fetch_assoc($result);

// Fetch tourist spots associated with the package
$tourist_sql = "SELECT * FROM tourist WHERE package_id = '$package_id'";
$tourist_result = mysqli_query($con, $tourist_sql);

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดแพ็คเกจท่องเที่ยว</title>
    <link rel="stylesheet" href="./styles/show_package_detail.css">
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
    <div class="container mt-5">
        <div class="row mb-4">
            <div class="col-12 text-end">
                <a href="show_packet.php" class="btn btn-secondary">กลับ</a>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-4">
                <img src="<?= str_replace("../img/", "./img/", $package['package_picture']) ?>" class="img-fluid rounded" alt="Package Image">
            </div>
            <div class="col-lg-6 mb-4">
                <h2 class="text-success"><?= htmlspecialchars($package['package_name']) ?></h2>
                <p class="text-muted">ที่พัก: <?= htmlspecialchars($package['package_hotel']) ?></p>
                <p class="text-muted">ราคา: <?= htmlspecialchars($package['package_price']) ?> บาท</p>
                <p class="text-muted">ระยะเวลา: <?= htmlspecialchars($package['package_date']) ?> วัน</p>
                <p><?= nl2br(htmlspecialchars($package['package_detail'])) ?></p>
                <a href="check_availability.php?package_id=<?= htmlspecialchars($package['package_id']) ?>" class="btn btn-warning">เช่าแพ็คเกจ</a>
            </div>
        </div>
        <div class="row mt-5">
            <h3 class="text-success mb-4">สถานที่ท่องเที่ยวแนะนำในแพ็คเกจ</h3>
            <?php while ($tourist = mysqli_fetch_assoc($tourist_result)) { ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="./img/tourist/<?= $tourist['tourist_picture'] ?>" class="card-img-top" alt="Tourist Image" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($tourist['tourist_name']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($tourist['tourist_detail'])) ?></p>
                        <a href="<?= htmlspecialchars($tourist['tourist_link']) ?>" target="_blank" class="btn btn-outline-primary">พิกัดสถานที่ท่องเที่ยว</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
