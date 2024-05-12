<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่ โดยตรวจสอบ session variable
if (!isset($_SESSION['MemberID'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: login.php");
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลส่วนตัว</title>
    <link rel="stylesheet" href="../styles/user_profile.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"> <!-- เพิ่ม class dropdown-menu-end เพื่อจัดให้ dropdown อยู่ด้านขวาของ Navbar -->
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
    <div class="container-fluid">
        <?php
        require '../conDB.php';

        $MemberID = $_SESSION['MemberID'];
        $sql = "SELECT * FROM member WHERE MemberID = $MemberID";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        ?>
        <div class="row">
            <div class="col-md-4 d-flex justify-content-center align-self-center">
            <img src="<?php echo $row['Memberpic']; ?>" alt="Profile Image" style="width:150px; height:150px; border-radius:50%; object-fit:cover;"><br>
            </div>
            <!-- Tab Navigation -->
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
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                            <label>Email:</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br>
                            <label>ชื่อ:</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br>
                            <label>นามสกุล:</label>
                            <input type="text" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>" required><br>
                            <label>ที่อยู่:</label>
                            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required><br>
                            <label>เบอร์โทรศัพท์:</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required><br>
                            <button type="submit">Update</button>
                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <label>รหัสปัจจุบัน:</label>
                            <input type="password" name="current_password" required><br>
                            <label>รหัสผ่านใหม่:</label>
                            <input type="password" name="new_password" required><br>
                            <label>ยืนยัน รหัสผ่านใหม่:</label>
                            <input type="password" name="confirm_new_password" required><br>
                            <button type="submit" name="change_password">เปลี่ยนรหัสผ่าน</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>