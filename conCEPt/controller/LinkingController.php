<?php

// controller for linker.twig
namespace Concept\Controller;
use Twig_Loader_Filesystem;
use Twig_Environment;
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

	//Displays the linking page, same page for every user
	function generatePage()
	{
		$Model = new LinkingModel();
		//don't do anything with the model
		$loader = new Twig_Loader_Filesystem('../view/adminpage');
	        $twig = new Twig_Environment($loader);
		
		$navbar = new NavbarAdminController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("linker.twig");
		return $template->render(array("navbar"=>$navbar, 
		"get_student"=>substr("http://community.dur.ac.uk/cs.seg04/" . strstr(__DIR__, "password/"), 0, -11) . "/public/admin.php/get_student", 
		"get_marker"=>substr("http://community.dur.ac.uk/cs.seg04/" . strstr(__DIR__, "password/"), 0, -11) . "/public/admin.php/get_marker", 
		"link_marker_student_pair"=>substr("http://community.dur.ac.uk/cs.seg04/" . strstr(__DIR__, "password/"), 0, -11) . "/public/admin.php/Staff_makeRelationship"));
	}

}


?>
