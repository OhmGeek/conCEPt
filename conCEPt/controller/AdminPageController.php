<?php

namespace Concept\Controller;

use Concept\Controller\AddingController;
use Concept\Model\AdminPageModel;
use Concept\Controller\NavbarAdminController;
use \Twig_Environment;
use \Twig_Loader_Filesystem;


class AdminPageController
{
	private function generateAddingPane($twig,$model) {
		$content = new AddingController();
		$content = $content->generatePage();
		return $content;
	}

	private function generateLinkingPane($twig, $model){
		$content = new LinkingController();
		$content = $content->generatePage();
		return $content;
	}
	function generatePage()
	{
		$model = new AdminPageModel();
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage/');
        $twig = new Twig_Environment($loader);

		return $this->generateAddingPane($twig, $model);
	}

	function generateIndexPage() {
		$model = new AdminPageModel();
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage/');
        $twig = new Twig_Environment($loader);

		$navbarCont = new NavbarAdminController();
		$navbar = $navbarCont->generateNavbarHtml();
		
		$template = $twig->loadTemplate('index.html');

		return $template->render(array(
				'numberOfStudents' => $model->getNumberOfStudents(),
				'numberOfMarkers' => $model->getNumberOfMarkers(),
				'username' => $_SERVER['REMOTE_USER'],
				'formsSubmitted' => $model->countSubmittedForms(1),
				'formsNotSubmitted' => $model->countSubmittedForms(0)
		));

	}
}

?>
