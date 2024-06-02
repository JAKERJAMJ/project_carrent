<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจัดการการเช่ารถ - รถที่กำลังใช้งาน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/manage_carrent.css">
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

    <div class="head-carrent">
        <a href="inside_management.php" class="btn btn-outline-dark" style="align-self: flex-start;">กลับ</a>
        <div class="title-carrent">
            <p class="title">รถที่กำลังใช้งาน</p>
        </div>
        <div class="btn-carrent">
            <button class="carrent" onclick="window.location.href='manage_carrent.php'">การเช่ารถ</button>
            <button class="btn btn-outline-info" onclick="window.location.href='carrent_use.php'">รถที่กำลังใช้งาน</button>
            <button class="btn-history" onclick="window.location.href='carrent_history.php'">ประวัติการเช่ารถ</button>
        </div>
        <div class="add-carrent">
            <a href="check_carrent.php" class="rent" name="rent" id="rent">เช็ครถที่ว่าง</a>
        </div>
    </div>
    <div class="body-carrent">
        <div class="title-body">
            รถที่กำลังใช้งาน
        </div>
        <div class="search-container">
            <form action="" method="GET">
                <input class="form-control" type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                <select class="form-select" name="carrent_status_id" disabled>
                    <option value="">สถานะ: กำลังใช้งาน</option>
                </select>
                <button type="submit" class="btn-s">ค้นหา</button>
                <a href="carrent_use.php">ล้างค้นหา</a>
            </form>
        </div>

        <div class="table-view-carrent">
            <table class="table table-bordered">
                <tr>
                    <th width="90px">ลำดับที่</th>
                    <th width="100px">ประเภท</th>
                    <th width="100px">ประเภทการเช่า</th>
                    <th width="100px">รหัสการเช่า</th>
                    <th width="400px">ชื่อผู้เช่า</th>
                    <th width="130px">รถที่เช่า</th>
                    <th width="90px">วันที่เริ่มเช่า</th>
                    <th width="100px">เวลารับรถ</th>
                    <th width="90px">วันที่คืนรถ</th>
                    <th width="100px">เวลาคืนรถ</th>
                    <th width="170px">จำนวนเงิน</th>
                    <th width="170px">สถานะการเช่า</th>
                    <th width="250px">Action</th>
                </tr>
                <?php

                // Set the number of records per page
                $limit = 15;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Add conditions for start date and carrent status
                $whereClause = "WHERE carrent.carrent_status = 'กำลังใช้งาน'"; // Only show status = กำลังใช้งาน
                if (!empty($_GET['start_date'])) {
                    $startDate = $_GET['start_date'];
                    $whereClause .= " AND carrent.carrent_date = '$startDate'";
                }

                $sql = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.type_rent, carrent.type_carrent, carrent.carrent_date, carrent.carrent_time, carrent.carrent_return, carrent.return_time,
                        carrent.carrent_price, carrent.carrent_status, carrent.carrent_timestamp,
                        member.Membername, member.Memberlastname,
                        car.car_name, car.car_price
                        FROM carrent
                        LEFT JOIN member ON carrent.MemberID = member.MemberID
                        LEFT JOIN car ON carrent.car_id = car.car_id
                        $whereClause
                        ORDER BY carrent.carrent_timestamp DESC
                        LIMIT $start, $limit";

                $result = mysqli_query($con, $sql);

                // Count the total number of records with the applied filters
                $countSql = "SELECT COUNT(*) AS total FROM carrent $whereClause";
                $countResult = mysqli_query($con, $countSql);
                $countRow = mysqli_fetch_assoc($countResult);
                $total = $countRow['total'];
                $pages = ceil($total / $limit);

                $counter = $start + 1; // Start counting from the current page
                while ($row = mysqli_fetch_assoc($result)) {
                    $status_class = '';
                    switch ($row['carrent_status']) {
                        case 'กำลังดำเนินการเช่า':
                            $status_class = 'btn-warning';
                            break;
                        case 'ดำเนินการเช่าเสร็จสิ้น':
                            $status_class = 'btn-success';
                            break;
                        case 'กำลังใช้งาน':
                            $status_class = 'btn-info';
                            break;
                        case 'ใช้งานเสร็จสิ้น':
                            $status_class = 'btn-secondary';
                            break;
                        default:
                            $status_class = 'btn-warning'; // Default class if none match
                            break;
                    }
                    $status_link = ($row['type_rent'] == 'เช่ารถแบบออนไลน์') ? 'status_carrent_online.php' : 'status_carrent.php';

                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['type_rent'] . "</td>";
                    echo "<td>" . $row['type_carrent'] . "</td>";
                    echo "<td>" . $row['carrent_id'] . "</td>";
                    echo "<td>" . $row['Membername'] . " " . $row['Memberlastname'] . "</td>";
                    echo "<td>" . $row['car_name'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_date'])) . "</td>";
                    echo "<td>" . $row['carrent_time'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_return'])) . "</td>";
                    echo "<td>" . $row['return_time'] . "</td>";
                    echo "<td>" . $row['carrent_price'] . "</td>";
                    echo "<td><a href='$status_link?id=" . $row['carrent_id'] . "' class='btn " . $status_class . " btn-sm'>" . $row['carrent_status'] . "</a></td>";
                    echo "<td>";
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
                            <a class="page-link" href="?page=<?php echo $i; ?>&start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
    <script src="../script/manage_carrent.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>
