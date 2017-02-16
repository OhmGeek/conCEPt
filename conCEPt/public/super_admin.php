<?php
    
require_once(__DIR__ . '/../vendor/autoload.php');

use Concept\Controller\EditCriteriaController;
use Concept\Controller\ViewSuperController;
use Concept\Model\UserAuthModel;

$username = $_SERVER['REMOTE_USER'];
$auth = new UserAuthModel($username);
$isAdmin = $auth->isSuperAdmin(); /*Check if user is SUPER admin not regular admin*/
if($isAdmin !== true)
{
    $loader = new Twig_Loader_Filesystem(__DIR__ . '/../view');
    $twig = new Twig_Environment($loader);
    $error_template = $twig->loadTemplate('403.twig');
    print($error_template->render(array()));

    exit;
}

$route = $_GET['route'];


/* TODO Add apropirate fucntion calls!*/
if ($route === 'edit')
{
    $page = new EditCriteriaController();
    if(isset($_GET['form']))
    {
        /*IDs for each base form. 1 design 2 presentation 3 project paper 4 project poster 5 oral*/
        switch ($form)
        {
            case 'Design':
                $bFormID = 1;
                break;
            case 'Presentation':
                $bFormID = 2;
                break;
            case 'Project Paper':
                $bFormID = 3;
                break;
            case 'Project Poster':
                $bFormID = 4;
                break;
            case 'Oral':
                $bFormID = 5;
                break;

            default:
                /*exit if requested form is invalid*/
                $page->displayFormList();
                exit;
        }

        $page->baseFormPage($bFormID);
    }
    else
    {
        
        $page->displayFormList();
    }
}
if ($route === 'view')
{
    $test = new ViewSuperController();
}