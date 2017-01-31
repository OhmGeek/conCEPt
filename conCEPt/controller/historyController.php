<?php
include '../model/historyModel.php';
class HistoryController
{

	function __construct()
	{
		$this->generatePage();
	}
	
	function getCurrentMarker()
	{
		return "hkd4hdk";
	}

	function generatePage()
	{
	
		$markerID = $this->getCurrentMarker();
		$Model = new historyModel();
		
		$rows = $Model->getAllDocuments($markerID);
	
		$documents = array();
		foreach($rows as $row)
		{
			$formID = $row["Form_ID"];
			$formName = $row["Form_Title"];
			$comment = $row["comment"];
			$studentName = $row["Fname"]." ".$row["Lname"];
			$year = $row["Year_Level"];
			$timeStamp = $row["Time_Stamp"];
			$date = split(" ", $timeStamp);
			$date = $date[0];
			
			$document = array();
			$document["name"] = $formName."-".$studentName."- year ".$year;
			$document["comment"] = $comment;
			$document["link"] = "index.php?route=receive&id=".$formID;
			$document["date"] = $date;
			
			array_push($documents, $document);
		}
		
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);
		
		$navbar = "<h1> Navbar will be generated here </h1>";
		
		$template = $twig->loadTemplate("history.twig");
		print($template->render(array("navbar"=>$navbar, "documents"=>$documents)));
	}

}


?>