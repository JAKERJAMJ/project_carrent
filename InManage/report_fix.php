<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';

// Summary queries
$totalCarsQuery = "SELECT COUNT(*) AS total_cars FROM car";
$totalCarsResult = mysqli_query($con, $totalCarsQuery);
$totalCars = mysqli_fetch_assoc($totalCarsResult)['total_cars'];

$totalMembersQuery = "SELECT COUNT(*) AS total_members FROM member";
$totalMembersResult = mysqli_query($con, $totalMembersQuery);
$totalMembers = mysqli_fetch_assoc($totalMembersResult)['total_members'];

$totalFixesQuery = "SELECT COUNT(*) AS total_fixes FROM fix_car";
$totalFixesResult = mysqli_query($con, $totalFixesQuery);
$totalFixes = mysqli_fetch_assoc($totalFixesResult)['total_fixes'];

$totalRevenueQuery = "SELECT SUM(fix_price) AS total_revenue FROM fix_car";
$totalRevenueResult = mysqli_query($con, $totalRevenueQuery);
$totalRevenue = mysqli_fetch_assoc($totalRevenueResult)['total_revenue'];

// Year and month selection
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('m');

// Function to get fixes by day
function getFixesByDay($con, $selectedYear, $selectedMonth) {
    $query = "SELECT fix_car.fix_id, fix_car.car_id, fix_car.carrent_id, fix_car.fix_detail, fix_car.fix_date, fix_car.fix_return, fix_car.fix_price, fix_car.fix_status,
              car.car_name
              FROM fix_car
              LEFT JOIN car ON fix_car.car_id = car.car_id
              WHERE YEAR(fix_car.fix_date) = '$selectedYear' AND MONTH(fix_car.fix_date) = '$selectedMonth'
              ORDER BY fix_car.fix_date ASC";
    $result = mysqli_query($con, $query);
    $fixesByDay = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fixesByDay[] = $row;
    }
    return $fixesByDay;
}

// Get fixes by day
$fixesByDay = getFixesByDay($con, $selectedYear, $selectedMonth);

// Get available years
$yearsQuery = "SELECT DISTINCT YEAR(fix_date) AS year FROM fix_car ORDER BY year DESC";
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
    <title>รายงานการซ่อมรถ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/report_fix.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="../admin.php">Admin Controller</a>
                <div class="dropdown ms-auto">
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
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <a href="inside_management.php" class="btn btn-outline-dark">กลับ</a>
            <h2 class="text-center">รายงานการซ่อมรถ</h2>
        </div>
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
                        จำนวนการซ่อมทั้งหมด
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $totalFixes; ?> ครั้ง</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header">
                        ค่าใช้จ่ายรวมในการซ่อม
                    </div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo number_format($totalRevenue, 2); ?> บาท</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <div class="btn-group">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='report.php'">รายงานการเช่ารถ</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='report_package.php'">รายงานการเช่ารถพร้อมแพ็คเกจ</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='report_fix.php'">รายงานการซ่อมรถ</button>
            </div>
        </div>

        <div class="mt-4">
            <form class="row g-3" method="GET" action="report_fix.php">
                <div class="col-md-6">
                    <label for="year" class="form-label">เลือกปี</label>
                    <select id="year" name="year" class="form-select">
                        <?php foreach ($years as $year) : ?>
                            <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="month" class="form-label">เลือกเดือน</label>
                    <select id="month" name="month" class="form-select">
                        <?php foreach ($months as $num => $name) : ?>
                            <option value="<?php echo $num; ?>" <?php echo ($num == $selectedMonth) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary mt-3">แสดงผล</button>
                </div>
            </form>
        </div>

        <div class="mt-5">
            <h3 class="text-center">ค่าใช้จ่ายรวมในการซ่อม: ฿<?php echo number_format($totalRevenue, 2); ?></h3>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="90px">ลำดับที่</th>
                        <th width="100px">รหัสการตรวจสอบ</th>
                        <th width="100px">รหัสรถ</th>
                        <th width="100px">รหัสการเช่า</th>
                        <th width="400px">รายละเอียดการซ่อม</th>
                        <th width="130px">รถที่เช่า</th>
                        <th width="90px">วันที่ส่งซ่อม</th>
                        <th width="100px">วันที่ซ่อมเสร็จ</th>
                        <th width="170px">สถานะการตรวจสอบ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 15;
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $start = ($page - 1) * $limit;

                    $sql = "SELECT fix_car.fix_id, fix_car.car_id, fix_car.carrent_id, fix_car.fix_detail, fix_car.fix_date, fix_car.fix_return, fix_car.fix_price, fix_car.fix_status,
                            car.car_name
                            FROM fix_car
                            LEFT JOIN car ON fix_car.car_id = car.car_id
                            ORDER BY fix_car.fix_date DESC
                            LIMIT $start, $limit";

                    $result = mysqli_query($con, $sql);

                    $countSql = "SELECT COUNT(*) AS total FROM fix_car";
                    $countResult = mysqli_query($con, $countSql);
                    $countRow = mysqli_fetch_assoc($countResult);
                    $total = $countRow['total'];
                    $pages = ceil($total / $limit);

                    $counter = $start + 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $fix_date = date('d/m/Y', strtotime($row['fix_date']));
                        $fix_return = date('d/m/Y', strtotime($row['fix_return']));
                        $fix_status_color = ($row['fix_status'] == 'ต้องส่งซ่อม') ? 'red' : ($row['fix_status'] == 'สภาพรถปกติ' ? 'green' : 'blue');

                        echo "<tr>";
                        echo "<td>" . $counter . "</td>";
                        echo "<td>" . $row['fix_id'] . "</td>";
                        echo "<td>" . $row['car_id'] . "</td>";
                        echo "<td>" . $row['carrent_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['fix_detail']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['car_name']) . "</td>";
                        echo "<td>" . $fix_date . "</td>";
                        echo "<td>" . $fix_return . "</td>";
                        echo "<td style='color: " . $fix_status_color . ";'>" . htmlspecialchars($row['fix_status']) . "</td>";
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
