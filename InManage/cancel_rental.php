<?php
require '../conDB.php';

// Check if car rent ID is provided
if (isset($_GET['carrent_id'])) {
    // Sanitize the input to prevent SQL injection
    $carRentID = mysqli_real_escape_string($con, $_GET['carrent_id']);

    // SQL query to delete the car rental entry
    $sql = "DELETE FROM carrent WHERE carrent_id = '$carRentID'";

    // Execute the query
    if (mysqli_query($con, $sql)) {
        // If deletion is successful, redirect back to the page
        header("Location: manage_carrent.php");
        exit();
    } else {
        // If deletion fails, display an error message
        echo "เกิดข้อผิดพลาดในการยกเลิกการเช่ารถ: " . mysqli_error($con);
    }
} else {
    // If car rent ID is not provided, redirect back to the page
    header("Location: manage_carrent.php");
    exit();
}

// Close the database connection
mysqli_close($con);
?>
