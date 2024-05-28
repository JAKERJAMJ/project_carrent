<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่ โดยตรวจสอบ session variable
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

// เชื่อมต่อฐานข้อมูล
require_once '../conDB.php';

// ดึงข้อมูลสรุป
$totalCarsQuery = "SELECT COUNT(*) AS total_cars FROM car";
$totalCarsResult = mysqli_query($con, $totalCarsQuery);
$totalCars = mysqli_fetch_assoc($totalCarsResult)['total_cars'];

$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM member";
$totalMembersResult = mysqli_query($con, $totalMembersQuery);
$totalMembers = mysqli_fetch_assoc($totalMembersResult)['total_members'];

$totalRentalsQuery = "SELECT COUNT(*) AS total_rentals FROM carrent";
$totalRentalsResult = mysqli_query($con, $totalRentalsQuery);
$totalRentals = mysqli_fetch_assoc($totalRentalsResult)['total_rentals'];

$totalRevenueQuery = "SELECT SUM(carrent_price) AS total_revenue FROM carrent";
$totalRevenueResult = mysqli_query($con, $totalRevenueQuery);
$totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['total_revenue'];

// ดึงปีและเดือนจากฟอร์ม
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

// ฟังก์ชันเพื่อดึงข้อมูลการเช่ารถ
function getRentalsByDay($con, $selectedYear, $selectedMonth)
{
    $rentalsByDayQuery = "SELECT carrent.carrent_date, carrent.carrent_id, car.car_name, carrent.type_carrent, member.Membername, member.Memberlastname, carrent_status.status_name
                          FROM carrent
                          JOIN car ON carrent.car_id = car.car_id
                          JOIN member ON carrent.MemberID = member.MemberID
                          JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                          WHERE YEAR(carrent.carrent_date) = '$selectedYear' AND MONTH(carrent.carrent_date) = '$selectedMonth'
                          AND carrent.type_carrent = 'เช่ารถส่วนตัว'
                          ORDER BY carrent.carrent_date ASC";
    $rentalsByDayResult = mysqli_query($con, $rentalsByDayQuery);
    $rentalsByDay = [];
    while ($row = mysqli_fetch_assoc($rentalsByDayResult)) {
        $rentalsByDay[] = $row;
    }
    return $rentalsByDay;
}

// ฟังก์ชันเพื่อดึงข้อมูลการซ่อมรถ
function getCarFixes($con, $selectedYear, $selectedMonth)
{
    $carFixQuery = "SELECT car_fix.fix_date, car_fix.fix_id, car.car_name, car_fix.detail, car_fix.price
                    FROM car_fix
                    JOIN car ON car_fix.car_id = car.car_id
                    WHERE YEAR(car_fix.fix_date) = '$selectedYear' AND MONTH(car_fix.fix_date) = '$selectedMonth'
                    ORDER BY car_fix.fix_date ASC";
    $carFixResult = mysqli_query($con, $carFixQuery);
    $carFixes = [];
    while ($row = mysqli_fetch_assoc($carFixResult)) {
        $carFixes[] = $row;
    }
    return $carFixes;
}

// ฟังก์ชันเพื่อดึงข้อมูลการคืนรถ
function getCarReturns($con, $selectedYear, $selectedMonth)
{
    $carReturnQuery = "SELECT return_car.return_date, return_car.return_id, car.car_name, member.Membername, member.Memberlastname
                       FROM return_car
                       JOIN car ON return_car.car_id = car.car_id
                       JOIN member ON return_car.MemberID = member.MemberID
                       WHERE YEAR(return_car.return_date) = '$selectedYear' AND MONTH(return_car.return_date) = '$selectedMonth'
                       ORDER BY return_car.return_date ASC";
    $carReturnResult = mysqli_query($con, $carReturnQuery);
    $carReturns = [];
    while ($row = mysqli_fetch_assoc($carReturnResult)) {
        $carReturns[] = $row;
    }
    return $carReturns;
}

// ดึงข้อมูลที่จะแสดงผล
$rentalsByDay = getRentalsByDay($con, $selectedYear, $selectedMonth);
$carFixes = getCarFixes($con, $selectedYear, $selectedMonth);
$carReturns = getCarReturns($con, $selectedYear, $selectedMonth);

// ดึงปีทั้งหมดที่มีข้อมูลในฐานข้อมูล
$yearsQuery = "SELECT DISTINCT YEAR(carrent_date) AS year FROM carrent ORDER BY year DESC";
$yearsResult = mysqli_query($con, $yearsQuery);
$years = [];
while ($row = mysqli_fetch_assoc($yearsResult)) {
    $years[] = $row['year'];
}

