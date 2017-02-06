<?php
require_once(__DIR__ . '/../model/AdminPageModel.php');
require_once(__DIR__ . '/NavbarAdminController.php');
require_once(__DIR__ . '/../view/adminpage/adder.twig');
require_once(__DIR__ . '/../view/adminpage/linker.twig');

class AdminPageController
{
	private function generateAddingPane($twig,$model) {
		$adding_pane = $twig->loadTemplate('adder.twig');
		$twig_data = array();
		return $adding_pane->render($twig_data);
	}

	private function generateLinkingPane($twig, $model){
		$linking_pane = $twig->loadTemplate('linker.twig');
		$twig_data = array();
		return $linking_pane->render($twig_data);
	}
	function generatePage()
	{
		$model = new AdminPageModel();
		
		$navbar = new navbarController();		
		//Get info
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage/');
        $twig = new Twig_Environment($loader);

		// student pane
		$student_pane = $this->generateAddingPane($twig,$model);
		$linking_pane = $this->generateLinkingPane($twig, $model);
		$template = $twig->loadTemplate('adminPage.twig');
		return $template->render(array(
			'navbar'=> $navbar->generateNavbarHtml(),
			'addingTab' => $student_pane,
			'linkingTab' => $linking_pane,
			'printingTab' => "<div>Incomplete</div>",
		));
		//Generate pending pane

		return $template;	
		//Generate  submitted pane
		
		//Generate clashes pane

		//Generate main page
	}
}
