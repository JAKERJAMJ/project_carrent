<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/login.css">
</head>

<body>
    <?php
    require 'nav.php';
    ?>
    <div class="signin-container">
        <div class="signin-title"> Sign in </div>
        <div class="signin-body">
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Sign In</button>
                <div class="signup-in">
                    คุณยังไม่ได้สมัครสมาชิกใช่หรือไม่? <a href="register.php">สมัครสมาชิก</a>
                </div>
            </form>
        </div>
    </div>
    <?php
    require 'conDB.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับค่าอีเมลและรหัสผ่านจากฟอร์ม
        $email = $_POST["email"];
        $password = $_POST["password"];

        // ตรวจสอบค่าอีเมลและรหัสผ่าน
        if (isset($_POST["email"]) && isset($_POST["password"])) {
            // เพิ่มเงื่อนไขตรวจสอบว่าเป็น admin หรือไม่
            if ($email == "admin@admin.com" && $password == "admin1234.") {
                $_SESSION['admin'] = true;
                // หากค่าตรงกัน ให้เปลี่ยนเส้นทางไปยังหน้าที่คุณต้องการ (Admin)
                header("Location: admin.php");
                exit;
            } else {
                // หากค่าไม่ตรงกัน สามารถทำอะไรต่อได้ตามต้องการ เช่น แสดงข้อความผิดพลาด
                $query = "SELECT * FROM member WHERE Memberemail = '$email' AND Memberpassword = '$password'";
                $result = mysqli_query($con, $query);

                $row = mysqli_fetch_array($result);

                if ($row) {
                    $_SESSION['MemberID'] = $row['MemberID'];
                    $_SESSION['Membername'] = $row['Membername'];
                    $_SESSION['Memberlastname'] = $row['Memberlastname'];

                    header("location: index.php");
                    exit;
                } else {
                    // หากไม่พบผู้ใช้ในฐานข้อมูล
                    echo "<script>alert('รหัสผ่านหรืออีเมลไม่ถูกต้อง');</script>";
                }
            }
        }
    }
    ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>