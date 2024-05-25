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
    <title>จัดการสมัครสมาชิก</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/manage_member.css">
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
    <div class="head-managemember">
        <a href="inside_management.php" class="btn btn-outline-dark" style="align-self: flex-start;">กลับ</a>
        <div class="title-member">
            <p class="title">การจัดการสมาชิก</p>
        </div>
        <div class="btn-button">
            <button type="button" class="btn btn-success" onclick="AddMember()">
                เพิ่มสมาชิก
            </button>
        </div>
    </div>
    <!-- Modal popup -->
    <div class="add-member" id="AddMember">
        <div class="title-addmember">
            เพิ่มสมาชิก
        </div>
        <div class="addmember-body">
            <form action="manage_member.php" method="POST">
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
                <button type="submit" class="btn btn-primary" name="register" id="register">เพิ่มสมาชิก</button>
                <button type="button" class="btn btn-secondary" name="cancel" id="cancel" onclick="Cancel()">ยกเลิก</button>
            </form>
        </div>
        <?php
        require '../conDB.php';

        if (isset($_POST['register'])) {
            $email = $_POST["Memberemail"];
            $password = $_POST["Memberpassword"];
            $name = $_POST["Membername"];
            $lastname = $_POST["Memberlastname"];
            $passport = $_POST["Memberpassport"];
            $address = $_POST["Memberaddress"];
            $phone = $_POST["Memberphone"];

            $defaultPic = '../img/default.webp';

            $sql = "INSERT INTO member (MemberID, Membername, Memberlastname, Memberaddress, Memberphone, Memberpassport, Memberpassword, Memberemail, Memberpic) 
                            VALUES (NULL, '$name', '$lastname', '$address', '$phone', '$passport', '$password', '$email', '$defaultPic')";

            if ($con->query($sql) === TRUE) {
                echo "<script>alert('เพิ่มสมาชิกเรียบร้อยแล้ว'); window.location.href = window.location.href;</script>";
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการเพิ่มข้อมูล');</script>";
            }
        }
        ?>
    </div>

    <div class="body-member">
        <div class="search-container">
            <form action="manage_member.php" method="GET">
                <input class="form-control" type="text" placeholder="ค้นหาด้วยเลขบัตรประชาชน" name="search">
                <button type="submit">ค้นหา</button>
                <a href="manage_member.php">ล้างค้นหา</a>
            </form>
        </div>
        <div class="member-view">
            <table class="table table-bordered">
                <tr>
                    <th>ลำดับที่</th>
                    <th>รหัสสมาชิก</th>
                    <th>ชื่อสมาชิก</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>รายละเอียดเพิ่มเติม</th>
                    <th>Action</th>
                </tr>
                <?php
                require_once '../conDB.php';

                $limit = 15;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start = ($page - 1) * $limit;

                // Check if the search parameter is set in the URL
                if (isset($_GET['search'])) {
                    $search_term = $_GET['search'];

                    // Use the search term to filter the query
                    $sql = "SELECT * FROM member WHERE Memberpassport LIKE '%$search_term%'";
                } else {
                    // If search parameter is not set, retrieve all members
                    $sql = "SELECT * FROM member";
                }

                $result = mysqli_query($con, $sql);

                $total_rows = mysqli_num_rows($result);
                $total_pages = ceil($total_rows / $limit); // Calculate total pages

                mysqli_data_seek($result, $start);

                $counter = $start + 1; // Start counting from the first item on the current page
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $counter . "</td>";
                    echo "<td>" . $row['MemberID'] . "</td>";
                    echo "<td>" . $row['Membername'] . ' ' . $row['Memberlastname'] . "</td>";
                    echo "<td>" . $row['Memberphone'] . "</td>";
                    echo "<td>";
                    echo '<button type="button" class="btn btn-warning">รายละเอียดเพิ่มเติม</button>';
                    echo "</td>";
                    echo "<td>";
                    echo '<button type="button" class="btn btn-warning btn-sm mr-2">แก้ไข</button>';
                    echo '&nbsp;&nbsp;&nbsp;';
                    echo '<button type="button" class="btn btn-danger btn-sm mr-2" onclick="cancelCarRental(' . $row['carrent_id'] . ')">ยกเลิก</button>';
                    echo "</td>";
                    echo "</tr>";
                    $counter++;
                }

                mysqli_close($con);
                ?>
            </table>
            <nav>
                <ul class="pagination">
                    <?php
                    // Display pagination links
                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item" . ($i == $page ? ' active' : '') . "'><a class='page-link' href='?page=$i'>$i</a></li>";
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>


    <script src="../script/manage_member.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>

