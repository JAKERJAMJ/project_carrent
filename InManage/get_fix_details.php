<?php
require '../conDB.php';

$fix_id = $_GET['fix_id'];

$sql = "SELECT * FROM fix_car WHERE fix_id = '$fix_id'";
$result = mysqli_query($con, $sql);

if ($result) {
    $data = mysqli_fetch_assoc($result);
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No data found']);
}

mysqli_close($con);
?>
