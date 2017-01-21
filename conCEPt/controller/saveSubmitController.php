<?php
//include model here
include '../model/saveSubmitModel.php';

class SaveSubmitController
{
	
	function __construct($postVariables)
	{
		$this->retrieveInformation($postVariables);
	}
	
	function getCurrentUser()
	{
		//Get user from cookies
	}


	//Retrieve data from the POST request variables from the form (sent in an array by index.php)
	function retrieveInformation($postVariables)
	{
		//$currentUser = $this->getCurrentUser();

		//Get the following from POST variables or from parameters into this function
 		
		$documentID = $postVariables["documentID"];
		
		$storeType = $postVariables["storeType"];

		$sections = array();
		
		// The number of sections in the form (ignoring the comments);
		$numberOfSections = $postVariables["numberOfSections"];
		
		// Iterate through all sections in the form
		for($n=1; $n <= $numberOfSections; $n++)
		{
			if (isset($postVariables["mark".$n]) || isset($postVariables["rationale".$n])){
				$sectionNumber = $n; //Section number in ordering on form
				if (isset($postVariables["mark".$n])){
					$mark = $postVariables["mark".$n]; //Mark for this section
				}else{
					$mark = 0;
				}
				if (isset($postVariables["rationale".$n])){
					$rationale = $postVariables["rationale".$n]; //Rationale for this section
				}else{
					$rationale = "";
				}	

				$section = array("sectionNumber"=>$n,"mark"=>$mark,
				"rationale"=>$rationale);
				
				array_push($sections, $section);
				
			}
		}
		
		// Deal with the comments
		if (isset($postVariables["comments"])){
			$comments = $postVariables["comments"];
			$section = array("sectionNumber"=>($numberOfSections+1),"mark"=>0, "rationale"=>$comments);
			array_push($sections, $section);
		}
		
		// Create array of results
		$response = array();
		$response["sections"] = $sections;
		$response["documentID"] = $documentID;
		$response["storeType"] = $storeType;
		$response = json_encode($response);

		echo $response;
/* 		SEND BACK DATA TO MODEL:
			-$markerID
			-$studentID
			-$documentType/$documentID
			-$sections 
			-$storeType (save or submit)
		
		If successful, send back JSON for success
		
		//Send back confirmation or error message as  JSON to original page
		if (successful){
			sendSuccessMessage("Succeded at ".$storeType."ing");
			exit;
		}else{
			sendErrorMessage("Failed to ".$storeType);
			exit;
		}
		*/		
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