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
$msg =[];

// CHECK IF RECEIVED DATA FROM THE REQUEST
if(isset($data->nama) && isset($data->email) && isset($data->username) && isset($data->password)){
    // CHECK DATA VALUE IS EMPTY OR NOT
    if(!empty($data->nama) && !empty($data->email) && !empty($data->username) && !empty($data->password)){

        $pass = md5($data->password);
        
        $insert_query = "INSERT INTO `user`(nama,email,username,password) VALUES(:nama,:email,:username,:password)";
        
        $insert_stmt = $conn->prepare($insert_query);
        // DATA BINDING
        $insert_stmt->bindValue(':nama', htmlspecialchars(strip_tags($data->nama)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':email', htmlspecialchars(strip_tags($data->email)),PDO::PARAM_STR);
        $insert_stmt->bindValue(':username', htmlspecialchars(strip_tags($data->username)),PDO::PARAM_STR);
		$insert_stmt->bindValue(':password', htmlspecialchars(strip_tags($pass)),PDO::PARAM_STR);
        
        if($insert_stmt->execute()){
            $msg['message'] = 'Data Inserted Successfully';
            $msg['data'] = [
                'nama' => $data->nama,
                'email' => $data->email,
                'username' => $data->username
            ];
        }
        else{
            $msg['message'] = 'Data not Inserted';
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