<?php
require '../conDB.php';  // เชื่อมต่อกับฐานข้อมูล

$carId = $_GET['id'];  // รับ ID จากคำขอ

// สร้างคำสั่ง SQL สำหรับการลบโดยใช้ prepared statement
$stmt = $con->prepare("DELETE FROM car WHERE car_id = ?");
$stmt->bind_param("i", $carId); // 'i' หมายถึง integer

// ทำการลบและตรวจสอบว่าลบสำเร็จหรือไม่
if($stmt->execute()){
    echo "รถ ID $carId ถูกยกเลิกการใช้งานแล้ว.";
} else {
    echo "เกิดข้อผิดพลาดในการยกเลิกการใช้งาน: " . $stmt->error;
}

$stmt->close(); // ปิดการเชื่อมต่อกับ statement
$con->close(); // ปิดการเชื่อมต่อกับฐานข้อมูล
?>
