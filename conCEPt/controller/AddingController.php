<?php
require_once(__DIR__ . '/../model/AddingModel.php');
require_once(__DIR__ . '/NavbarAdminController.php');
require_once(__DIR__ . '/../vendor/autoload.php');
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
		$Model = new addingModel();
		// don't do anything with the model
		$loader = new Twig_Loader_Filesystem('../view/adminpage');
        $twig = new Twig_Environment($loader);
		
		$navbar = new NavbarAdminController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("adder.twig");
		return $template->render(array("navbar"=>$navbar));
	}

}


?>
