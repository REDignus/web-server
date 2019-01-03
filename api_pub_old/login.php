<?php 
require "../vendor/autoload.php";
require "../api/class.php";

use DiDom\Query;
use DiDom\Document;
use DiDom\Element;

$axios = new axios;
if (!empty($_GET)) {
    $output = $axios->login($_GET['cf'], $_GET['username'], $_GET['password'], $_GET['key']);
} elseif (!empty($_POST)) {
    $output = $axios->login($_POST['cf'], $_POST['username'], $_POST['password'], $_POST['key']);
}

echo json_encode($output);


?>