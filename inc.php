<?php 
ini_set("display_errors", 1);
date_default_timezone_set('Africa/Nairobi');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header('content-type:application/json');

// user objects ..
// require_once __DIR__ .'/objects/Database.php';
// require_once __DIR__ .'/objects/Users.php';
// require_once __DIR__ .'/objects/Organization.php';
// require_once __DIR__ .'/objects/Recepients.php';
// require_once __DIR__ .'/aux/functions.php';
// require_once __DIR__ .'/objects/Messages.php';
// require_once __DIR__ .'/objects/Account.php';
// require_once __DIR__ .'/objects/RecepientCategory.php';
// require_once __DIR__ .'/objects/Transaction.php';
// require_once __DIR__ .'/objects/Sms.php';
// require_once __DIR__ .'/objects/Apps.php';



$jsonReqData          = json_decode(file_get_contents("php://input"));
$db                   = new Database();
$connection           = $db->getConnection();
$users                = new User($connection);
$org                  = new Organization($connection);
$recepient            = new Recepient($connection);
$messages             = new Messages($connection);
$account              = new Account($connection);
$recepientcategory    = new RecepientCategory($connection);
$sms                  = new Sms($connection);
$app                  = new Apps($connection);




// Parse the incoming request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath   = $_SERVER['REQUEST_URI'];
// Remove query string from the request path (if present)
$requestPath = strtok($requestPath, '?');
// for localhost we will need to trim th path 
// Check if the request path contains '/Api'
if (strpos($requestPath, '/Api') !== false) {
    // Find the position of '/Api' in the path
    $apiIndex = strpos($requestPath, '/Api');
    // Extract the substring starting from '/Api'
    $requestPath  = substr($requestPath, $apiIndex);
    // Output the new path
    // print($requestPath );
    // die();
} 


// Handle GET requests
if ($requestMethod === 'GET') {
    // Users endpoints
    if ($requestPath === '/Api.php/users/read/all') {


?>