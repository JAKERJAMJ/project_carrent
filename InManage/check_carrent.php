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
    <title>เช็ควันว่าง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/check_carrent.css">
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
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"> <!-- เพิ่ม class dropdown-menu-end เพื่อจัดให้ dropdown อยู่ด้านขวาของ Navbar -->
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <a href="manage_carrent.php" class="btn btn-outline-dark btn-back">กลับ</a>
    <div class="check" id="CheckDate">
        <div class="check-title">
            เช็ควันที่ว่างของรถ
        </div>
        <div class="check-form" id="CheckFormDate">
            <form action="check_carrent.php" method="post">
                <div class="box">
                    <label for="carrent_date">วันที่เช่า</label>
                    <input class="form-control" type="date" name="carrent_date" id="carrent_date" required>
                </div>
                <div class="box">
                    <label for="carrent_return">วันที่คืน</label>
                    <input class="form-control" type="date" name="carrent_return" id="carrent_return" required>
                </div>
                <button type="submit" name="CheckDate" id="CheckDate" class="btn btn-primary">ค้นหา</button>
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
                    <table class="table table-bordered mx-auto">
                        <tr>
                            <th>ชื่อรถ</th>
                            <th>สถานะ</th>
                            <th>จองรถ</th>
                        </tr>
                        <?php while ($car = mysqli_fetch_assoc($availableCars)) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($car['car_name']); ?></td>
                                <?php if ($car['availability'] === 'ว่าง') : ?>
                                    <td><span class="btn btn-success"><?php echo $car['availability']; ?></span></td>
                                    <?php
                                    // สร้างลิงก์ "จองรถ" โดยรวมข้อมูลวันที่เช่าและวันที่คืนในลิงก์ด้วย
                                    echo '<td><a href="booking_page.php?id=' . $car['car_id'] . '&start_date=' . $startDate . '&end_date=' . $endDate . '" class="btn btn-success">จองรถ</a></td>';
                                    ?>
                                <?php else : ?>
                                    <td><span class="btn btn-danger"><?php echo $car['availability']; ?></span></td>
                                    <td><button type="button" class="btn btn-danger" disabled>ไม่สามารถจองได้</button></td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>