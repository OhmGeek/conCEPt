<?php
namespace Concept\Controller;

use Concept\Model\MainPageModel;
use Concept\Controller\NavbarController;
use Twig_Loader_FileSystem;
use Twig_Environment;
class MainPageController
{
    function generatePage()
    {
        $model = new MainPageModel();

        $navbar = new NavbarController();
        //Get info

        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);

	//Individual forms
	$student_forms = $model->getStudentForms();
        $students = $model->getStudentInformation();
	
	
        // student pane
        //$student_pane = $this->generateStudentPane($twig, $model, $student_forms, $students);
    
		$separatedForms = $this->separateIntoArrays($student_forms, $students, $model);
		$submitted = $separatedForms["submitted"];
		$pending = $separatedForms["pending"];
		$merged = $separatedForms["merged"];
		$clashes = $merged["clashes"];
		$normalMerged = $merged["normal"];
		
		print_r($separatedForms);
		$studentTab = $this->generateStudentPane($twig, $model, $student_forms, $students);
		$submittedTab = $this->generateStudentPane($twig, $model, $submitted, $students);
		$pendingTab = $this->generateStudentPane($twig, $model, $pending, $students);
		
		
		$clashesTab = $this->generateStudentPane($twig, $model, $clashes, $students);

		$mergedTab = $this->generateMergedPane($twig, $model, $normalMerged, $students);
		
		
		// for now we shall just return the student pane
		$template = $twig->loadTemplate('homepage/homePage.twig');
        return $template->render(array(
            'navbar' => $navbar->generateNavbarHtml(),
            'studentTab' => $studentTab,
            'pendingTab' => $pendingTab,
            'submittedTab' =>$submittedTab,
            'clashesTab' => $clashesTab,
			'mergedTab' => $mergedTab
        ));
        

        return $template;
        

        //Generate clashes pane

