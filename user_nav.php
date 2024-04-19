<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบหรือไม่ โดยตรวจสอบ session variable
if (!isset($_SESSION['MemberID'])) {
    // ถ้าไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: login.php");
    exit;
}

// เชื่อมต่อฐานข้อมูล
require_once 'conDB.php';

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
<header>
    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid d-flex justify-content-between">
            <a class="navbar-brand" href="index.php">เมืองเลยรถเช่า</a>
            <div class="d-flex">
                <div class="me-3">
                    <a class="nav-link active text-dark" aria-current="page" href="#">รถยนต์ส่วนตัว</a>
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
                    <li><a class="dropdown-item" href="./user/user_profile.php">ข้อมูลส่วนตัว</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>