<?php

class formSelectionModel{
	
	function __construct()
	{
	}

	
	function getMarkerID()
	{
		//Returns the id of the marker currently logged in
	}
	
	//Return an array of student names, along with form ID's for each student 
	function getStudentOptions($formTypeID)
	{
		$markerID = $this->getMarkerID();
		
		//$db = DB::getDB();
		$statement = $db->prepare("");
								
		$statement->bindValue(':markerID',$markerID, PDO::PARAM_INT);
		$statement->bindValue(':formTypeID',$formTypeID, PDO::PARAM_INT);

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

}
?>