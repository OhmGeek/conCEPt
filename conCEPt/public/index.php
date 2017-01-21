<?php

require_once(__DIR__ . '/../controller/auth/Auth_Controller.php');

// deal with the odd installation we have going on

$base = __DIR__;
$path = preg_replace("/cs.seg04\/password\/conCEPt\/conCEPt\/public\//", "", $_SERVER['REQUEST_URI']);
$request_type = $_SERVER['REQUEST_METHOD'];


// strip the base path


switch ($path) {
	case "/?":
	case "/":
	case "":
		echo Auth_Controller::auth_page($_SERVER['REMOTE_USER']);
		break;

	case "/?admin/":
		echo "Admin";
		break;

	case "/?marker/":
		echo "Marker";
		break;
	default:
		echo "404 Error\n";
		echo $path;
}
