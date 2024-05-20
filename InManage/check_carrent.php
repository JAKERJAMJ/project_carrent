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

    <div class="check" id="CheckDate">
        <div class="check-title">
            เช็ควันที่ว่างของรถ
        </div>
        <button type="button" class="ondate">เช็คจากวันที่</button>
        <button type="button" class="oncar">เช็คตารางรถ</button>
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

</body>

</html>