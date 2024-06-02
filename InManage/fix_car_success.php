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
    <title>การจัดการการตรวจสอบสภาพรถ - Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/fix_car.css">
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

    <div class="head-car">
        <a href="inside_management.php" class="btn btn-outline-dark" style="align-self: flex-start;">กลับ</a>
        <div class="title-car">
            <p class="title">การตรวจสอบเสร็จสมบูรณ์</p>
        </div>
        <div class="btn-car">
            <button class="btn btn-outline-info" onclick="window.location.href='fix_car.php'">การตรวจสอบสภาพรถ</button>
            <button class="btn btn-outline-warning" onclick="window.location.href='fix_car_success.php'">รถที่ตรวจเช็คสภาพรถแล้ว</button>
        </div>
    </div>
    <div class="body-car">
        <div class="title-body">
            การตรวจสอบสภาพรถที่เสร็จสมบูรณ์
        </div>
        <div class="body-btn">
            <button type="button" class="btn btn-danger" onclick="filterStatus('ต้องส่งซ่อม')">ต้องส่งซ่อม</button>
            <button type="button" class="btn btn-warning" onclick="filterStatus('ซ่อมเสร็จสิ้น')">ซ่อมเสร็จสิ้น</button>
            <button type="button" class="btn btn-success" onclick="filterStatus('สภาพรถปกติ')">สภาพรถปกติ</button>
        </div>
        <div class="table-view-car">
            <table class="table table-bordered">
                <tr>
                    <th width="90px">ลำดับที่</th>
                    <th width="100px">รหัสรถ</th> <!-- car_id -->
                    <th width="100px">รหัสการเช่า</th> <!-- carrent_id -->
                    <th width="400px">รายละเอียดการซ่อม</th> <!-- fix_detail -->
                    <th width="130px">รถที่เช่า</th> <!-- car_name -->
                    <th width="100px">วันที่ส่งซ่อม</th> <!-- fix_date -->
                    <th width="100px">วันที่ซ่อมเสร็จ</th> <!-- fix_return -->
                    <th width="100px">ค่าใช้จ่าย</th> <!-- fix_return -->
                    <th width="170px">สถานะการตรวจสอบ</th> <!-- fix_status -->
                    <th width="170px">การดำเนินการ</th> <!-- action -->
                </tr>
                <?php

                // Set the number of records per page
                $limit = 15;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Get the status from the query parameter, default to "ต้องส่งซ่อม"
                $status = isset($_GET['status']) ? $_GET['status'] : 'ต้องส่งซ่อม';

                // Add conditions for the selected status
                $whereClause = "WHERE fix_car.fix_status = '$status'";

                $sql = "SELECT fix_car.fix_id, fix_car.car_id, fix_car.carrent_id, fix_car.fix_detail, fix_car.fix_date, fix_car.fix_return, fix_car.fix_price, fix_car.fix_status,
                        car.car_name
                        FROM fix_car
                        LEFT JOIN car ON fix_car.car_id = car.car_id
                        $whereClause
                        ORDER BY fix_car.fix_date DESC
                        LIMIT $start, $limit";

                $result = mysqli_query($con, $sql);

                // Count the total number of records with the applied filters
                $countSql = "SELECT COUNT(*) AS total FROM fix_car $whereClause";
                $countResult = mysqli_query($con, $countSql);
                $countRow = mysqli_fetch_assoc($countResult);
                $total = $countRow['total'];
                $pages = ceil($total / $limit);

                $counter = $start + 1; // Start counting from the current page
                while ($row = mysqli_fetch_assoc($result)) {
                    $fix_date = date('d/m/Y', strtotime($row['fix_date']));
                    $fix_return = date('d/m/Y', strtotime($row['fix_return']));
                    $fix_status_color = ($row['fix_status'] == 'ต้องส่งซ่อม') ? 'red' : ($row['fix_status'] == 'สภาพรถปกติ' ? 'green' : 'blue');

                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['car_id'] . "</td>";
                    echo "<td>" . $row['carrent_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['fix_detail']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['car_name']) . "</td>";
                    echo "<td>" . $fix_date . "</td>";
                    echo "<td>" . $fix_return . "</td>";
                    echo "<td>" . $row['fix_price'] . "</td>";
                    echo "<td style='color: " . $fix_status_color . ";'>" . htmlspecialchars($row['fix_status']) . "</td>";
                    if ($row['fix_status'] == 'ต้องส่งซ่อม') {
                        echo "<td>
                                <button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#inspectModal' data-fixid='" . $row['fix_id'] . "'>แก้ไข</button>
                                <button type='button' class='btn btn-danger btn-sm' onclick='deleteFix(" . $row['fix_id'] . ")'>ยกเลิก</button>
                              </td>";
                    } else {
                        echo "<td></td>";
                    }
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
                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

    <div class="modal fade" id="inspectModal" tabindex="-1" aria-labelledby="inspectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inspectModalLabel">แก้ไขการตรวจสอบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inspectForm" action="update_fix_car.php" method="post">
                        <input type="hidden" name="fix_id" id="fixIdInput">
                        <div class="mb-3">
                            <label for="fixDetail" class="form-label">รายละเอียดการซ่อม</label>
                            <textarea class="form-control" name="fix_detail" id="fixDetail" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fixDate" class="form-label">วันที่คาดว่าจะส่งซ่อม</label>
                            <input class="form-control" type="date" name="fix_date" id="fixDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="fixReturn" class="form-label">วันที่คาดว่าจะเสร็จ</label>
                            <input class="form-control" type="date" name="fix_return" id="fixReturn" required>
                        </div>
                        <div class="mb-3">
                            <label for="fixPrice" class="form-label">ราคาประมาณการซ่อม</label>
                            <input class="form-control" type="number" name="fix_price" id="fixPrice" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit_button" value="update">อัพเดต</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var inspectModal = document.getElementById('inspectModal');
            inspectModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var fixId = button.getAttribute('data-fixid');

                var fixIdInput = document.getElementById('fixIdInput');

                fixIdInput.value = fixId;

                // Fetch the details for the selected fix_id
                fetch('get_fix_details.php?fix_id=' + fixId)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('fixDetail').value = data.fix_detail;
                        document.getElementById('fixDate').value = data.fix_date;
                        document.getElementById('fixReturn').value = data.fix_return;
                        document.getElementById('fixPrice').value = data.fix_price;
                    });
            });
        });

        function filterStatus(status) {
            window.location.href = 'fix_car_success.php?status=' + status;
        }

        function deleteFix(fixId) {
            if (confirm('คุณแน่ใจว่าต้องการยกเลิกรายการนี้หรือไม่?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete_fix_car.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('ยกเลิกรายการสำเร็จ');
                        window.location.reload();
                    } else {
                        alert('เกิดข้อผิดพลาดในการลบรายการ');
                    }
                };
                xhr.send('fix_id=' + fixId);
            }
        }
    </script>
</body>

</html>
