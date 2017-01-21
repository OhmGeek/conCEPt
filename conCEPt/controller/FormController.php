<?php

include '../model/FormModel.php';

Class FormController
{
    function __construct()
    {
        $this->generateEditableForm();
    }

/* 	TODO:
		- CHECK THAT THE USER HAS ACCESS TO THIS FORM BEFORE SENDING IT, IF NOT, SEND TO ERROR PAGE SAYING DENIED ACCESS
		- Generate completed individual form
		- Generate merged form
		- Generate editable merged form */

    function generateEditableForm()
    {
		$Model = new FormModel();
		$loader = new Twig_Loader_Filesystem('../view/formPage/');
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
		$studentName = $studentInfo["Fname"]." ".$studentInfor["Lname"];

		//Get marker's details (if it returns 2 rows, form is merged
		//For now, assume only one returned
		$markerInfo = $Model->getMarkerInformation($formID);
		$markerType = "Examiner";
		if ($markerInfo["isSupervisor"]){
			$markerType = "Supervisor";
		}
		$markerName = $markerInfo["Fname"]." ".$markerInfo["Lname"];

		//Get general form information
		$formInformation = $Model->getFormInformation($formID);
		$formTitle = $formInformation["Form_Title"];
		$isSubmitted = $formInformation["isSubmitted"];
		//$isMerged = $formInformation["isMerged"];
		
		//Get the form details (title and sections)
		$formDetails = $Model->getFormSections($formID);

		$id = 1;
		$sections = array();
		foreach($formDetails as $row)
		{
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
												'criteriaList'=>$criteria);
			
			$section["criteria"] = $criteria;
			$section["markID"] = $id;
			$section["markReadOnly"] = "";
			if($isSubmitted){
				$section["markReadOnly"] = "readonly";
			}
			$section["mark"] = $sectionMark;
			$section["rationaleID"] = $id;
			$section["rationaleReadOnly"] = "";
			if ($isSubmitted){
				$section["rationaleReeadOnly"] = "readonly";
			}
			$section["rationale"] = $sectionRationale;

			array_push($sections, $section);
		}
		
		$markingSections = array('formID'=>$formID, 'rows'=>$sections);
	
		//GENERATE HTML TABLE FROM allTables.twig
        $template = $twig->loadTemplate("allTables.twig");
        $table = $template->render($markingSections);

		//GENERATE HTML FORM FROM editableForm.twig
        $template = $twig->loadTemplate("editableForm.twig");
        $form = $template->render(array('table'=> $table,
                                        'title'=> $formTitle,
                                        'markerType' => $markerType,
                                        'markerName' => $markerName,
                                        'studentName' => $studentName));

		//GENERATE MAIN FORM PAGE FROM mainFormPage.twig
        $template = $twig->loadTemplate("mainFormPage.twig");
        print($template->render(array('form'=> $form)));
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