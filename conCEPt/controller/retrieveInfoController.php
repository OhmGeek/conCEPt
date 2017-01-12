<?php
//include model here
	
class RetrieveInformationController
{
	
	function __construct()
	{
		$this->retrieveInformation();
	}
	
	function getCurrentUser()
	{
		//Get user from cookies
	}
	//Form sent here via post request
	
	
	function retrieveInformation()
	{
		$currentUser = $this->getCurrentUser();
		
		//Get the following from POST variables or from parameters into this function
		$student = //Get from POST variable or from documentID POST variable
		$documentType = //Get from documentID POST variable? Could be in form "documentType - studentID"
		$storeType = $_POST["name"]; //Will be "save" or "submit"
		
		$sections = array();
		$n = 1
		// While a section still exists
		while (!empty($_GET["mark".$n]))
		{
			$sectionNumber = $n; //Section number in ordering on form
			$mark = $_GET["mark".$n]; //Mark for this section
			$rationale = $_GET["rationale".$n]; //Rationale for this section
			
			// If either the mark or rationale is filled in, add section to sections array
			if (!($mark === "" && $rationale === "")){ 
				$section = array("sectionNumber"=>$n,"mark"=>$mark,
				"rationale"=>$rationale);
				
				array_push($sections, $section);
			}
			$n = $n+1;
		}
		
/* 		SEND BACK DATA TO MODEL:
			-$markerID
			-$studentID
			-$documentType/$documentID
			-$sections 
			-$storeType
		
		If successful, send back JSON for success
		
		//Send back confirmation or error message as  JSON to original page
		$response = array();
		if (successful){
			$response["success"] = "Succeded at ".$storeType."ing";
		}else{
			$response["error"] = "Failed to ".$storeType;			
		}
		
		echo json_encode($response);*/
				
	}
}
?>