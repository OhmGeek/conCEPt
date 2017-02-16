<?php
namespace Concept\Model;
use Concept/Model/DB;

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
                                   WHERE `BForm_ID` = :BformID
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
    }
}