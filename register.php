<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="stylesheet" href="./styles/register.css">
    <script></script>
</head>

<body>
    <?php
    require 'nav.php';
    ?>
    <div class="register-container" id="RegisterContainer">
        <div class="register-title"> สมัครสมาชิก </div>
        <div class="register-body">
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="Memberemail" class="form-label">อีเมล</label>
                    <input type="email" class="form-control" id="Memberemail" name="Memberemail" aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="Memberpassword" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" id="Memberpassword" name="Memberpassword" placeholder="ควรประกอบไปด้วย(a-z), (A-Z), (0-9) และ!@#$%^&*().">
                    <div id="passwordStrength"></div> <!-- แสดงความปลอดภัยของรหัสผ่าน -->
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" class="form-control" id="confirmPassword">
                    <div id="passwordError" style="color: red;"></div> <!-- ตำแหน่งที่จะแสดงข้อความแจ้งเตือน -->
                </div>
                <div class="mb-3">
                    <label for="Membername" class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" id="Membername" name="Membername">
                </div>
                <div class="mb-3">
                    <label for="Memberlastname" class="form-label">นามสกุล</label>
                    <input type="text" class="form-control" id="Memberlastname" name="Memberlastname">
                </div>
                <div class="mb-3">
                    <label for="Memberpassport" class="form-label">เลขบัตรประชาชน / Passport .</label>
                    <input type="text" class="form-control" id="Memberpassport" name="Memberpassport">
                </div>
                <div class="mb-3">
                    <label for="Memberaddress" class="form-label">ที่อยู่</label>
                    <textarea class="form-control" id="Memberaddress" name="Memberaddress" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="Memberphone" class="form-label">เบอร์โทรศัพท์</label>
                    <input type="text" class="form-control" id="Memberphone" name="Memberphone" oninput="formatPhoneNumber(this)" maxlength="10">
                </div>
                <button type="submit" class="btn btn-primary" name="register" id="registerBtn">สมัครสมาชิก</button>
            </form>
        </div>
    </div>

    <!-- php ในการสมัครสมาชิก -->
    <?php
require 'conDB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["Memberemail"];
    $password = $_POST["Memberpassword"];
    $name = $_POST["Membername"];
    $lastname = $_POST["Memberlastname"];
    $passport = $_POST["Memberpassport"];
    $address = $_POST["Memberaddress"];
    $phone = $_POST["Memberphone"];

    $defaultPic = './img/defult.webp';

    $sql = "INSERT INTO member (MemberID, Membername, Memberlastname, Memberaddress, Memberphone, Memberpassport, Memberpassword, Memberemail, Memberpic) 
    VALUES (NULL, '$name', '$lastname', '$address', '$phone', '$passport', '$password', '$email', '$defaultPic')";

    if ($con->query($sql) === TRUE) {
        // แสดง popup สมัครสมาชิกสำเร็จ
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("RegisterContainer").style.display = "none";
                document.getElementById("popup").style.display = "block";
                document.querySelector("nav").style.display = "none";
            });
        </script>';
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }

    $con->close();
}
?>

    <div class="register-done" id="popup">
        <div class="done-title">สมัครสมาชิกสำเร็จ</div>
        <img src="./img/success.png" alt="" class="img-success">
        <a href="login.php"><div class="to-signin">ไปที่หน้าเข้าสู่ระบบ</div></a>
    </div>

    <script src="./script/register.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>