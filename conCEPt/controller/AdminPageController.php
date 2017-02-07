<?php
namespace Concept\Controller;

use Concept\Controller\AddingController;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Concept\Model\AdminPageModel;

class AdminPageController
{
	private function generateAddingPane($twig,$model) {
		$content = new AddingController();
		$content = $content->generatePage();
		return $content;
	}

	private function generateLinkingPane($twig, $model){
		$linking_pane = $twig->loadTemplate('linker.twig');
		$twig_data = array();
		return $linking_pane->render($twig_data);
	}
	function generatePage()
	{
		$model = new AdminPageModel();
		
		$loader = new Twig_Loader_Filesystem('../view/adminpage/');
        $twig = new Twig_Environment($loader);

		/*
		$adding_pane = $this->generateAddingPane($twig,$model);
		$template = $twig->loadTemplate('adminPage.twig');
		return $template->render(array(
			'content'=>$adding_pane
		));
		return $template;	
		*/
		return $this->generateAddingPane($twig, $model);
	}
}
