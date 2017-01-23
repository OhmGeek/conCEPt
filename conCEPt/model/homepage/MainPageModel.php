<?php

require_once(__DIR__ . '/../db.php');


class mainPageModel
{
	public function __construct() {
			$this->db = DB::getDB();
	}
	
	public function getMarkerID() {
		return $_SERVER['REMOTE_USER'];
	}

	//Generates a list of students separated by examined and supervised
	public function getStudents() {
		$marker = $this->getMarkerID();
		$statement = $this->db->prepare(
				"SELECT Student.Fname, Student.Lname, Student.Year_Level
				 FROM MS_Form
				 JOIN MS ON MS.MS_ID = MS_Form.MS_ID
				 JOIN Marker ON Marker.Marker_ID = MS.Marker_ID
				 JOIN Student ON Student.Student_ID = MS.Student_ID
				 WHERE Marker.Marker_ID = :markerID");

		$statement->bindValue(':markerID',$marker,PDO::PARAM_STR);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	//Get documents for marker and student pair
	//Need to know if submitted and merged
	//Send back id's of individual and merged forms
	//Need to separate by pending, submitted, and clashed
	public function getDocuments()	{
		$marker = $this->getMarkerID();
		$statement = $this->db->prepare(
				"SELECT Form.Form_ID
		$statement->bindValue(':markerID',$marker,PDO::PARAM_STR);
		$statement->execute();

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	
	}



}

?>
