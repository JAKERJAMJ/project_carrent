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
                    <th width="170px">สถานะการเช่า</th>
                    <th width="250px">Action</th>

                </tr>
                <?php
                require '../conDB.php';

                $sql = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.carrent_date, carrent.carrent_return, carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
        member.Membername, member.Memberlastname,
        car.car_name, car.car_price,
        carrent_status.status_name
        FROM carrent
        LEFT JOIN member ON carrent.MemberID = member.MemberID
        LEFT JOIN car ON carrent.car_id = car.car_id
        LEFT JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
        ORDER BY carrent.carrent_id";

                $result = mysqli_query($con, $sql);
                $counter = 1;

                while ($row = mysqli_fetch_array($result)) {
                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['carrent_id'] . "</td>";
                    echo "<td>" . date('d/m/Y H:i:s', strtotime($row['carrent_timestamp'])) . "</td>"; // Use date function with UNIX timestamp
                    echo "<td>" . $row['Membername'] . " " . $row['Memberlastname'] . "</td>";
                    echo "<td>" . $row['car_name'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_date'])) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_return'])) . "</td>";
                    echo "<td>" . $row['carrent_price'] . "</td>";
                    echo "<td><button type='button' class='btn btn-warning'>" . $row['status_name'] . "</button>
    </td>";
                    echo "<td>";
                    echo '<button type="button" class="btn btn-warning btn-sm mr-2">แก้ไข</button>';
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '<button type="button" class="btn btn-danger btn-sm mr-2">ยกเลิก</button>';
                    echo "</td>";
                    echo "</tr>";

                    $counter++;
                }

                mysqli_close($con);
                ?>
            </table>
        </div>
    </div>

    <div class="carrent-popup" id="Search">
        <button type="button" class="close" aria-label="Close" onclick="CloseSearchPopup()">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="search-title">
            ค้นหาสมาชิก
        </div>
        <div class="search-member" id="SearchMember">
            <form action="manage_carrent.php" method="post">
                <div class="box">
                    <label for="Memberpassport">เลขบัตรประจำตัวประชาชน:</label>
                    <input type="text" id="Memberpassport" name="Memberpassport">
                    <button type="submit" class="search" name="search" id="search" onclick="ShowAddDetailPopup()">ค้นหา</button>
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


    <?php if ($search_result) : ?>

        <div class="Adddetail" id="AddDetail">
            <div class="carrent-title">
                การเช่ารถ
            </div>
            <form action="manage_carrent.php" method="post">
                <div class="box">
                    <label for='Membername'>ชื่อผู้เช่า:</label><br>
                    <input class="form-control" type='text' id='Membername' name='Membername' value='<?php echo $search_result['Membername'] . " " . $search_result['Memberlastname']; ?>'><br>
                    <input type='hidden' id='MemberID' name='MemberID' value='<?php echo $search_result['MemberID']; ?>'>
                </div>
                <div class="box">
                    <p>รถที่ต้องการเช่า</p>
                    <select name="car_id" id="car_id" class="form-select" onchange="updateRentalRate()">
                        <option selected>เลือกรถที่ต้องการเช่า</option>
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
                    <input class="form-control" type="date" id="carrent_date" name="carrent_date" onchange="updateRentalRate()">
                </div>
                <div class="box">
                    <label for="ReturnDate">วันที่ส่งคืน:</label><br>
                    <input class="form-control" type="date" id="carrent_return" name="carrent_return" onchange="updateRentalRate()">
                </div>
                <div class="box">
                    <label for="RentalPrice">ราคาเช่า:</label><br>
                    <input class="form-control" type='text' id='carrent_price' name='carrent_price'>
                </div>
                <button type="submit" name="AddRent" id="AddRent">เพิ่ม</button>
                <button class="closecarrent" onclick="CloseCarrentPopup()">ปิด</button>
            </form>
        </div>
    <?php endif; ?>
    <?php
    // Include the database connection file
    require '../conDB.php';

    // Check if the form is submitted
    if (isset($_POST['AddRent'])) {
        // Get the values from the form
        $memberID = $_POST['MemberID'];
        $carID = $_POST['car_id'];
        $rentalDate = $_POST['carrent_date'];
        $returnDate = $_POST['carrent_return'];
        $rentalPrice = $_POST['carrent_price'];

        // Insert the data into the database
        $sql = "INSERT INTO carrent (car_id, MemberID, driver_status, driver_id, carrent_date, carrent_return, carrent_price, carrent_status_id) 
            VALUES ('$carID', '$memberID', 'ไม่ต้องการคนขับ', '5', '$rentalDate', '$returnDate', '$rentalPrice', '1')";
        if (mysqli_query($con, $sql)) {
            echo "<script>alert('เพิ่มข้อมูลเรียบร้อยแล้ว'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล');</script>";
        }
    }

    // Close the database connection
    mysqli_close($con);
    ?>



    <script src="../script/manage_carrent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>