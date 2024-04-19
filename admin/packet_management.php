<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการแพ็คเกจท่องเที่ยว</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/packet_manage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="top-button">
        <a href="data_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        <div class="manage-car">การจัดการแพ็คเกจ</div>
        <button onclick="showPopup()" id="uppacket" type="button" class="btn btn-success"> เพิ่ม</button>
    </div>

    <!-- container view packet in database -->
    <div class="container">
        <div class="row view-packet">
            <?php
            require '../conDB.php';
            $sql = "SELECT * FROM packet ORDER BY packet_id";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
            ?>
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <img src="<?= $row['packet_main_picture'] ?>" class="card-img-top" alt="packet img" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?= $row['packet_name'] ?></h5>
                            <p class="card-text">
                                ID : <?= $row['packet_id'] ?><br>
                                ราคาของแพ็คเกจ : <?= $row['packet_price'] ?><br>
                            </p>
                            <a href="packet_detail.php?id=<?= $row['packet_id'] ?>" class="btn btn-outline-success">รายละเอียด</a>
                            <button type="button" class="btn btn-outline-danger" onclick="deletePacket(<?= $row['packet_id'] ?>)">ยกเลิกการใช้งาน</button>
                        </div>
                    </div>
                </div>

                <script>
                    function deletePacket(packet_id) {
                        if (confirm('ต้องการยกเลิกการใช้งานรถ ID ' + packet_id + '?')) {
                            // ส่งคำขอไปยังไฟล์ PHP พร้อมกับ ID ของรถยนต์
                            fetch('delete_packet.php?id=' + packet_id, {
                                    method: 'GET'
                                })
                                .then(response => response.text())
                                .then(data => {
                                    alert(data); // แสดงข้อความจากการตอบกลับของ PHP
                                    window.location.reload(); // โหลดหน้าใหม่เพื่ออัพเดทข้อมูล
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

    <!-- container-box form add data to packet_database -->
    <div class="container-box" id="add_packet">
        <form action="packet_management.php" method="post" enctype="multipart/form-data">
            <!-- ทำฟอร์มหรือเพิ่มองค์ประกอบในป็อปอัพ -->
            <div class="title-box"><u>เพิ่มข้อมูลแพ็คเกจท่องเที่ยว</u></div>
            <div class="box">
                <label for="ชื่อแพ็คเกจท่องเที่ยว">ชื่อแพ็คเกจท่องเที่ยว</label><br>
                <input type="text" name="packet_name" placeholder="-- ชื่อแพ็คเกจท่องเที่ยว --">
            </div>
            <div class="box">
                <label for="ราคาของแพ็คเกจท่องเที่ยว">ราคาของแพ็คเกจ</label><br>
                <input type="text" name="packet_price" placeholder="-- ราคาของแพ็คเกจ --">
            </div>
            <div class="box">
                <label for="สถานที่ท่องเที่ยวหลัก">สถานที่ท่องเที่ยวหลัก</label><br>
                <input type="text" name="packet_main_tourist" placeholder="-- สถานที่ท่องเที่ยวหลัก --">
            </div>
            <div class="box">
                <label for="วันที่เริ่มต้นของแพ็คเกจ">วันเริ่มต้น</label><br>
                <input type="date" id="start_tourist" name="start_tourist">
            </div>
            <div class="box">
                <label for="วันที่วันสิ้นสุดของแพ็คเกจ">วันสิ้นสุด</label><br>
                <input type="date" id="end_tourist" name="end_tourist">
            </div>
            <div class="box">
                <label for="รูปภาพหลัก">รูปภาพหลัก</label><br>
                <label for="file-input" id="file-input-label"><u>เลือกไฟล์รูปภาพ</u></label>
                <input type="file" name="packet_main_picture" accept="img/" id="packet_main_picture">
                <img src="" id="image-preview" class="image-preview" alt="รูปภาพตัวอย่าง">
            </div>
            <input type="submit" name="submit" value="Submit">
            <button onclick="hidePopup()" id="close-car">Close</button>
    </div>
    </form>
    </div>



    <?php
    require '../conDB.php'; // Include your DB connection file

    if (isset($_POST['submit'])) {
        $packet_name = $_POST['packet_name'];
        $packet_price = $_POST['packet_price'];
        $packet_main_tourist = $_POST['packet_main_tourist'];
        $start_tourist = $_POST['start_tourist'];
        $end_tourist = $_POST['end_tourist'];

        $target_dir = "../img/packet/"; // ปรับเส้นทางตามที่ต้องการ

        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "pack_" . rand(1000, 999999) . "." . $fileExtension; // สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }


        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $packet_main_picture = $target_dir . createNewFileName($_FILES["packet_main_picture"]["name"]);
        move_uploaded_file($_FILES["packet_main_picture"]["tmp_name"], $packet_main_picture);
        // ทำซ้ำสำหรับ car_picture2, car_picture3, และ car_picture4 ...


        // SQL Query
        $sql = "INSERT INTO packet (packet_name, packet_price, 
            packet_main_tourist, start_tourist, end_tourist, packet_main_picture ) 
            VALUES ('$packet_name', '$packet_price', '$packet_main_tourist', '$start_tourist',
            '$end_tourist', '$packet_main_picture')";

        // Execute SQL Query
        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

        $con->close();
    }


    ?>


    <script src="../script/packet_manage.js"></script>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
</body>

</html>