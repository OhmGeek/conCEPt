<?php
//include db (in index file)

class SaveSubmitModel
{
	function __construct()
	{
	}
	

	function sendSection($formID, $sectionOrderID, $mark, $rationale)
	{
		$db = DB::getDB();
		
		
		$statement = $db->prepare("UPDATE `SectionMarking` 
									JOIN `Section` ON `Section`.`Sec_ID` = `SectionMarking`.`Sec_ID`
									SET `SectionMarking`.`Comment` =:rationale,`SectionMarking`.`Mark`= :mark
									WHERE `SectionMarking`.`Form_ID` = :formID AND `Section`.`Sec_Order` = :sectionOrder;");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);													
		$statement->bindValue(':rationale',$rationale, PDO::PARAM_STR);							
		$statement->bindValue(':mark',$mark, PDO::PARAM_INT);							
		$statement->bindValue(':sectionOrder',$sectionOrderID, PDO::PARAM_INT);							

		$result = $statement->execute();
		
		return $result;
		
	}
	
	function submitForm($formID)
	{
		$db = DB::getDB();

		$statement = $db->prepare("UPDATE `Form` 
								SET `IsSubmitted`= 1, `Time_Stamp` = NOW()
								WHERE `Form_ID` = :formID;");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);	
	
		
		$result = $statement->execute();

		return $result;
		
	}
	
	function getGeneralDetails($formID)
	{
		//QUERY TO RETURN BaseFormId, studentID and isSupervisor
		$db = DB::getDB();

		$statement = $db->prepare("SELECT `Form`.`BForm_ID`, `Student`.`Student_ID`, `MS`.`IsSupervisor`
									FROM `Form` 
									JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
									JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
									JOIN `Student` ON `Student`.`Student_ID` = `MS`.`Student_ID`
									WHERE `Form`.`Form_ID` = 1");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);	
	
		
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
		
	}
	
	function getOtherMarkerForm($studentID, $bFormID, $isSupervisor)
	{
		//QUERY TO GET OTHER MARKER'S FORM
		//Can just get it from the Merged table if I know if this marker is examiner or supervisor!
		$db = DB::getDB();

		$statement = $db->prepare("SELECT `Form`.`Form_ID`
									FROM `Form` 
									JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
									JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
									JOIN `Student` ON `Student`.`Student_ID` = `MS`.`Student_ID`
									WHERE `Student`.`Student_ID` = :studentID AND `Form`.`BForm_ID` = :bFormID 
									AND `Form`.`Form_ID` != 1 AND `Form`.`IsSubmitted` = 1 AND `MS`.`IsSupervisor` != :supervisor
									ORDER BY `Form`.`Time_Stamp` DESC");
								
								
		$statement->bindValue(':studentID',$studentID, PDO::PARAM_STR);	
		$statement->bindValue(':bFormID',$bFormID, PDO::PARAM_INT);	
		$statement->bindValue(':superviosr',$isSupervisor, PDO::PARAM_INT);	
	
		
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	//The following functions deal with Merge related forms
	
	//Returns 1 if the form is merged
	function isMergedForm($formID)
	{
		$db = DB::getDB();

		$statement = $db->prepare("");
								
								
		=
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);	
		
		$statement->execute();

		return count($statement->fetchAll(PDO::FETCH_ASSOC));
	}
	
	//Updates the edited flag of the given form to the given value
	function changeEditedFlag($formID, $value)
	{
		$db = DB::getDB();

		$statement = $db->prepare("");
								
								
		$statement->bindValue(':value',$value, PDO::PARAM_INT);
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);	
		
		$result = $statement->execute();

		return $result;
	}
	
	//Returns 1 if forms merged successfully
	function mergeForms($EForm, $SForm)
	{
		$db = DB::getDB();

		$statement = $db->prepare("");
								
								
		$statement->bindValue(':eForm',$EForm, PDO::PARAM_INT);
		$statement->bindValue(':sForm',$SForm, PDO::PARAM_INT);	
		
		$result = $statement->execute();

		return $result;
	}
	
	function findConflicts($mergedFormID)
	{
		$db = DB::getDB();

		$statement = $db->prepare("");
		//Query needs to :
		//	-Compare each section in the two forms that make up the merged form
		//	-Return any section number where the marks are more than 10 apart
								
		$statement->bindValue(':eForm',$EForm, PDO::PARAM_INT);
		$statement->bindValue(':sForm',$SForm, PDO::PARAM_INT);	
		
		$result = $statement->execute();

		return $result;
	}
	
	//Adds conflicts to the Conflict table
	function updateConflicts($mergedForm, $conflicts)
	{
		$db = DB::getDB();

		$statement = $db->prepare("");
		//Query needs to :
		//	-Compare each section in the two forms that make up the merged form
		//	-Return any section number where the marks are more than 10 apart
								
		$statement->bindValue(':eForm',$EForm, PDO::PARAM_INT);
		$statement->bindValue(':sForm',$SForm, PDO::PARAM_INT);	
		
		$result = $statement->execute();

		return $result;
	}
	
	//Re-opens a submitted form to allow it to be edited
	function openForm($formID)
	{
		$db = DB::getDB();

		$statement = $db->prepare("");
		//Query needs to :
		//	-Compare each section in the two forms that make up the merged form
		//	-Return any section number where the marks are more than 10 apart
								
		$statement->bindValue(':eForm',$EForm, PDO::PARAM_INT);
		$statement->bindValue(':sForm',$SForm, PDO::PARAM_INT);	
		
		$result = $statement->execute();

		return $result;
	}
}
?>