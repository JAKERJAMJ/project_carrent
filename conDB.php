<?php
$servername = "localhost";
$username = "root";
$password = "root";
$database = "carrent";
// create connection
$con = new mysqli($servername,$username,$password,$database);

// check connection
if (mysqli_connect_error()){
    echo "connect database fail";
}else{
    echo "";
}
    
?>