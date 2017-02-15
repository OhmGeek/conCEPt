<?php

use Concept/Model/EditCriteriaModel;

Class EditCriteriaController
{
    function __construct()
    {
        if(isset($_GET['form']))
        {
            $form = $_GET['form'];
            $this->editCritria($form);
        }
        else
        {
            $this->displayFormList();
        }
    }

    function editCriteria($form)
    {
        $model = new EditCriteriaModel();
        $criteria = $model->getFormCriteria($form);

        
    }
}