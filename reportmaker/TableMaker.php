<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:17
 */

class TableMaker {

    private $twig;
    /**
     * TableMaker constructor.
     */
    public function __construct()
    {
        //create a twig instance to use to render the page
        require_once '../vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('../reportmaker/views/');
        $this->twig = new Twig_Environment($loader);

    }

    public function getTableFromXMLFile($filename) {
        $elemData = XMLConfigFileReader::readConfigFile($filename);
        $table = $this->twig->render('table.twig', $elemData);
        return $table;
    }


    public function getTableFromXMLString($str) {
        global $twig;
        $elemData = XMLConfigFileReader::readConfigString($str);
        $table = $this->$twig->render('table.twig', array($elemData));
        return $table;
    }

}