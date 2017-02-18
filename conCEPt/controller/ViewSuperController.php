<?php
namespace Concept\Controller;

use Concept\Model\MainPageModel;
use Concept\Model\SuperAdminPageModel;
use Concept\Controller\NavbarController;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ViewSuperController
{
	
	function __construct()
	{
		$this->generatePage();
	}

	function generatePage()
	{
		$studentsModel = new MainPageModel();

		$markersModel = new SuperAdminPageModel();

		$navbar = new NavbarController();

        //Get info

		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

		//markers
		$markers = $markersModel->getAllMarkers();
		$markersPanel = $this->generateMarkersPanels($twig,$studentsModel,$markers);
		
		$navbarTemplate = $twig->loadTemplate('superAdminNavbar.twig');
        $navbar = $navbarTemplate->render(array());
		
		// for now we shall just return the student pane
		$template = $twig->loadTemplate('ViewSuper.twig');
		$output = $template->render(array(
			'navbar' => $navbar,
			'markerList' => $markersPanel,
			'isEmpty' => empty($markersPanel)
			));
		print($output);
	}

	private function generateMarkersPanels($twig, $model, $markers){
		$twig_data = array();
		
		foreach ($markers as $marker => $value) {
			$model->setMarkerID($value['Marker_ID']);

			//Individual forms
			$student_forms = $model->getStudentForms();
			$students = $model->getStudentInformation();

			$students = $this->generateStudentPane($twig, $model, $student_forms, $students);

			$markerForms = array(
				'markerName' => $value['Fname'] . " " . $value['Lname'],
				'markerID' => $value['Marker_ID'],
				'students' => $students
				);

			array_push($twig_data, $markerForms);
		}
		return $twig_data;
	}

	/* 
	- duplicated code to the one in MainPageController 
	- need to change the routes of the forms
	*/
	private function generateStudentPane($twig, $model, $student_forms, $students)
	{
        //Generate Student pane
		
		$twig_data = array('ExaminedStudents' => array(), 'SupervisedStudents' => array());
		foreach ($student_forms as $studentID => $data) {
			$forms = array();
			foreach ($data as $value) {
				$merged_link = "";
				if ($value['IsMerged'] == 1) {
					$mergedForm = $model->getMergedFormFromIndividual($value["Form_ID"]);

	           //Form will be in Clashes or Merged tab
					$mergedForm = $mergedForm[0];
					$mergedFormID = $mergedForm["MForm_ID"];

		    //Get merged form here
					$merged_link = "forms.php?route=receive&formid=" . $mergedFormID;
				}

				$mergedTextValue = $value['IsMerged'];




				$form_id = $value['Form_ID'];
				$mergedForm = $model->getMergedFormFromIndividual($form_id);
				if(count($mergedForm) > 0){
					$mergedForm = $mergedForm[0];
					$mergedForm = $mergedForm["MForm_ID"];
					$hasClashes = $model->checkClashes($mergedForm);
					if($hasClashes > 0){
						$mergedTextValue = 2;
					}
				}
				$form = array(
					'title' => $value['Form_title'],
					'submitted' => $value['IsSubmitted'],
					'submitted_link' =>
					"forms.php?route=receive&formid=$form_id",
					'merged' => $mergedTextValue,
					'linkMerged' => $merged_link,
					'type' => 'submitted'
					);
                // now add this form to the list of forms for the student
				array_push($forms, $form);
				
			}
			
			$isSupervisor = $students[$studentID][0]['IsSupervisor'];

			
			$student = array(
				'studentName' => $students[$studentID][0]['Fname'] . " " . $students[$studentID][0]['Lname'],
				'forms' => $forms
				);
			
			if(count($forms) > 0){
				if($isSupervisor){
					array_push($twig_data['SupervisedStudents'], $student);
				}else{
					array_push($twig_data['ExaminedStudents'], $student);
				}
			}

		}
        // now go through all the data gathered, rendering the page itself

		$students = $twig->loadTemplate('homepage/studentPanel.twig');
		return $students->render($twig_data);
	}
}

