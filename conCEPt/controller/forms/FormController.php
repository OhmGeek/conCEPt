<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../model/forms/FormModel.php');


class FormController
{
    public static function generateEditableForm($baseFormID)
    {
	$form_model = new FormModel();
	$form_data = $form_model->getFormByID($baseFormID);
	$studentNames = $form_model->getStudentName($baseFormID);
	$markerNames = $form_model->getMarkerName($baseFormID);
	$loader = new Twig_Loader_Filesystem('../view/formPage/');
        $twig = new Twig_Environment($loader);

        $title = $form_model->getFormTitle($baseFormID);
        $markerType = "Unknown";

	if($markerNames["IsSupervisor"] === true) {
		$markerType = "Supervisor";
	}
	else {
		$markerType = "Marker";
	}
        $markerName = $markerNames['Fname'] . " " . $markerNames['Lname'];
        $studentName = $studentNames['Fname'] . " " . $studentNames['Lname'];


        $template = $twig->loadTemplate("allTables.twig");
        $table = $template->render(array('rows'=> $form_data));

        $template = $twig->loadTemplate("editableForm.twig");
        $form = $template->render(array('table'=> $table,
                                        'title'=> $title,
                                        'markerType' => $markerType,
                                        'markerName' => $markerName,
                                        'studentName' => $studentName));

        $template = $twig->loadTemplate("mainFormPage.twig");
        return $template->render(array('form'=> $form));
    }
}
