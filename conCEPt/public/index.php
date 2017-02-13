<?php


require_once(__DIR__ . '/../vendor/autoload.php');
use Concept\Controller\MainPageController;
use Concept\Controller\AdminPageController;
use Concept\Model\UserAuthModel;


// create a user model
$user_model = new UserAuthModel($_SERVER['REMOTE_USER']);

// create a twig loader
$loader = new Twig_Loader_Filesystem(__DIR__ . '/../view/');
$twig = new Twig_Environment($loader);

// admin goes to the admin page
if($user_model->isAdmin()) {
	$admin_page = new AdminPageController();
	echo $admin_page->generateIndexPage();
}

elseif($user_model->isMarker()) {
	//marker goes to the main marker page (MainPage)
	$main_page = new MainPageController();
	echo $main_page->generatePage();
}
else {
	// 403 Error: Not Authorised
	$error_template = $twig->loadTemplate('403.twig');
	echo $error_template->render(array());
}
