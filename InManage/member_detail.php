<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่ โดยตรวจสอบ session variable
if (!isset($_SESSION['admin'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: ../login.php");
    exit;
}

// เชื่อมต่อฐานข้อมูล
require_once '../conDB.php';

if (isset($_GET['MemberID'])) {
    $MemberID = $_GET['MemberID'];

    // ดึงข้อมูลของผู้ใช้จากฐานข้อมูลโดยใช้ MemberID
    $query = "SELECT * FROM member WHERE MemberID = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $MemberID);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Check if user data is fetched
    if (!$user) {
        echo "<script>alert('ไม่พบข้อมูลสมาชิก'); window.location.href='manage_member.php';</script>";
        exit;
    }

    // Pagination settings
    $limit = 5; // Number of entries to show in a page.
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $queryTotalInProgress = "SELECT COUNT(*) as total FROM carrent
                            WHERE MemberID = ? AND carrent_status_id IN (1, 2, 3)";
    $stmtTotalInProgress = $con->prepare($queryTotalInProgress);
    $stmtTotalInProgress->bind_param('i', $MemberID);
    $stmtTotalInProgress->execute();
    $resultTotalInProgress = $stmtTotalInProgress->get_result();
    $totalInProgress = $resultTotalInProgress->fetch_assoc()['total'];

    $queryInProgress = "SELECT carrent.*, car.car_name, carrent_status.status_name FROM carrent
                        JOIN car ON carrent.car_id = car.car_id
                        JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                        WHERE carrent.MemberID = ? AND carrent.carrent_status_id IN (1, 2, 3)
                        LIMIT ?, ?";
    $stmtInProgress = $con->prepare($queryInProgress);
    $stmtInProgress->bind_param('iii', $MemberID, $start, $limit);
    $stmtInProgress->execute();
    $resultInProgress = $stmtInProgress->get_result();

    $totalPagesInProgress = ceil($totalInProgress / $limit);

    $queryTotalCompleted = "SELECT COUNT(*) as total FROM carrent
                            WHERE MemberID = ? AND carrent_status_id = 4";
    $stmtTotalCompleted = $con->prepare($queryTotalCompleted);
    $stmtTotalCompleted->bind_param('i', $MemberID);
    $stmtTotalCompleted->execute();
    $resultTotalCompleted = $stmtTotalCompleted->get_result();
    $totalCompleted = $resultTotalCompleted->fetch_assoc()['total'];

    $queryCompleted = "SELECT carrent.*, car.car_name, carrent_status.status_name FROM carrent
                       JOIN car ON carrent.car_id = car.car_id
                       JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                       WHERE carrent.MemberID = ? AND carrent.carrent_status_id = 4
                       LIMIT ?, ?";
    $stmtCompleted = $con->prepare($queryCompleted);
    $stmtCompleted->bind_param('iii', $MemberID, $start, $limit);
    $stmtCompleted->execute();
    $resultCompleted = $stmtCompleted->get_result();

    $totalPagesCompleted = ceil($totalCompleted / $limit);
} else {
    echo "<script>alert('ไม่พบข้อมูลสมาชิก'); window.location.href='manage_member.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดสมาชิก</title>
    <link rel="stylesheet" href="../styles/member_detail.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="../admin.php">Admin Controller</a>
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
    <a href="manage_member.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="user">
        <div class="user-title">
            รายละเอียดสมาชิก
        </div>
        <div class="user-body">
            <div class="user-detail">
                <div class="detail">
                    <div class="pic">
                        <img src="<?php echo $user['Memberpic']; ?>" alt="Member Image" class="img-fluid-center">
                    </div>
                    <p><strong>รหัสสมาชิก:</strong> <?php echo $user['MemberID']; ?></p>
                    <p><strong>ชื่อ:</strong> <?php echo $user['Membername']; ?> <?php echo $user['Memberlastname']; ?></p>
                    <p><strong>อีเมล:</strong> <?php echo $user['Memberemail']; ?></p>
                    <p><strong>เบอร์โทรศัพท์:</strong> <?php echo $user['Memberphone']; ?></p>
                    <p><strong>เลขบัตรประชาชน / Passport:</strong> <?php echo $user['Memberpassport']; ?></p>
                    <p><strong>ที่อยู่:</strong> <?php echo nl2br($user['Memberaddress']); ?></p>
                </div>
                <div class="history">
                    <div class="container-fluid mt-4">
                        <h3 class="text-center">ประวัติการเช่า</h3>

                        <!-- In Progress Rentals -->
                        <h4 class="mt-4">กำลังดำเนินการอยู่</h4>
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>รหัสการเช่า</th>
                                    <th>รถ</th>
                                    <th>ประเภทการเช่า</th>
                                    <th>วันที่เช่า</th>
                                    <th>วันที่คืน</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($rowInProgress = $resultInProgress->fetch_assoc()) : ?>
                                    <?php
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($rowInProgress['carrent_status_id'] == 3) {
                                        $badgeClass = 'bg-info text-dark';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $rowInProgress['carrent_id']; ?></td>
                                        <td><?php echo $rowInProgress['car_name']; ?></td>
                                        <td><?php echo $rowInProgress['type_carrent']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($rowInProgress['carrent_date'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($rowInProgress['carrent_return'])); ?></td>
                                        <td><span class='badge <?php echo $badgeClass; ?>'><?php echo $rowInProgress['status_name']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $totalPagesInProgress; $i++) : ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="user_detail.php?MemberID=<?php echo $MemberID; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>

                        <!-- Completed Rentals -->
                        <h4 class="mt-4">ใช้งานเสร็จสิ้น</h4>
                        <table class="table table-bordered table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>รหัสการเช่า</th>
                                    <th>รถ</th>
                                    <th>ประเภทการเช่า</th>
                                    <th>วันที่เช่า</th>
                                    <th>วันที่คืน</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($rowCompleted = $resultCompleted->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo $rowCompleted['carrent_id']; ?></td>
                                        <td><?php echo $rowCompleted['car_name']; ?></td>
                                        <td><?php echo $rowCompleted['type_carrent']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($rowCompleted['carrent_date'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($rowCompleted['carrent_return'])); ?></td>
                                        <td><span class='badge bg-secondary'><?php echo $rowCompleted['status_name']; ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $totalPagesCompleted; $i++) : ?>
                                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                        <a class="page-link" href="user_detail.php?MemberID=<?php echo $MemberID; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>