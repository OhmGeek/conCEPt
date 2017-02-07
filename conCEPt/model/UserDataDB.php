<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 12/01/17
 * Time: 12:16
 */
namespace Concept\Model;
class UserDataDB
{
    public static function getDB()
    {
        return new \PDO("mysql:host=mysql.dur.ac.uk;dbname=Pdcl0www_userdata", 'nobody', '');
    }
}