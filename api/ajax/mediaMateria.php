<?php
// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 7200);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(7200);

session_start(); // ready to go!

//Controllo login
if (strpos($_SERVER['PHP_SELF'], 'login') === false) {
    if (empty($_SESSION["cookies"]["__AntiXsrfToken"]) || empty($_SESSION["cookies"]["ASP.NET_SessionId"]) || $_SESSION["expire"] < time()) {
        header("location: /login?page=" . $_SERVER['PHP_SELF']);
        exit;
    }
}

include "../../vendor/autoload.php";
include "../class.php";

$axios = new axios;

$axios->postREFamilyData = $_SESSION["getPostREFamily"];
$axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
$axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
$axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
$axios->cookies = $_SESSION["cookies"];

$output = $axios->getAverageVote();

echo json_encode($output);


if ($_GET["debug"] == true) {
    echo "<hr><pre>";
    var_dump($output);
    echo "</pre>";
}
