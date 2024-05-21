<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';

$id = $_GET['id'];
$sql = "SELECT * FROM car WHERE car_id = '$id'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช่ารถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/booking.css">
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
    <a href="check_carrent.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="head-booking">
        <img src="<?= $row['car_picture1'] ?>" alt="รูปภาพรถ">
    </div>
    <div class="carrent-popup" id="Search">
        <button type="button" class="close" aria-label="Close" onclick="CloseSearchPopup()">
            <span aria-hidden="true">X</span>
        </button>
        <div class="search-title">
            ค้นหาสมาชิก
        </div>
        <div class="search-member" id="SearchMember">
            <form action="booking_page.php?id=<?php echo $row['car_id']; ?>" method="post"> <!-- เพิ่ม id ใน URL -->
                <div class="box">
                    <label for="Memberpassport">เลขบัตรประจำตัวประชาชน:</label>
                    <input type="text" id="Memberpassport" name="Memberpassport">
                    <button type="submit" class="search" name="search" id="search" onclick="searchMember('<?php echo $row['car_id']; ?>')">ค้นหา</button>
                </div>
            </form>

        </div>
    </div>
    <?php
    require '../conDB.php';
    $search_result = null; // เพิ่มบรรทัดนี้เพื่อกำหนดค่าเริ่มต้นให้ $search_result เป็น null
    if (isset($_POST['search'])) {
        $Memberpassport = $_POST['Memberpassport'];
        $sql = "SELECT * FROM member WHERE Memberpassport = '$Memberpassport'";
        $result = mysqli_query($con, $sql);
        if ($result) {
            // เมื่อค้นหาเจอข้อมูล ให้กำหนดค่าให้กับ $search_result
            $search_result = mysqli_fetch_assoc($result);
            if ($search_result) {
                echo "
                <script>
                    document.getElementById('Membername').value = '" . $search_result['Membername'] . " " . $search_result['Memberlastname'] . "';
                    document.getElementById('MemberID').value = '" . $search_result['MemberID'] . "';
                </script>
            ";
            } else {
                echo "<script>alert('ไม่พบข้อมูลสมาชิก');</script>";
            }
        }
    }
    ?>
    <div class="booking-body">
        <form action="booking_page.php?id=<?php $row['car_id']; ?>" method="post">
            <div class="box">
                <label for="car_name">ชื่อรถ</label>
                <input class="form-control" type="text" name="car_name" id="car_name" value="<?= $row['car_name'] ?>">
                <input class="form-control" type="hidden" name="car_id" id="car_id" value="<?= $row['car_id'] ?>">
            </div>
            <div class="box">
                <label for='Membername'>ชื่อผู้เช่า</label><br>
                <input class="form-control" type='text' id='Membername' name='Membername' value='<?php echo $search_result['Membername'] . " " . $search_result['Memberlastname']; ?>'><br>
                <input type='hidden' id='MemberID' name='MemberID' value='<?php echo $search_result['MemberID']; ?>'>
                <button type="button" class="btn btn-primary" onclick="OpenSearch()">ค้นหาผู้เช่า</button>
            </div>
            <div class="box">
                <label for="RentalDate">วันที่ต้องการเช่า:</label><br>
                <input class="form-control" type="date" id="carrent_date" name="carrent_date" value="<?php echo $startDate; ?>" onchange="updateRentalRate()">
            </div>
            <div class="box">
                <label for="ReturnDate">วันที่ส่งคืน:</label><br>
                <input class="form-control" type="date" id="carrent_return" name="carrent_return" value="<?php echo $endDate; ?>" onchange="updateRentalRate()">
            </div>
            <div class="box">
                <label for="RentalPrice">ราคาเช่า:</label><br>
                <input class="form-control" type='text' id='carrent_price' name='carrent_price'>
            </div>
        </form>
    </div>
    <script src="../script/booking.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>