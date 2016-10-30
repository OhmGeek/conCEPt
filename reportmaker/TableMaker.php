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

        print ($this->twig->render('table.html', array('test' => 'Hello, World')));
    }

/*    public function getTableFromXMLFile($filename) {
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigFile($filename);

        $table = $this->twig->render('table.html', array(elemdata);

        return $table;
    }

    public function getTableFromXMLString($str) {
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigString($str);
        $table = ""; //temp until I get composer

        return $table;
    }*/
    
}
