<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 09/01/17
 * Time: 12:22
 */
require_once(__DIR__ . '/../../model/auth/UserAuthModel.php');

class Auth_Controller
{
    public static function auth_page($username) {
        // create a user model
        $user_model = new UserAuthModel($username);

        // create a twig loader
        $loader = new Twig_Loader_Filesystem(__DIR__ . '/../../view/auth');
        $twig = new Twig_Environment($loader);

        if($user_model->isAdmin()) {
            // render admin page
            return "Admin";
        }
        elseif($user_model->isMarker()) {
            return "Marker";
        }
        else {
            $error_template = $twig->loadTemplate('403.twig');
            return $error_template->render(array());
        }
    }
}