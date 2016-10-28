<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 28/10/16
 * Time: 16:17
 */

public class TableMaker {
    private $twig;
    //todo use composer
    /**
     * TableMaker constructor.
     */
    public function __construct()
    {
        //create a twig instance to use to render the page
        require_once '../vendor/autoload.php';
        $loader = new Twig_Loader_Filesystem('.views/');
        $twig = new Twig_Environment($loader);
    }

    public function getTableFromXMLFile($filename) {
        global $twig;
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigFile($filename);

        $table = $twig->render('table.html',elemData);

        return $table;
    }

    public function getTableFromXMLString($str) {
        $reader = new XMLConfigFileReader();
        $elemData = $reader->readConfigString($str);
        $table = ""; //temp until I get composer

        return $table;
    }
    
}
