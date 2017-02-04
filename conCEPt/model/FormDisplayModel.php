<?php

class FormModel
{
	function __construct()
	{
	}

	//Returns an array of inforamtion about the student (Name and year level)
	function getStudentInformation($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `Student`.`Fname` , `Student`.`Lname` , `Student`.`Year_Level`
									FROM  `MS_Form`
									JOIN  `MS` ON  `MS`.`MS_ID` =  `MS_Form`.`MS_ID` 
									JOIN  `Student` ON  `Student`.`Student_ID` =  `MS`.`Student_ID` 
									WHERE  `MS_Form`.`Form_ID` = :formID");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	//Returns an array of information about the marker (Name and if they are a supervisor on this form)
	function getMarkerInformation($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT  `Marker`.`Fname` ,  `Marker`.`Lname` , `MS`.`IsSupervisor`
									FROM  `MS_Form`
									JOIN  `MS` ON  `MS`.`MS_ID` =  `MS_Form`.`MS_ID` 
									JOIN  `Marker` ON  `Marker`.`Marker_ID` =  `MS`.`Marker_ID` 
									WHERE  `MS_Form`.`Form_ID` = :formID");
								
		$statement->bindValue(':formID', $formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	//Returns the Form Title, and whether it is submitted and/or merged
	function getFormInformation($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `BaseForm`.`Form_title` , `Form`.`IsSubmitted` , `Form`.`IsMerged`
									FROM `Form` 
									JOIN `BaseForm` ON `BaseForm`.`BForm_ID` = `Form`.`BForm_ID`
									WHERE `Form`.`Form_ID` = :formID");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	//Returns 1 if if the given marker is the marker for the given form
	function checkMarkerIndividual($formID, $markerID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT *
									FROM Form
									JOIN MS_Form ON MS_Form.Form_ID = Form.Form_ID
									JOIN MS ON MS.MS_ID = MS_Form.MS_ID
									JOIN Marker ON Marker.Marker_ID = MS.Marker_ID
									WHERE Marker.Marker_ID = :markerID 
									AND Form.Form_ID = :formID;");
		$statement->bindValue(":formID", $formID, PDO::PARAM_INT);
		$statement->bindValue(":markerID", $markerID, PDO::PARAM_STR);
		$statement->execute();
		return count($statement->fetchAll(PDO::FETCH_ASSOC));
	}
	
	//Returns 1 if the given marker is a contributor to the given merged form
	function checkMarkerMerged($formID, $markerID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT *
									FROM MergedForm
									JOIN Form ON (Form.Form_ID = MergedForm.EForm_ID OR Form.Form_ID = MergedForm.SForm_ID)
									JOIN MS_Form ON MS_Form.Form_ID = Form.Form_ID
									JOIN MS ON MS.MS_ID = MS_Form.MS_ID
									JOIN Marker ON Marker.Marker_ID = MS.Marker_ID
									WHERE Marker.Marker_ID = :markerID 
									AND MergedForm.MForm_ID = :formID;");
		$statement->bindValue(":formID", $formID, PDO::PARAM_INT);
		$statement->bindValue(":markerID", $markerID, PDO::PARAM_STR);
		$statement->execute();
		return count($statement->fetchAll(PDO::FETCH_ASSOC));
	}
	
	//Returns 1 if the given user is an admin
	function checkAdmin($markerID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT *
									FROM Admin
									WHERE ADMIN_ID = :markerID; ");
		$statement->bindValue(":markerID", $markerID, PDO::PARAM_STR);
		$statement->execute();
		return count($statement->fetchAll(PDO::FETCH_ASSOC));
	}
	
	//Returns all sections from a given form
	function getFormSections($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT  `Section`.`Sec_Order` , `Section`.`Sec_Name` , `Section`.`Sec_Percent` , `Section`.`Sec_Criteria` , `SectionMarking`.`Comment` , `SectionMarking`.`Mark`  
									FROM  `SectionMarking` 
									JOIN `Section` ON `Section`.`Sec_ID` = `SectionMarking`.`Sec_ID`
									WHERE  `SectionMarking`.`Form_ID` =  :formID
									ORDER BY `Section`.`Sec_Order`");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	//Returns the total mark of a given form
	function getTotalMark($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT SUM(`Section`.`Sec_Percent`*`SectionMarking`.`Mark` / 100) 
									AS `Total`
									FROM  `SectionMarking` 
									JOIN `Section` ON `Section`.`Sec_ID` = `SectionMarking`.`Sec_ID`
									WHERE  `SectionMarking`.`Form_ID` =  :formID");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}


	//FUNCTIONS FOR DEALING WITH ANYTHING MERGE RELATED

       //Returns an array of information about the student from a merged form (Name and year level)
	function getStudentInformationMerged($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `Student`.`Fname` , `Student`.`Lname` , `Student`.`Year_Level`
								FROM `MergedForm`
								JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `MergedForm`.`EForm_ID`
								JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
								JOIN  `Student` ON  `Student`.`Student_ID` =  `MS`.`Student_ID` 
								WHERE `MergedForm`.`MForm_ID` = :formID");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	//Returns an array of information about both markers who contributed to the given merged form
	function getMarkerInformationMerged($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `Marker`.`Fname`, `Marker`.`Lname`, `MS`.`IsSupervisor`
									FROM `MergedForm`
									JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `MergedForm`.`EForm_ID` OR `MS_Form`.`Form_ID` = `MergedForm`.`SForm_ID`
									JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
									JOIN `Marker` ON `Marker`.`Marker_ID` = `MS`.`Marker_ID`
									WHERE `MergedForm`.`MForm_ID` = :formID");
								
		$statement->bindValue(':formID', $formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	//Returns the ID of the merged form that $formID contributes to
	function getMergedForm($formID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `MForm_ID` 
									FROM `MergedForm` 
									WHERE `EForm_ID` = :eformID OR `SForm_ID` = :sformID");
								
		$statement->bindValue(':eformID',$formID, PDO::PARAM_INT);												
		$statement->bindValue(':sformID',$formID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	//Returns the sections causing conflicts in a merged form
	function getConflicts($mergedFormID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `Sec_ID` 
									FROM `SectionConflict` 
									WHERE `Form_ID` = :formID");
								
		$statement->bindValue(':formID',$mergedFormID, PDO::PARAM_INT);												

		$statement->execute();
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	//Returns 1 if the current marker is the supervisor for this merged form, 0 if examiner
	function isSupervisor($mergedFormID, $currentMarkerID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `MS`.`IsSupervisor` 
									FROM `MergedForm`
									JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `MergedForm`.`EForm_ID` OR `MS_Form`.`Form_ID` = `MergedForm`.`SForm_ID`
									JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
									WHERE `MergedForm`.`MForm_ID` = :formID AND `MS`.`Marker_ID` = :markerID");
								
		$statement->bindValue(':formID',$mergedFormID, PDO::PARAM_INT);												
		$statement->bindValue(':markerID',$currentMarkerID, PDO::PARAM_STR);												

		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	//Returns 1 if the given merged from has been edited by the supervisor
	function isEdited($mergedFormID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `IsEdited` 
									FROM `MergedForm` 
									WHERE `MForm_ID` = :formID");
								
		$statement->bindValue(':formID',$mergedFormID, PDO::PARAM_INT);												

		$statement->execute();
		
		return $statement->fetchAll(PDO::FETCH_ASSOC);
		
	}

	//Returns the number of the section in the form, given its sectionID from the Section table
	function getSectionOrderFromID($sectionID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT `Section`.`Sec_Order` 
									FROM `Section`
									WHERE `Section`.`Sec_ID` = :sectionID");
								
		$statement->bindValue(':sectionID',$sectionID, PDO::PARAM_INT);												

		$statement->execute();
		
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
	
		$result = $results[0];
		return $result["Sec_Order"];
	}

}

?>
