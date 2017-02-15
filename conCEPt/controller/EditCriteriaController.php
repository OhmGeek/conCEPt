<?php

use Concept/Model/EditCriteriaModel;

Class EditCriteriaController
{
    function __construct()
    {

    }

    function displayFormList()
    {

    }
    function baseFormPage($bFormID)
    {
        if(empty($_POST))
        {
            $this->displayBaseForm($bFormID);
        }
        else
        {
            $this->editCriteria($bFormID);
            $this->displayBaseForm($bFormID);
        }
    }

    function displayBaseForm($bFormID)
    {

    }

    function editCriteria($form)
    {
        $model = new EditCriteriaModel();
        $criteria = $model->getFormCriteria($form);


    }
}