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

$sql_MemberCarrent = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.type_rent, carrent.type_carrent, carrent.driver_status, carrent.driver_id, carrent.carrent_date, carrent.carrent_time, 
                        carrent.carrent_return, carrent.return_time, carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
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

// ดึงข้อมูลคนขับรถ
$drivers = [];
$sql_drivers = "SELECT driver_id, driver_name FROM driver";
$result_drivers = mysqli_query($con, $sql_drivers);
while ($row_driver = mysqli_fetch_assoc($result_drivers)) {
    $drivers[] = $row_driver;
}

// ดึงข้อมูลสถานะการเช่า
$statuses = [];
$sql_statuses = "SELECT carrent_status_id, status_name FROM carrent_status";
$result_statuses = mysqli_query($con, $sql_statuses);
while ($row_status = mysqli_fetch_assoc($result_statuses)) {
    $statuses[$row_status['carrent_status_id']] = $row_status['status_name'];
}

// ตรวจสอบว่าขณะนี้สถานะเป็น 1 หรือไม่
$is_status_1 = ($row['carrent_status_id'] == 1);

// กำหนดค่าจ้างรายวันของคนขับรถ
$driver_daily_wage = 300; // กำหนดเป็นค่าจ้างรายวันของคนขับรถ

// คำนวณจำนวนวันเช่า
$carrent_date = new DateTime($row['carrent_date']);
$carrent_return = new DateTime($row['carrent_return']);
$interval = $carrent_date->diff($carrent_return);
$rental_days = $interval->days + 1; // บวก 1 เพื่อรวมวันที่รับรถด้วย

// คำนวณค่าจ้างคนขับทั้งหมด
$total_driver_cost = $rental_days * $driver_daily_wage;

$rentStartDate = date('d/m/Y', strtotime($row['carrent_date']));
$rentEndDate = date('d/m/Y', strtotime($row['carrent_return']));

$show_success_alert = false;

