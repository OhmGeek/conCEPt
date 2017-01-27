<?php

include '../model/FormModel.php';

Class FormController
{
    function __construct($formID)
    {
        $this->generateForm($formID);
    }

	function getCurrentMarker()
	{
		//return $_SERVER["REMOTE_USER"];
		//return something hardcoded this time
		return "hkd4hdk";
		//return "knd6usj";
	}
	
    function generateForm($formID)
    {
		$Model = new FormModel();
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

		//CHECK THIS PERSON HAS ACCESS TO THE FORM (Either through merged form, or their own form)
		
		//Get general form information (title, isSubmitted, isMerged)
		$formInformation = $Model->getFormInformation($formID);
		$formInformation = $formInformation[0];
		$formTitle = $formInformation["Form_title"];
		$isSubmitted = $formInformation["IsSubmitted"];
		$isMerged = $formInformation["IsMerged"]; //0 means not merged, -1 means merged, 1 means forms a merge??

		
		//Checks to determine what to display
		if($isMerged == 0){
			//Individual form, not involved in a merge
			if (!($isSubmitted)){$this->displayEditableForm($formID, $twig, $Model, $formTitle);}
			else{$this->displaySubmitted($formID, $twig, $Model, $formTitle);}
		}elseif($isMerged == 1){
			//Individual form involved in merge, doesn't matter who is trying to view it
			//Get formID of merged form
			$mergedFormID = $Model->getMergedForm($formID);
			$mergedFormID = $mergedFormID[0];
			$mergedFormID = $mergedForm["Form_ID"];
			//Check if it has conflicts
			$results = $Model->getConflicts($mergedFormID);
			$conflictSections = array();
			foreach($results as $conflict)
			{
				$sectionID = $conflict["Sec_ID"];
				$sectionOrder = $Model->getSectionOrderFromID();
				array_push($sectionOrder); // Check the Param name
			}
			if (count($conflictSections) > 0){$this->displayEditableForm($formID, $twig, $Model, $formTitle, $conflictSections);}
			else{$this->displaySubmitted($formID, $twig, $Model, $formTitle);}
		}else{
			print_r("Should take this branch");
			//This is the merged document
			if ($isSubmitted){
				//Display final non-editable form
				$this->displaySubmitted($formID, $twig, $Model, $formTitle, 0, 1);
			}else{
				print_r("Hasn't been submitted");
			//Matters who is trying to view it
				$currentMarker = $this->getCurrentMarker();
				$isEdited = $Model->isEdited($formID);
				$isEdited = $isEdited[0];
				$isEdited = $isEdited["IsEdited"];
				$isSupervisor = $Model->isSupervisor($formID, $currentMarker);
				$isSupervisor = $isSupervisor[0];
				$isSupervisor = $isSupervisor["IsSupervisor"];
				print_r("Edited - ".$isEdited);
				print_r("Supervisor - ".$isSupervisor);
				if ($isSupervisor){
					if(!($isEdited)){
						print_r("Not edited");
						//See form with editable rationales (could use displayIndividual??)
						$this->displayEditableForm($formID, $twig, $Model, $formTitle, array(), 1, 1);
					}else{
						print_r("Edited");
						$this->displaySubmitted($formID, $twig, $Model, $formTitle, 0, 1);
					}
				}else{
					print_r("Not supervisor");
					if($isEdited){
						$this->displaySubmitted($formID, $twig, $Model, $formTitle, 1, 1);
					}else{
						$this->displaySubmitted($formID, $twig, $Model, $formTitle, 0, 1);
					}
				}
			}
			
		}
	}
	
	
	//FROM HERE, GENERATES NORMAL INDIVIDUAL EDITABLE FORMS
	//Inputs:
	//	-formID - id of form to display
	//	-twig - the twig object
	//	-Model - model used 
	//	-isSubmitted - is the form submitted
	//	-formTitle - title of the document (e.g Design Report)
	//  -conflictSections - If involved in a merge conflict, make these sections editable
	//  -marksReadOnly - allows you to set all marks to be read only (used when editing merged forms)
	function displayEditableForm($formID, $twig, $Model, $formTitle, $conflictSections=array(), $marksReadOnly = 0, $isMerged=0){
		print_r("Displaying editable form");
		//Get student's information
		if ($isMerged){
			$studentInfo = $Model->getStudentInformationMerged($formID);
		}
		else{
			$studentInfo = $Model->getStudentInformation($formID);
		}
		$studentInfo = $studentInfo[0];
		$studentName = $studentInfo["Fname"]." ".$studentInfo["Lname"];

		//Get marker's details (for non-merged form)
		$examinerName = "";
		$supervisorName = "";
		
		//Get marker information (information for both markers)
		if ($isMerged){
			$markerInformation = $Model->getMarkerInformationMerged($formID);
		}else{
			$markerInformation = $Model->getMarkerInformation($formID);
		}
		foreach($markerInformation as $marker){
			$markerName = $marker["Fname"]." ".$marker["Lname"];
			if ($marker["IsSupervisor"]){
				$supervisorName = $markerName;
			}else{
				$examinerName = $markerName;
			}
		}


		//Get the form sections (section criteria, order, mark, rationale, weighting)
		$formDetails = $Model->getFormSections($formID);
	
		$sections = array(); //array to hold each the sections

		
		//Get data for each non-comments section
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
			$section["markReadOnly"] =  ((1-(in_array(($id+1), $conflictSections)) && count($conflictSections) > 0) || $marksReadOnly);

			$section["mark"] = $sectionMark;
			$section["rationaleID"] = $id+1;
			$section["rationaleReadOnly"] = ((1-(in_array(($id+1), $conflictSections))) && count($conflictSections) > 0);

			
			$section["rationale"] = $sectionRationale;

			array_push($sections, $section);
			
		}
		
		//Get comments section
		$commentsRow = $formDetails[count($formDetails)-1];
		$comments = $commentsRow["Comment"];
		$commentID = count($formDetails);
		$commentsReadOnly = ((1-(in_array((count($formDetails)), $conflictSections))) && count($conflictSections) > 0);
		
		$markingSections = array('formID'=>$formID, 'rows'=>$sections);
	
		//Generate html table from allTables.twig
        $template = $twig->loadTemplate("editableTable.twig");
        $table = $template->render($markingSections);

		$totalMark = -1; //Will be -1 if not a submitted form
		if ($isMerged){
			$totalMark = $Model->getTotalMark($formID);
			$totalMark = $totalMark[0];
			$totalMark = $totalMark["Total"];
			$totalMark = round($totalMark, 2);
		}
		
		$subtitle = "";
		if ($examinerName == ""){
			$subtitle = "Individual Supervisor's Report";
		}elseif($supervisorName == ""){
			$subtitle = "Individual Examiner's Report";
		}else{
			$subtitle = "Final Report";
		}
		
		//Don't display submission buttons if isSubmitted is false and conflictSections are empty
		$displayFormSubmission = 1;
		
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
										'commentsReadOnly'=>$commentsReadOnly,
										'displayFormSubmission'=>$displayFormSubmission));

		
		
		//GENERATE NAVBAR (Will be done by a separate file because of changes to forms in navbar)
		//$template = $twig->loadTemplate("navbar.twig");
		//$navbar = $template->render();
		$navbar = "<h1> Navbar will be generated here </h1>";
		
		
		//GENERATE MAIN FORM PAGE FROM mainFormPage.twig
        $template = $twig->loadTemplate("mainFormPage.twig");
        print($template->render(array('navbar'=>$navbar,'form'=> $form)));
    }

	
	
