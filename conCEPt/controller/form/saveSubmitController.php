<?php
//include model here
require_once(__DIR__ . '/../../model/forms/saveSubmitModel.php');

class SaveSubmitController
{
	function __construct($postVariables)
	{
		$this->retrieveInformation($postVariables);
	}
	
	function getMarkerID()
	{
		//return "hkd4hdk";
		//return "knd6usj";
		return $_SERVER["REMOTE_USER"];
	}
	
	//Retrieve data from the POST request variables from the form (sent in an array by index.php)
	function retrieveInformation($postVariables)
	{
		
		$Model = new saveSubmitModel();

		//Get the formID and the storeType
		$formID = $postVariables["documentID"];
		$storeType = $postVariables["action"]; //Save or Submit, or Confirm or Reject
		
		//If storeType is Confirm or Reject, form is completed merged form, only need to change a flag
		if($storeType == "Confirm"){			
			$result = $Model->updateSubmitFlag($formID,1);
			if ($result){
				echo ($this->sendSuccessMessage("Confirm successful"));
			}else{
				echo ($this->sendErrorMessage("Confirm failed, try again"));
			}
			exit;
		}elseif($storeType == "Reject"){
			$result = $Model->changeEditedFlag($formID, 0);
			if ($result){
				echo ($this->sendSuccessMessage("Successfully rejected"));
			}else{
				echo ($this->sendErrorMessage("Failed to reject, try again"));
			}
			exit;
		}
		$sections = array();
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
		if(isset($postVariables["submitComment"])){
			$submissionComment = stripslashes(trim($postVariables["submitComment"]));
		}else{
			$submissionComment = "Initial submission";
		}
		
		foreach($sections as $section)
		{
			$result = $Model->sendSection($formID, $section["sectionNumber"], $section["mark"], $section["rationale"]);
			if (!($result)){
				echo($this->sendErrorMessage("Couldn't send a section, please try again"));
				exit;
			}
		}
		
		if ($storeType == "Submit")
		{
			//If this is a merged form (it has been edited by the supervisor)
			$isMerged = $Model->isMergedForm($formID);
			if ($isMerged){
				$result = $Model->changeEditedFlag($formID, 1);
				if ($result){
					echo ($this->sendSuccessMessage("Edit successful"));
				}else{
					echo ($this->sendErrorMessage("Edit failed, try again"));
				}
				exit;
			}
			else{
				$result = $Model->updateSubmitFlag($formID, 1);
				$result2 = $Model->addSubmitComment($formID, $submissionComment);
				if (!($result && $result2)){
					echo ($this->sendErrorMessage("Submission failed"));
				}
				//Get details from this form
				$details = $Model->getGeneralDetails($formID);
				$details = $details[0];
				$BFormID = $details["BForm_ID"];
				$studentID = $details["Student_ID"];
				$isSupervisor = $details["IsSupervisor"];
				
				//Find other marker's form
				$otherForm = $Model->getOtherMarkerForm($studentID, $BFormID, $isSupervisor);
				if (count($otherForm) > 0){
					$otherForm = $otherForm[0];
					$otherFormSubmitted = $otherForm["IsSubmitted"];
					if ($otherFormSubmitted){
						$otherForm = $otherForm["Form_ID"];
						if($isSupervisor){
							$SForm = $formID;
							$EForm = $otherForm;
						}else{
							$SForm = $otherForm;
							$EForm = $formID;
						}
						$mergedForm = $Model->getMergedForm($EForm);
						if (count($mergedForm) == 0){
							//$result = $Model->mergeForms($Eform, $SForm);
							$result = $this->mergeForms($EForm, $SForm, $BFormID, $Model);
							if (!($result)){
								echo ($this->sendErrorMessage("Merge failed, please resubmit"));
							}
						}else{
							$mergedForm = $mergedForm[0];
							$mergedForm = $mergedForm["MForm_ID"];
							$result = $Model->updateMergedForm($mergedForm, $EForm, $SForm);
							if (!($result)){
								echo ($this->sendErrorMessage("Merge failed, please resubmit"));
								//Have to reopen form for resubmission
								//$this->reset($EForm, $SForm, $mergedForm); //Don't remove the form, just open up this one again??
							}
						}
						$mergedForm = $Model->getMergedForm($EForm);
						if (count($mergedForm) > 0){
							$mergedForm = $mergedForm[0];
							$mergedForm = $mergedForm["MForm_ID"];
							//Remove conflicts first so they aren't re-found even after they've been dealt with
							$Model->removeConflicts($mergedForm);
							//Find conflicts (either in a query or in a function in this file)
							$Model->createConflicts($mergedForm, $EForm);
							$conflicts = $Model->getConflicts($mergedForm);
							if (count($conflicts) > 0){
								$Model->duplicateForm($EForm);
								$Model->duplicateForm($SForm);
								$Model->openForm($EForm);
								$Model->openForm($SForm);
							}
						}
					}
				}
			}
		}
		
		
		//Assume it has been successful if it got this far
		return ($this->sendSuccessMessage($storeType." successfull"));
		exit;
		
	}
	
	function mergeForms($EForm, $SForm, $BFormType, $Model)
	{
		$result = true; //assume it worked
		$Model->createBlankForm($EForm);
		$mergedForm = $Model->getBlankMergedForm($BFormType);
		if (count($mergedForm) == 0){
			$result = false;
		}else{
			//Check for errors at this point
			$mergedForm = $mergedForm[0];
			$mergedForm = $mergedForm["Form_ID"];
			$result1 = $Model->updateMergeTable($mergedForm, $EForm, $SForm);
			$result2 = $Model->updateMergedForm($mergedForm, $EForm, $SForm);
			$result3 = $Model->updateMergeFlag($EForm, 1);
			$result4 = $Model->updateMergeFlag($SForm, 1);
			$result = ($result1 && $result2 && $result3 && $result4); //all must have succeeded
		}
		
/* 		if (!($result)){
			$this->reset($EForm, $SForm, $mergedForm);
		} */
		return $result;
	}
	
	function reset($EForm, $SForm, $mergedForm)
	{
		//remove merged form from form table
		//change merge flags back to 0
		//Open up original so they can resubmit??
	}
	
	function sendErrorMessage($e)
	{
		echo '{"success":"'.$e.'"}';
	}
	
	function sendSuccessMessage($e)
	{
		echo '{"success":"'.$e.'"}';
		
	}
}
?>
