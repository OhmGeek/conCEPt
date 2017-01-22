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

		public function getNamesOfParticipants($formID) {
			$statement = $this->db->prepare(
					"SELECT Student.Fname, Student.Lname, Marker.Fname, Marker.Lname
					 FROM Student, Marker, MS, MS_Form
					 WHERE MS_Form.Form_ID = :formID
					 	AND MS_Form.MS_ID = MS.MS_ID
						AND MS.Marker_ID = Marker.Marker_ID
						AND MS.Student_ID = Student.Student_ID");
			$statement->bindValue(":formID",$formID,PDO::PARAM_STR);
			$statement->execute();
			$data = $statement->fetchAll(PDO::FETCH_ASSOC);
			return array(
			'student' => $data[0]['Student.Fname'] . " " . $data[0]['Student.Lname'],
			'marker' => $data[0]['Marker.Fname'] . " " . $data[0]['Marker.Lname']
			);
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
