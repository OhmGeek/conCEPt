<?php
    
require_once(__DIR__ . '/../vendor/autoload.php');

use Concept/Controller/EditCritriaController;
use Concept/Model/UserAuthModel;


/*TODO implement Auth check for SUPER ADMIN*/


$route = $_GET['route'];

if ($route === 'edit')
{
    $test = new EditCriteriaController();
}
if ($route === 'view')
{
    $test = new ViewSuperController();
}