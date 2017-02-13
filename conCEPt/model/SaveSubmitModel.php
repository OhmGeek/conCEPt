<?php

namespace Concept\Model;

use PDO;

class SaveSubmitModel
{
    function __construct()
    {
    }

    //Inserts a section for a given form
    function sendSection($formID, $sectionOrderID, $mark, $rationale)
    {
        $db = DB::getDB();


        $statement = $db->prepare("UPDATE `SectionMarking` 
									JOIN `Section` ON `Section`.`Sec_ID` = `SectionMarking`.`Sec_ID`
									SET `SectionMarking`.`Comment` =:rationale,`SectionMarking`.`Mark`= :mark
									WHERE `SectionMarking`.`Form_ID` = :formID AND `Section`.`Sec_Order` = :sectionOrder;");

        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);
        $statement->bindValue(':rationale', $rationale, PDO::PARAM_STR);
        $statement->bindValue(':mark', $mark, PDO::PARAM_INT);
        $statement->bindValue(':sectionOrder', $sectionOrderID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;

    }

    //Sets the submit flag of a given form to a given value

    function addSubmitComment($formID, $comment)
    {
        $db = DB::getDB();

        $statement = $db->prepare("UPDATE `Form` 
								SET `Comment`= :comment
								WHERE `Form_ID` = :formID");

        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);
        $statement->bindValue(':comment', $comment, PDO::PARAM_STR);


        $result = $statement->execute();

        return $result;
    }

    //Adds a submission commment to a form

    function getGeneralDetails($formID)
    {
        //QUERY TO RETURN BaseFormId, studentID and isSupervisor
        $db = DB::getDB();

        $statement = $db->prepare("SELECT `Form`.`BForm_ID`, `Student`.`Student_ID`, `MS`.`IsSupervisor`
									FROM `Form` 
									JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
									JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
									JOIN `Student` ON `Student`.`Student_ID` = `MS`.`Student_ID`
									WHERE `Form`.`Form_ID` = :formID");

        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);


        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }

    //Gets general details from a form (BaseFormID, StudentID, IsSupervisor)

    function getOtherMarkerForm($studentID, $bFormID, $isSupervisor)
    {
        //QUERY TO GET OTHER MARKER'S FORM
        //Can just get it from the Merged table if I know if this marker is examiner or supervisor!
        $db = DB::getDB();

        $statement = $db->prepare("SELECT `Form`.`Form_ID`, `Form`.`IsSubmitted`
									FROM `Form` 
									JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
									JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
									JOIN `Student` ON `Student`.`Student_ID` = `MS`.`Student_ID`
									WHERE `Student`.`Student_ID` = :studentID AND `Form`.`BForm_ID` = :bFormID 
									AND `MS`.`IsSupervisor` != :supervisor
									ORDER BY `Form`.`Time_Stamp` DESC,`Form`.`Form_ID` ASC");


        $statement->bindValue(':studentID', $studentID, PDO::PARAM_STR);
        $statement->bindValue(':bFormID', $bFormID, PDO::PARAM_INT);
        $statement->bindValue(':supervisor', $isSupervisor, PDO::PARAM_INT);


        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //Gets the form submitted by the other marker for a given student and form type

    function isMergedForm($formID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT * 
									FROM `MergedForm`
									WHERE `MergedForm`.`MForm_ID` = :formID");

        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);

        $statement->execute();

        return count($statement->fetchAll(PDO::FETCH_ASSOC));
    }

    //Returns 1 if the form is merged, 0 if individual

    function changeEditedFlag($formID, $value)
    {
        $db = DB::getDB();

        $statement = $db->prepare("UPDATE `MergedForm` 
								SET `IsEdited`= :value
								WHERE `MForm_ID` = :formID");


        $statement->bindValue(':value', $value, PDO::PARAM_INT);
        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Updates the edited flag of the given form to the given value

    function createBlankForm($formID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("INSERT INTO `Form`(`BForm_ID`, `IsMerged`) 
									SELECT `Form`.`BForm_ID`, -1
									FROM `Form`
									WHERE `Form`.`Form_ID` = :formID;");


        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Creates a blank form of the same type as the one given

    function getBlankMergedForm($bFormTypeID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT `Form_ID`
									FROM `Form`
									LEFT JOIN `MergedForm` ON `MergedForm`.`MForm_ID` = `Form`.`Form_ID`
									WHERE `IsMerged` = -1 AND `MergedForm`.`MForm_ID` IS NULL AND `BForm_ID` = :bFormID
									ORDER BY `Form_ID` ASC
									LIMIT 1");


        $statement->bindValue(':bFormID', $bFormTypeID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //Gets a blank form of a given type from the DB to use

    function updateMergeTable($mergedFormID, $EFormID, $SFormID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("INSERT INTO `MergedForm`(`MForm_ID`, `EForm_ID`, `SForm_ID`) 
									VALUES (:mergedFormID,:eFormID,:sFormID)");


        $statement->bindValue(':mergedFormID', $mergedFormID, PDO::PARAM_INT);
        $statement->bindValue(':eFormID', $EFormID, PDO::PARAM_INT);
        $statement->bindValue(':sFormID', $SFormID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Adds a merged form to the merge table and links the two contributor forms to it

    function updateMergedForm($mergedFormID, $EFormID, $SFormID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("INSERT INTO `SectionMarking`(`Sec_ID`, `Form_ID`, `Comment`, `Mark`) 
									SELECT `Sec_ID`, :mergedFormID, GROUP_CONCAT(`Comment` SEPARATOR ' '), AVG(`Mark`)
									FROM (
										SELECT `Sec_ID`, `Form_ID`, `Comment`, `Mark` FROM `SectionMarking` WHERE `Form_ID` = :eFormID
										UNION ALL
										SELECT `Sec_ID`, `Form_ID`, `Comment`, `Mark` FROM `SectionMarking` WHERE `Form_ID` = :sFormID
									) AS B
									GROUP BY `Sec_ID`
									ON DUPLICATE KEY UPDATE
									 `Sec_ID` = VALUES(`Sec_ID`), `Comment` = VALUES(`Comment`), `Mark`= VALUES(`Mark`)");


        $statement->bindValue(':mergedFormID', $mergedFormID, PDO::PARAM_INT);
        $statement->bindValue(':eFormID', $EFormID, PDO::PARAM_INT);
        $statement->bindValue(':sFormID', $SFormID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Updates the sections of a merged form by combining the sections from its contributors

    function updateMergeFlag($formID, $value)
    {
        $db = DB::getDB();

        $statement = $db->prepare("UPDATE `Form` 
								SET `IsMerged`= :value
								WHERE `Form_ID` = :formID");


        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);
        $statement->bindValue(':value', $value, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Updates the IsMerged flag of a given form to a given value

    function getMergedForm($formID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("SELECT `MForm_ID` 
									FROM `MergedForm` 
									WHERE `EForm_ID` = :formID OR `SForm_ID` = :formID2");


        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);
        $statement->bindValue(':formID2', $formID, PDO::PARAM_INT);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //Returns the merged form that the given form contributes to

    function removeConflicts($mergedFormID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("DELETE 
									FROM `SectionConflict`
									WHERE `SectionConflict`.`Form_ID` = :mergedFormID");


        $statement->bindValue(':mergedFormID', $mergedFormID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Deletes all conflicts associated with the given form

    function createConflicts($mergedFormID, $anyFormID)
    {
        $db = DB::getDB();

        $statement = $db->prepare("INSERT INTO `SectionConflict`(`Form_ID`,`Sec_ID`)
									SELECT s1.`Form_ID`,s1.`Sec_ID`
									FROM (SELECT `Sec_ID`, `Form_ID`, `Comment`, `Mark` FROM `SectionMarking` WHERE `Form_ID` = :mergedFormID) s1
									INNER JOIN (SELECT `Sec_ID`, `Form_ID`, `Comment`, `Mark` FROM `SectionMarking` WHERE `Form_ID` = :otherFormID) s2 ON s2.`Sec_ID` = s1.`Sec_ID`
									WHERE s1.`Form_ID` = :mergedFormID2 AND s2.`Form_ID`= :otherFormID2 AND s1.`Mark` - s2.`Mark` > 5 OR s2.`Mark` - s1.`Mark` > 5");


        $statement->bindValue(':mergedFormID', $mergedFormID, PDO::PARAM_INT);
        $statement->bindValue(':mergedFormID2', $mergedFormID, PDO::PARAM_INT);
        $statement->bindValue(':otherFormID', $anyFormID, PDO::PARAM_INT);
        $statement->bindValue(':otherFormID2', $anyFormID, PDO::PARAM_INT);

        $result = $statement->execute();

        return $result;
    }

    //Finds conflicts between forms and adds them to the DB

    function getConflicts($mergedFormID)
    {
        $db = DB::getDB();
        $statement = $db->prepare("SELECT `Sec_ID` 
									FROM `SectionConflict` 
									WHERE `Form_ID` = :formID");

        $statement->bindValue(':formID', $mergedFormID, PDO::PARAM_INT);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //Returns the section IDs sections causing conflicts in a merged form

    function duplicateForm($formID)
    {
        $db = DB::getDB();
        //Change comment to duplicate
        $statement = $db->prepare("INSERT INTO `Form`(`BForm_ID`, `IsSubmitted`, `IsMerged`, `Comment`, `Time_Stamp`) 
									SELECT `BForm_ID`, `IsSubmitted`, `IsMerged`, `Comment`, `Time_Stamp`
									FROM `Form`
									WHERE `Form_ID` = :formID;

									INSERT INTO `MS_Form`(`MS_ID`, `Form_ID`)
									SELECT `MS_ID`, LAST_INSERT_ID()
									FROM `MS_Form`
									WHERE `Form_ID` = :formID2;

									INSERT INTO `SectionMarking`(`Sec_ID`, `Form_ID`, `Comment`, `Mark`) 
									SELECT `Sec_ID`, LAST_INSERT_ID() , `Comment`, `Mark`
									FROM (
										SELECT `Sec_ID`, `Form_ID`, `Comment`, `Mark` FROM `SectionMarking` WHERE `Form_ID` = :formID3
									) AS B
									GROUP BY `Sec_ID`");

        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);
        $statement->bindValue(':formID2', $formID, PDO::PARAM_INT);
        $statement->bindValue(':formID3', $formID, PDO::PARAM_INT);


        $result = $statement->execute();

        return $result;
    }

    //Creates complete duplicate of a given form, with a new ID

    function openForm($formID)
    {
        $this->updateSubmitFlag($formID, 0);
    }

    //Changes the IsSubmitted flag of a form to 0 (allows it to be edited again)

    function updateSubmitFlag($formID, $value)
    {
        $db = DB::getDB();

        $statement = $db->prepare("UPDATE `Form` 
								SET `IsSubmitted`= :value, `Time_Stamp` = NOW()
								WHERE `Form_ID` = :formID");

        $statement->bindValue(':formID', $formID, PDO::PARAM_INT);
        $statement->bindValue(':value', $value, PDO::PARAM_INT);


        $result = $statement->execute();

        return $result;

    }
}

?>
