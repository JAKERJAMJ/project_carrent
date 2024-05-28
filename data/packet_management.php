<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการแพ็คเกจท่องเที่ยว</title>
    <link rel="stylesheet" href="../styles/packet_manage.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <div class="top-button">
        <a href="data_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        <div class="manage-car">การจัดการแพ็คเกจ</div>
        <button onclick="showPopup()" id="uppacket" type="button" class="btn btn-success"> เพิ่ม</button>
    </div>

    <div class="container">
        <div class="row view-packet">
            <?php
            require '../conDB.php';
            $sql = "SELECT * FROM package ORDER BY package_id";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $row['package_picture'] ?>" class="card-img-top" alt="packet img" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['package_name'] ?></h5>
                            <p class="card-text">
                                ID : <?= $row['package_id'] ?><br>
                                ราคาของแพ็คเกจ : <?= $row['package_price'] ?><br>
                            </p>
                            <a href="packet_detail.php?id=<?= $row['packet_id'] ?>" class="btn btn-outline-success">รายละเอียด</a>
                            <button type="button" class="btn btn-outline-danger" onclick="deletePacket(<?= $row['packet_id'] ?>)">ยกเลิกการใช้งาน</button>
                        </div>
                    </div>
                </div>

                <script>
                    function deletePacket(packet_id) {
                        if (confirm('ต้องการยกเลิกการใช้งานรถ ID ' + packet_id + '?')) {
                            fetch('delete_packet.php?id=' + packet_id, {
                                    method: 'GET'
                                })
                                .then(response => response.text())
                                .then(data => {
                                    alert(data);
                                    window.location.reload();
                                })
                                .catch(error => console.error('Error:', error));
                        }
                    }
                </script>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="package-container" id="add_packet">
        <div class="title-package">
            เพิ่มข้อมูลแพ็คเกจท่องเที่ยว
        </div>
        <div class="package-body">
            <form action="packet_management.php" method="post" enctype="multipart/form-data">
                <div class="box">
                    <label for="ชื่อแพ็คเกจท่องเที่ยว">ชื่อแพ็คเกจท่องเที่ยว</label><br>
                    <input class="form-control" type="text" id="package_name" name="package_name" placeholder="-- ชื่อแพ็คเกจท่องเที่ยว --">
                </div>
                <div class="box">
                    <label for="ราคาของแพ็คเกจท่องเที่ยว">ราคาของแพ็คเกจ</label><br>
                    <input class="form-control" type="text" name="package_price" placeholder="-- ราคาของแพ็คเกจ --">
                </div>
                <div class="box">
                    <label for="ระยะเวลา">ระยะเวลา (จำนวนกี่วัน)</label><br>
                    <input class="form-control" type="text" id="package_date" name="package_date">
                </div>
                <div class="box">
                    <label for="รูปภาพหลัก">รูปภาพหลัก</label><br>
                    <label for="file-input" id="file-input-label"><u>เลือกไฟล์รูปภาพ</u></label>
                    <input class="form-control" type="file" name="package_picture" accept="image/*" id="package_picture">
                    <img src="" id="image-preview" class="image-preview" alt="รูปภาพตัวอย่าง">
                </div>
                <div class="box-btn">
                    <button class="btn btn-success" type="submit" name="submit">บันทึก</button>
                    <button class="btn btn-danger" type="button" onclick="hidePopup()" id="close-car">Close</button>
                </div>
            </form>
        </div>
    </div>

    <?php
    require '../conDB.php';

    if (isset($_POST['submit'])) {
        $package_name = $_POST['package_name'];
        $package_price = $_POST['package_price'];
        $package_date = $_POST['package_date'];
        $package_status = "ยังไม่ถูกเช่า";

        $target_dir = "../img/packet/";

        function createNewFileName($originalFileName) {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "pack_" . rand(1000, 999999) . "." . $fileExtension;
            return $newFileName;
        }

        $package_picture = $target_dir . createNewFileName($_FILES["package_picture"]["name"]);
        move_uploaded_file($_FILES["package_picture"]["tmp_name"], $package_picture);

        $sql = "INSERT INTO package (package_name, package_picture, 
            package_price, package_date, package_status) 
            VALUES ('$package_name', '$package_picture', '$package_price', '$package_date', '$package_status')";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }
    ?>

    <script src="../script/packet_manage.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>
