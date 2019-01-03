<?php
require "../vendor/autoload.php";
require "../api/class.php";

use DiDom\Query;
use DiDom\Document;
use DiDom\Element;

$axios = new axios;
if (!empty($_GET)) {
    $axios->cookies = [
        'ASP.NET_SessionId' => $_GET["ASP_NET_SessionId"],
        '__AntiXsrfToken' => $_GET['__AntiXsrfToken'],
    ];
    $axios->students[0] = [
        'num' => $_GET["num"],
        'qualcosa' => $_GET["qualcosa"],
        'id' => $_GET['id'],
    ];
    $axios->getPostREFamily($_GET["date"]); //Ottieni gli input messi a caso da axios
    $output = $axios->getHomeworkFull();
} elseif (!empty($_POST)) {
    $axios->cookies = [
        'ASP.NET_SessionId' => $_POST["ASP_NET_SessionId"],
        '__AntiXsrfToken' => $_POST['__AntiXsrfToken'],
    ];
    $axios->students[0] = [
        'num' => $_POST["num"],
        'qualcosa' => $_POST["qualcosa"],
        'id' => $_POST['id'],
    ];
    $axios->getPostREFamilyFull($_POST["date"]); //Ottieni gli input messi a caso da axios
    $output = $axios->getHomework();
}

echo json_encode($output);