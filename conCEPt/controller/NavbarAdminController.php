<?php

require_once(__DIR__ . '/../model/NavbarAdminModel.php');

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
