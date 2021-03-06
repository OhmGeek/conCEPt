<?php

namespace Concept\Controller;

use Concept\Model\NavbarModel;
use Twig_Loader_Filesystem;
use Twig_Environment;

class NavbarController
{
    function __construct()
    {

    }

    //Returns the HTML of the navbar
    function generateNavbarHtml()
    {

        $Model = new NavbarModel();

        $results = $Model->getFormTypes();

        $forms = array();
        foreach ($results as $row) {
            $name = $row["Form_Title"];
            $id = $row["BForm_ID"];

            $form = array();
            $form["name"] = $name;
            $form["id"] = $id;

            array_push($forms, $form);
        }

        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate("navbar.twig");

        return ($template->render(array("forms" => $forms)));
    }
}

?>
