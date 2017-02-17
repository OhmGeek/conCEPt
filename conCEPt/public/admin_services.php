<?php
//handler of admin page navigation
require_once(__DIR__ . '/../vendor/autoload.php');

use Concept\Controller\AddingController;
use Concept\Controller\LinkingController;
use Concept\Controller\PDFController;
use Concept\Model\UserAuthModel;

$username = $_SERVER['REMOTE_USER'];
$auth = new UserAuthModel($username);
$isAdmin = $auth->isAdmin();
if($isAdmin !== true)
{
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../view');
    $twig = new Twig_Environment($loader);
    $error_template = $twig->loadTemplate('403.twig');
    print($error_template->render(array()));

    exit;
}



$route = $_GET["route"];

if($route == "adding"){
	$test = new AddingController();
	print($test->generatePage());
}
elseif($route == "linking"){
	$test = new LinkingController();
	print($test->generatePage());
}
elseif($route == "printing"){
	/*File will print to screen of its own accord. Handles the Printing page and PDF generation*/
	$test = new PDFController();
}


?>