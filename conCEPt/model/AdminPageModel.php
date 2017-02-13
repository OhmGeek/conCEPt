<?php
namespace Concept\Model;

use PDO;

class AdminPageModel
{
	public function __construct() {
		$this->db = DB::getDB();
	}

	public function getStaffID() {
		return $_SERVER['REMOTE_USER']; 
	}

	public function countSubmittedForms($isSubmitted) {
		// go through forms, get number of submitted.
		$statement = $this->db->prepare("SELECT COUNT(Form_ID) as total
					FROM Form
					WHERE IsSubmitted = :sub");
		$statement->bindValue(":sub", $isSubmitted, PDO::PARAM_INT);
		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data['total'];
	}

	public function getNumberOfStudents() {
		$statement = $this->db->prepare("SELECT COUNT(Student.Student_ID) as stu
										 FROM Student");

		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data['stu'];
	}

	public function getNumberOfMarkers() {
		$statement = $this->db->prepare("SELECT COUNT(Marker.Marker_ID) as mark
										 FROM Marker");

		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data['mark'];
	}

}
