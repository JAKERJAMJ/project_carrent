<?php
session_start();
require '../conDB.php';

if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    echo "Forbidden";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fix_id = $_POST['fix_id'];

    $sql = "DELETE FROM fix_car WHERE fix_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $fix_id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo "ลบรายการสำเร็จ";
    } else {
        http_response_code(500);
        echo "เกิดข้อผิดพลาดในการลบรายการ";
    }
} else {
    http_response_code(405);
    echo "Method Not Allowed";
}
?>
