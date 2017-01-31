<?php

include '../model/formSelectionModel.php';
class formSelectionController{
	
	
	function __construct()
	{
	}
	
	function getCurrentMarker()
	{
		//return $_SERVER["REMOTE_USER"];
		//return something hardcoded this time
		//return "hkd4hdk";
		return "knd6usj";
	}
	
	function generateSelectionPage($formTypeID)
	{
		$Model = new formSelectionModel();
		
		
		//Generate navbar (Will be done by a separate file because of changes to forms in navbar)
		$navbar = new navbarController();
		$navbar = $navbar->generateNavbarHtml();
		
		$markerID = $this->getCurrentMarker();
		
		//Get name of form
		$formDetails = $Model->getFormName($formTypeID);
		$documentName = $formDetails[0]["Form_Title"];
		
		
		//Get list of students
		$results = $Model->getStudentOptions($formTypeID, $markerID);
		
		
		$students = array();
		foreach($results as $row)
		{
			
			$studentFName = $row["Fname"];
			$studentLName = $row["Lname"];
			$studentLevel = $row["Year_Level"];
			$formID = $row["Form_ID"];

			$student = array();
			$student["name"] = $studentFName." ".$studentLName;
			$student["level"] = $studentLevel;
			$student["formID"] = $formID;

			array_push($students, $student);
		}
		
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

		$template = $twig->loadTemplate("formSelection.twig");
		print($template->render(array("navbar"=>$navbar,"documentName"=>$documentName,"students"=>$students)));
	}
}
?>