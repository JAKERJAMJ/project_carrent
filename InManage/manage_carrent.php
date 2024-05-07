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
            <button class="rent" name="rent" id="rent" onclick="CheckDate()">เช็ครถที่ว่าง</button>
        </div>
    </div>
    <div class="body-carrent">
        <div class="title-body">
            การเช่ารถ
        </div>
        <div class="search-container">
            <form action="" method="GET">
                <input class="form-control" type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                <button type="submit" class="btn-s">ค้นหา</button>
                <a href="manage_carrent.php">ล้างค้นหา</a>
            </form>
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

                // ตั้งค่าจำนวนข้อมูลต่อหน้า
                $limit = 15;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // เพิ่มเงื่อนไขสำหรับวันที่เริ่มเช่าถ้ามีการค้นหา
                $whereClause = "";
                if (!empty($_GET['start_date'])) {
                    $startDate = $_GET['start_date'];
                    $whereClause = " WHERE carrent.carrent_date = '$startDate'";
                }

                $sql = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.carrent_date, carrent.carrent_return, carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
                        member.Membername, member.Memberlastname,
                        car.car_name, car.car_price,
                        carrent_status.status_name
                        FROM carrent
                        LEFT JOIN member ON carrent.MemberID = member.MemberID
                        LEFT JOIN car ON carrent.car_id = car.car_id
                        LEFT JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                        $whereClause
                        ORDER BY carrent.carrent_id
                        LIMIT $start, $limit";

                $result = mysqli_query($con, $sql);

                // การคำนวณจำนวนหน้าทั้งหมดต้องแก้ไขเพื่อตรงกับการค้นหา
                $countSql = "SELECT COUNT(*) AS total FROM carrent $whereClause";
                $countResult = mysqli_query($con, $countSql);
                $countRow = mysqli_fetch_assoc($countResult);
                $total = $countRow['total'];
                $pages = ceil($total / $limit);

                mysqli_close($con);
                ?>

                <?php
                $counter = $start + 1; // เริ่มต้นการนับจากข้อมูลของหน้าปัจจุบัน
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['carrent_id'] . "</td>";
                    echo "<td>" . date('d/m/Y H:i:s', strtotime($row['carrent_timestamp'])) . "</td>"; // Use date function with UNIX timestamp
                    echo "<td>" . $row['Membername'] . " " . $row['Memberlastname'] . "</td>";
                    echo "<td>" . $row['car_name'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_date'])) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_return'])) . "</td>";
                    echo "<td>" . $row['carrent_price'] . "</td>";
                    echo "<td><a href='status_carrent.php?id=" . $row['carrent_id'] . "' class='btn btn-warning'>" . $row['status_name'] . "</a></td>";
                    echo "<td>";
                    echo '<button type="button" class="btn btn-warning btn-sm mr-2">แก้ไข</button>';
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '<button type="button" class="btn btn-danger btn-sm mr-2" onclick="cancelCarRental(' . $row['carrent_id'] . ')">ยกเลิก</button>';
                    echo "</td>";
                    echo "</tr>";

                    $counter++;
                }
                mysqli_close($con);
                ?>
            </table>
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++) : ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&start_date=<?php echo $startDate; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
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
        $rentalDate = $_POST['carrent_date'];
        $returnDate = $_POST['carrent_return'];
        $rentalPrice = $_POST['carrent_price'];

        // Check if there's any overlapping rental for the selected car within the specified date range
        if (isOverlappingRental($carID, $rentalDate, $returnDate)) {
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
            // Insert the data into the database
            $sql = "INSERT INTO carrent (car_id, MemberID, driver_status, driver_id, carrent_date, carrent_return, carrent_price, carrent_status_id) 
            VALUES ('$carID', '$memberID', 'ไม่ต้องการคนขับ', '5', '$rentalDate', '$returnDate', '$rentalPrice', '1')";
            if (mysqli_query($con, $sql)) {
                echo "<script>alert('เพิ่มข้อมูลเรียบร้อยแล้ว'); window.location.href = window.location.href;</script>";
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล');</script>";
            }
        }
    }

    // Close the database connection
    mysqli_close($con);

    ?>


    <!-- HTML to display the car availability -->
    <div class="check" id="CheckDate">
        <button type="button" class="close" aria-label="Close" onclick="CloseCheckPopup(event)">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="check-title">
            เช็ควันที่ว่างของรถ
        </div>
        <button type="button" class="ondate">เช็คจากวันที่</button>
        <button type="button" class="oncar">เช็คตารางรถ</button>
        <div class="check-form" id="CheckFormDate">
            <form action="manage_carrent.php" method="post">
                <div class="box">
                    <label for="carrent_date">วันที่เช่า:</label>
                    <input class="form-control" type="date" name="carrent_date" id="carrent_date" required>
                </div>
                <div class="box">
                    <label for="carrent_return">วันที่คืน:</label>
                    <input class="form-control" type="date" name="carrent_return" id="carrent_return" required>
                </div>
                <button type="submit" name="CheckDate">ค้นหา</button>
            </form>
            <?php
            require '../conDB.php';

            // Function to check car availability
            function checkCarAvailability($startDate, $endDate)
            {
                global $con; // Use the global database connection
                $sql = "SELECT car.car_id, car.car_name, 
                   (CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM carrent 
                            WHERE carrent.car_id = car.car_id 
                            AND ((carrent_date <= '$endDate' AND carrent_return >= '$startDate'))
                        ) THEN 'ไม่ว่าง'
                        ELSE 'ว่าง'
                    END) AS availability
            FROM car";
                $result = mysqli_query($con, $sql);
                return $result; // Returns the result of the query
            }

            // Check if the CheckDate form was submitted
            if (isset($_POST['CheckDate'])) {
                $startDate = $_POST['carrent_date'];
                $endDate = $_POST['carrent_return'];
                $availableCars = checkCarAvailability($startDate, $endDate);

                // Show selected dates
                $startDateThai = date('d/m/Y', strtotime($startDate));
                $endDateThai = date('d/m/Y', strtotime($endDate));

                // Show selected dates
                echo '<div class="selected-dates">';
                echo '<p>วันที่เช่า: ' . $startDateThai . '</p>';
                echo '<p>วันที่คืน: ' . $endDateThai . '</p>';
                echo '</div>';
            }
            ?>
            <?php if (isset($availableCars) && $availableCars) : ?>
                <div class="table-view-datecheck">
                    <table class="table table-bordered">
                        <tr>
                            <th>ชื่อรถ</th>
                            <th>สถานะ</th>
                        </tr>
                        <?php while ($car = mysqli_fetch_assoc($availableCars)) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($car['car_name']); ?></td>
                                <td><?php echo $car['availability']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    mysqli_close($con);
    ?>






    <script src="../script/manage_carrent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>