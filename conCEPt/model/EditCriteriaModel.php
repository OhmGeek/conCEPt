<?php
namespace Concept\Model;

use Concept\Model\DB;
use PDO;

class EditCriteriaModel
{
    function __construct()
    {

    }

    function getFormCriteria($bFormID)
    {

        $db = DB::getDB();
        $statement = $db->prepare("SELECT * 
                                   FROM `Section`
                                   WHERE `BForm_ID` = :BFormID
                                   ORDER BY `Sec_Order`");


        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function setFormCriteria($secName, $secCrit, $secPerc, $bFormID, $secOrder)
    {
        $db = DB::getDB();
        $statement = $db->prepare("UPDATE `Section` 
                                   SET `Sec_Name`= :Sec_Name ,`Sec_Criteria`= :Sec_Criteria ,`Sec_Percent`= :Sec_Percent
                                   WHERE `BForm_ID` = :BFormID AND `Sec_Order`= :Sec_Order");

        $statement->bindValue(':Sec_Name', $secName, PDO::PARAM_STR);
        $statement->bindValue(':Sec_Criteria', $secCrit, PDO::PARAM_STR);
        $statement->bindValue(':Sec_Percent', $secPerc, PDO::PARAM_STR);

        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->bindValue(':Sec_Order', $secOrder, PDO::PARAM_INT);


        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);


    }

    function createFormCriteria($secName, $secCrit, $secPerc, $bFormID, $secOrder)
    {
        $db = DB::getDB();
        $statement = $db->prepare("INSERT INTO `Section`(`Sec_Name`, `BForm_ID`, `Sec_Criteria`, `Sec_Percent`, `Sec_Order`) VALUES (:Sec_Name,:BFormID,:Sec_Criteria,:Sec_Percent,:Sec_Order)");

        $statement->bindValue(':Sec_Name', $secName, PDO::PARAM_STR);
        $statement->bindValue(':Sec_Criteria', $secCrit, PDO::PARAM_STR);
        $statement->bindValue(':Sec_Percent', $secPerc, PDO::PARAM_STR);

        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->bindValue(':Sec_Order', $secOrder, PDO::PARAM_INT);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function deleteFormCriteria($bFormID, $secOrder)
    {
        $db = DB::getDB();
        $statement = $db->prepare("DELETE FROM `Section` 
                                   WHERE `BForm_ID` = :BFormID AND `Sec_Order` = :secOrder");

        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->bindValue(':Sec_Order', $secOrder, PDO::PARAM_INT);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function countNoSections($bFormID)
    {
        $db = DB::getDB();
        $statement = $db->prepare("SELECT COUNT(`Sec_ID`) AS Num_Sections
                                   FROM `Section` 
                                   WHERE `BForm_ID` = :BFormID");

        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    deleteBaseForm - delete a base form that in turn would remove all sections and forms that are related to it from all the tables in the database. Once the forms are deleted, there is no way of getting them back unless a backup of the database is made.
    */
    function deleteBaseForm($bFormID){
        $db = DB::getDB();
        $statement = $db->prepare("DELETE FROM `BaseForm` WHERE `BForm_ID` = :BFormID");

        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->execute();
    }

    /*
    linkAllMSToBaseForm - link all marker-student pairs to the new base form. The base form and its sections must be created before running this function
    */
    function linkAllMSToBaseForm($bFormID){
        $db = DB::getDB();
        $statement = $db->prepare("INSERT INTO Form(BForm_ID) 
                                    SELECT :BFormID
                                    FROM MS;

                                    SET @row_numberM = 0;
                                    SET @row_numberF = 0;

                                    INSERT MS_Form (MS_ID, Form_ID)
                                    SELECT MS_ID, Form_ID
                                    FROM (SELECT MS_ID, (@row_numberM:=@row_numberM + 1) AS num
                                    FROM MS) m 
                                    JOIN (SELECT Form_ID, (@row_numberF:=@row_numberF + 1) AS num
                                    FROM Form WHERE BForm_Id = :BFormID) f ON f.num = m.num;

                                    INSERT INTO SectionMarking(Sec_ID, Form_ID)
                                    SELECT Section.Sec_ID, MS_Form.Form_ID
                                    FROM Form
                                    JOIN Section ON Section.BForm_ID = Form.BForm_ID
                                    JOIN MS_Form ON MS_Form.Form_ID = Form.Form_ID
                                    WHERE Form.BForm_ID = :BFormID;
                                    ");

        $statement->bindValue(':BFormID', $bFormID, PDO::PARAM_INT);
        $statement->execute();
    }
}