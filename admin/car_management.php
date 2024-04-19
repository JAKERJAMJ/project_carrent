<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการรถ</title>
    <link rel="stylesheet" href="../styles/car_manage.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="../script/car_manage.js"></script>
</head>
<body>
<div class="top-button">
        <a href="data_management.php"><button type="button" class="btn btn-outline-dark" id="back">กลับ</button></a>
        <div class="manage-car">การจัดการรถ</div>
        <button onclick="showPopup()" id="upcar" type="button" class="btn btn-success">เพิ่ม</button>
</div>
        <div class="container">
            <div class="row view-car">
                <?php
                require '../conDB.php';
                $sql = "SELECT * FROM car ORDER BY car_id";
                $result = mysqli_query($con,$sql);
                while ($row = mysqli_fetch_array($result)){
                ?>
                <div class= "col-md-3 mb-4">
                    <div class="card">
                        <img src="<?=$row['car_picture1']?>" class="card-img-top" alt="Car Image" style="width: 100%; height: 250px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title text-success"><?=$row['car_name']?></h5>
                            <p class="card-text">
                            ID : <?=$row['car_id']?><br>
                            ยี่ห้อรถ : <?=$row['car_brand']?><br>
                            ป้ายทะเบียนรถ : <?=$row['car_numplate']?>
                            </p>
                            <a href="car_detail.php?id=<?=$row['car_id']?>" class="btn btn-outline-success">รายละเอียด</a>
                            <button type="button" class="btn btn-outline-danger" onclick="deleteCar(<?=$row['car_id']?>)">ยกเลิกการใช้งาน</button>
                        </div>

                    </div>
                </div>
                <?php
                    }
                ?>
            </div>
        </div>
</div>

<!-- popuo container-box => add_car -->
<div class="container-box" id="add_car">
    <form action="car_management.php" method="post" enctype="multipart/form-data">
        <!-- ทำฟอร์มหรือเพิ่มองค์ประกอบในป็อปอัพ -->
        <div class="title-box"><u>เพิ่มข้อมูลรถ</u></div>
            <div class="box">
                <label for="ชื่อรถ">ชื่อรถ</label><br>
                <input type="text" name="car_name" placeholder="-- ชื่อรถ --">
            </div>
            <div class="box">
                <label for="ยี่ห้อ">ยี่ห้อ</label><br>
                <input type="text" name="car_brand" placeholder="-- ยี่ห้อรถ --">
            </div>
            <div class="box">
                <label for="ป้ายทะเบียนรถ">ป้ายทะเบียนรถ</label><br>
                <input type="text" name="car_numplate" placeholder="-- ป้ายทะเบียนรถ --">
            </div>
            <div class="box">
                <label for="เลขตัวถัง">เลขตัวถัง</label><br>
                <input type="text" name="car_vin" placeholder="-- เลขตัวถัง --">
            </div>
            <div class="box">
                <label for="ราคาเช่า">ราคาเช่า</label><br>
                <input type="text" name="car_price" placeholder="-- ราคาเช่า --">
            </div>
            <div class="box">
                <label for="รายละเอียด">รายละเอียด</label><br>
                <textarea name="car_detail" class="detailbox" placeholder="-- กรุณากรอกรายละเอียด --"></textarea>
            </div>
            <div class="box">
                <label for="รูปภาพ">รูปภาพ</label><br>
                <label for="file-input" id="file-input-label">เลือกไฟล์รูปภาพ</label>
                <input type="file" name="car_picture1" accept="img/" id="car_picture1" onchange="previewImage()">
                <div class="preview-img">
                    <img src="" id="image-preview" class="image-preview" alt="รูปภาพตัวอย่าง" style="display:none;">
                </div>
            </div>
        <input type="submit" name="submit" value="Submit" id="submit">
        <button onclick="hidePopup()" id="close-car">Close</button>

        </div>
    </form>
</div>

<?php
require '../conDB.php'; // Include your DB connection file

    if(isset($_POST['submit'])) { 
        $car_name = $_POST['car_name'];
        $car_brand = $_POST['car_brand'];
        $car_numplate = $_POST['car_numplate'];
        $car_vin = $_POST['car_vin'];
        $car_price = $_POST['car_price'];
        $car_detail = $_POST['car_detail'];

        $target_dir = "../img/car/"; // ปรับเส้นทางตามที่ต้องการ

        function createNewFileName($originalFileName) {
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
            $newFileName = "CN_" . rand(1000,999999) . "." . $fileExtension;// สร้างชื่อไฟล์แบบไม่ซ้ำ
            return $newFileName;
        }


        // ประมวลผลและย้ายไฟล์ทั้งหมด
        $car_picture1 = $target_dir . createNewFileName($_FILES["car_picture1"]["name"]);
        move_uploaded_file($_FILES["car_picture1"]["tmp_name"], $car_picture1);
        // ทำซ้ำสำหรับ car_picture2, car_picture3, และ car_picture4 ...


        // SQL Query
        $sql = "INSERT INTO car (car_name, car_brand, 
                car_numplate, car_vin, car_price, car_detail, 
                car_picture1 ) 
                VALUES ('$car_name', '$car_brand', '$car_numplate', 
                '$car_vin', '$car_price', '$car_detail', '$car_picture1')";

        // Execute SQL Query
        if ($con->query($sql) === TRUE) {
            echo '<script>window.location.href = window.location.href;</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $con->error;
        }

$con->close();
    }

?>
</body>
</html>