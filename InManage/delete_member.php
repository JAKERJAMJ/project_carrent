<?php
require '../conDB.php';

if (isset($_GET['id'])) {
    $memberID = $_GET['id'];

    $sql = $con->prepare("DELETE FROM member WHERE MemberID = ?");
    $sql->bind_param('i', $memberID);

    if ($sql->execute()) {
        echo "<script>alert('ลบสมาชิกเรียบร้อยแล้ว'); window.location.href='manage_member.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.location.href='manage_member.php';</script>";
    }

    $sql->close();
    $con->close();
}
?>
