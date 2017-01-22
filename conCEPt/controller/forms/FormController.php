<?php


Class FormController
{
    function __construct()
    {
        $this->generateEditableForm();
    }

    function generateEditableForm()
    {
        $loader = new Twig_Loader_Filesystem('../view/formPage/');
        $twig = new Twig_Environment($loader);



        $markingCriteria = /*GET FROM SQL*/ array(array('criteria'=> "Did the student submit any work? (95%)",
                                                        'markID'=> "1111",
                                                        'markReadOnly'=>"readonly",
                                                        'mark'=>"50%",
                                                        'rationaleID'=>"2222",
                                                        'rationaleReadOnly' => "readonly",
                                                        'rationale' => "I forgot to check so I'm hedging my bets"),
                                                  array('criteria'=> "Is it any good? 5%",
                                                        'markID'=> "3333",
                                                        'markReadOnly'=>"readonly",
                                                        'mark'=>"0%",
                                                        'rationaleID'=>"4444",
                                                        'rationaleReadOnly' => "readonly",
                                                        'rationale' => "It's unlikely") );

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
        print($template->render(array('form'=> $form)));
    }
}