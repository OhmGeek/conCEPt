<?php

namespace Concept\Controller;
use Concept\Model\NavbarAdminModel;
use Twig_Environment;
use Twig_Loader_Filesystem;

class NavbarAdminController
{
	function __construct()
	{
		
	}

	//Returns the HTML of the navbar
	function generateNavbarHtml()
	{
	
		$model = new NavbarAdminModel();
		
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);
		$template = $twig->loadTemplate("navbarAdmin.twig");
		
		return($template->render(array()));
	}
}
?>
