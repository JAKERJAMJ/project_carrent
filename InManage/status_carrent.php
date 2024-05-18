<?php
session_start();

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
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"> <!-- เพิ่ม class dropdown-menu-end เพื่อจัดให้ dropdown อยู่ด้านขวาของ Navbar -->
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="status-container">
        <div class="title-status">
            <p>ตรวจสอบการชำระเงิน</p>
        </div>
        <div class="payment">
            <?php
            require '../conDB.php';
            $id = $_GET['id'];
            $sql = "SELECT * FROM carrent WHERE carrent_id = $id";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($result);
            ?>
            <p class="price-payment">จำนวนเงิน <?php echo $row['carrent_price']; ?> บาท</p>

            <?php
            require_once '../conDB.php';
            require_once("../lib/PromptPayQR.php");

            $PromptPayQR = new PromptPayQR(); // new object
            $PromptPayQR->size = 4; // Set QR code size to 8
            $PromptPayQR->id = '0610299843'; // PromptPay ID
            $PromptPayQR->amount = $row['carrent_price']; // Set amount from car rent price
            echo '<img src="' . $PromptPayQR->generate() . '">';
            ?>
            <div class="number-payment">
                <p>หรือ<br>
                    เลขบัญชี 06-587-5-6117 ธนาคารกสิกรไทย<br>
                    ชื่อบัญชี ธนวรรณ คัมภ์บุญยอ
                </p>
            </div>
            <div class="file-slip">
                <form action="" method="post">
                    <label for="">กรุณาใส่ไฟล์รูปภาพ</label>
                    <input class="form-control" type="file" name="payment_slip" id="">
                    <button type="submit" class="btn btn-primary">เพิ่ม</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>