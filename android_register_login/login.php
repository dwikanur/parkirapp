<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require 'connect.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CREATE MESSAGE ARRAY AND SET EMPTY
$msg = [];

// CHECK IF RECEIVED DATA FROM THE REQUEST
if(isset($data->username) && isset($data->password) ){
    // CHECK DATA VALUE IS EMPTY OR NOT
    if(!empty($data->username) && !empty($data->password)){

        $pass = md5($data->password);
        
        $query = "SELECT * FROM `user` WHERE username='$data->username' AND password='$data->password'";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $data->username);
        $stmt->bindParam(':password', $pass);
        $stmt->execute();
        $hasildata = $stmt->fetch(); // jalankan query

         // mengecek row
        if($stmt->rowCount()>0){
            $msg['status'] = 'success';
            $msg['message'] = 'Login Successfully';
            $msg['data'] = [
                'id' => $hasildata['id'],
                'nama' => $hasildata['nama'],
                'email' =>$hasildata['email'],
                'username' => $hasildata['username']
            ];
        }else{
            $msg['status'] = 'failed';
            $msg['message'] = 'Username OR Password Wrong';
        } 
        
    }else{
        $msg['message'] = 'Oops! empty field detected. Please fill all the fields';
    }
}
else{
    $msg['message'] = 'Please fill all the fields';
}
//ECHO DATA IN JSON FORMAT
echo  json_encode($msg);
?>