//FINAL MERGED DOCUMENT or FINAL INDIVIDUAL DOCUMENT
function displaySubmitted($formID, $twig, $Model, $formTitle, $confirmButton=0, $merged = 0)
	{
		print_r("Displaying editable form");
		//Get student's information
		if ($merged){
			$studentInfo = $Model->getStudentInformationMerged($formID);
		}
		else{
			$studentInfo = $Model->getStudentInformation($formID);
		}
		$studentInfo = $studentInfo[0];
		$studentName = $studentInfo["Fname"]." ".$studentInfo["Lname"];

		//Get marker's details (for non-merged form)
		$examinerName = "";
		$supervisorName = "";
		
		//Get marker information (information for both markers)
		if ($merged){
			$markerInformation = $Model->getMarkerInformationMerged($formID);
		}else{
			$markerInformation = $Model->getMarkerInformation($formID);
		}
		foreach($markerInformation as $marker){
			$markerName = $marker["Fname"]." ".$marker["Lname"];
			if ($marker["IsSupervisor"]){
				$supervisorName = $markerName;
			}else{
				$examinerName = $markerName;
			}
		}


		//Get the form sections (section criteria, order, mark, rationale, weighting)
		$formDetails = $Model->getFormSections($formID);
	
		$sections = array(); //array to hold each the sections

		//Get data for each non-comments section
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
			$section["mark"] = $sectionMark;		
			$section["rationale"] = $sectionRationale;

			array_push($sections, $section);
			
		}
		
		//Get comments section
		$commentsRow = $formDetails[count($formDetails)-1];
		$comments = $commentsRow["Comment"];

		$markingSections = array('formID'=>$formID, 'rows'=>$sections);
		
		//Possible to get section conflicts and add them to the form display here
		
		//Generate html table from nonEditableTable.twig (No textareas)
        $template = $twig->loadTemplate("nonEditableTable.twig");
        $table = $template->render($markingSections);


		$totalMark = $Model->getTotalMark($formID);
		$totalMark = $totalMark[0];
		$totalMark = $totalMark["Total"];
		$totalMark = round($totalMark, 2);
		
		if ($examinerName == ""){
			$subtitle = "Individual Supervisor's Report";
		}elseif($supervisorName == ""){
			$subtitle = "Individual Examiner's Report";
		}else{
			$subtitle = "Final Report";
		}
	
	
        $template = $twig->loadTemplate("nonEditableForm.twig");
        $form = $template->render(array('table'=> $table,
                                        'title'=> $formTitle,
										'subtitle' => $subtitle,
                                        'examinerName' => $examinerName,
                                        'supervisorName' => $supervisorName,
                                        'studentName' => $studentName,
										'totalMark' => $totalMark,
										'comments'=>$comments,
										'confirmButton'=>$confirmButton));

		
		//GENERATE NAVBAR (Will be done by a separate file because of changes to forms in navbar)
		//$template = $twig->loadTemplate("navbar.twig");
		//$navbar = $template->render();
		$navbar = "<h1> Navbar will be generated here </h1>";
		
		
		//GENERATE MAIN FORM PAGE FROM mainFormPage.twig
        $template = $twig->loadTemplate("mainFormPage.twig");
        print($template->render(array('navbar'=>$navbar,'form'=> $form)));
	}
		
}