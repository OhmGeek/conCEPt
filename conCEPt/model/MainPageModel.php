<?php

namespace Concept\Model;


use PDO;

class MainPageModel
{
    public function __construct()
    {
        $this->db = DB::getDB();
    }

    public function getStudentForms()
    {
        $marker = $this->getMarkerID();
        $statement = $this->db->prepare(
            "SELECT Student.Student_ID, Form.Form_ID, BaseForm.Form_title, Form.IsSubmitted, Form.IsMerged
				 FROM MS_Form
				 JOIN MS ON MS.MS_ID = MS_Form.MS_ID
				 JOIN Marker ON Marker.Marker_ID = MS.Marker_ID
				 JOIN Student ON Student.Student_ID = MS.Student_ID
				 JOIN Form ON Form.Form_ID=MS_Form.Form_ID
				 JOIN BaseForm ON BaseForm.BForm_ID=Form.BForm_ID
				 WHERE Marker.Marker_ID = :markerID
				 GROUP BY Student.Student_ID, BaseForm.BForm_ID
				 ORDER BY Student.Student_ID, BaseForm.BForm_ID, Form.Time_Stamp DESC;");

        $statement->bindValue(':markerID', $marker, PDO::PARAM_STR);
        $statement->execute();

        //fetch all forms, grouped by student ID
        return $statement->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
    }

    //Generates a list of students separated by examined and supervised

    public function getMarkerID()
    {
        return $_SERVER['REMOTE_USER'];
    }

    public function getMarkerName() {
	$markerID = $this->getMarkerID();
        $statement = $this->db->prepare(
	    "SELECT Fname, Lname
             FROM Marker
             WHERE Marker_ID = :mID");
	$statement->bindValue(':mID', $markerID, PDO::PARAM_INT);
	$statement->execute();
	$data = $statement->fetchAll(PDO::FETCH_ASSOC);
	return $data[0];

    }
    public function getStudentInformation()
    {
        $marker = $this->getMarkerID();
        $statement = $this->db->prepare(
            "SELECT Student.Student_ID, Student.Fname, Student.Lname, MS.IsSupervisor
				 FROM MS_Form
				 JOIN MS ON MS.MS_ID = MS_Form.MS_ID
				 JOIN Marker ON Marker.Marker_ID = MS.Marker_ID
				 JOIN Student ON Student.Student_ID = MS.Student_ID
				 WHERE Marker.Marker_ID = :markerID
				 GROUP BY Student.Student_ID
		");

        $statement->bindValue(':markerID', $marker, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
    }
	
	public function getMergedFormFromIndividual($formID)
	{
		$marker = $this->getMarkerID();
		$statement = $this->db->prepare("SELECT MForm_ID
										FROM MergedForm
										WHERE EForm_ID = :formID OR SForm_ID = :formID2;");
		
		$statement->bindValue(':formID', $formID, PDO::PARAM_INT);
		$statement->bindValue(':formID2', $formID, PDO::PARAM_INT);
		
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
		
	}
	
	public function checkClashes($mergedFormID)
	{
		$db = DB::getDB();
		$statement = $db->prepare("SELECT * 
						FROM `SectionConflict` 
						WHERE `Form_ID` = :formID");
								
		$statement->bindValue(':formID',$mergedFormID, PDO::PARAM_INT);												

		$statement->execute();
		return count($statement->fetchAll(PDO::FETCH_ASSOC));
	}
	
	public function isMergedFormEdited($mergedFormID) {
		$statement = $this->db->prepare(
				"SELECT MergedForm.IsEdited
				 FROM MergedForm
				 WHERE MergedForm.MForm_ID = :mformID
			 ");
		$statement->bindValue(':mformID',$mergedFormID,PDO::PARAM_INT);
		$statement->execute();
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		return $results[0]["IsEdited"];
	}
	
	public function isFormSubmitted($formID) {
		$statement = $this->db->prepare(
				"SELECT Form.IsSubmitted
				 FROM Form
				 WHERE Form.Form_ID = :formID
		");
		$statement->bindValue(":formID",$formID,PDO::PARAM_INT);
		$statement->execute();
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $results[0]["IsSubmitted"];
	}

}
