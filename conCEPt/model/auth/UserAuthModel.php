<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 09/01/17
 * Time: 12:19
 */
require_once(__DIR__ . '/../db.php');
class UserAuthModel
{
    public function __construct()
    {
        $this->db = new DB();
    }
    public function isAdmin() {
        // todo get admin
        return false;
    }
    public function isMarker() {
        //todo get marker or not
        return false;
    }

}