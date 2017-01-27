<?php
//include model here
include '../model/saveSubmitModel.php';

class SaveSubmitController
{
	
	function __construct($postVariables)
	{
		$this->retrieveInformation($postVariables);
	}
	
	function getMarkerID()
	{
		//return "hkd4hdk";
		return "knd6usj";
		//return $_SERVER["REMOVE_USER"];
	}


	//Retrieve data from the POST request variables from the form (sent in an array by index.php)
	function retrieveInformation($postVariables)
	{
		print_r("This has been called");
		
		//$currentUser = $this->getMarkerID();

		//Get the formID and the storeType
		$formID = $postVariables["documentID"];
		$storeType = $postVariables["action"]; //Save or Submit
		
		//If storeType is Confirm or Reject, form is completed merged form, only need to change a flag
		if($storeType == "Confirm"){
			print_r("Confirm");
			$Model->updateSubmitFlag($formID,1);
		}elseif($storeType == "Reject"){
			//$Model->changeEditedFlag($formID, 0);
		}

		$sections = array();
		print_r($storeType);
		// The number of sections in the form (ignoring the comments);
		$numberOfSections = $postVariables["numberOfSections"];
		
		// Iterate through all sections in the form
		for($n=1; $n < $numberOfSections-1; $n++)
		{
			
			$sectionNumber = $n; //Section number in ordering on form
			if (!(empty($postVariables["mark-".$n]))){
				$mark = $postVariables["mark-".$n]; //Mark for this section
			}else{
				$mark = 0;
			}
			if (!(empty($postVariables["rationale-".$n]))){
				$rationale = trim($postVariables["rationale-".$n]); //Rationale for this section
				$rationale = stripslashes($rationale);
			}else{
				$rationale = "";
			}	

			$section = array("sectionNumber"=>$n,"mark"=>$mark,
			"rationale"=>$rationale);
			
			array_push($sections, $section);
		
		}
		
		// Deal with the comments
		if (isset($postVariables["comments"])){
			$comments = stripslashes(trim($postVariables["comments"]));
			$section = array("sectionNumber"=>($numberOfSections-1),"mark"=>0, "rationale"=>$comments);
			array_push($sections, $section);
		}
		
/* 		if(isset($postVariables["submitComment"])){
			$submissionComment = stripslashes(trim($postVariables["submitComment"]));
		} */
		
		$Model = new saveSubmitModel();
		
		foreach($sections as $section)
		{
			$result = $Model->sendSection($formID, $section["sectionNumber"], $section["mark"], $section["rationale"]);
			if (!($result)){
				echo($this->sendErrorMessage("Couldn't send a section"));
				exit;
			}
		}

		if ($storeType == "Submit")
		{
			print_r("Submitting");
			//If this is a merged form (it has been edited by the supervisor)
			$isMerged = $Model->isMergedForm($formID);
			if ($isMerged){$Model->changeEditedFlag($formID, 1);}
			else{
				print_r("Not merged");
				$Model->updateSubmitFlag($formID, 1); //Also add the submission comment here
				//Get details from this form
				$details = $Model->getGeneralDetails($formID);
				$details = $details[0];
				$BFormID = $details["BForm_ID"];
				$studentID = $details["Student_ID"];
				$isSupervisor = $details["IsSupervisor"];
				//Find other marker's form
				print_r($details);
				$otherForm = $Model->getOtherMarkerForm($studentID, $BFormID, $isSupervisor);
				print_r($otherForm);
				if (count($otherForm) > 0){
					print_r("Found other form");
					$otherForm = $otherForm[0];
					$otherForm = $otherForm["Form_ID"];
					print_r($otherForm);
					if($isSupervisor){
						print_r("Is supervisor");
						$SForm = $formID;
						$EForm = $otherForm;
					}else{
						print_r("Is not supervisor");
						$SForm = $otherForm;
						$EForm = $formID;
					}
					print_r($SForm);
					print_r($EForm);
					$mergedForm = $Model->getMergedForm($EForm);
					print_r("/n".$mergedForm."/n");
					if (count($mergedForm) == 0){
						print_r("Merging");
						//$result = $Model->mergeForms($Eform, $SForm);
						$result = $this->mergeForms($EForm, $SForm, $BFormID, $Model);
						print_r($result);
					}else{
						$mergedForm = $mergedForm[0];
						$mergedForm = $mergedForm["MForm_ID"];
						$Model->updateMergedForm($mergedForm, $EForm, $SForm);
					}
					
					$mergedForm = $Model->getMergedForm($EForm);
					$mergedForm = $mergedForm[0];
					$mergedForm = $mergedForm["MForm_ID"];
					print_r($mergedForm);
					if (count($mergedForm) > 0){
						print_r("Finding conflicts");
						//Find conflicts (either in a query or in a function in this file)
						$Model->createConflicts($mergedForm, $EForm);
						$conflicts = $Model->getConflicts($mergedForm);
						if (count($conflicts) > 0){
							$Model->duplicateForm($Eform);
							$Model->duplicateForm($SForm);
							$Model->openForm($Eform);
							$Model->openForm($SForm);
						}
					}
				}
			}
		}

		
		//Send back confirmation or error message as  JSON to original page
		if (successful){
			echo($this->sendSuccessMessage("Succeded at ".$storeType."ing"));
			exit;
		}else{
			echo("Error");
			echo($this->sendErrorMessage("Failed to ".$storeType));
			exit;
		}
			
	}
	
	function mergeForms($EForm, $SForm, $BFormType, $Model)
	{
		print_r("Base form- ".$BFormType);
		print_r("Eform - ".$EForm);
		print_r("Sform - ".$SForm);
		$Model->createBlankForm($EForm);
		print_r("created blank form");
		$mergedForm = $Model->getBlankMergedForm($BFormType);
		//Check for errors at this point
		$mergedForm = $mergedForm[0];
		$mergedForm = $mergedForm["Form_ID"];
		print_r("Merged form - ".$mergedForm);
		$Model->updateMergeTable($mergedForm, $EForm, $SForm);
		print_r("Updated merge table");
		$Model->updateMergedForm($mergedForm, $EForm, $SForm);
		print_r("Updated sections");
		return 1;
	}
	
	function sendErrorMessage($e)
	{
		$response = array();
		$response["error"] = $e;
		echo json_encode($response);
	}
	
	function sendSuccessMessage($e)
	{
		$response = array();
		$response["success"] = $e;
		echo json_encode($response);
	}
}
?>