if (isset($_POST['confirmReceiveCar'])) {
    $id = $_POST['id'];
    $new_status_id = 3;

    $update_sql = "UPDATE carrent SET carrent_status_id = '$new_status_id' WHERE carrent_id = '$id'";

    if (mysqli_query($con, $update_sql)) {
        $show_success_alert = true;
    } else {
        echo "<script>window.location.href = window.location.href;</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="th">

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
    <!-- Success Alert -->
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert" style="display: <?= $show_success_alert ? 'block' : 'none'; ?>;">
        <strong>ยืนยันการรับรถสำเร็จ!</strong> การเช่ารถได้รับการอัพเดตเรียบร้อยแล้ว
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="member-detail">
        <div class="detail">
            <div class="detail-title">
                <p>รายละเอียดการเช่า</p>
            </div>
            <div class="detail-body">
                <form action="status_carrent.php?id=<?= $row['carrent_id'] ?>" method="post" id="statusForm">
                    <div class="box">
                        <label for="carrent_rent">ประเภทบริการ</label>
                        <input class="form-control" type="text" name="type_rent" id="type_rent" value="<?= $row['type_rent']; ?>" readonly>
                    </div>
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
                        <input class="form-control" type="text" name="carrent_date" id="carrent_date" value="<?= $rentStartDate ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="carrent_time">เวลาที่รับรถ</label>
                        <input class="form-control" type="text" name="carrent_time" id="carrent_time" value="<?= $row['carrent_time']; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="carrent_return">วันที่คืน</label>
                        <input class="form-control" type="text" name="carrent_return" id="carrent_return" value="<?= $rentEndDate; ?>" readonly>
                    </div>
                    <div class="box">
                        <label for="return_time">เวลาในการคืนรถ</label>
                        <input class="form-control" type="text" name="return_time" id="return_time" value="<?= $row['return_time']; ?>" readonly>
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
                        <input type="hidden" id="driver_daily_wage" value="<?= $driver_daily_wage; ?>">
                        <input type="hidden" id="rental_days" value="<?= $rental_days; ?>">
                        <input type="hidden" id="total_driver_cost" value="<?= $total_driver_cost; ?>">
                        <div class="center-button">
                            <button type="submit" class="btn btn-primary mt-3" name="qrgen" id="qrgen" disabled>QRcode</button>
                            <button type="submit" class="btn btn-info" name="confirm" id="confirm" <?= $is_status_1 ? '' : 'disabled'; ?>>ยืนยัน</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="detail-right">
            <div class="status-container" id="statusContainer" style="display: <?= $is_status_1 ? 'block' : 'none'; ?>;">
                <div class="title-status">
                    <p>ตรวจสอบการชำระเงิน</p>
                </div>
                <div class="payment">
                    <p class="price-payment">จำนวนเงิน <?php echo $row['carrent_price']; ?> บาท</p>
                    <div class="payment-logo">
                        <img src="../img/PromptPay-logo.png" alt="">
                    </div>
                    <?php
                    require_once '../conDB.php';
                    require_once("../lib/PromptPayQR.php");

                    $PromptPayQR = new PromptPayQR(); // new object
                    $PromptPayQR->size = 4; // Set QR code size to 4
                    $PromptPayQR->id = '0610299843'; // PromptPay ID
                    $PromptPayQR->amount = $row['carrent_price']; // Set amount from car rent price
                    echo '<img src="' . $PromptPayQR->generate() . '">';
                    ?>
                </div>
                <div class="number-payment">
                    <p>หรือ<br>
                        เลขบัญชี 06-587-5-6117 ธนาคารกสิกรไทย<br>
                        ชื่อบัญชี ธนวรรณ คัมภ์บุญยอ
                    </p>
                </div>
            </div>
            <div class="change-status" id="changeStatus" style="display: <?= $is_status_1 ? 'none' : 'block'; ?>;">
                <div class="title-change">
                    <p>สถานะการเช่า</p>
                </div>
                <div class="box-status" id="statusBox">
                    <?php
                    $statusClass = '';
                    $statusName = $Member['status_name'];
                    if ($statusName == 'กำลังดำเนินการ') {
                        $statusClass = 'status-processing';
                    } elseif ($statusName == 'ดำเนินการเช่าเสร็จสิ้น') {
                        $statusClass = 'status-completed';
                    } elseif ($statusName == 'กำลังใช้งาน') {
                        $statusClass = 'status-in-use';
                    } elseif ($statusName == 'ใช้งานเสร็จสิ้น') {
                        $statusClass = 'status-finished';
                    } else {
                        $statusClass = 'status-other';
                    }
                    ?>
                    <div class="status-bar <?= $statusClass; ?>">
                        <?= $statusName; ?>
                    </div>
                </div>
                <div class="box">
                    <?php if ($statusName == 'กำลังใช้งาน') : ?>
                        <button type="button" class="btn btn-warning mt-3" id="returnCar" onclick="ReturnCar()">คืนรถ</button>
                    <?php else : ?>
                        <button type="button" class="btn btn-success mt-3" id="receiveCar" data-bs-toggle="modal" data-bs-target="#confirmModal">รับรถ</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- alert bootstrap -->
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">ยืนยันการรับรถ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>คุณต้องการยืนยันการรับรถดังนี้หรือไม่?</p>
                    <p>ชื่อลูกค้า: <span id="customerName"><?= $Member['Membername'] . ' ' . $Member['Memberlastname']; ?></span></p>
                    <p>ชื่อรถ: <span id="carName"><?= $Member['car_name']; ?></span></p>
                    <p>เช่าตั้งแต่วันที่: <span id="rentStartDate"><?= $rentStartDate; ?></span> ถึงวันที่: <span id="rentEndDate"><?= $rentEndDate; ?></span></p>
                    <!-- ฟอร์มที่ซ่อนสำหรับการยืนยันการรับรถ -->
                    <form id="confirmReceiveCarForm" action="status_carrent.php?id=<?= $row['carrent_id'] ?>" method="post" style="display: none;">
                        <input type="hidden" name="confirmReceiveCar" value="1">
                        <input type="hidden" name="id" value="<?= $row['carrent_id'] ?>">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="confirmReceiveCar">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    if (isset($_POST['qrgen']) && $is_status_1) {
        $updated_price = $_POST['updated_price'];
        $driver_status = $_POST['driver_status'];
        $driver_id = $_POST['driver_id'] ?? null;

        // อัปเดตบันทึกการเช่า
        $update_sql = "UPDATE carrent SET driver_status= '$driver_status', driver_id='$driver_id', carrent_price='$updated_price' WHERE carrent_id=$id";
        if (mysqli_query($con, $update_sql)) {
            echo "<script>window.location.href = window.location.href;</script>";
            exit;
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
    if (isset($_POST['confirm']) && $is_status_1) {
        $updated_price = $_POST['original_price'] ?? null;
        $driver_status = $_POST['driver_status'];
        $driver_id = $_POST['driver_id'] ?? null;

        // ตรวจสอบ type_rent
        $type_rent = $row['type_rent'];

        // อัปเดตบันทึกการเช่า
        $update_sql = "UPDATE carrent SET driver_status='$driver_status', driver_id='$driver_id', carrent_status_id='2' WHERE carrent_id=$id";
        if (mysqli_query($con, $update_sql)) {
            // เพิ่มข้อมูลลงในตาราง payment ถ้า type_rent เป็น 'เช่ารถหน้าร้าน'
            if ($type_rent == 'เช่ารถหน้าร้าน') {
                $insert_sql = "INSERT INTO payment (carrent_id, payment_type, payment_date, payment_time, payment_slip, payment_status, payment_timestamp) VALUES ('$id', 'ชำระเงินหน้าร้าน', CURDATE(), CURTIME(), 'ชำระเงินหน้าร้าน', 'ยังไม่ได้อนุมัติ', CURRENT_TIMESTAMP)";
                if (mysqli_query($con, $insert_sql)) {
                    echo "<script>window.location.href = window.location.href;</script>";
                    exit;
                } else {
                    echo "Error: " . mysqli_error($con);
                }
            } else {
                echo "<script>window.location.href = window.location.href;</script>";
                exit;
            }
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }

    // เปลี่ยนสถานะการเช่ารถ
    if (isset($_POST['confirmReceiveCar'])) {
        $id = $_POST['id'];
        $new_status_id = 3;

        $update_sql = "UPDATE carrent SET carrent_status_id = '$new_status_id' WHERE carrent_id = '$id'";

        if (mysqli_query($con, $update_sql)) {
            echo "<script>document.getElementById('successAlert').style.display = 'block'; setTimeout(function(){ window.location.href = window.location.href; }, 4000);</script>";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
    ?>
    <div class="view-payment">
        <div class="view-payment-title">
            การชำระเงิน
        </div>
        <table class="table">
            <tr>
                <th>Payment ID</th>
                <th>เวลาการชำระเงิน</th>
                <th>รหัสการเช่า</th>
                <th>จำนวนเงิน</th>
                <th>ลักษณะการชำระเงิน</th>
                <th>หลักฐานการชำระเงิน</th>
                <th>Action</th>
            </tr>
            <?php
            $sql_payments = "SELECT payment.payment_id, payment.payment_date, payment.payment_time, payment.carrent_id, carrent.type_rent, carrent.carrent_price, payment.payment_type, payment.payment_slip, payment.payment_status 
                         FROM payment
                         JOIN carrent ON payment.carrent_id = carrent.carrent_id
                         WHERE payment.carrent_id = $id";
            $result_payments = mysqli_query($con, $sql_payments);

            while ($row_payment = mysqli_fetch_assoc($result_payments)) {
                // จัดรูปแบบวันที่และเวลาให้เป็นสไตล์ไทย
                $date = new DateTime($row_payment['payment_date'] . ' ' . $row_payment['payment_time']);
                $fmt = new IntlDateFormatter('th_TH', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Asia/Bangkok', IntlDateFormatter::GREGORIAN, "d MMMM yyyy HH:mm:ss");
                $thai_date = $fmt->format($date);

                // กำหนดคลาสของปุ่มตามสถานะการชำระเงิน
                $button_class = $row_payment['payment_status'] == 'อนุมัติการชำระเงิน' ? 'btn-success' : 'btn-danger';

                echo "<tr>";
                echo "<td>" . $row_payment['payment_id'] . "</td>";
                echo "<td>" . $thai_date . "</td>";
                echo "<td>" . $row_payment['carrent_id'] . "</td>";
                echo "<td>" . $row_payment['carrent_price'] . "</td>";
                echo "<td>" . $row_payment['payment_type'] . "</td>";

                if ($row_payment['type_rent'] == 'เช่ารถหน้าร้าน') {
                    echo "<td>" . $row_payment['payment_slip'] . "</td>";
                } else {
                    echo "<td>" . (!empty($row_payment['payment_slip']) ? '<a href="../uploads/' . $row_payment['payment_slip'] . '" target="_blank">ดูหลักฐาน</a>' : 'ไม่มี') . "</td>";
                }

                echo "<td><button class='btn $button_class payment-status-btn' data-payment-id='" . $row_payment['payment_id'] . "' data-payment-status='" . $row_payment['payment_status'] . "' data-bs-toggle='modal' data-bs-target='#paymentConfirmModal'>" . $row_payment['payment_status'] . "</button></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <?php
    // อัปเดตสถานะการชำระเงิน
    if (isset($_POST['confirmPayment'])) {
        $payment_id = $_POST['payment_id'];
        $update_payment_sql = "UPDATE payment SET payment_status = 'อนุมัติการชำระเงิน' WHERE payment_id = '$payment_id'";

        if (mysqli_query($con, $update_payment_sql)) {
            echo "<script>document.getElementById('successAlert').style.display = 'block'; setTimeout(function(){ window.location.href = window.location.href; }, 4000);</script>";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
    ?>

    <!-- Payment Confirmation Modal -->
    <div class="modal fade" id="paymentConfirmModal" tabindex="-1" aria-labelledby="paymentConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentConfirmModalLabel">ยืนยันการชำระเงิน</h5>
                </div>
                <div class="modal-body">
                    <p>คุณต้องการยืนยันการชำระเงินสำหรับ Payment ID: <span id="paymentId"></span> ใช่หรือไม่?</p>
                </div>
                <form id="confirmPaymentForm" action="status_carrent.php?id=<?= $row['carrent_id'] ?>" method="post" style="display: none;">
                    <input type="hidden" name="confirmPayment" value="1">
                    <input type="hidden" name="payment_id" id="paymentIdInput">
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" id="confirmPaymentButton">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Car Modal -->
    <div class="modal fade" id="ReturnCarModal" tabindex="-1" aria-labelledby="ReturnCarModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ReturnCarModalLabel">การคืนรถ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="carrent_id" class="form-label">รหัสการเช่า</label>
                        <input class="form-control" type="text" name="carrent_id" id="carrent_id" value="<?= $row['carrent_id'] ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="Membername" class="form-label">ชื่อผู้เช่า</label>
                        <input class="form-control" type="text" name="Membername" id="Membername" value="<?= $Member['Membername'] . ' ' . $Member['Memberlastname']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="carrent_return" class="form-label">วันที่ต้องคืน</label>
                        <input class="form-control" type="text" name="carrent_return" id="carrent_return" value="<?= $rentEndDate; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="return_time" class="form-label">เวลาที่ต้องคืนรถ</label>
                        <input class="form-control" type="text" name="return_time" id="return_time" value="<?= $row['return_time']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="display_date_return" class="form-label">วันที่คืน</label>
                        <input class="form-control" type="text" name="display_date_return" id="display_date_return" readonly>
                        <input type="hidden" name="date_return" id="date_return">
                        <button type="button" onclick="setToday()" class="btn btn-outline-secondary btn-sm mt-2">Set Today</button>
                    </div>
                    <div class="mb-3">
                        <label for="display_time_return" class="form-label">เวลาที่คืน</label>
                        <input class="form-control" type="text" name="display_time_return" id="display_time_return" readonly>
                        <input type="hidden" name="time_return" id="time_return">
                        <button type="button" onclick="setTimeNow()" class="btn btn-outline-secondary btn-sm mt-2">Set Time</button>
                    </div>
                    <div class="mb-3">
                        <label for="return_price" class="form-label">ค่าบริการส่วนเกิน</label>
                        <input class="form-control" type="text" name="return_price" id="return_price" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../script/status_carrent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>