$months = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสรุปผล</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/report.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar bg-body-tertiary">
            <div class="container-fluid d-flex justify-content-between">
                <div>
                    <a class="navbar-brand" href="../admin.php">Admin Controller</a>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="head">
        <a href="inside_management.php" class="btn btn-outline-dark" style="align-self: flex-start;">กลับ</a>
        <div class="head-title">รายงานสรุปผล</div>
    </div>
    <div class="container-total">
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header">
                        จำนวนรถยนต์ทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalCars; ?> คัน</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header">
                        จำนวนสมาชิกทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalMembers; ?> คน</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header">
                        จำนวนการเช่าทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalRentals; ?> ครั้ง</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header">
                        รายได้รวม
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($totalRevenue, 2); ?> บาท</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="search-container">
    <div class="option-btn">
        <button type="button" class="btn btn-secondary btn-report-carrent">รายงานการเช่ารถ</button>
        <button type="button" class="btn btn-secondary btn-report-package">รายงานการเช่ารถพร้อมแพ็คเกจ</button>
        <button type="button" class="btn btn-secondary btn-report-fix">รายงานการซ่อมรถ</button>
        <button type="button" class="btn btn-secondary btn-report-revenue">รายได้สุทธิ</button>
    </div>
</div>


    <div class="report-carrent">
        <div class="search-date">
            <div class="search-box">
                <form class="row g-3" method="GET" action="summary_report.php">
                    <div class="box">
                        <label for="year" class="form-label">เลือกปี</label>
                        <select id="year" name="year" class="form-select">
                            <?php foreach ($years as $year) : ?>
                                <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="box">
                        <label for="month" class="form-label">เลือกเดือน</label>
                        <select id="month" name="month" class="form-select">
                            <?php foreach ($months as $num => $name) : ?>
                                <option value="<?php echo $num; ?>" <?php echo ($num == $selectedMonth) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
            </div>
            <div class="btn-search">
                <button type="submit" class="btn btn-primary">แสดงผล</button>
            </div>
            </form>
        </div>
        <div class="carrent-title">
            รายงานการเช่ารถ
        </div>
        <div class="income-container">
            <div class="income-body">
                <div class="income-title">
                    รายได้รวมจากการเช่ารถส่วนตัว
                </div>
                <div class="income-show">
                    <?php
                    require '../conDB.php';

                    // Query to calculate the total income from private car rentals
                    $incomeSql = "SELECT SUM(carrent_price) AS total_income FROM carrent WHERE type_carrent = 'เช่ารถส่วนตัว'";
                    $incomeResult = mysqli_query($con, $incomeSql);
                    $incomeRow = mysqli_fetch_assoc($incomeResult);
                    $totalIncome = $incomeRow['total_income'];

                    echo "฿" . number_format($totalIncome, 2);
                    ?>
                </div>
            </div>
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
                    <th width="90px">วันที่คืนรถ</th>
                    <th width="170px">จำนวนเงิน</th>
                    <th width="170px">สถานะการเช่า</th>
                </tr>
                <?php
                // Set the number of records per page
                $limit = 15;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Add conditions for start date and carrent status
                $whereClause = "WHERE carrent.type_carrent = 'เช่ารถส่วนตัว'";
                if (!empty($_GET['start_date'])) {
                    $startDate = $_GET['start_date'];
                    $whereClause .= " AND carrent.carrent_date = '$startDate'";
                }

                if (!empty($_GET['carrent_status_id'])) {
                    $statusId = $_GET['carrent_status_id'];
                    $whereClause .= " AND carrent.carrent_status_id = '$statusId'";
                }

                if (isset($_GET['status_filter']) && $_GET['status_filter'] == 'กำลังใช้งาน') {
                    $whereClause = "WHERE carrent_status.status_name = 'กำลังใช้งาน' AND carrent.type_carrent = 'เช่ารถส่วนตัว'";
                }

                $sql = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.type_rent, carrent.type_carrent, carrent.carrent_date, carrent.carrent_return,
                carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
                member.Membername, member.Memberlastname,
                car.car_name, car.car_price,
                carrent_status.status_name
                FROM carrent
                LEFT JOIN member ON carrent.MemberID = member.MemberID
                LEFT JOIN car ON carrent.car_id = car.car_id
                LEFT JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
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
                    // Determine the class for the status button
                    $status_class = '';
                    switch ($row['status_name']) {
                        case 'กำลังดำเนินการ':
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

                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['type_rent'] . "</td>";
                    echo "<td>" . $row['type_carrent'] . "</td>";
                    echo "<td>" . $row['carrent_id'] . "</td>";
                    echo "<td>" . $row['Membername'] . " " . $row['Memberlastname'] . "</td>";
                    echo "<td>" . $row['car_name'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_date'])) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['carrent_return'])) . "</td>";
                    echo "<td>" . $row['carrent_price'] . "</td>";
                    echo "<td><a href='#' class='btn " . $status_class . " btn-sm'>" . $row['status_name'] . "</a></td>";
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
                            <a class="page-link" href="?page=<?php echo $i; ?>&start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>&carrent_status_id=<?php echo isset($_GET['carrent_status_id']) ? $_GET['carrent_status_id'] : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>