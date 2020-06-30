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
if(isset($data->id)){
    
    $msg = [];
    $id = $data->id;
    date_default_timezone_set ("Asia/Jakarta");
    //GET POST BY ID FROM DATABASE
    $query = "SELECT * FROM `user` WHERE id = :id ";
    $get_stmt = $conn->prepare($query);
    $get_stmt->bindValue(':id', $id,PDO::PARAM_INT);
    $get_stmt->execute();
    
    
    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
    if($get_stmt->rowCount() > 0){
        
        // FETCH POST FROM DATBASE 
        $row = $get_stmt->fetch(PDO::FETCH_ASSOC);
        
        $user_nama = isset($data->nama) ? $data->nama : $row['nama'];
        $user_email = isset($data->email) ? $data->email : $row['email'];
        $user_username = isset($data->username) ? $data->username : $row['username'];
        $user_password = isset($data->password) ? $data->password : $row['password'];
        $user_kendaraan = isset($data->kendaraan) ? $data->kendaraan : $row['kendaraan'];
        $user_plat = isset($data->plat) ? $data->plat : $row['plat'];
        $user_modified_at = date('Y-m-d H:i:s');
        
        $update_query = "UPDATE `user` SET nama = :nama, email = :email, username = :username, password = :password, kendaraan = :kendaraan, plat = :plat, modified_at = :modified_at
        WHERE id = :id";
        
        $update_stmt = $conn->prepare($update_query);
        
        // DATA BINDING AND REMOVE SPECIAL CHARS AND REMOVE TAGS
        $update_stmt->bindValue(':nama', htmlspecialchars(strip_tags($user_nama)),PDO::PARAM_STR);
        $update_stmt->bindValue(':email', htmlspecialchars(strip_tags($user_email)),PDO::PARAM_STR);
        $update_stmt->bindValue(':username', htmlspecialchars(strip_tags($user_username)),PDO::PARAM_STR);
        $update_stmt->bindValue(':password', htmlspecialchars(strip_tags($user_password)),PDO::PARAM_STR);
        $update_stmt->bindValue(':kendaraan', htmlspecialchars(strip_tags($user_kendaraan)),PDO::PARAM_STR);
        $update_stmt->bindValue(':plat', htmlspecialchars(strip_tags($user_plat)),PDO::PARAM_STR);
        $update_stmt->bindValue(':modified_at', htmlspecialchars(strip_tags($user_modified_at)),PDO::PARAM_STR);
        $update_stmt->bindValue(':id', $id,PDO::PARAM_INT);
        
        
        if($update_stmt->execute()){
            $msg['message'] = 'Data updated successfully';
            $msg['data'] = [
            'id' => $row['id'],
            'nama' => $row['nama'],
            'email' =>$row['email'],
            'username' => $row['username'],
            'kendaraan' => $row['kendaraan'],
            'plat' => $row['plat']
        ];
        }else{
            $msg['message'] = 'data not updated';
        }   
        
    }
    else{
        $msg['message'] = 'Invlid ID';
    }  
    
    echo  json_encode($msg);
    
}
?>