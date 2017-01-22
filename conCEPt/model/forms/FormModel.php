<?php

include_once(__DIR__ . '/../db.php');
include_once(__DIR__ . '/../auth/UserAuthModel.php');

class FormModel {
	

		function __construct() {
			$this->db = DB::getDB();
		}

		public function getFormByID($formID) {
				$statement = $this->db->prepare(
						"SELECT Section.Sec_Name, Section.Sec_Percent, Section.Sec_Criteria, SectionMarking.Comment, SectionMarking.Comment, SectionMarking.Mark
						FROM SectionMarking
						JOIN Section ON Section.Sec_ID = SectionMarking.Sec_ID
						WHERE SectionMarking.Form_ID = :formID
						ORDER BY Section.Sec_Order");
				$statement->bindValue(":formID", $formID,PDO::PARAM_STR);
				$statement->execute();
				$data = $statement->fetchAll(PDO::FETCH_ASSOC);
				$output = array();	
				// now go through and change formatting
				foreach($data as $row) {
					$rowArray = array(
							'criteria' => $row['Sec_Criteria'],
							'markID' => "1111",
							'markReadOnly' => "readonly",
							'mark' => $row['Mark'],
							'rationaleID' => "1",
							'rationaleReadOnly' => "readonly",
							'rationale' => $row['Comment']
					);
					array_push($output,$rowArray);
				}	
				
				return $output;
		}

		public function getStudentName($formID) {
			$statement = $this->db->prepare(
					"SELECT Student.Fname, Student.Lname
					FROM MS_Form
					JOIN MS ON MS.MS_ID = MS_Form.MS_ID
					JOIN Student ON Student.Student_ID = MS.Student_ID
					WHERE MS_Form.Form_ID = :formID");
			$statement->bindValue(":formID",$formID,PDO::PARAM_STR);
			$statement->execute();
			$output = $statement->fetchAll(PDO::FETCH_ASSOC);
			return $output[0];
		}

		public function getMarkerName($formID) {
			$statement = $this->db->prepare(
					"SELECT Marker.Fname, Marker.Lname, MS.IsSupervisor
					 FROM MS_Form
					 JOIN MS ON MS.MS_ID = MS_Form.MS_ID
					 JOIN Marker ON Marker.Marker_ID = MS.Marker_ID
					 WHERE MS_Form.Form_ID = :formID");
			$statement->bindValue(":formID",$formID,PDO::PARAM_STR);
			$statement->execute();
			$output = $statement->fetchAll(PDO::FETCH_ASSOC);
			return $output[0];

		}

		public function getFormTitle($formID) {
			$statement = $this->db->prepare(
					"SELECT BaseForm.Form_title
					 FROM Form
					 JOIN BaseForm ON BaseForm.BForm_ID = Form.BForm_ID
					 WHERE Form.Form_ID = :formID");
			$statement->bindValue(":formID", $formID, PDO::PARAM_STR);
			$statement->execute();
			$output = $statement->fetchAll(PDO::FETCH_ASSOC);
			return $output[0]['Form_title'];
			

		}
		public function getBlankFormByBaseID($bFormID) {
				$statement = $this->db->prepare(
						"SELECT Sec_Name, Sec_Criteria, Sec_Percent
						 FROM Section
						 WHERE Section.BForm_ID = :baseFormID
						 ORDER BY Sec_Order");
				$statement->bindValue(":baseFormID", $bFormID,PDO::PARAM_STR);
				$statement->execute();

				$data = $statement->fetchAll(PDO::FETCH_ASSOC);

				// now go through the data formatting it correctly





				return $data;
		}


}
