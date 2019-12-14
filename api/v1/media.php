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

use DiDom\Document;
use DiDom\Query;
use DiDom\Element;

$axios = new axios;

$axios->postREFamilyData = json_decode(base64_decode($data->postREFamilyData), true);
$axios->QuadrimestreFT = $data->QuadrimestreFT;
$axios->student = (array) $data->StudentId;
$axios->cookies = json_decode(base64_decode($data->cookies), true);

$output = $axios->getAverageVote();

echo json_encode($output);
