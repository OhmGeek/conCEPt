<?php

class mainPageController
{

	function __construct()
	{
	}

	function generatePage()
	{
		$Model = new mainPageModel();
		
		//Get info
		
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

		//Generate Student pane
		
		//Generate pending pane
		
		//Generate  submitted pane
		
		//Generate clashes pane

		//Generate main page
	}
}

?>
