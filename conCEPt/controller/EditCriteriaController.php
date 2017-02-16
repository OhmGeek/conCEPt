<?php
namespace Concept\Controller;
use Concept\Model\EditCriteriaModel;

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
        $criteria = $model->getFormCriteria($form);


        $loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);


    }

    function editCriteria($form)
    {
        $model = new EditCriteriaModel();
        $criteria = $model->getFormCriteria($form);


    }
}