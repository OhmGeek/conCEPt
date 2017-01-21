<?php

require_once(__DIR__ . '/../controller/auth/Auth_Controller.php');
// deal with the odd installation we have going on

$path = $_GET['page'];
$request_type = $_SERVER['REQUEST_METHOD'];


switch ($path) {
	case "/":
		echo "Root";
		break;

	case "/admin":
		echo "Admin";
		break;

	case "/marker":
		echo "Marker";
		break;
}
