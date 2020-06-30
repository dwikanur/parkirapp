<?php
// SET HEADER
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: PUT");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// INCLUDING DATABASE AND MAKING OBJECT
require 'connect.php';
date_default_timezone_set ("Asia/Jakarta");
$db_connection = new Database();
$conn = $db_connection->dbConnection();

// GET DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));

//CHECKING, IF ID AVAILABLE ON $data
if(isset($data->plat) && isset($data->parkir_id)){
    
    $msg['message'] = '';
    $platnomor = $data->plat;
    
    //GET POST BY ID FROM DATABASE
    $get_post = "SELECT parkir.user_id, parkir.id, parkir.slot_id, slot.nama, lokasi.harga, user.plat, parkir.masuk, parkir.keluar, parkir.booking
    FROM user
    JOIN parkir ON user.id = parkir.user_id
    JOIN slot ON parkir.slot_id = slot.id
    JOIN lokasi ON slot.lokasi_id = lokasi.id
    WHERE user.plat = '$platnomor'";
    $get_stmt = $conn->prepare($get_post);
    $get_stmt->execute();
    
    
    //CHECK WHETHER THERE IS ANY POST IN OUR DATABASE
    if($get_stmt->rowCount() > 0){
        
        // FETCH POST FROM DATBASE 
        $row = $get_stmt->fetch(PDO::FETCH_ASSOC);
        $harga = $row['harga'];
        $waktumasuk = $row['masuk'];
        $waktukeluar = date('H:i:s');
        $tanggal = date('Y-m-d H:i:s');
        $parkir_id = $row['id'];
        $slot_parkir = $row['nama'];
        $tanggalparkir = date('Y-m-d', strtotime($tanggal)); 


        $startparkir = strtotime($waktumasuk);
        $endparkir = strtotime($waktukeluar);
        $difference = abs($endparkir-$startparkir);
        $result = $difference/3600;
        $hours = floor($difference/3600);
        $minute = floor(($difference-$hours*3600)/60);
        $second = floor (($difference-$hours*3600-$minute*60));
        if($minute>0){
            $result = $hours+1;
        }
        $total_harga = $result*$harga;

        // CHECK, IF NEW UPDATE REQUEST DATA IS AVAILABLE THEN SET IT OTHERWISE SET OLD DATA
        //$post_title = isset($data->title) ? $data->title : $row['title'];
        //$post_body = isset($data->body) ? $data->body : $row['body'];
        //$post_author = isset($data->author) ? $data->author : $row['author'];
        
        $update_query = "UPDATE parkir SET keluar = '$waktukeluar' WHERE id ='$parkir_id'";
        $update_stmt = $conn->prepare($update_query);
        $query_insert = "INSERT INTO pembayaran (parkir_id, total_time, total_harga, tanggal) VALUES ('$parkir_id', '$result', '$total_harga', '$tanggal')";
        $insert_stmt = $conn->prepare($query_insert);


        // DATA BINDING AND REMOVE SPECIAL CHARS AND REMOVE TAGS
        //$update_stmt->bindValue(':title', htmlspecialchars(strip_tags($post_title)),PDO::PARAM_STR);
        //$update_stmt->bindValue(':body', htmlspecialchars(strip_tags($post_body)),PDO::PARAM_STR);
        //$update_stmt->bindValue(':author', htmlspecialchars(strip_tags($post_author)),PDO::PARAM_STR);
        //$update_stmt->bindValue(':id', $post_id,PDO::PARAM_INT);
        
        
        if($update_stmt->execute() && $insert_stmt->execute()){
            $msg['parkir_id'] = $parkir_id;
            $msg['slot_parkir'] = $slot_parkir;
            $msg['plat_nomor'] = $platnomor;
            $msg['waktu_masuk'] = $waktumasuk;
            $msg['waktu_keluar'] = $waktukeluar;
            $msg['total_waktu'] = $result;
            $msg['total_harga'] = $total_harga;
            $msg['tanggalparkir'] = $tanggalparkir;
            $msg['message'] = 'Data updated successfully';
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