        //Generate main page
    
	}
	
	private function separateIntoArrays($student_forms, $students, $model)
	{
		$pending = array();
		$submitted = array();
		$mergedClashes = array();
		$mergedNormal = array();
		$merged = array();
		
		foreach ($student_forms as $studentID => $data) {
			$studentSubmitted = array();
			$studentPending = array();
			$studentMerged = array();
			$studentMergedClashed = array();
			
			
			foreach ($data as $value){
				$formID = $value["Form_ID"];
				
				$mergedForm = $model->getMergedFormFromIndividual($formID);
				if(count($mergedForm) > 0){
print_r("======================================================================================");
print_r("Merged form exists");
print_r($mergedForm);
$mergedForm = $mergedForm[0];
$mergedForm = $mergedForm[MForm_ID];
					$hasClashes = $model->checkClashes($mergedForm);
print_r($hasClashes);
print_r("=====================================================================================");
					if($hasClashes > 0){
						array_push($studentMergedClashed,$value);
					}else{
						array_push($studentMerged, $value);
					}
					
				}
				else{
					if ($value['IsSubmitted']){
						//print_r("Is submitted");
						array_push($studentSubmitted, $value);
	
					}else{
						//print_r("Pending");
						array_push($studentPending, $value);
					}
				}
			}
			
			if (count($studentSubmitted) > 0){
				$submitted[$studentID] = $studentSubmitted;
			}
			if (count($studentPending) > 0){
				$pending[$studentID] = $studentPending;
			}
			
			$mergedNormal[$studentID] = $studentMerged;
			$mergedClashes[$studentID] = $studentMergedClashed;
			
		}
	
		$merged = array("normal"=>$mergedNormal, "clashes"=>$mergedClashes);
		return array("submitted"=>$submitted, "pending"=>$pending, "merged"=>$merged);
		
	}
	

    private function generateStudentPane($twig, $model, $student_forms, $students)
    {
        //Generate Student pane
		//print_r($student_forms);
		//print_r("\r\n");
        $twig_data = array('ExaminedStudents' => array(), 'SupervisedStudents' => array());
        foreach ($student_forms as $studentID => $data) {
            $forms = array();
            foreach ($data as $value) {
                $merged_link = "";
                if ($value['IsMerged'] == 1) {
                    //$merged_text = "Merged";
		    $merged_form_id = 20; //Get merged form (I have a query for this in SaveSubmitController I think)
		    //Get merged form here
                    $merged_link = "forms.php?route=receive&formid=" . $merged_form_id;
                }
                $form_id = $value['Form_ID'];
                $form = array(
                    'title' => $value['Form_title'],
                    'submitted' => $value['IsSubmitted'],
                    'submitted_link' =>
                        "forms.php?route=receive&formid=$form_id",
                    'merged' => $value['IsMerged'],
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
	
        $student_pane = $twig->loadTemplate('homepage/studentPanel.twig');
        return $student_pane->render($twig_data);
    }
	
	
	private function generateMergedPane($twig, $model, $student_forms, $students)
	{
		//Generate Student pane
		//print_r($student_forms);
		//print_r("\r\n");
        $twig_data = array('Complete' => array(), 'NeedsEditing' => array(), 'NeedsConfirming' => array());
        foreach ($student_forms as $studentID => $data) {
            $formsComplete = array();
			$formsNeedsEditing = array();
			$formsNeedsConfirming = array();
			
            foreach ($data as $value) {
                $merged_link = "";
                
				//$merged_text = "Merged";
				$mergedForm = $model->getMergedFormFromIndividual($value["Form_ID"]);
				print_r($mergedForm);
				//Form will be in Clashes or Merged tab
				$mergedForm = $mergedForm[0];
				$mergedFormID = $mergedForm["MForm_ID"];
				$isEdited = $model->isMergedFormEdited($mergedFormID);
				$isSubmitted = $model->isFormSubmitted($mergedFormID);
				$hasClashes = $model->checkClashes($mergedFormID);
				print_r("Edited-".$isEdited);
				print_r("Submitted-".$isSubmitted);
				print_r("Clashes-".$hasClashes);
		
		    //Get merged form here
                $merged_link = "forms.php?route=receive&formid=" . $mergedFormID;
                
                $form_id = $value['Form_ID'];
                $form = array(
                    'title' => $value['Form_title'],
                    'submitted' => $value['IsSubmitted'],
                    'submitted_link' =>
                        "forms.php?route=receive&formid=$form_id",
                    'merged' => $value['IsMerged'],
                    'linkMerged' => $merged_link,
                    'type' => 'submitted'
                );
				
				if($isSubmitted){
					array_push($formsComplete, $form);
				}elseif($isEdited){
					array_push($formsNeedsConfirming, $form);
				}else{
					array_push($formsNeedsEditing, $form);
				}
				
            }

			
			$studentComplete = array(
                'studentName' => $students[$studentID][0]['Fname'] . " " . $students[$studentID][0]['Lname'],
                'forms' => $formsComplete,
            );
			
			$studentNeedsEditing = array(
                'studentName' => $students[$studentID][0]['Fname'] . " " . $students[$studentID][0]['Lname'],
                'forms' => $formsNeedsEditing,
            );
			
			$studentNeedsConfirming = array(
                'studentName' => $students[$studentID][0]['Fname'] . " " . $students[$studentID][0]['Lname'],
                'forms' => $formsNeedsConfirming,
            );
			
			
			if(count($studentComplete["forms"]) > 0){
				array_push($twig_data["Complete"], $studentComplete);
			}
			if(count($studentNeedsEditing["forms"]) > 0){
				array_push($twig_data["NeedsEditing"], $studentNeedsEditing);
			}
			if(count($studentNeedsConfirming["forms"]) > 0){
				array_push($twig_data["NeedsConfirming"], $studentNeedsConfirming);
			}
            
        }
        // now go through all the data gathered, rendering the page itself
		
        $student_pane = $twig->loadTemplate('homepage/mergedPanel.twig');
        return $student_pane->render($twig_data);
		
	}
}

