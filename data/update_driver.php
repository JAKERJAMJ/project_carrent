<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $driver_id = $_POST['driver_id'];

    $sql = "UPDATE driver SET driver_status = 'ยกเลิกการใช้งาน' WHERE driver_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $driver_id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo "ยกเลิกการใช้งานสำเร็จ";
    } else {
        http_response_code(500);
        echo "เกิดข้อผิดพลาดในการยกเลิกการใช้งาน";
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
?>
