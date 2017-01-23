<?php

include '../model/FormModel.php';

Class FormController
{
    function __construct($formID)
    {
        $this->generateEditableForm($formID);
    }

/* 	TODO:
		- CHECK THAT THE USER HAS ACCESS TO THIS FORM BEFORE SENDING IT, IF NOT, SEND TO ERROR PAGE SAYING DENIED ACCESS
		- Generate completed individual form
		- Generate merged form
		- Generate editable merged form */

    function generateEditableForm($formID)
    {
		$Model = new FormModel();
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

		//GET THE DATA FROM SQL using form ID
		/* NEED:
			- criteria, percentage, mark, rationale, order (for each section)
			- markerType (Examiner or supervisor)
			- markerName
			- studentName
			- saved or submitted? */
		
		//Get student's name
		$studentInfo = $Model->getStudentInformation($formID);
		$studentInfo = $studentInfo[0];
		$studentName = $studentInfo["Fname"]." ".$studentInfo["Lname"];

		//Get general form information
		$formInformation = $Model->getFormInformation($formID);
		$formInformation = $formInformation[0];
		$formTitle = $formInformation["Form_title"];
		$isSubmitted = $formInformation["IsSubmitted"];
		
		//$isMerged = $formInformation["isMerged"];


		//Get marker's details (if not a merged form)
		$examinerName = "";
		$supervisorName = "";
	
		$markerInformation = $Model->getMarkerInformation($formID);
		$markerInfo = $markerInformation[0];

		$isSupervisor = $markerInfo["IsSupervisor"];
		$markerName = $markerInfo["Fname"]." ".$markerInfo["Lname"];

		if ($isSupervisor){
			$supervisorName = $markerName;
		}else{
			$examinerName = $markerName;
		}
/* 		print_r($markerInformation);
		print_r("Supervisor - ".$supervisorName);
		print_r("Examiner - ".$examinerName); */
		//If there is a second marker, get this marker
/* 		if (count($markerInformation) > 1){
			$markerInfo = $markerInfo[0];
			$markerType = "Examiner";
			if ($markerInfo["isSupervisor"]){
				$markerType = "Supervisor";
			}
			$markerName = $markerInfo["Fname"]." ".$markerInfo["Lname"];
		} */

		
		//Get the form details (title and sections)
		$formDetails = $Model->getFormSections($formID);

		
		//Need to add comments separately
		//$id = 1;
		$sections = array();
		//foreach($formDetails as $row)

		for ($id = 0; $id < count($formDetails)-1; $id++)
		{
			$row = $formDetails[$id];
			$sectionName = $row["Sec_Name"];
			$sectionWeight = $row["Sec_Percent"];
			$sectionCriteria = $row["Sec_Criteria"];
			$sectionMark = $row["Mark"];
			$sectionRationale = $row["Comment"];

			$section = array();
			
			//Split criteria, generate html
			$criteria = explode("\n",$sectionCriteria);
			$template = $twig->loadTemplate("criteria.twig");
			$criteria = $template->render(array('criteriaName'=>$sectionName,
												'criteriaWeighting'=>$sectionWeight,
												'criteriaList'=>$criteria));
			
			$section["criteria"] = $criteria;
			$section["markID"] = $id+1;
			$section["markReadOnly"] =  false;
			if($isSubmitted){
				$section["markReadOnly"] = true;
			}
			$section["mark"] = $sectionMark;
			$section["rationaleID"] = $id+1;
			$section["rationaleReadOnly"] = false;
			if ($isSubmitted){
				$section["rationaleReadOnly"] = true;
			}
			
			$section["rationale"] = $sectionRationale;

			array_push($sections, $section);
			
		}
		$commentsRow = $formDetails[count($formDetails)-1];
		$comments = $commentsRow["Comment"];
		$commentID = count($formDetails);
		$commentsReadOnly = false;
		if ($isSubmitted){
			$commentsReadOnly = true;
		}
		
		
		$markingSections = array('formID'=>$formID, 'rows'=>$sections);
	
		//GENERATE HTML TABLE FROM allTables.twig
		//$template = $twig->loadTemplate("allTables.twig");
        $template = $twig->loadTemplate("editableTable.twig");
        $table = $template->render($markingSections);

		
		//GENERATE HTML FORM FROM editableForm.twig
		$totalMark = -1; //Will be -1 if not a submitted form
		
		if ($isSubmitted){
			$totalMark = $Model->getTotalMark($formID);
			$totalMark = $totalMark[0];
			$totalMark = $totalMark["Total"];
		}
		
		$subtitle = "";
		if ($examinerName == "" && $supervisorName != ""){
			$subtitle = "Individual Supervisor's Report";
		}elseif($examinerName != "" && $supervisorName == ""){
			$subtitle = "Individual Examiner's Report";
		}else{
			$subtitle = "Final Report";
		}
        $template = $twig->loadTemplate("editableForm.twig");
        $form = $template->render(array('table'=> $table,
                                        'title'=> $formTitle,
										'subtitle' => $subtitle,
                                        'examinerName' => $examinerName,
                                        'supervisorName' => $supervisorName,
                                        'studentName' => $studentName,
										'totalMark' => $totalMark,
										'formID' => $formID,
										'comments'=>$comments,
										'commentID'=>$commentID,
										'commentsReadOnly'=>$commentsReadOnly));

		
		
		//GENERATE NAVBAR (Will be done by a separate file because of changes to forms in navbar)
		//$template = $twig->loadTemplate("navbar.twig");
		//$navbar = $template->render();
		$navbar = "<h1> Navbar will be generated here </h1>";
		
		
		//GENERATE MAIN FORM PAGE FROM mainFormPage.twig
        $template = $twig->loadTemplate("mainFormPage.twig");
        print($template->render(array('navbar'=>$navbar,'form'=> $form)));
    }

/*       //Generate the criteria for each section
        $markingCriteria =  array('formID' => '1', array('criteria'=> "Did the student submit any work? (95%)",
                                                        'markID'=> "1111",
                                                        'markReadOnly'=>"readonly",
                                                        'mark'=>"50%",
                                                        'rationaleID'=>"2222",
                                                        'rationaleReadOnly' => "readonly",
                                                        'rationale' => "I forgot to check so I'm hedging my bets"),
                                                  array('criteria'=> "Is it any good? 5%",
                                                        'markID'=> "3333",
                                                        'markReadOnly'=>"readonly",
                                                        'mark'=>"0%",
                                                        'rationaleID'=>"4444",
                                                        'rationaleReadOnly' => "readonly",
                                                        'rationale' => "It's unlikely") ); 
        $title = "Design Report";
        $markerType = "Supervisor";
        $markerName = "Stephen McGough";
        $studentName = "Ben Hemsworth";*/
		
		
}