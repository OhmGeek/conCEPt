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
        
		$separatedForms = $this->separateIntoArrays($student_forms, $students);
		$studentTab = $this->generateStudentPane($twig, $model, $student_forms, $students);
		$submittedTab = $this->generateStudentPane($twig, $model, $separatedForms["submitted"], $students);
		$pendingTab = $this->generateStudentPane($twig, $model, $separateForms["pending"], $students);
		
		// for now we shall just return the student pane
		$template = $twig->loadTemplate('homepage/homePage.twig');
        return $template->render(array(
            'navbar' => $navbar->generateNavbarHtml(),
            'studentTab' => $student_pane,
            'pendingTab' => $pendingTab,
            'submittedTab' => $submittedTab,
            'clashesTab' => "<div>BI</div>"
        ));
        

        return $template;
        

        //Generate clashes pane

        //Generate main page
    
	}
	
	private function separateIntoArrays($student_forms, $students)
	{
		$pending = array();
		$submitted = array();
		$merged = array();
		
		foreach ($student_forms as $studentID => $data) {
			//Get merged form
			//If it exists
			//Put in merged array
			if(0){
				return;
			}
			else{
				if ($data['IsSubmitted']){
					$submitted[$studentID] = $data;
				}else{
					$pending[$studentID] = $data;
				}
			}
		}
		
		return array("submitted"=>$submitted, "pending"=>$pending, "merged"=>$merged);
		
	}

    private function generateStudentPane($twig, $model, $student_forms, $students)
    {
        //Generate Student pane
		print_r($student_forms);
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
			
			if($isSupervisor){
				array_push($twig_data['SupervisedStudents'], $student);
			}else{
				array_push($twig_data['ExaminedStudents'], $student);
			}
           
            
        }
        // now go through all the data gathered, rendering the page itself
	
        $student_pane = $twig->loadTemplate('homepage/studentPanel.twig');
        return $student_pane->render($twig_data);
    }
}

