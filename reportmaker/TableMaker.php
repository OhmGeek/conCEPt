<?php

/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:17
 */

class TableMaker {

    private $twig;
    //todo use composer
    /**
     * TableMaker constructor.
     */
    public function __construct()
    {
        //create a twig instance to use to render the page
        global $twig;
        require_once '../vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('../reportmaker/views/');
        $this->twig = new Twig_Environment($loader);

        print ($this->twig->render('table.html', array('test' => 'Hello, World')));
    }

    public function getTableFromXMLFile($filename) {
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigFile($filename);

        $table = $this->twig->render('table.twig', array(elemdata);
    }


    public function getTableFromXMLString($str) {
        global $twig;

        $elemData = XMLConfigFileReader::readConfigString($str);
        $table = $twig->render('table.twig',array(elemData));
        return $table;
    }
    
}
