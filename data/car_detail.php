<?php 
session_start(); 
require '../conDB.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>รายละเอียด</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/car_detail.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="../script/car_detail.js"></script>
</head>

<body>
    <div class="text-center">
        <h1>รายละเอียด</h1>
    </div>
    <hr>
    <button type="button" class="btn btn-warning right-align-button" onclick="EditCar()">แก้ไข</button>
    <?php
    // รับค่า id จาก URL
    $car_id = $_GET['id'];

    // คำสั่ง SQL เพื่อดึงข้อมูลรูปภาพจากตาราง car_picture โดยใช้ค่า car_id
    $sql = "SELECT * FROM car_picture WHERE car_id = '$car_id'";

    $result = mysqli_query($con, $sql);

    // ตรวจสอบว่ามีรูปภาพสำหรับรถนี้ในฐานข้อมูลหรือไม่
    if (mysqli_num_rows($result) > 0) {
        // ซ่อนปุ่ม "เพิ่มรูปภาพ" หากมีรูปภาพอยู่ในฐานข้อมูล
        echo '<script>document.querySelector(".update-picture").style.display = "none";</script>';
    } else {
        // แสดงปุ่ม "เพิ่มรูปภาพ" หากไม่มีรูปภาพในฐานข้อมูล
        echo '<button type="button" class="btn btn-warning right-align-button" onclick="showAddPicture()">เพิ่มรูปภาพ</button>';
    }
    ?>
    <!-- แสดงข้อมูลบน database ตามค่า id ที่ถูกส่งมา -->
    <div class="container">
        <div class="row">
            <?php
            $ids = $_GET['id'];
            $sql = "SELECT * FROM car WHERE car_id = '$ids' ";
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_array($result);
            ?>
            <div class="col-md-4">
                <div class="main-picture"><img src="<?= $row['car_picture1'] ?>" alt="" class="carpic"></div>
            </div>
            <div class="col-md-6">
                <div class="main-detail">
                    ID : <?= $row['car_id'] ?> <br>
                    <h5 class="text-success"> <?= $row['car_name'] ?><h5><br>
                            รายละเอียด : <?= $row['car_detail'] ?> <br>
                            ราคา <b class="text-danger"><?= $row['car_price'] ?> </b> บาท <br>
                </div>
            </div>
            <a href="car_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        </div>
    </div>

    <div class="popup-edit-car" id="UpdateCar">
        <form action="car_detail.php?id=<?= $row['car_id'] ?>" method="post" enctype="multipart/form-data">
            <div class="title-edit-car">แก้ไขข้อมูลรถ ID : <?= $row['car_id'] ?> </div>
            <div class="box">
                <label for="ชื่อรถ">ชื่อรถ</label><br>
                <input type="text" name="car_name" placeholder="<?= $row['car_name'] ?>">
            </div>
            <div class="box">
                <label for="ยี่ห้อรถ">ยี่ห้อรถ</label><br>
                <input type="text" name="car_brand" placeholder="<?= $row['car_brand'] ?>">
            </div>
            <div class="box">
                <label for="ป้ายทะเบียนรถ">ป้ายทะเบียนรถ</label><br>
                <input type="text" name="car_numplate" placeholder="<?= $row['car_numplate'] ?>">
            </div>
            <div class="box">
                <label for="เลขตัวถัง">เลขตัวถัง</label><br>
                <input type="text" name="car_vin" placeholder="<?= $row['car_vin'] ?>">
            </div>
            <div class="box">
                <label for="ราคาเช่ารถ">ราคาเช่ารถ</label><br>
                <input type="text" name="car_price" placeholder="<?= $row['car_price'] ?>">
            </div>
            <div class="box">
                <label for="รายละเอียด">รายละเอียด</label><br>
                <textarea name="car_detail" id="" cols="25" rows="25" laceholder="<?= $row['car_detail'] ?>"></textarea>
            </div>
            <input type="submit" name="updateInfo" value="อัพเดตข้อมูล">
            <button onclick="hideEditcar()">Close</button>
        </form>
    </div>

    <!--popupของเพิ่มรูปภาพ-->

    <div id="add_picture" class="AddPicture">
        <h2> เพิ่มรูปภาพ CarID: <?= $row['car_id'] ?> </h2>
        <form action="car_detail.php?id=<?= $row['car_id'] ?>" method="post" enctype="multipart/form-data">
            <table>
                <tr>
                    <td>เพิ่มรูปภาพ: <img id="previewImage2" src="" width="150px" class="mt-5 p-2 my-2 border"></td>
                    <td><input type="file" name="picture1" accept="img/" id="picture1" onchange="previewSelectedImage2(event)"></td>
                </tr>
                <tr>
                    <td>เพิ่มรูปภาพ: <img id="previewImage3" src="" width="150px" class="mt-5 p-2 my-2 border"></td>
                    <td><input type="file" name="picture2" accept="img/" id="picture2" onchange="previewSelectedImage3(event)"></td>
                </tr>
                <tr>
                    <td>เพิ่มรูปภาพ: <img id="previewImage4" src="" width="150px" class="mt-5 p-2 my-2 border"></td>
                    <td><input type="file" name="picture3" accept="img/" id="picture3" onchange="previewSelectedImage4(event)"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><a herf='#'><input type="submit" name="uploadImage" value="เพิ่มรูปภาพ"></a></td>
                </tr>
            </table>
        </form>
        <button onclick="hideAddPicture()">Close</button>
    </div>

    <!--Updateข้อมูลบนDB-->
    <?php
    $ids = $_GET['id'];
    $sql = "SELECT * FROM car WHERE car_id = '$ids' ";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);

    if (isset($_POST['updateInfo'])) {
        $car_name = $_POST['car_name'];
        $car_brand = $_POST['car_brand'];
        $car_numplate = $_POST['car_numplate'];
        $car_vin = $_POST['car_vin'];
        $car_price = $_POST['car_price'];

        $sql = "UPDATE car SET 
              car_name = '$car_name', 
              car_brand = '$car_brand', 
              car_numplate = '$car_numplate',
              car_vin = '$car_vin',
              car_price = '$car_price'
              WHERE car_id = '$ids'";

        // ทำการ query ข้อมูลไปยังฐานข้อมูล
        if (mysqli_query($con, $sql)) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error updating record: " . mysqli_error($con);
        }
    }
    ?>
    <!--สิ้นสุดฟังชั่นป็อปอัพแก้ไขข้อมูลรถ-->


    <!--เพิ่มรูปภาพเพิ่มเติม-->
    <?php
    $ids = $_GET['id'];
    $sql = "SELECT * FROM car WHERE car_id = '$ids' ";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);

    if (isset($_POST['uploadImage'])) {

        $target_dir = "../img/car/"; // ปรับเส้นทางตามที่ต้องการ

        function createNewFileName($originalFileName)
        {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "CN_" . rand(1000, 999999) . "." . $fileExtension;
            return $newFileName;
        }


        $picture1 = $target_dir . createNewFileName($_FILES["picture1"]["name"]);
        move_uploaded_file($_FILES["picture1"]["tmp_name"], $picture1);
        $picture2 = $target_dir . createNewFileName($_FILES["picture2"]["name"]);
        move_uploaded_file($_FILES["picture2"]["tmp_name"], $picture2);
        $picture3 = $target_dir . createNewFileName($_FILES["picture3"]["name"]);
        move_uploaded_file($_FILES["picture3"]["tmp_name"], $picture3);


        $sql = "INSERT INTO car_picture (carpic_id, car_id, picture1, picture2, picture3) 
            VALUES (NULL, '$ids', '$picture1', '$picture2', '$picture3')";

        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }
        $con->close();
    }
    ?>
    <!--สิ้นสุดการเพิ่มรูปภาพ-->


    <!-- รูปภาพเพิ่มเติม -->

    <div class="head-detail">
        <div class="title-detail">รูปภาพเพิ่มเติม</div>
        <div class="update-picture">
            <?php
            // รับค่า id จาก URL
            $car_id = $_GET['id'];

            // คำสั่ง SQL เพื่อดึงข้อมูลรูปภาพจากตาราง car_picture โดยใช้ค่า car_id
            $sql = "SELECT * FROM car_picture WHERE car_id = '$car_id'";
            $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
                // แสดงปุ่ม "อัพเดตรูปภาพ" หากมีรูปภาพอยู่ในฐานข้อมูล
                echo '<button type="button" class="btn btn-warning right-align-button" onclick="showUpdate()">อัพเดตรูปภาพ</button>';
            } else {
                // ซ่อนปุ่ม "เพิ่มรูปภาพ" หากไม่มีรูปภาพ
                echo '<script>document.querySelector(".update-picture").style.display = "none";</script>';
            }
            ?>
        </div>
    </div>

    <!-- popup การอัพเดตรูปภาพเพิ่มเติม -->
    <div class="popup-update" id="popUpdate">
        <form action="car_detail.php?id=<?= $row['car_id'] ?>" method="post" enctype="multipart/form-data">
            <div class="title-box">อัพเดตรูปภาพ</div>
            <?php
            // รับค่า id จาก URL
            $car_id = $_GET['id'];

            // คำสั่ง SQL เพื่อดึงข้อมูลรูปภาพจากตาราง car_picture โดยใช้ค่า car_id
            $sql = "SELECT * FROM car_picture WHERE car_id = '$car_id'";

            $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_array($result);
                $picture1 = $row['picture1'];
                $picture2 = $row['picture2'];
                $picture3 = $row['picture3'];
            ?>
                <div class="box">
                    <label for="รูปภาพ">รูปภาพ</label><br>
                    <label for="file-input" id="file-input-label">เลือกไฟล์รูปภาพ</label>
                    <input type="file" name="picture1" accept="img/" id="edit-img" onchange="EditImg1(event)">
                    <div class="preview-img">
                        <img src="<?php echo $picture1; ?>" alt="" width="250px">
                        <img id="preview-edit-img1" src="" width="250px">
                    </div>
                </div>
                <div class="box">
                    <label for="รูปภาพ">รูปภาพ</label><br>
                    <label for="file-input" id="file-input-label">เลือกไฟล์รูปภาพ</label>
                    <input type="file" name="picture2" accept="img/" id="edit-img" onchange="EditImg2(event)">
                    <div class="preview-img">
                        <img src="<?php echo $picture2; ?>" alt="" width="250px">
                        <img id="preview-edit-img2" src="" width="250px">
                    </div>
                </div>
                <div class="box">
                    <label for="รูปภาพ">รูปภาพ</label><br>
                    <label for="file-input" id="file-input-label">เลือกไฟล์รูปภาพ</label>
                    <input type="file" name="picture3" accept="img/" id="edit-img" onchange="EditImg3(event)">
                    <div class="preview-img">
                        <img src="<?php echo $picture3; ?>" alt="" width="250px">
                        <img id="preview-edit-img3" src="" width="250px">
                    </div>
                </div>
                <div class="btn-update-car-pic">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button onclick="hideUpdate()" id="close-car" class="btn btn-danger">Close</button>
                </div>
        </form>
    </div>
