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
}
