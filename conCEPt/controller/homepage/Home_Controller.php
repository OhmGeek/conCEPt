<?php

class MainPageController
{

	function __construct()
	{
	}

	function generatePage()
	{
		$model = new MainPageModel();
		
		//Get info
		
		$loader = new Twig_Loader_Filesystem('../view/homepage/');
        $twig = new Twig_Environment($loader);

		//Generate Student pane
		$student_forms = $model->getStudentForms();
		$students = $model->getStudentInformation();

		echo "Forms";
		print_r($student_forms);
		echo "Students Themselves";
		print_r($students);
		//$student_pane = $twig->loadTemplate('studentPanel.twig');
		//Generate pending pane

		
		//Generate  submitted pane
		
		//Generate clashes pane

		//Generate main page
	}
}