<?php
            } else {
            } ?>
<!-- Update picture php -->

<?php
// เชื่อมต่อกับฐานข้อมูล
include '../conDB.php';

// ตรวจสอบว่ามีการส่งฟอร์มมาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่า id จาก URL
    $car_id = $_GET['id'];

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพมาหรือไม่
    if (isset($_FILES['picture1']) && isset($_FILES['picture2']) && isset($_FILES['picture3'])) {
        // รับรูปภาพจากฟอร์ม
        $picture1 = $_FILES['picture1'];
        $picture2 = $_FILES['picture2'];
        $picture3 = $_FILES['picture3'];

        // กำหนดตัวแปรเก็บชื่อไฟล์ใหม่
        $new_filename1 = uniqid('image_1_') . '.' . pathinfo($picture1['name'], PATHINFO_EXTENSION);
        $new_filename2 = uniqid('image_2_') . '.' . pathinfo($picture2['name'], PATHINFO_EXTENSION);
        $new_filename3 = uniqid('image_3_') . '.' . pathinfo($picture3['name'], PATHINFO_EXTENSION);

        // เก็บรูปภาพใหม่ลงในโฟลเดอร์
        $target_dir = "../img/car/";
        $target_file1 = $target_dir . $new_filename1;
        $target_file2 = $target_dir . $new_filename2;
        $target_file3 = $target_dir . $new_filename3;

        // อัปโหลดและบันทึกรูปภาพใหม่
        if (
            move_uploaded_file($picture1['tmp_name'], $target_file1) &&
            move_uploaded_file($picture2['tmp_name'], $target_file2) &&
            move_uploaded_file($picture3['tmp_name'], $target_file3)
        ) {
            // อัปเดตที่อยู่ของรูปภาพในฐานข้อมูล
            $sql = "UPDATE car_picture SET picture1 = '$target_file1', picture2 = '$target_file2', picture3 = '$target_file3' WHERE car_id = '$car_id'";
            if (mysqli_query($con, $sql)) {
                echo "อัปเดตรูปภาพเรียบร้อยแล้ว";
            } else {
                echo "เกิดข้อผิดพลาดในการอัปเดตรูปภาพ: " . mysqli_error($con);
            }
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
        }
    } else {
        echo "กรุณาเลือกไฟล์รูปภาพที่ต้องการอัปโหลด";
    }
}
?>




<!--  -->


<!-- ส่วนของการแสดงรูปภาพ -->
<?php
// รับค่า id จาก URL
$car_id = $_GET['id'];

// คำสั่ง SQL เพื่อดึงข้อมูลรูปภาพจากตาราง car_picture โดยใช้ค่า car_id
$sql = "SELECT * FROM car_picture WHERE car_id = '$car_id'";

$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $picture1 = $row['picture1'];
    $picture2 = $row['picture2'];
    $picture3 = $row['picture3'];
?>
    <div class="detail">
        <div class="detail-carpicture">
            <!-- แสดงรูปภาพจากตาราง car_picture -->
            <img src="<?php echo $picture1; ?>" alt="">
            <img src="<?php echo $picture2; ?>" alt="">
            <img src="<?php echo $picture3; ?>" alt="">
        </div>
    </div>
<?php
} else {
}
$con->close();
?>

<!-- องค์ประกอบอื่นๆ -->
<div class="container-none">
    <div class="forget">
        อย่าลืมเพิ่มรูปภาพเพิ่มเติม
    </div>
</div>





<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

</html>