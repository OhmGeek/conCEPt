<?php
include '../model/LinkingModel.php';
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
		$Model = new linkingModel();
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage');
        $twig = new Twig_Environment($loader);
		
		$navbar = new navbarAdminController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("linker.twig");
		print($template->render(array("navbar"=>$navbar, "documents"=>$documents)));
	}

}


?>
