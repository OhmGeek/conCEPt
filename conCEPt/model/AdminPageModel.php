<?php
namespace Concept\Model;

use PDO;

class AdminPageModel
{
	//a model to server the admin page
	public function __construct() {
		$this->db = DB::getDB();
	}

	public function getStaffID() {
		return $_SERVER['REMOTE_USER']; 
	}

	public function getStaffName() {
		$statement = $this->db->prepare("SELECT Fname, Lname
						 FROM Admin
						 WHERE Admin_ID = :uname");
		$statement->bindValue(":uname", $_SERVER['REMOTE_USER'],PDO::PARAM_STR);
		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data[0]['Fname'] . " " . $data[0]['Lname'];

	}
	public function countSubmittedForms($isSubmitted) {
		// go through forms, get number of submitted.
		$statement = $this->db->prepare("SELECT COUNT(Form_ID) as total
					FROM Form
					WHERE IsSubmitted = :sub");
		$statement->bindValue(":sub", $isSubmitted, PDO::PARAM_INT);
		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data[0]['total'];
	}

	public function getNumberOfStudents() {
		$statement = $this->db->prepare("SELECT COUNT(Student.Student_ID) as stu
										 FROM Student");

		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data[0]['stu'];
	}

	public function getNumberOfMarkers() {
		$statement = $this->db->prepare("SELECT COUNT(Marker.Marker_ID) as mark
										 FROM Marker");

		$statement->execute();
		$data = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $data[0]['mark'];
	}

}
