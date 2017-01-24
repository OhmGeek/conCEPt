<?php

class mainPageController
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
		$students = $model->getStudents();
		$all_students = array();
		foreach($student as $students) {
			array_push($all_students,$student['Fname'] . " " .  $student['Lname']);
		}
		//todo get the documents for each student (from the model).
		$student_pane = $twig->loadTemplate('studentPanel.twig');
		//Generate pending pane

		
		//Generate  submitted pane
		
		//Generate clashes pane

		//Generate main page
	}
}

