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

        // student pane
        $student_pane = $this->generateStudentPane($twig, $model);
        // for now we shall just return the student pane
        $template = $twig->loadTemplate('homepage/homePage.twig');
        return $template->render(array(
            'navbar' => $navbar->generateNavbarHtml(),
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

    private function generateStudentPane($twig, $model)
    {
        //Generate Student pane
        $student_forms = $model->getStudentForms();
		print_r($student_forms);
		print_r("\n");
        $students = $model->getStudentInformation();
		//print_r($students);
		//print_r("\n");
        $twig_data = array('students' => array());
        foreach ($student_forms as $studentID => $data) {
            $forms = array();
            foreach ($data as $value) {
                $submitted_msg = "Not Submitted"; //Instead of this, use a boolean for submitted and change the text in twig file, allows for easier changes to twig file later
                if ($value['IsSubmitted'] == 1) {
                    $submitted_msg = "Submitted";
                }
                $merged_text = "";
                $merged_link = "";
                if ($value['IsMerged'] == 1) {
                    $merged_text = "Merged";
					//Get merged form here
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
                array_push($forms, $form);
				
				//print_r($forms);
				//print_r("\n");

            }
            $student = array(
                'studentName' => $students[$studentID][0]['Fname'] . " " . $students[$studentID][0]['Lname'],
                'forms' => $forms
            );
            array_push($twig_data['students'], $student);
        }
        // now go through all the data gathered, rendering the page itself
	
		//print_r($twig_data);
        $student_pane = $twig->loadTemplate('homepage/studentPanel.twig');
        return $student_pane->render($twig_data);
    }
}

