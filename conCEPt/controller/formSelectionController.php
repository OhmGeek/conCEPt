<?php

	class formSelectionController
	{
	}
	
	function __construct()
	{
	}
	
	function generateSelectionPage($formTypeID)
	{
		$Model = new formSelectionModel();
		
		//Generate navbar
		$template = $twig->loadTemplate("navbar.twig");
		$navbar = $template->render();
		
		//Get name of form
		$formDetails = $Model->getFormDetails();
		$documentName = //?????
		
		//Get list of students
		$results = $Model->getStudentOptions($formTypeID);
		
		$students = array();
		foreach($results as $row)
		{
			$studentFName = $row["FName"];
			$studentLName = $row["LName"];
			$formID = $row["formID"];

			$student = array();
			$student["name"] = $studentFName." ".$studentLName;
			$student["formID"] = $formID;

			array_push($students, $student);
		}
		
		$loader = new Twig_Loader_Filesystem('../view/'/*Not sure where it will be*/);
        $twig = new Twig_Environment($loader);

		$template = $twig->loadTemplate("formSelection.twig");
		print($template->render(array("navbar"=>$navbar,"documentName"=>$documentName,"students"=>$students)));
	}
?>