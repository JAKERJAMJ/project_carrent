<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require '../conDB.php';

if (isset($_POST['submit_button']) && $_POST['submit_button'] == 'next') {
    // รับข้อมูลจากฟอร์ม
    $car_id = $_POST['car_id'];
    $carrent_id = $_POST['carrent_id'];
    $car_status = $_POST['car_status'];
}

$sql_car = "SELECT * FROM car WHERE car_id = $car_id";
$result_car = mysqli_query($con, $sql_car);
$car = mysqli_fetch_assoc($result_car);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การตรวจสอบสภาพรถ - <?php echo htmlspecialchars($car_status); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/inspect.css">
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
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">ออกจากระบบ</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="head-inspect">
        <a href="fix_car.php" class="btn btn-outline-dark" style="align-self: flex-start;">กลับ</a>
        <div class="title-inspect" style="<?php if ($car_status == 'ต้องซ่อมแซม') {
                                                echo 'background-color: rgb(74, 171, 255); color: #fff;';
                                            } ?>">
            <p><?php echo htmlspecialchars($car_status); ?></p>
        </div>
    </div>
    <div class="inspect-body">
        <div class="title">รายละเอียด</div>
        <div class="inspect-form">
            <form id="inspectForm" action="process_inspection.php" method="post">
                <div class="box">
                    <label for="carrent_id">รหัสการเช่ารถ</label>
                    <input class="form-control" type="text" name="carrent_id" id="carrent_id" value="<?= htmlspecialchars($carrent_id) ?>" readonly>
                    <input class="form-control" type="hidden" name="car_id" id="car_id" value="<?= htmlspecialchars($car_id) ?>" readonly>
                    <input class="form-control" type="hidden" name="car_status" id="car_status" value="<?= htmlspecialchars($car_status) ?>" readonly>
                    <img src="<?= htmlspecialchars($car['main_picture']) ?>" alt="">
                </div>
                <div class="detail_fix" style="display: none;">
                    <div class="box">
                        <label for="fix_detail">รายละเอียดการซ่อม</label>
                        <textarea class="form-control" name="fix_detail" id="fix_detail"></textarea>
                    </div>
                    <div class="box">
                        <label for="fix_date">วันที่คาดว่าจะส่งซ่อม</label>
                        <input class="form-control" type="date" name="fix_date" id="fix_date">
                    </div>
                    <div class="box">
                        <label for="fix_return">วันที่คาดว่าจะเสร็จ</label>
                        <input class="form-control" type="date" name="fix_return" id="fix_return">
                    </div>
                    <div class="box">
                        <label for="fix_price">ราคาประมาณการซ่อม</label>
                        <input class="form-control" type="text" name="fix_price" id="fix_price">
                    </div>
                </div>
                <div class="box">
                    <button type="submit" class="btn btn-primary" id="showDataModal" name="submit_button" value="next">ยืนยัน</button>
                    <a href="fix_car.php" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const carStatus = "<?php echo $car_status; ?>";
            if (carStatus === 'ต้องซ่อมแซม') {
                document.querySelector('.detail_fix').style.display = 'block';
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>

<?php
mysqli_close($con);
?>
