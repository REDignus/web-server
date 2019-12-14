<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

//Include
include "../../vendor/autoload.php";
include "../class.php";

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get posted data
$data = json_decode(file_get_contents("php://input"));

$axios = new axios;

$output["cookies"] = base64_encode(json_encode($axios->login($data->cfscuola, $data->username, $data->password)));
$output["postREFamilyData"] = base64_encode(json_encode($axios->getPostREFamily()));
$output["QuadrimestreFTAll"] = $axios->getPeriodYear();
$output["getStudentId"] = $axios->getStudentId();

$output["expire"] = time() + 1800;

echo json_encode($output);
