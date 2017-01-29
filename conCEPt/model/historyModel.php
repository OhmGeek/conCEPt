<?php

class HistoryModel
{

	function __construct()
	{
	}
	
	function getAllDocuments($markerID)
	{
		$db = DB::getDB();

		$statement = $db->prepare("SELECT Form.Form_ID, Form.comment, Form.Time_Stamp, BaseForm.Form_Title, Student.Fname, Student.Lname, Student.Year_Level
									FROM Form
									JOIN MS_Form ON MS_Form.Form_ID = Form.Form_ID
									JOIN MS ON MS_Form.MS_ID = MS.MS_ID
									JOIN BaseForm ON BaseForm.BForm_ID = Form.BForm_ID
									JOIN Student ON Student.Student_ID = MS.Student_ID
									WHERE MS.Marker_ID = :markerID
									AND Form.IsSubmitted = 1
									AND Form.IsMerged != -1
									ORDER BY Form.Time_Stamp DESC;");
								
								
		
		$statement->bindValue(':markerID',$markerID, PDO::PARAM_STR);	
		
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

}


?>