<?php 
ini_set("display_errors", 1);
date_default_timezone_set('Africa/Nairobi');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('content-type:application/json');

// class files 

require_once __DIR__ .'/objects/database.php';
require_once __DIR__ .'/objects/users.php';


$action               = $_GET['action'];
$data                 = json_decode(file_get_contents("php://input"), true);
$db                   = new Database("localhost", "root", "", "mula");
$connection           = $db->get_connection();
$user                 = new User($connection);

if($action ==  "register"){
   
    $user_response        = $user->create($data['name'], $data['email'], $data['phone'],  $data['password'], $data['status'], $data['role'] );

    if($user_response['error'] == true){
        $responseData = array(
            'status' => 404,
            'message' => "Failed to create a user"
        );
    }else{
        $responseData = array(
            'status' => 200,
            'message' => "User created successfully"
        );
    }

}else if($action == "login"){
    $login_response   = $user->login($data['email'], $data['password']);

   

    if($login_response['error'] == true){
        $responseData = array(
            'status' => 404,
            'message' => "In correct username or password"
        );
    }else{
        $responseData = array(
            'status' => 200,
            'message' => "Login  successfully",
            'data' => $login_response['data']
        );
    }
   

}else{
    $phone   = $_GET['phone'];
    $message = $_GET['message'];
    // print(json_encode(array("message"=>"Welcome to Geni \nselect an option to continue\n 1. ", "status"=>200)));
} 

echo json_encode($responseData);


?>