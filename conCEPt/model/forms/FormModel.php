<?php

include_once(__DIR__ . '/../db.php');
include_once(__DIR__ . '/../auth/UserAuthModel.php');

class FormModel {
	

		function __construct() {
			$this->db = DB::getDB();
		}

		public function getFormByID($formID) {
				$statement = $this->db->prepare(
						"SELECT Section.Sec_Order, Section.Sec_Name, Section.Sec_Percent, Section.Sec_Criteria, SectionMarking.Comment, SectionMarking.Comment, SectionMarking.Mark
						FROM SectionMarking
						JOIN Section ON Section.Sec_ID = SectionMarking.Sec_ID
						WHERE SectionMarking.Form_ID = :formID");
				$statement->bindValue(":formID", $formID,PDO::PARAM_STR);
				$statement->execute();

				return $statement->fetchAll(PDO::FETCH_ASSOC);
		}

		public function getBlankFormByBaseID($bFormID) {
				$statement = $this->db->prepare(
						"SELECT Sec_Name, Sec_Criteria, Sec_Percent, Sec_Order
						 FROM Section
						 WHERE Section.BForm_ID = :baseFormID");
				$statement->bindValue(":baseFormID", $bFormID,PDO::PARAM_STR);
				$statement->execute();

				return $statement->fetchAll(PDO::FETCH_ASSOC);


		}


}
