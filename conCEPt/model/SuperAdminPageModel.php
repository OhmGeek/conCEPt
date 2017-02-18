<?php
namespace Concept\Model;

use PDO;

class SuperAdminPageModel
{
	
	function __construct()
	{
	}

	function getAllMarkers()
	{
		$db = DB::getDB();
        $statement = $db->prepare("SELECT  `Marker`.`Fname` ,  `Marker`.`Lname` , `Marker`.`Marker_ID`
									FROM  `MS_Form`
									JOIN  `MS` ON  `MS`.`MS_ID` =  `MS_Form`.`MS_ID` 
									JOIN  `Marker` ON  `Marker`.`Marker_ID` =  `MS`.`Marker_ID`");

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>