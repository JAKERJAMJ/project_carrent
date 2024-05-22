<?php
session_start();
require 'conDB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แพ็คเกจท่องเที่ยว</title>
    <link rel="stylesheet" href="./styles/show_package.css">
    <link rel="stylesheet" href="./styles/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
    <?php
    if (isset($_SESSION['MemberID'])) {
        require 'user_nav.php'; // Include user navigation if user is logged in
    } else {
        require 'nav.php'; // Include default navigation if user is not logged in
    }
    ?>
    <div class="head-show-package">
        <div class="title-show-package">
            แพ็คเกจท่องเที่ยว
        </div>
    </div>
    <div class="show-package">
        <div class="row view-package">
            <?php
            $sql = "SELECT * FROM package ORDER BY package_id";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($result)) {
                $new_url = str_replace("./img/", "../img/", $row['package_main_picture']);
            ?>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <img src="<?= $row['package_main_picture'] ?>" class="card-img-top" alt="package Image" style="width: 100%; height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title text-success"><?= $row['package_name'] ?></h5>
                        <p class="card-text">
                            ราคาของแพ็คเกจ : <?= $row['package_price'] ?><br>
                        </p>
                        <a href="package_detail.php?id=<?= $row['package_id'] ?>" class="btn btn-outline-success">รายละเอียด</a>
                        <button type="button" class="btn btn-outline-warning">เช่าแพ็คเกจ</button>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>            
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
