<?php
namespace Concept\Model;
use PDO;

class FormSelectionModel
{

    function __construct()
    {
    }


    //Given a form typeID (ID from the BaseForm table), returns a list of formIDs this marker has access to, along with the names of the students associated with those forms
    function getStudentOptions($formTypeID, $markerID)
    {
        $db = DB::getDB();


        //$db = DB::getDB();
        $statement = $db->prepare("SELECT `Form`.`Form_ID`, `Student`.`Fname` , `Student`.`Lname` , `Student`.`Year_Level`
									FROM  `MS_Form`
									JOIN  `MS` ON  `MS`.`MS_ID` =  `MS_Form`.`MS_ID` 
									JOIN  `Marker` ON  `Marker`.`Marker_ID` =  `MS`.`Marker_ID` 
									JOIN  `Student` ON  `Student`.`Student_ID` =  `MS`.`Student_ID`
									JOIN `Form` ON `Form`.`Form_ID` = `MS_Form`.`Form_ID`
									JOIN `BaseForm` ON `BaseForm`.`BForm_ID` = `Form`.`BForm_ID`
									WHERE  `Marker`.`Marker_ID` = :markerID AND `BaseForm`.`BForm_ID` = :formTypeID");

        $statement->bindValue(':markerID', $markerID, PDO::PARAM_STR);
        $statement->bindValue(':formTypeID', $formTypeID, PDO::PARAM_INT);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //Returns the title of the given form
    function getFormName($formTypeID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT `BaseForm`.`Form_Title`
									FROM `BaseForm`
									WHERE `BForm_ID` = :formTypeID");
        $statement->bindValue(':formTypeID', $formTypeID, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

}
