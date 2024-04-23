<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

if (isset($_POST['search'])) {
    $Memberpassport = $_POST['Memberpassport'];
    $sql = "SELECT * FROM member WHERE Memberpassport = '$Memberpassport'";
    $result = mysqli_query($con, $sql);

    $response = ['found' => false];
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $response = [
            'found' => true,
            'memberName' => $row['Membername'] . " " . $row['Memberlastname'],
            'memberID' => $row['MemberID']
        ];
    }
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจัดการการเช่ารถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/manage_carrent.css">
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

    <div class="head-carrent">
        <div class="title-carrent">
            <p class="title">การจัดการการเช่ารถ</p>
        </div>
        <div class="btn-carrent">
            <button class="carrent">การเช่ารถ</button>
            <button class="returncar">การคืนรถ</button>
        </div>
        <div class="add-carrent">
            <button class="rent" name="rent" id="rent" onclick="Carrent()">เช่า</button>
        </div>
    </div>
    <div class="body-carrent">
        <div class="title-body">
            การเช่ารถ
        </div>
        <div class="table-view-carrent">
            <table class="table table-bordered">
                <tr>
                    <th width="90px">ลำดับที่</th>
                    <th width="100px">รหัสการเช่า</th>
                    <th width="100px">วันที่เช่า</th>
                    <th width="400px">ชื่อผู้เช่า</th>
                    <th width="130px">รถที่เช่า</th>
                    <th width="100px">วันที่เริ่มเช่า</th>
                    <th width="120px">วันที่คืนรถ</th>
                    <th width="170px">จำนวนเงิน</th>
                    <th width="170px">การชำระเงิน</th>
                    <th width="170px">สถานะการเช่า</th>
                    <th width="200px">Action</th>

                </tr>
            </table>
        </div>
    </div>



    <div class="carrent-popup" id="AddRent">
        <div class="carrent-title">
            การเช่ารถ
        </div>
        <div class="search-member" id="SearchMember">
            <form action="manage_carrent.php" method="post">
                <div class="box">
                    <label for="Memberpassport">เลขบัตรประจำตัวประชาชน:</label>
                    <input type="text" id="Memberpassport" name="Memberpassport">
                    <button type="button" class="search" onclick="searchMember()">ค้นหา</button>
                </div>
            </form>
        </div>

        <div class="detail-rent" id="DetailRent">
            <form action="manage_carrent.php" method="post">
                <div class="box">
                    <label for='Membername'>ชื่อผู้เช่า:</label><br>
                    <input class="form-control" type='text' id='Membername' name='Membername' value='<?php echo $memberName; ?>'><br>
                    <input type='hidden' id='MemberID' name='MemberID' value='<?php echo $memberID; ?>'>
                </div>
                <div class="box">
                    <p>รถที่ต้องการเช่า</p>
                    <select name="car_id" id="car_id" class="form-select" onchange="updateRentalRate()">
                        <?php
                        require '../conDB.php';
                        $sql = "SELECT * FROM car ORDER BY car_id";
                        $result = mysqli_query($con, $sql);
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row['car_id'] . "' data-rate='" . $row['car_price'] . "'>" . $row['car_name'] . "</option>";
                        }
                        mysqli_close($con);
                        ?>
                    </select>
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
                    <input class="form-control" type='text' id='RentalPrice' name='RentalPrice' readonly>
                </div>
                <button>เพิ่ม</button>
                <button class="closecarrent" onclick="CloseCarrent()">ปิด</button>
            </form>
        </div>

    </div>

    <script src="../script/manage_carrent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>