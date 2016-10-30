<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:17
 */

class TableMaker {
    public $twig;
    //todo use composer
    /**
     * TableMaker constructor.
     */
    public function __construct()
    {
        //create a twig instance to use to render the page
        global $twig;
        require_once '../vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('.views/');
        $twig = new Twig_Environment($loader);
    }

    public function getTableFromXMLFile($filename) {
        global $twig;
        echo "from file";

        $elemData = XMLConfigFileReader::readConfigFile($filename);

        $table = $twig->render('table.twig',elemData);

        return $table;
    }

    public function getTableFromXMLString($str) {
        global $twig;

        $elemData = XMLConfigFileReader::readConfigString($str);
        $table = $twig->render('table.twig',elemData);
        return $table;
    }
    
}
