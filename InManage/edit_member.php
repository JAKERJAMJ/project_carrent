<?php
require_once '../conDB.php';

if (isset($_POST['MemberID'])) {
    $MemberID = $_POST['MemberID'];
    $Membername = $_POST['Membername'];
    $Memberlastname = $_POST['Memberlastname'];
    $Memberphone = $_POST['Memberphone'];
    $Memberaddress = $_POST['Memberaddress'];
    $Memberpassword = isset($_POST['Memberpassword']) && !empty($_POST['Memberpassword']) ? password_hash($_POST['Memberpassword'], PASSWORD_DEFAULT) : null;

    $update_query = "UPDATE member SET Membername = ?, Memberlastname = ?, Memberphone = ?, Memberaddress = ?" . ($Memberpassword ? ", Memberpassword = ?" : "") . " WHERE MemberID = ?";
    $stmt = $con->prepare($update_query);
    
    if ($Memberpassword) {
        $stmt->bind_param('sssssi', $Membername, $Memberlastname, $Memberphone, $Memberaddress, $Memberpassword, $MemberID);
    } else {
        $stmt->bind_param('ssssi', $Membername, $Memberlastname, $Memberphone, $Memberaddress, $MemberID);
    }

    if ($stmt->execute()) {
        echo "<script>alert('อัพเดตข้อมูลสำเร็จ'); window.location.href = 'manage_member.php';</script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "<script>alert('ไม่พบข้อมูลสมาชิก'); window.location.href = 'manage_member.php';</script>";
}
$con->close();
?>
