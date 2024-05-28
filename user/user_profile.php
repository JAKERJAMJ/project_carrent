<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่ โดยตรวจสอบ session variable
if (!isset($_SESSION['MemberID'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: ../login.php");
    exit;
}

// เชื่อมต่อฐานข้อมูล
require_once '../conDB.php';

// ดึงข้อมูลของผู้ใช้จากฐานข้อมูลโดยใช้ Member_ID จาก session
$MemberID = $_SESSION['MemberID'];
$query = "SELECT * FROM member WHERE MemberID = '$MemberID'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

// Check if user data is fetched
if (!$user) {
    header("Location: login.php");
    exit;
}

// Pagination settings
$limit = 5; // Number of entries to show in a page.
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$queryTotalInProgress = "SELECT COUNT(*) as total FROM carrent
                        WHERE MemberID = '$MemberID' AND carrent_status_id IN (1, 2, 3)";
$resultTotalInProgress = mysqli_query($con, $queryTotalInProgress);
$totalInProgress = mysqli_fetch_assoc($resultTotalInProgress)['total'];

$queryInProgress = "SELECT carrent.*, car.car_name, carrent_status.status_name FROM carrent
                    JOIN car ON carrent.car_id = car.car_id
                    JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                    WHERE carrent.MemberID = '$MemberID' AND carrent.carrent_status_id IN (1, 2, 3)
                    LIMIT $start, $limit";
$resultInProgress = mysqli_query($con, $queryInProgress);

$totalPagesInProgress = ceil($totalInProgress / $limit);

$queryTotalCompleted = "SELECT COUNT(*) as total FROM carrent
                        WHERE MemberID = '$MemberID' AND carrent_status_id = 4";
$resultTotalCompleted = mysqli_query($con, $queryTotalCompleted);
$totalCompleted = mysqli_fetch_assoc($resultTotalCompleted)['total'];

$queryCompleted = "SELECT carrent.*, car.car_name, carrent_status.status_name FROM carrent
                   JOIN car ON carrent.car_id = car.car_id
                   JOIN carrent_status ON carrent.carrent_status_id = carrent_status.carrent_status_id
                   WHERE carrent.MemberID = '$MemberID' AND carrent.carrent_status_id = 4
                   LIMIT $start, $limit";
$resultCompleted = mysqli_query($con, $queryCompleted);

$totalPagesCompleted = ceil($totalCompleted / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="../styles/user_profile.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <header>
        <nav class="navbar bg-body-tertiary">
            <div class="container-fluid d-flex justify-content-between">
                <a class="navbar-brand" href="../index.php">เมืองเลยรถเช่า</a>
                <div class="d-flex">
                    <div class="me-3">
                        <a class="nav-link active text-dark" aria-current="page" href="../show_car.php">รถยนต์ส่วนตัว</a>
                    </div>
                    <div class="me-3">
                        <a class="nav-link active text-dark" aria-current="page" href="#">แพ็คเกจท่องเที่ยว</a>
                    </div>
                    <div>
                        <a class="nav-link active text-dark" aria-current="page" href="#">เกี่ยวกับเรา</a>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $user['Membername'] . ' ' . $user['Memberlastname']; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="user_profile.php">ข้อมูลส่วนตัว</a></li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="head-profile">
        <p class="title-head">ข้อมูลส่วนตัว</p>
    </div>
    <div class="container-fluid" id="body-profile">
        <?php
        require '../conDB.php';

        $MemberID = $_SESSION['MemberID'];
        $sql = "SELECT * FROM member WHERE MemberID = $MemberID";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        ?>

        <div class="left-side">
            <div class="profile">
                <img src="<?php echo $row['Memberpic']; ?>" alt="Profile Image" class="profile-img"><br>
                <button type="button" class="btn btn-primary" id="edit-btn">แก้ไขรูปภาพ</button>
            </div>
            <div class="edit-img">
                <form action="user_profile.php" method="post" enctype="multipart/form-data" id="upload-form" style="display: none;">
                    <input class="form-control" type="file" name="profile_pic" id="profile_pic" style="display: none; width: 250px;">
                    <button type="submit" class="btn btn-success" name="update-profile" id="update-profile-btn" disabled>บันทึกรูปภาพ</button>
                </form>
                <?php
                if (isset($_POST["update-profile"])) {
                    $target_dir = "../img/member/";

                    function createNewFileName($originalFileName)
                    {
                        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
                        $newFileName = "Member_" . rand(1000, 999999) . "." . $fileExtension;
                        return $newFileName;
                    }

                    $profile_pic = $target_dir . createNewFileName($_FILES["profile_pic"]["name"]);
                    move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic);

                    // เพิ่มโค้ดสำหรับอัปเดตข้อมูลในฐานข้อมูล
                    $update_query = "UPDATE member SET Memberpic = '$profile_pic' WHERE MemberID = $MemberID";

                    // ประมวลผลคำสั่ง SQL
                    if (mysqli_query($con, $update_query)) {
                        echo "<script>alert('อัพเดตรูปภาพเสร็จสิ้น'); window.location.href = window.location.href;</script>";
                    } else {
                        echo "Error updating profile picture: " . mysqli_error($con);
                    }
                }
                ?>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="right-side">
            <div class="col-md-8">
                <ul class="nav nav-tabs" id="profileTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">ข้อมูลโปรไฟล์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="edit-tab" data-toggle="tab" href="#edit" role="tab" aria-controls="edit" aria-selected="false">แก้ไขโปรไฟล์</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">เปลี่ยนรหัสผ่าน</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="profileTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <p>Email: <?php echo $row['Memberemail']; ?></p>
                        <p>ชื่อ: <?php echo $row['Membername']; ?></p>
                        <p>นามสกุล: <?php echo $row['Memberlastname']; ?></p>
                        <p>ที่อยู่: <?php echo $row['Memberaddress']; ?></p>
                        <p>เบอร์โทรศัพท์: <?php echo $row['Memberphone']; ?></p>
                    </div>

                    <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                        <form action="user_profile.php" method="post" enctype="multipart/form-data">
                            <label>Email</label>
                            <input class="form-control" type="email" name="email" value="<?php echo $row['Memberemail']; ?>" required><br>
                            <label>ชื่อ</label>
                            <input class="form-control" type="text" name="name" value="<?php echo $row['Membername']; ?>" required><br>
                            <label>นามสกุล</label>
                            <input class="form-control" type="text" name="surname" value="<?php echo $row['Memberlastname']; ?>" required><br>
                            <label>ที่อยู่</label>
                            <input class="form-control" type="text" name="address" value="<?php echo $row['Memberaddress']; ?>" required><br>
                            <label>เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="Memberphone" name="phone" value="<?php echo $row['Memberphone']; ?>" oninput="formatPhoneNumber(this)" maxlength="10">
                            <button class="btn btn-success" type="submit" name="update">Update</button>
                        </form>
                        <?php
                        if (isset($_POST['update'])) {
                            // รับค่าจากฟอร์ม
                            $email = $_POST['email'];
                            $name = $_POST['name'];
                            $surname = $_POST['surname'];
                            $address = $_POST['address'];
                            $phone = $_POST['phone'];

                            // อัปเดตข้อมูลในฐานข้อมูล
                            $sql = "UPDATE member SET Memberemail = '$email', Membername = '$name', Memberlastname = '$surname', Memberaddress = '$address', Memberphone = '$phone' WHERE MemberID = $MemberID";
                            $result = mysqli_query($con, $sql);

                            // ตรวจสอบการอัปเดต
                            if ($result) {
                                echo "<script>alert('อัพเดตข้อมูลเสร็จสิ้น'); window.location.href = window.location.href;</script>";
                            } else {
                                echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . mysqli_error($con);
                            }
                        }
                        ?>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form action="user_profile.php" method="post">
                            <div class="box">
                                <label>รหัสปัจจุบัน:</label>
                                <input type="hidden" name="PasswordOld" id="PasswordOld" value="<?php echo $row['Memberpassword']; ?>">
                                <input class="form-control" type="password" name="EnterPassword" id="EnterPassword" required>
                                <div id="passwordError" style="color: red; "></div>
                            </div>
                            <div class="box">
                                <label>รหัสผ่านใหม่:</label>
                                <input type="password" class="form-control" id="Memberpassword" name="Memberpassword" placeholder="ควรประกอบไปด้วย(a-z), (A-Z), (0-9) และ!@#$%^&*().">
                                <div id="passwordStrength"></div>
                            </div>
                            <div class="box">
                                <label>ยืนยัน รหัสผ่านใหม่:</label>
                                <input type="password" class="form-control" id="confirmPassword">
                                <div id="PasswordError" style="color: red;"></div>
                            </div>
                            <button class="btn btn-success" type="submit" name="change_password" id="change_password">เปลี่ยนรหัสผ่าน</button>
                        </form>
                        <?php
                        if (isset($_POST['change_password'])) {
                            $Memberpassword = $_POST['Memberpassword'];

                            $sql = "UPDATE member SET Memberpassword = '$Memberpassword' WHERE MemberID = $MemberID";
                            $result = mysqli_query($con, $sql);

                            // ตรวจสอบการอัปเดต
                            if ($result) {
                                echo "<script>alert('อัพเดตผ่านเสร็จสิ้น'); window.location.href = window.location.href;</script>";
                            } else {
                                echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . mysqli_error($con);
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <?php
                while ($rowInProgress = mysqli_fetch_assoc($resultInProgress)) {
                    $badgeClass = 'bg-warning text-dark';
                    if ($rowInProgress['carrent_status_id'] == 3) {
                        $badgeClass = 'bg-info text-dark';
                    }
                    echo "<tr>";
                    echo "<td>{$rowInProgress['carrent_id']}</td>";
                    echo "<td>{$rowInProgress['car_name']}</td>";
                    echo "<td>{$rowInProgress['type_carrent']}</td>";
                    echo "<td>" . date('d/m/Y', strtotime($rowInProgress['carrent_date'])) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($rowInProgress['carrent_return'])) . "</td>";
                    echo "<td><span class='badge {$badgeClass}'>{$rowInProgress['status_name']}</span></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPagesInProgress; $i++) : ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="user_profile.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
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
                <?php
                while ($rowCompleted = mysqli_fetch_assoc($resultCompleted)) {
                    echo "<tr>";
                    echo "<td>{$rowCompleted['carrent_id']}</td>";
                    echo "<td>{$rowCompleted['car_name']}</td>";
                    echo "<td>{$rowCompleted['type_carrent']}</td>";
                    echo "<td>" . date('d/m/Y', strtotime($rowCompleted['carrent_date'])) . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($rowCompleted['carrent_return'])) . "</td>";
                    echo "<td><span class='badge bg-secondary'>{$rowCompleted['status_name']}</span></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPagesCompleted; $i++) : ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="user_profile.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    <script>
        document.getElementById('edit-btn').addEventListener('click', function() {
            document.getElementById('profile_pic').style.display = 'block';
            document.getElementById('update-profile-btn').disabled = false;
            document.getElementById('upload-form').style.display = 'flex';
        });

        function formatPhoneNumber(input) {
            // Custom logic to format phone number
        }
    </script>

    <script src="../script/user_profile.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>