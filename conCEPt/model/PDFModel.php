<?php

namespace Concept\Model;

use Concept\Model\DB;
use PDO;

class PDFModel
{
    public function __construct() 
    {

    }

    public function getPDF($html_input)
    {
        file_put_contents("../temporaryFiles/temp.html", $html_input);
        $encoded_html = rawurlencode($html_input);
        $postdata = http_build_query(array('html' => $encoded_html));

        $url = 'http://test.ohmgeek.co.uk/PDFGenerator/generate_pdf.php';
        //$url = 'http://searchtest.xyxthris.xyz/PDFGenerator/generate_pdf.php';

        $options = array(
            'http' => array(
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'method'  => 'POST',
                'content' => $postdata
            )
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        file_put_contents("../temporaryFiles/temp.pdf", $result);
    }

    public function getFormContentsByID($formID)
    {
        $db = DB::getDB();
        $statement = $db->prepare("SELECT  `Section`.`Sec_Order` , `Section`.`Sec_Name` , `Section`.`Sec_Percent` , `Section`.`Sec_Criteria` , `SectionMarking`.`Comment` , `SectionMarking`.`Mark`
                                   FROM  `SectionMarking`
                                   JOIN `Section` 
                                   ON `Section`.`Sec_ID` = `SectionMarking`.`Sec_ID`
                                   WHERE  `SectionMarking`.`Form_ID` =  :formID");
        $statement->bindValue(":formID", $formID,PDO::PARAM_STR);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }
    public function getTotalFormMarkByID($formID)
    {
        $db = DB::getDB();
        $statement = $db->prepare("SELECT SUM(`Section`.`Sec_Percent`*`SectionMarking`.`Mark` / 100) AS `Total`
                                   FROM  `SectionMarking` 
                                   JOIN `Section`
                                   ON `Section`.`Sec_ID` = `SectionMarking`.`Sec_ID`
                                   WHERE  `SectionMarking`.`Form_ID` =  :formID");
        $statement->bindValue(":formID", $formID,PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (is_numeric($result['Total']))
        {
            $num = floatval($result['Total']);
            return intval(round($num));
        }
        else
        {
            return false;
        }

        return;
        
    }
    public function getAllCompletedFormIDs()
    {
        $db = DB::getDB();
        $statement = $db->prepare("SELECT `Student`.`Student_ID`, `Student`.`Fname`, `Student`.`Lname`, `Form`.`Form_ID`, `BaseForm`.`Form_Title`
                                   FROM `Form`
                                   JOIN `BaseForm` ON `BaseForm`.`BForm_ID` = `Form`.`BForm_ID`
                                   JOIN `MergedForm` ON `MergedForm`.`MForm_ID` = `Form`.`Form_ID`
                                   JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `MergedForm`.`EForm_ID`
                                   JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
                                   JOIN `Student` ON `Student`.`Student_ID` = `MS`.`Student_ID`
                                   WHERE `Form`.`IsSubmitted` = 1 AND `Form`.`IsMerged` = -1
                                   ORDER BY `Student`.`Student_ID` ASC");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC); 
    }
}
