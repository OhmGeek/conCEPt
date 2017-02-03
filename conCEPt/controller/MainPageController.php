<?php
require_once(__DIR__ . '/../../model/homepage/MainPageModel.php');
require_once(__DIR__ . '/../../controller/navbarController.php');


class MainPageController
{
	private function generateStudentPane($twig,$model) {
		//Generate Student pane
		$student_forms = $model->getStudentForms();
		$students = $model->getStudentInformation();
		$twig_data = array('students'=>array());
		foreach($student_forms as $studentID => $data) {
			$forms = array();
			foreach($data as $value) {
					$submitted_msg = "Not Submitted";
					if($value['IsSubmitted'] == 1) {
						$submitted_msg = "Submitted";
					}
					$merged_text = "";	
					$merged_link = "";
					if($value['IsMerged'] == 1) {
						$merged_text = "Merged";
						$merged_link = "#merged"; 
					}
					$form_id = $value['Form_ID'];
					$form = array(
						'title' => $value['Form_title'],
						'submitted' => $submitted_msg,
						'submitted_link' =>
"forms.php?route=receive&formid=$form_id",
						'merged' => $merged_text,
						'linkMerged' => $merged_link,
						'type' => 'submitted'
					);
					// now add this form to the list of forms for the student
					array_push($forms,$form);

			}
			$student = array(
					'studentName' => $students[$studentID][0]['Fname'] . " " . $students[$studentID][0]['Lname'],
					'forms' => $forms
			);
			array_push($twig_data['students'],$student);
		}
		// now go through all the data gathered, rendering the page itself
		
		$student_pane = $twig->loadTemplate('studentPanel.twig');
		return $student_pane->render($twig_data);
	}

	function generatePage()
	{
		$model = new MainPageModel();
		
		$navbar = new navbarController();		
		//Get info
		
		$loader = new Twig_Loader_Filesystem('../view/homepage/');
        $twig = new Twig_Environment($loader);

		// student pane
		$student_pane = $this->generateStudentPane($twig,$model);
		// for now we shall just return the student pane
		$template = $twig->loadTemplate('homePage.twig');
		return $template->render(array(
			'navbar'=> $navbar->generateNavbarHtml(),
			'studentTab' => $student_pane,
			'pendingTab' => "<div>HI</div>",
			'submittedTab' => "<div>SI</div>",
			'clashesTab' => "<div>BI</div>"
		));
		//Generate pending pane

		return $template;	
		//Generate  submitted pane
		
		//Generate clashes pane

		//Generate main page
	}
}

