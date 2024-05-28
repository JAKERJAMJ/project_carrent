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

// ดึงข้อมูลการเช่าในแต่ละวันของเดือนที่เลือก
$rentalsByDayQuery = "SELECT carrent.carrent_date, carrent.carrent_id, car.car_name, carrent.type_carrent, member.Membername, member.Memberlastname, carrent_status.status_name
                      FROM carrent
                      JOIN car ON carrent.car_id = car.car_id
                      JOIN member ON carrent.MemberID = member.MemberID
                      JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                      WHERE YEAR(carrent.carrent_date) = '$selectedYear' AND MONTH(carrent.carrent_date) = '$selectedMonth'
                      ORDER BY carrent.carrent_date ASC";
$rentalsByDayResult = mysqli_query($con, $rentalsByDayQuery);
$rentalsByDay = [];
while ($row = mysqli_fetch_assoc($rentalsByDayResult)) {
    $rentalsByDay[] = $row;
}

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
    <div class="container mt-5">
        <h1 class="text-center">รายงานสรุปผล</h1>
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

        <div class="row mt-5">
            <form class="row g-3" method="GET" action="summary_report.php">
                <div class="col-md-4">
                    <label for="year" class="form-label">เลือกปี</label>
                    <select id="year" name="year" class="form-select">
                        <?php foreach ($years as $year) : ?>
                            <option value="<?php echo $year; ?>" <?php echo ($year == $selectedYear) ? 'selected' : ''; ?>><?php echo $year; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="month" class="form-label">เลือกเดือน</label>
                    <select id="month" name="month" class="form-select">
                        <?php foreach ($months as $num => $name) : ?>
                            <option value="<?php echo $num; ?>" <?php echo ($num == $selectedMonth) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary">แสดงผล</button>
                </div>
            </form>
        </div>

        <h3 class="text-center mt-5">จำนวนการเช่ารถยนต์ในเดือน <?php echo $months[$selectedMonth] . ' ' . $selectedYear; ?></h3>
        <table class="table table-bordered mt-3">
            <thead class="table-primary">
                <tr>
                    <th>วันที่</th>
                    <th>รหัสการเช่า</th>
                    <th>รถ</th>
                    <th>ประเภทการเช่า</th>
                    <th>ชื่อผู้เช่า</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rentalsByDay as $row) : ?>
                    <tr>
                        <td><?php echo date('d/m/Y', strtotime($row['carrent_date'])); ?></td>
                        <td><?php echo $row['carrent_id']; ?></td>
                        <td><?php echo $row['car_name']; ?></td>
                        <td><?php echo $row['type_carrent']; ?></td>
                        <td><?php echo $row['Membername'] . ' ' . $row['Memberlastname']; ?></td>
                        <td><span class="badge <?php echo ($row['status_name'] == 'ใช้งานเสร็จสิ้น') ? 'bg-secondary' : 'bg-info text-dark'; ?>"><?php echo $row['status_name']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>
