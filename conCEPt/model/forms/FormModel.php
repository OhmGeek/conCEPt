<?php

class FormModel
{
	function __construct()
	{
	}

	//Returns an array of inforamtion about the student
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

	//Returns an array of information about 1 or 2 markers
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

	//Have separate function for merged document as there will be 2 markers?

}

?>