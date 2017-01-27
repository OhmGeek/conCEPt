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
									WHERE `SectionMarking`.`Form_ID` = :formID AND `Section`.`Sec_Order` = :sectionOrder");
								
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
								WHERE `Form_ID` = :formID");
								
		$statement->bindValue(':formID',$formID, PDO::PARAM_INT);																			

		$result = $statement->execute();
		
		return $result;
		
	}
}
?>