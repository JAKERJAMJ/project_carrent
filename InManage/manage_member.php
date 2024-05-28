<?php
session_start();

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); window.location.href='../login.php';</script>";
    exit;
}

require_once '../conDB.php';

$sql_member = "SELECT * FROM member";
$member_result = mysqli_query($con, $sql_member);

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
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="head-member">
        <a href="inside_management.php" class="btn btn-outline-dark" style="align-self: flex-start;">กลับ</a>
        <div class="title-member">
            <p class="title">การจัดการสมาชิก</p>
        </div>
        <div class="btn-member">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#AddMemberModal">เพิ่มสมาชิก</button>
        </div>
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="AddMemberModal" tabindex="-1" aria-labelledby="AddMemberModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="AddMemberModalLabel">เพิ่มสมาชิก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="manage_member.php" method="POST" id="AddMemberForm">
                        <div class="mb-3">
                            <label for="Memberemail" class="form-label">อีเมล</label>
                            <input type="email" class="form-control" id="Memberemail" name="Memberemail" aria-describedby="emailHelp" required>
                        </div>
                        <div class="mb-3">
                            <label for="Memberpassword" class="form-label">รหัสผ่าน</label>
                            <input type="password" class="form-control" id="Memberpassword" name="Memberpassword" required>
                            <div id="passwordStrength"></div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">ยืนยันรหัสผ่าน</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                            <div id="passwordError" style="color: red;"></div>
                        </div>
                        <div class="mb-3">
                            <label for="Membername" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" id="Membername" name="Membername" required>
                        </div>
                        <div class="mb-3">
                            <label for="Memberlastname" class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" id="Memberlastname" name="Memberlastname" required>
                        </div>
                        <div class="mb-3">
                            <label for="Memberpassport" class="form-label">เลขบัตรประชาชน / Passport</label>
                            <input type="text" class="form-control" id="Memberpassport" name="Memberpassport" required>
                        </div>
                        <div class="mb-3">
                            <label for="Memberaddress" class="form-label">ที่อยู่</label>
                            <textarea class="form-control" id="Memberaddress" name="Memberaddress" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="Memberphone" class="form-label">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="Memberphone" name="Memberphone" oninput="formatPhoneNumber(this)" maxlength="10" required>
                        </div>
                        <button type="submit" class="btn btn-primary" name="register">เพิ่มสมาชิก</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div class="body-member">
        <div class="title-body">การจัดการสมาชิก</div>
        <div class="search-container">
            <form action="manage_member.php" method="GET">
                <input class="form-control" type="text" placeholder="ค้นหาด้วยเลขบัตรประชาชน" name="search" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="btn btn-outline-primary">ค้นหา</button>
                <a href="manage_member.php" class="btn btn-outline-secondary">ล้างค้นหา</a>
            </form>
        </div>
        <div class="table-view-member">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ลำดับที่</th>
                        <th>รหัสสมาชิก</th>
                        <th>ชื่อสมาชิก</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>รายละเอียดเพิ่มเติม</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once '../conDB.php';

                    $limit = 15;
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $start = ($page - 1) * $limit;

                    if (isset($_GET['search'])) {
                        $search_term = $_GET['search'];
                        $sql = $con->prepare("SELECT * FROM member WHERE Memberpassport LIKE ? LIMIT ?, ?");
                        $search_term = "%{$search_term}%";
                        $sql->bind_param('sii', $search_term, $start, $limit);
                    } else {
                        $sql = $con->prepare("SELECT * FROM member LIMIT ?, ?");
                        $sql->bind_param('ii', $start, $limit);
                    }

                    $sql->execute();
                    $result = $sql->get_result();
                    $total_rows = $result->num_rows;
                    $total_pages = ceil($total_rows / $limit);

                    $counter = $start + 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $counter . "</td>";
                        echo "<td>" . $row['MemberID'] . "</td>";
                        echo "<td>" . $row['Membername'] . ' ' . $row['Memberlastname'] . "</td>";
                        echo "<td>" . $row['Memberphone'] . "</td>";
                        echo "<td><a href='member_detail.php?MemberID=" . $row['MemberID'] . "' class='btn btn-warning'>รายละเอียดเพิ่มเติม</a></td>";
                        echo "<td>
                                <button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editMemberModal" . $row['MemberID'] . "'>แก้ไข</button>
                                <button type='button' class='btn btn-danger btn-sm' onclick='deleteMember(" . $row['MemberID'] . ")'>ยกเลิก</button>
                              </td>";
                        echo "</tr>";

                        // Edit Member Modal
                        echo "<div class='modal fade' id='editMemberModal" . $row['MemberID'] . "' tabindex='-1' aria-labelledby='editMemberModalLabel' aria-hidden='true'>
                                <div class='modal-dialog'>
                                    <div class='modal-content'>
                                        <div class='modal-header'>
                                            <h5 class='modal-title' id='editMemberModalLabel'>แก้ไขสมาชิก</h5>
                                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                        </div>
                                        <div class='modal-body'>
                                            <form action='edit_member.php' method='POST'>
                                                <input type='hidden' name='MemberID' value='" . $row['MemberID'] . "'>
                                                <div class='mb-3'>
                                                    <label for='editMembername" . $row['MemberID'] . "' class='form-label'>ชื่อ</label>
                                                    <input type='text' class='form-control' id='editMembername" . $row['MemberID'] . "' name='Membername' value='" . $row['Membername'] . "' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label for='editMemberlastname" . $row['MemberID'] . "' class='form-label'>นามสกุล</label>
                                                    <input type='text' class='form-control' id='editMemberlastname" . $row['MemberID'] . "' name='Memberlastname' value='" . $row['Memberlastname'] . "' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label for='editMemberphone" . $row['MemberID'] . "' class='form-label'>เบอร์โทรศัพท์</label>
                                                    <input type='text' class='form-control' id='editMemberphone" . $row['MemberID'] . "' name='Memberphone' value='" . $row['Memberphone'] . "' required>
                                                </div>
                                                <div class='mb-3'>
                                                    <label for='editMemberaddress" . $row['MemberID'] . "' class='form-label'>ที่อยู่</label>
                                                    <textarea class='form-control' id='editMemberaddress" . $row['MemberID'] . "' name='Memberaddress' rows='3' required>" . $row['Memberaddress'] . "</textarea>
                                                </div>
                                                <div class='mb-3'>
                                                    <label for='editMemberpassword" . $row['MemberID'] . "' class='form-label'>รหัสผ่านใหม่</label>
                                                    <input type='password' class='form-control' id='editMemberpassword" . $row['MemberID'] . "' name='Memberpassword'>
                                                </div>
                                                <button type='submit' class='btn btn-primary'>บันทึกการเปลี่ยนแปลง</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                              </div>";

                        $counter++;
                    }

                    mysqli_close($con);
                    ?>
                </tbody>
            </table>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>


    <script src="../script/manage_member.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
    <script>
        function formatPhoneNumber(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length > 3 && value.length <= 7) {
                input.value = value.substring(0, 3) + '-' + value.substring(3);
            } else if (value.length > 7) {
                input.value = value.substring(0, 3) + '-' + value.substring(3, 7) + '-' + value.substring(7, 10);
            } else {
                input.value = value;
            }
        }

        function deleteMember(memberID) {
            if (confirm('Are you sure you want to delete this member?')) {
                window.location.href = 'delete_member.php?id=' + memberID;
            }
        }
    </script>
</body>

</html>
