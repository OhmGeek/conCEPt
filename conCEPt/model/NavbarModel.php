<?php

namespace Concept\Model;

class NavbarModel
{
    function __construct()
    {

    }

    //Returns an array of all form titles along with their base IDs
    function getFormTypes()
    {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT BaseForm.Form_Title, BaseForm.BForm_ID
									FROM BaseForm
									ORDER BY BaseForm.BForm_ID ASC;");


        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
