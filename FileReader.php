<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:30
 */
interface FileReader
{
    public static function readConfigFile($filename);
    public static function readConfigString($str);
}