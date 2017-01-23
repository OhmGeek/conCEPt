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
	}


	//Retrieve data from the POST request variables from the form (sent in an array by index.php)
	function retrieveInformation($postVariables)
	{
		
		//$currentUser = $this->getMarkerID();

		//Get the following from POST variables or from parameters into this function
 		
		$documentID = $postVariables["documentID"];
		
		$storeType = $postVariables["action"]; //Save or Submit

		$sections = array();
		
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
		
		// Create array of results
/* 		$response = array();
		$response["sections"] = $sections;
		$response["documentID"] = $documentID;
		$response["storeType"] = $storeType;
		$response = json_encode($response);

		//echo $response; */

		$Model = new saveSubmitModel();
		
		
		foreach($sections as $section)
		{
			$result = $Model->sendSection($documentID, $section["sectionNumber"], $section["mark"], $section["rationale"]);
			if (!($result)){
				echo($this->sendErrorMessage("Couldn't send a section"));
				exit;
			}
		}

		if ($storeType = "submit")
		{
			echo "submitted";
			//Need to change the flag isSubmitted
			echo($Model->submitForm($formID));
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