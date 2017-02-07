<?php
namespace Concept\Controller;
use Concept\Model\AddingModel;
use Concept\Controller\NavbarAdminController;


class AddingController
{

	function __construct()
	{
		$this->generatePage();
	}
	
	function getCurrentUser()
	{
		return $_SERVER['REMOTE_USER'];
	}

	function generatePage()
	{
		$Model = new AddingModel();
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage');
        $twig = new Twig_Environment($loader);
		
		$navbar = new NavbarAdminController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("adder.twig");
		print($template->render(array("navbar"=>$navbar, "documents"=>$documents)));
	}

}


?>
