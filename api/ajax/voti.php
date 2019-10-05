<?php
header('Content-Type: text/plane; charset=utf-8');

// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 7200);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(7200);

session_start(); // ready to go!

//Controllo login
if (strpos($_SERVER['PHP_SELF'], 'login') === false) {
    if (empty($_SESSION["cookies"]["__AntiXsrfToken"]) || empty($_SESSION["cookies"]["ASP.NET_SessionId"]) || $_SESSION["expire"] < time()){
        header("location: /login?page=".$_SERVER['PHP_SELF']);
        exit;
    }
}

include "../../vendor/autoload.php";
include "../class.php";

use DiDom\Document;
use DiDom\Query;
use DiDom\Element;

$axios = new axios;

$axios->postREFamilyData = $_SESSION["getPostREFamily"];
$axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
$axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
$axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
$axios->cookies = $_SESSION["cookies"];

$result = $axios->getVote();

if (!empty($_GET["materia"])) {
    foreach ($result as $key => $value) {
        if ($value["topic"] == $_GET["materia"]) {
            $output[] = $value;
        }
    }
} else {
    $output = $result;
}


echo json_encode($output);


// if ($_GET["lastweek"] == true) {
//     echo "<hr><pre>";

//     $d = strtotime("today");
//     $start_week = strtotime("last sunday midnight", $d);
//     // $end_week = strtotime("next saturday", $d);
//     for ($i = 0; $i < 7; $i++) {
//         if ($i == 0) {
//             $date[] = date("d/m/Y", $start_week);
//             $last_date = $start_week;
//         } else {
//             $last_date = strtotime($last_date . " +1 day");
//             $date[] = date("d/m/Y", $last_date);
//         }

//     }


//     foreach ($date as $key => $value) {
        

//         $key[] = array_search($date, array_column($result, "date"));
//     }
//     // var_dump($result[$key]);
//     var_dump($date);
//     echo "</pre>";
// }


if ($_GET["debug"] == true) {
    echo "<hr><pre>";
    var_dump($output);
    echo "</pre>";
}



