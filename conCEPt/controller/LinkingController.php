<?php

namespace Concept\Controller;
use Concept\Model\LinkingModel;
use Concept\Controller\NavbarAdminController;


class LinkingController
{

	function __construct()
	{
		$this->generatePage();
	}
	
	function getCurrentUser()
	{
		return $_SERVER['REMOTE_USER'];
	}

	//Displays the History page for the current marker
	function generatePage()
	{
		$Model = new LinkingModel();
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage');
	        $twig = new Twig_Environment($loader);
		
		$navbar = new NavbarAdminController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("linker.twig");
		return $template->render(array("navbar"=>$navbar));
	}

}


?>
