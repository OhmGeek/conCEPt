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
        $user_model = new UserAuthModel();

        if($user_model->isAdmin()) {
            // render admin page
            echo "Admin";
        }
        elseif($user_model->isMarker()) {
            echo "Marker";
        }
        else {
            echo "Go away, you are not welcome here.";
        }
    }
}