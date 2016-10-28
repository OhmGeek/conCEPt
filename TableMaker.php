<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:17
 */
public class TableMaker {
    //todo use composer
    /**
     * TableMaker constructor.
     */
    public function __construct()
    {

    }

    public function getTableFromXMLFile($filename) {
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigFile($filename);
        $table = ""; //won't be a string.
        //TODO: use composer along with the array to generate a table

        return $table;
    }

    public function getTableFromXMLString($str) {
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigString($str);
        $table = ""; //temp until I get composer

        return $table;
    }
    
}
