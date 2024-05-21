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
        <div class="car-name"><?= $row['car_name'] ?></div>
        <img src="<?= $row['car_picture1'] ?>" alt="รูปภาพรถ">
        <div class="btn-search">
            <button type="button" class="btn btn-primary" onclick="OpenSearch()">ค้นหาผู้เช่า</button>
        </div>
    </div>
    <div class="carrent-popup" id="Search">
        <button type="button" class="close" aria-label="Close" onclick="CloseSearchPopup()">
            <span aria-hidden="true">X</span>
        </button>
        <div class="search-title">
            ค้นหาสมาชิก
        </div>
        <div class="search-member" id="SearchMember">
            <form action="booking_page.php?id=<?php echo $row['car_id']; ?>" method="post">
                <div class="box">
                    <label for="Memberpassport">เลขบัตรประจำตัวประชาชน:</label>
                    <input type="text" id="Memberpassport" name="Memberpassport">
                    <button type="submit" class="btn btn-primary" name="search" id="search" onclick="searchMember('<?php echo $row['car_id']; ?>')">ค้นหา</button>
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
        <form action="booking_page.php?id=<?php echo $row['car_id']; ?>" method="post">
            <div class="box">
                <label for="car_name">ชื่อรถ</label>
                <input class="form-control" type="text" name="car_name" id="car_name" value="<?= $row['car_name'] ?>">
                <input class="form-control" type="hidden" name="car_id" id="car_id" value="<?= $row['car_id'] ?>">
                <input class="form-control" type="hidden" name="car_price" id="car_price" value="<?= $row['car_price'] ?>">
            </div>
            <div class="box">
                <label for='Membername'>ชื่อผู้เช่า</label><br>
                <input class="form-control" type='text' id='Membername' name='Membername' value='<?php echo $search_result['Membername'] . " " . $search_result['Memberlastname']; ?>'><br>
                <input type='hidden' id='MemberID' name='MemberID' value='<?php echo $search_result['MemberID']; ?>'>
            </div>
            <div class="box">
                <label for="RentalDate">วันที่ต้องการเช่า:</label><br>
                <input class="form-control" type="date" id="RentalDate" name="RentalDate" onchange="calculateTotal()">
            </div>
            <div class="box">
                <label for="ReturnDate">วันที่ส่งคืน:</label><br>
                <input class="form-control" type="date" id="ReturnDate" name="ReturnDate" onchange="calculateTotal()">
            </div>
            <div class="box">
                <label for="RentalPrice">ราคาเช่า:</label><br>
                <input class="form-control" type='text' id='RentalPrice' name='RentalPrice'>
            </div>
            <div class="box">
                <button class="btn btn-primary" type="submit" name="AddRent" id="AddRent">เพิ่ม</button>
            </div>
        </form>
    </div>
    <?php
    // Include the database connection file
    require '../conDB.php';

    // Function to check if there's any overlapping rental for a given car within the specified date range
    function isOverlappingRental($carID, $rentalDate, $returnDate)
    {
        global $con;
        $sql = "SELECT * FROM carrent WHERE car_id = '$carID' AND 
            ((carrent_date BETWEEN '$rentalDate' AND '$returnDate') OR 
            (carrent_return BETWEEN '$rentalDate' AND '$returnDate'))";
        $result = mysqli_query($con, $sql);
        return mysqli_num_rows($result) > 0;
    }

    // Check if the form is submitted
    if (isset($_POST['AddRent'])) {
        // Get the values from the form
        $memberID = $_POST['MemberID'];
        $carID = $_POST['car_id'];
        $rentalDate = $_POST['RentalDate'];
        $returnDate = $_POST['ReturnDate'];
        $rentalPrice = $_POST['RentalPrice'];

        // Check if there's any overlapping rental for the selected car within the specified date range
        if (isOverlappingRental($carID, $rentalDate, $returnDate)) {
            // Retrieve overlapping rental details for alert message
            $sql = "SELECT car.car_name, carrent_date, carrent_return FROM carrent
                INNER JOIN car ON carrent.car_id = car.car_id
                WHERE carrent.car_id = '$carID' AND 
                ((carrent_date BETWEEN '$rentalDate' AND '$returnDate') OR 
                (carrent_return BETWEEN '$rentalDate' AND '$returnDate'))";
            $result = mysqli_query($con, $sql);
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $carName = $row['car_name'];
                $overlappingDates = "$carName มีกำหนดการเช่าในช่วงเวลาตั้งแต่ " . date('d/m/Y', strtotime($row['carrent_date'])) . " ถึง " . date('d/m/Y', strtotime($row['carrent_return'])) . "\\n";
                echo "<script>alert('มีการเช่ารถในช่วงเวลาที่กำหนดแล้ว\\n$overlappingDates กรุณาเลือกวันหรือรถคันอื่น');</script>";
                echo "<script>window.location.href = window.location.href;</script>";
            }
        } else {
            // Insert the data into the database if there's no overlapping rental
            $sql = "INSERT INTO carrent (car_id, MemberID, type_carrent, driver_status, driver_id, carrent_date, carrent_return, carrent_price, carrent_status_id) 
            VALUES ('$carID', '$memberID', 'เช่ารถส่วนตัว', 'ไม่ต้องการคนขับ', '5', '$rentalDate', '$returnDate', '$rentalPrice', '1')";
            if (mysqli_query($con, $sql)) {
                echo "<script>alert('เพิ่มข้อมูลเรียบร้อยแล้ว'); window.location.href = 'manage_carrent.php';</script>";
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล');</script>";
            }
        }
    }
    ?>

    <?php
    // Close the database connection
    mysqli_close($con);
    ?>



    <script src="../script/booking.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>