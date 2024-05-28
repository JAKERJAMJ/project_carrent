<?php
require '../conDB.php';

$driver_id = $_GET['driver_id'];
$sql = "SELECT * FROM driver WHERE driver_id = '$driver_id'";
$result = mysqli_query($con, $sql);
$driver = mysqli_fetch_assoc($result);

mysqli_close($con);
?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <img src="<?= $driver['driver_picture'] ?>" class="img-fluid" alt="Driver Image">
        </div>
        <div class="col-md-8">
            <h3><?= $driver['driver_name'] ?></h3>
            <p>เบอร์โทรศัพท์: <?= $driver['driver_phone'] ?></p>
            <p>ประวัติย่อ: <?= $driver['driver_detail'] ?></p>
        </div>
    </div>
</div>
