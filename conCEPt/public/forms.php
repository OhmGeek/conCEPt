<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Concept\Controller\FormDisplayController;
use Concept\Controller\FormSelectionController;
use Concept\Controller\HistoryController;
use Concept\Controller\NavbarController;
use Concept\Controller\SaveSubmitController;

$route = $_GET["route"];

if ($route == "send") {
    $test = new SaveSubmitController($_POST);
} elseif ($route == "receive") {
    $formID = $_GET["formid"];
    if(isset($_GET["id"])) {
        $formID = $_GET["id"];
    }
    $test = new FormDisplayController();
    echo $test->generatePage($formID);
} elseif ($route == "select") {
    $formTypeID = $_GET["typeId"];
    $test = new FormSelectionController();
    echo $test->generateSelectionPage($formTypeID);
} elseif ($route == "history") {
    $test = new HistoryController();
} elseif ($route == "navbar") {
    $test = new NavbarController();
    print_r($test->generateNavbarHtml());
}
