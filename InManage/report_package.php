<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require_once '../conDB.php';

// Summary queries
$totalCarsQuery = "SELECT COUNT(*) AS total_cars FROM car";
$totalCarsResult = mysqli_query($con, $totalCarsQuery);
$totalCars = mysqli_fetch_assoc($totalCarsResult)['total_cars'];

$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM member";
$totalMembersResult = mysqli_query($con, $totalMembersQuery);
$totalMembers = mysqli_fetch_assoc($totalMembersResult)['total_members'];

$totalRentalsQuery = "SELECT COUNT(*) AS total_rentals FROM carrent WHERE type_carrent = 'เช่ารถพร้อมแพ็คเกจ'";
$totalRentalsResult = mysqli_query($con, $totalRentalsQuery);
$totalRentals = mysqli_fetch_assoc($totalRentalsResult)['total_rentals'];

$totalRevenueQuery = "SELECT SUM(carrent_price) AS total_revenue FROM carrent WHERE type_carrent = 'เช่ารถพร้อมแพ็คเกจ'";
$totalRevenueResult = mysqli_query($con, $totalRevenueQuery);
$totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['total_revenue'];

// Year and month selection
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

// Function to get rentals by day
function getRentalsByDay($con, $selectedYear, $selectedMonth) {
    $query = "SELECT carrent.carrent_date, carrent.carrent_id, car.car_name, carrent.type_carrent, member.Membername, member.Memberlastname, carrent_status.status_name
              FROM carrent
              JOIN car ON carrent.car_id = car.car_id
              JOIN member ON carrent.MemberID = member.MemberID
              JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
              WHERE YEAR(carrent.carrent_date) = '$selectedYear' AND MONTH(carrent.carrent_date) = '$selectedMonth'
              AND carrent.type_carrent = 'เช่ารถพร้อมแพ็คเกจ'
              ORDER BY carrent.carrent_date ASC";
    $result = mysqli_query($con, $query);
    $rentalsByDay = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rentalsByDay[] = $row;
    }
    return $rentalsByDay;
}

// Get rentals by day
$rentalsByDay = getRentalsByDay($con, $selectedYear, $selectedMonth);

// Get available years
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
    <title>รายงานการเช่ารถพร้อมแพ็คเกจ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/report_package.css">
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
        <div class="head-title">รายงานการเช่ารถพร้อมแพ็คเกจ</div>
    </div>
    <div class="container-total">
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-header bg-primary text-white">
                        จำนวนรถยนต์ทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalCars; ?> คัน</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-header bg-success text-white">
                        จำนวนสมาชิกทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalMembers; ?> คน</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-header bg-warning text-white">
                        จำนวนการเช่าทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalRentals; ?> ครั้ง</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center shadow">
                    <div class="card-header bg-danger text-white">
                        รายได้รวม
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($totalRevenue, 2); ?> บาท</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="search-container mt-5">
        <div class="option-btn mb-4">
            <button type="button" class="btn btn-primary" onclick="window.location.href='report.php'">รายงานการเช่ารถ</button>
            <button type="button" class="btn btn-success" onclick="window.location.href='report_package.php'">รายงานการเช่ารถพร้อมแพ็คเกจ</button>
            <button type="button" class="btn btn-warning" onclick="window.location.href='report_fix.php'">รายงานการซ่อมรถ</button>
        </div>

        <div class="report-carrent">
            <div class="search-date">
                <form class="row g-3 justify-content-center" method="GET" action="report_package.php">
                    <div class="col-md-3">
                        <label for="year" class="form-label">เลือกปี</label>
                        <select id="year" name="year" class="form-select">
                            <?php foreach ($years as $year) : ?>
                                <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label">เลือกเดือน</label>
                        <select id="month" name="month" class="form-select">
                            <?php foreach ($months as $num => $name) : ?>
                                <option value="<?php echo $num; ?>" <?php echo ($num == $selectedMonth) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">แสดงผล</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="package-title mt-5">
            รายงานการเช่ารถพร้อมแพ็คเกจ
        </div>
        <div class="income-container mt-4">
            <div class="income-body shadow p-4">
                <div class="income-title">
                    รายได้รวมจากการเช่ารถพร้อมแพ็คเกจ
                </div>
                <div class="income-show mt-3 text-center">
                    <?php
                    require '../conDB.php';

                    // Query to calculate the total income from private car rentals with package
                    $incomeSql = "SELECT SUM(carrent_price) AS total_income FROM carrent WHERE type_carrent = 'เช่ารถพร้อมแพ็คเกจ'";
                    $incomeResult = mysqli_query($con, $incomeSql);
                    $incomeRow = mysqli_fetch_assoc($incomeResult);
                    $totalIncome = $incomeRow['total_income'];

                    echo "฿" . number_format($totalIncome, 2);
                    ?>
                </div>
            </div>
        </div>
        <div class="table-view-carrent mt-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>ลำดับที่</th>
                        <th>ประเภท</th>
                        <th>ประเภทการเช่า</th>
                        <th>รหัสการเช่า</th>
                        <th>ชื่อผู้เช่า</th>
                        <th>รถที่เช่า</th>
                        <th>วันที่เริ่มเช่า</th>
                        <th>วันที่คืนรถ</th>
                        <th>จำนวนเงิน</th>
                        <th>สถานะการเช่า</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 15;
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $start = ($page - 1) * $limit;

                    $sql = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.type_rent, carrent.type_carrent, carrent.carrent_date, carrent.carrent_return,
                    carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
                    member.Membername, member.Memberlastname,
                    car.car_name, car.car_price,
                    carrent_status.status_name
                    FROM carrent
                    LEFT JOIN member ON carrent.MemberID = member.MemberID
                    LEFT JOIN car ON carrent.car_id = car.car_id
                    LEFT JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                    WHERE carrent.type_carrent = 'เช่ารถพร้อมแพ็คเกจ'
                    ORDER BY carrent.carrent_timestamp DESC
                    LIMIT $start, $limit";

                    $result = mysqli_query($con, $sql);

                    $countSql = "SELECT COUNT(*) AS total FROM carrent WHERE carrent.type_carrent = 'เช่ารถพร้อมแพ็คเกจ'";
                    $countResult = mysqli_query($con, $countSql);
                    $countRow = mysqli_fetch_assoc($countResult);
                    $total = $countRow['total'];
                    $pages = ceil($total / $limit);

                    $counter = $start + 1;
                    while ($row = mysqli_fetch_assoc($result)) {
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
                                $status_class = 'btn-warning';
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
                        echo "<td>" . number_format($row['carrent_price'], 2) . "</td>";
                        echo "<td><a href='#' class='btn " . $status_class . " btn-sm'>" . $row['status_name'] . "</a></td>";
                        echo "</tr>";

                        $counter++;
                    }
                    mysqli_close($con);
                    ?>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $pages; $i++) : ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&year=<?php echo $selectedYear; ?>&month=<?php echo $selectedMonth; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
