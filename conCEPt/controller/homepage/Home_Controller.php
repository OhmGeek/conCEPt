<?php
require_once(__DIR__ . '/../../model/homepage/MainPageModel.php');
class MainPageController
{

	function __construct()
	{
	}

	private function generateStudentPane($twig,$model) {
		//Generate Student pane
		$student_forms = $model->getStudentForms();
		$students = $model->getStudentInformation();
		$twig_data = array();

		foreach($students as $studentID) {
			$forms = array();
			foreach($student_forms[$studentID] as $form) {
					$form = array('formID'=> array(
						'title' => $form['title'],
						'submitted' => $form['submittedmsg'],
						'submitted_link' => $form['completelink'],
						'shadow_submitted' => $form['complete2'],
						'shadow_link' => $form['complete2link'],
						'linkMerged' => $form['mergedlink'],
						'type' => $form['type'],
					));
			}
		}
		// now go through all the data gathered, rendering the page itself
		
		$student_pane = $twig->loadTemplate('studentPanel.twig');
		return $student_pane->render($twig_data);
	}

	function generatePage()
	{
		$model = new MainPageModel();
		
		//Get info
		
		$loader = new Twig_Loader_Filesystem('../view/homepage/');
        $twig = new Twig_Environment($loader);

		// student pane
		$student_pane = $this->generateStudentPane($twig,$model);

		// for now we shall just return the student pane
		return $student_pane;
		//Generate pending pane

		
		//Generate  submitted pane
		
		//Generate clashes pane

		//Generate main page
	}
}

