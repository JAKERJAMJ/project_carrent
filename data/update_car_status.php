<?php
require '../conDB.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['car_id'])) {
    $car_id = $input['car_id'];
    $sql = "UPDATE car SET car_status = 'ยกเลิกการใช้งาน' WHERE car_id = ?";
    
    if ($stmt = $con->prepare($sql)) {
        $stmt->bind_param("i", $car_id);
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
    echo json_encode(['success' => false, 'message' => 'Invalid car ID']);
}

$con->close();
?>
