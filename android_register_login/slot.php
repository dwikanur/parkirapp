<?php

// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");

// INCLUDING DATABASE AND MAKING OBJECT
require 'connect.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// MAKE SQL QUERY
// IF GET POSTS ID, THEN SHOW POSTS BY ID OTHERWISE SHOW ALL POSTS
$query = "SELECT * FROM `slot`"; 

$stmt = $conn->prepare($query);

$stmt->execute();

//CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
if($stmt->rowCount() > 0){
    
    // CREATE POSTS ARRAY
    $array = [];
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        $data = [
            'id' => $row['id'],
            'lokasi_id' => $row['lokasi_id'],
            'nama slot' => $row['nama'],
            'available' => $row['available']
        ];
        // PUSH POST DATA IN OUR $posts_array ARRAY
        array_push($array, $data);
    }
    //SHOW POST/POSTS IN JSON FORMAT
    echo json_encode($array);

}
else{
    //IF THER IS NO POST IN OUR DATABASE
    echo json_encode(['message'=>'No post found']);
}
?>