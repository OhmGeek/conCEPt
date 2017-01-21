<?php
//include db (in index file)

class SaveSubmitModel
{
	function __construct()
	{
	}
	
	//DON'T NEED THIS IF USING FORMID
	function getCurrentUser()
	{
		//Return markerID
	}

	function sendSection($formID, $sectionOrderID, $mark, $rationale)
	{
		$db = DB::getDB();
		
		//$currentUser = $this->getCurrentUser(); //If I know the form ID, I don't need this!
		$statement = $db->prepare("INSERT INTO `SectionMarking`(`Sec_ID`, `Form_ID`, `Comment`, `Mark`) 
									SELECT `Section`.`Sec_ID`, :formID , :rationale, :mark
									FROM `Section`
									JOIN `FormSubmission` ON `FormSubmission`.`Form_Title` = `Section`.`Form_Title`
									WHERE `FormSubmission`.`Form_ID` = :formIDCheck AND `Section`.`Sec_Order` = :sectionOrder");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);							
		$statement->bindValue(':formIDCheck',$formID, PDO::PARAM_INT);							
		$statement->bindValue(':rationale',$rationale, PDO::PARAM_STRING);							
		$statement->bindValue(':mark',$mark, PDO::PARAM_INT);							
		$statement->bindValue(':sectionOrder',$sectionOrderID, PDO::PARAM_INT);							

		$statement->execute();
		
	}
}
?>