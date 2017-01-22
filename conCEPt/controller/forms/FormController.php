<?php

require_once(__DIR__ . '/../../vendor/autoload.php');
require_once(__DIR__ . '/../../model/forms/FormModel.php');


class FormController
{
    function __construct()
    {
        $this->generateEditableForm();
    }

    function generateEditableForm($baseFormID)
    {
		$form_model = new FormModel();
		$form_data = $form_model->getBlankFormByBaseID($baseFormID);
	
		$loader = new Twig_Loader_Filesystem('../view/formPage/');
        $twig = new Twig_Environment($loader);

        $title = "Design Report";
        $markerType = "Supervisor";
        $markerName = "Stephen McGough";
        $studentName = "Ben Hemsworth";


        $template = $twig->loadTemplate("allTables.twig");
        $table = $template->render(array('rows'=> $markingCriteria));

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
