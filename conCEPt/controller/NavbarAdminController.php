<?php

include '../model/NavbarModel.php';

class NavbarController
{
	function __construct()
	{
		
	}

	//Returns the HTML of the navbar
	function generateNavbarHtml()
	{
	
		$Model = new navbarAdminModel();
		
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);
		$template = $twig->loadTemplate("navbarAdmin.twig");
		
		return($template->render(array()));
	}
}
?>
