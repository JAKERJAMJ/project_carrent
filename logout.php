<?php
// เริ่มหรือดำเนินการกับเซสชันที่มีอยู่แล้ว
session_start();

// ลบข้อมูล session ทั้งหมด
session_destroy();

// ส่งผู้ใช้กลับไปยังหน้าหลักหรือหน้าที่ต้องการ
header("Location: index.php"); // แก้ตามเส้นทางที่คุณต้องการ
exit();
?>
