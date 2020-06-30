<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require 'connect.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CHECKING, IF ID AVAILABLE ON $data
if(isset($data->user_id) && isset($data->slot_id)){
    
    $msg['message'] = '';
    $user_id = $data->user_id;
    $slot_id = $data->slot_id;
    date_default_timezone_set ("Asia/Jakarta");
    $booking = date('Y-m-d H:i:s');
    
    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
    $insert_query = "INSERT INTO parkir (user_id, slot_id, booking) VALUES ('$user_id', '$slot_id', '$booking')";
    $insert_stmt = $conn->prepare($insert_query);

    if($insert_stmt->execute()){
        $msg['message'] = 'Booking success!';
    }else{
        $msg['message'] = 'data not updated';
    } 

    echo  json_encode($msg);
    
}
?>