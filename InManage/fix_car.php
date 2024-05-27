<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจัดการการตรวจสอบสภาพรถ</title>
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
            <p class="title">การตรวจสอบสภาพรถ</p>
        </div>
        <div class="btn-car">
            <button class="btn-outline-info" onclick="window.location.href='fix_car.php'">การตรวจสอบสภาพรถ</button>
            <button class="btn-outline-info" onclick="window.location.href='fix_car.php'">การตรวจสอบสภาพรถ</button>
        </div>
    </div>
    <div class="body-car">
        <div class="title-body">
            การตรวจสอบสภาพรถ
        </div>
        <div class="search-container">
            <form action="" method="GET">
                <input class="form-control" type="date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">
                <button type="submit" class="btn-s">ค้นหา</button>
                <a href="fix_car.php">ล้างค้นหา</a>
            </form>
        </div>

        <div class="table-view-car">
            <table class="table table-bordered">
                <tr>
                    <th width="90px">ลำดับที่</th>
                    <th width="100px">ประเภท</th>
                    <th width="100px">ประเภทการเช่า</th>
                    <th width="100px">รหัสการเช่า</th>
                    <th width="400px">ชื่อผู้เช่า</th>
                    <th width="130px">รถที่เช่า</th>
                    <th width="90px">วันที่คืนรถ</th>
                    <th width="100px">เวลาที่คืนรถ</th>
                    <th width="170px">ตรวจสอบสภาพรถ</th>
                </tr>
                <?php
                require '../conDB.php';

                // Set the number of records per page
                $limit = 15;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Add conditions for start date and carrent status
                $whereClause = "WHERE carrent.carrent_status_id = 4 AND carrent.carrent_id NOT IN (SELECT carrent_id FROM fix_car)"; // Only show status = 4 and not in fix_car
                if (!empty($_GET['start_date'])) {
                    $startDate = $_GET['start_date'];
                    $whereClause .= " AND carrent.carrent_date = '$startDate'";
                }

                $sql = "SELECT carrent.carrent_id, carrent.car_id, carrent.MemberID, carrent.type_rent, carrent.type_carrent, carrent.carrent_date, carrent.carrent_time, 
                        return_carrent.date_return, return_carrent.time_return, 
                        carrent.carrent_price, carrent.carrent_status_id, carrent.carrent_timestamp,
                        member.Membername, member.Memberlastname,
                        car.car_name, car.car_price,
                        carrent_status.status_name
                        FROM carrent
                        LEFT JOIN member ON carrent.MemberID = member.MemberID
                        LEFT JOIN car ON carrent.car_id = car.car_id
                        LEFT JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                        LEFT JOIN return_carrent ON carrent.carrent_id = return_carrent.carrent_id
                        $whereClause
                        ORDER BY carrent.carrent_timestamp DESC
                        LIMIT $start, $limit";

                $result = mysqli_query($con, $sql);

                // Count the total number of records with the applied filters
                $countSql = "SELECT COUNT(*) AS total FROM carrent LEFT JOIN return_carrent ON carrent.carrent_id = return_carrent.carrent_id $whereClause";
                $countResult = mysqli_query($con, $countSql);
                $countRow = mysqli_fetch_assoc($countResult);
                $total = $countRow['total'];
                $pages = ceil($total / $limit);

                $counter = $start + 1; // Start counting from the current page
                while ($row = mysqli_fetch_assoc($result)) {
                    $date_return = date('d/m/Y', strtotime($row['date_return']));
                    $time_return = date('H:i', strtotime($row['time_return']));

                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['type_rent'] . "</td>";
                    echo "<td>" . $row['type_carrent'] . "</td>";
                    echo "<td>" . $row['carrent_id'] . "</td>";
                    echo "<td>" . $row['Membername'] . " " . $row['Memberlastname'] . "</td>";
                    echo "<td>" . $row['car_name'] . "</td>";
                    echo "<td>" . $date_return . "</td>";
                    echo "<td>" . $time_return . "</td>";
                    echo "<td><button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#inspectModal' data-carid='" . $row['car_id'] . "' data-carrentid='" . $row['carrent_id'] . "'>ตรวจสอบสภาพรถ</button></td>";
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

    <div class="modal fade" id="inspectModal" tabindex="-1" aria-labelledby="inspectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inspectModalLabel">ตรวจสอบสภาพรถ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inspectForm" action="inspect_car_details.php" method="post">
                        <input type="hidden" name="car_id" id="carIdInput">
                        <input type="hidden" name="carrent_id" id="carrentIdInput">
                        <div class="mb-3">
                            <label for="carStatus" class="form-label">การตรวจสอบ</label>
                            <select class="form-select" id="carStatus" name="car_status" required>
                                <option selected>เลือกการตรวจสอบ</option>
                                <option value="สภาพรถปกติ">สภาพรถปกติ</option>
                                <option value="ต้องซ่อมแซม">ต้องซ่อมแซม</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" name="submit_button" value="next">ต่อไป</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../script/fix_car.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var inspectModal = document.getElementById('inspectModal');
        inspectModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var carId = button.getAttribute('data-carid');
            var carrentId = button.getAttribute('data-carrentid');

            var carIdInput = document.getElementById('carIdInput');
            var carrentIdInput = document.getElementById('carrentIdInput');

            carIdInput.value = carId;
            carrentIdInput.value = carrentId;
        });
    });
    </script>
</body>

</html>
