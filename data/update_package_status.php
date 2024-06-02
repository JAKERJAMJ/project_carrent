<?php
require '../conDB.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['package_id'])) {
    $package_id = $input['package_id'];
    $sql = "UPDATE package SET package_status = 'ยกเลิกการใช้งาน' WHERE package_id = ?";
    
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $package_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing query']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing query']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid package ID']);
}

$con->close();
?>
