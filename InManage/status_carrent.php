<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}
require '../conDB.php';
$id = $_GET['id'];
$sql = "SELECT * FROM carrent WHERE carrent_id = $id";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$sql_MemberCarrent = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.type_carrent, carrent.driver_status, carrent.driver_id, carrent.carrent_date, carrent.carrent_return, carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
                        member.Membername, member.Memberlastname,
                        car.car_name, car.car_price,
                        driver.driver_name, 
                        carrent_status.status_name
                        FROM carrent
                        LEFT JOIN driver ON carrent.driver_id = driver.driver_id
                        LEFT JOIN member ON carrent.MemberID = member.MemberID
                        LEFT JOIN car ON carrent.car_id = car.car_id
                        LEFT JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                        WHERE carrent.carrent_id = $id";
$result_MemberCarrent = mysqli_query($con, $sql_MemberCarrent);
$Member = mysqli_fetch_assoc($result_MemberCarrent);

$enum_values = [];
$sql_enum = "SHOW COLUMNS FROM carrent LIKE 'driver_status'";
$result_enum = mysqli_query($con, $sql_enum);
$row_enum = mysqli_fetch_assoc($result_enum);
preg_match("/^enum\(\'(.*)\'\)$/", $row_enum['Type'], $matches);
$enum_values = explode("','", $matches[1]);

// Fetch drivers
$drivers = [];
$sql_drivers = "SELECT driver_id, driver_name FROM driver";
$result_drivers = mysqli_query($con, $sql_drivers);
while ($row_driver = mysqli_fetch_assoc($result_drivers)) {
    $drivers[] = $row_driver;
}

// Fetch carrent statuses
$statuses = [];
$sql_statuses = "SELECT carrent_status_id, status_name FROM carrent_status";
$result_statuses = mysqli_query($con, $sql_statuses);
while ($row_status = mysqli_fetch_assoc($result_statuses)) {
    $statuses[] = $row_status;
}

// Check if the status is 1
$is_status_1 = ($row['carrent_status_id'] == 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการเช่า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/status_carrent.css">
    <link rel="stylesheet" href="../styles/style.css">
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
    <a href="manage_carrent.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="member-detail">
        <div class="detail">
            <div class="detail-title">
                <p>รายละเอียดการเช่า</p>
            </div>
            <div class="detail-body">
                <form action="status_carrent.php?id=<?= $row['carrent_id'] ?>" method="post" id="statusForm">
                    <div class="box">
                        <label for="carrent_id">รหัสการเช่า</label>
                        <input class="form-control" type="text" name="carrent_id" id="carrent_id" value="<?= $row['carrent_id']; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="Membername">ชื่อผู้เช่า</label>
                        <input class="form-control" type="text" name="Membername" id="Membername" value="<?= $Member['Membername'] . ' ' . $Member['Memberlastname']; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="car_name">รถที่ต้องการเช่า</label>
                        <input class="form-control" type="text" name="car_name" id="car_name" value="<?= $Member['car_name']; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="carrent_date">วันที่เช่า</label>
                        <input class="form-control" type="text" name="carrent_date" id="carrent_date" value="<?= $row['carrent_date']; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="carrent_date">วันที่คืน</label>
                        <input class="form-control" type="text" name="carrent_date" id="carrent_date" value="<?= $row['carrent_return']; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="driver_status">ต้องการคนขับหรือไม่</label>
                        <select class="form-select" name="driver_status" id="driver_status" <?= $is_status_1 ? '' : 'disabled'; ?>>
                            <option selected><?= $row['driver_status']; ?></option>
                            <?php foreach ($enum_values as $value) : ?>
                                <option value="<?= $value; ?>" <?= ($row['driver_status'] == $value) ? 'selected' : ''; ?>><?= $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="box" id="driverSelectBox" style="display: <?= $row['driver_status'] == 'ต้องการคนขับ' ? 'block' : 'none'; ?>;">
                        <label for="driver_id">เลือกคนขับ</label>
                        <select class="form-select" name="driver_id" id="driver_id" <?= $is_status_1 ? '' : 'disabled'; ?>>
                            <option value="">เลือกคนขับ</option>
                            <?php foreach ($drivers as $driver) : ?>
                                <option value="<?= $driver['driver_id']; ?>" <?= ($row['driver_id'] == $driver['driver_id']) ? 'selected' : ''; ?>><?= $driver['driver_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="box">
                        <label for="price">ราคา</label>
                        <?php if (!$is_status_1) : ?>
                            <input class="form-control" type="text" id="original_price" value="<?= $row['carrent_price']; ?>" readonly>
                            <input type="hidden" name="updated_price" id="updated_price" value="<?= $row['carrent_price']; ?>">
                        <?php else : ?>
                            <input type="hidden" id="original_price" value="<?= $row['carrent_price']; ?>">
                            <input class="form-control" type="text" name="updated_price" id="updated_price" value="<?= $row['carrent_price']; ?>" readonly>
                        <?php endif; ?>
                    </div>
                    <div class="box">
                        <div class="center-button">
                            <button type="submit" class="btn btn-info" name="confirm" id="confirm" <?= $is_status_1 ? '' : 'disabled'; ?>>ยืนยัน</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="detail-right">
            <div class="status-container">
                <div class="title-status">
                    <p>ตรวจสอบการชำระเงิน</p>
                </div>
                <div class="payment">
                    <p class="price-payment">จำนวนเงิน <?php echo $row['carrent_price']; ?>บาท</p>
                    <div class="payment-logo">
                        <img src="../img/PromptPay-logo.png" alt="">
                    </div>
                    <?php if (isset($qr_code)) : ?>
                        <img src="<?= $qr_code; ?>">
                    <?php else : ?>
                        <?php
                        require_once '../conDB.php';
                        require_once("../lib/PromptPayQR.php");

                        $PromptPayQR = new PromptPayQR(); // new object
                        $PromptPayQR->size = 4; // Set QR code size to 4
                        $PromptPayQR->id = '0610299843'; // PromptPay ID
                        $PromptPayQR->amount = $row['carrent_price']; // Set amount from car rent price
                        echo '<img src="' . $PromptPayQR->generate() . '">';
                        ?>
                    <?php endif; ?>
                </div>
                <div class="number-payment">
                    <p>หรือ<br>
                        เลขบัญชี 06-587-5-6117 ธนาคารกสิกรไทย<br>
                        ชื่อบัญชี ธนวรรณ คัมภ์บุญยอ
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['confirm']) && $is_status_1) {
        $updated_price = $_POST['updated_price'];
        $driver_status = $_POST['driver_status'];
        $driver_id = $_POST['driver_id'] ?? null;
    
        // Update the carrent record
        $update_sql = "UPDATE carrent SET driver_status='$driver_status', driver_id='$driver_id', carrent_price='$updated_price', carrent_status_id = '2' WHERE carrent_id=$id";
        if (mysqli_query($con, $update_sql)) {
            // Insert data into another table
            $insert_sql = "INSERT INTO payment (carrent_id, payment_type, payment_slip) VALUES ('$id', 'ชำระเงินหน้าร้าน', 'ชำระเงินหน้าร้าน')";
            if (mysqli_query($con, $insert_sql)) {
                echo "<script>window.location.href = window.location.href;</script>";
                exit;
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
    ?>

    <script src="../script/status_carrent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>