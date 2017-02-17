<?php

//controller for adder.twig
namespace Concept\Controller;

use Concept\Controller\NavbarAdminController;
use Concept\Model\AddingModel;
use Twig_Loader_Filesystem;
use Twig_Environment;
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
	//generates the admin adding page, same for every user
	function generatePage()
	{
		$Model = new addingModel();
		// don't do anything with the model
		$loader = new Twig_Loader_Filesystem('../view/adminpage');
	        $twig = new Twig_Environment($loader);
		
		$navbar = new NavbarAdminController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("adder.twig");
		return $template->render(array("navbar"=>$navbar,"add_marker"=>substr("http://community.dur.ac.uk/cs.seg04/" . strstr(__DIR__, "password/"), 0, -11) . '/public/admin.php/Staff_makeMarker', "add_student"=>substr("http://community.dur.ac.uk/cs.seg04/" . strstr(__DIR__, "password/"), 0, -11) . '/public/admin.php/Staff_makeStudent'));
	}

}


?>
