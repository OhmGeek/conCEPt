<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../controller/auth/Auth_Controller.php');
// deal with the odd installation we have going on
$base = dirname($_SERVER['PHP_SELF']);

if(ltrim($base,'/')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],strlen($base));
}

// main routing
$router = new \Klein\Klein();

$router->respond('GET', '/', function() {
    $controller = new Auth_Controller();
    return $controller::auth_page($_SERVER['REMOTE_USER']);
});

// routing just for the admin namespace
$router->with('/admin', function() use ($klein) {
    $klein->respond('GET', '/', function($request,$response) {
       return "Admin Area";
    });
});

// this is just the routing for the marker namespace
$router->with('/marker', function() use ($klein) {
    $klein->respond('GET', '/', function($request,$response) {
       return "Marker Area";
    });
});






// PUT NOTHING AFTER THIS LINE!!!
// ---------------------------------------------------------------

$router->onHttpError(function ($code, $router) {
    if ($code >= 400 && $code < 500) {
        $router->response()->body(
            'Oh no, a bad error happened that caused a '. $code . $_SERVER['REQUEST_URI']
        );
    } elseif ($code >= 500 && $code <= 599) {
        error_log('uhhh, something bad happened');
    }
});

$router->dispatch();

