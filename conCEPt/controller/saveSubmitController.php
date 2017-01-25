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
		return "hkd4hdk";
		//return $_SERVER["REMOVE_USER"];
	}


	//Retrieve data from the POST request variables from the form (sent in an array by index.php)
	function retrieveInformation($postVariables)
	{
		
		//$currentUser = $this->getMarkerID();

		//Get the formID and the storeType
		$formID = $postVariables["documentID"];
		$storeType = $postVariables["action"]; //Save or Submit
		
		//If storeType is Confirm or Reject, form is completed merged form, only need to change a flag
		if($storeType == "Confirm"){
			$Model->submitForm($formID);
		}elseif($storeType == "Reject"){
			//$Model->changeEditedFlag($formID, 0);
		}

		$sections = array();
		print_r($storeType);
		// The number of sections in the form (ignoring the comments);
		$numberOfSections = $postVariables["numberOfSections"];
		
		// Iterate through all sections in the form
		for($n=1; $n < $numberOfSections; $n++)
		{
			
			$sectionNumber = $n; //Section number in ordering on form
			if (!(empty($postVariables["mark".$n]))){
				$mark = $postVariables["mark".$n]; //Mark for this section
			}else{
				$mark = 0;
			}
			if (!(empty($postVariables["rationale".$n]))){
				$rationale = trim($postVariables["rationale".$n]); //Rationale for this section
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
			$section = array("sectionNumber"=>($numberOfSections),"mark"=>0, "rationale"=>$comments);
			array_push($sections, $section);
		}
		
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
			//If this is a merged form (it has been edited by the supervisor)
			$isMerged = $Model->isMergedForm($formID);
			if ($isMerged){$Model->changeEditedFlag($formID, 1);}
			else{
				$Model->submitForm($formID);
				//Get details from this form
				$details = $Model->getGeneralDetails($formID);
				$details = $details[0];
				$BFormID = $details["BForm_ID"];
				$studentID = $details["Student_ID"];
				$isSupervisor = $details["IsSupervisor"];
				//Find other marker's form
				$otherForm = $Model->getOtherMarkersForm($studentID, $BFormID, $isSupervisor);
				if (count($otherForm) > 0){
					$otherForm = $otherForm[0];
					$otherForm = $otherForm["Form_ID"];
					if($isSupervisor){
						$SForm = $formID;
						$EForm = $otherForm;
					}else{
						$SForm = $otherForm;
						$EForm = $formID;
					}
					$result = $Model->mergeForms($Eform, $SForm);
					//Find conflicts (either in a query or in a function in this file)
					$Model->updateConflicts($mergedForm, $conflicts);
					//Write conflicts to table
					//Open up two forms 
					$Model->openForm($Eform);
					$Model->openForm($SForm);
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