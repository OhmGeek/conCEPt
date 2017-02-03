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
		return $_SERVER['REMOTE_USER'];
	}

	//Displays the History page for the current marker
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
			$details = split(" ", $timeStamp);
			$date = $details[0];
			$date = date('d-m-Y', strtotime($date));
			$time = $details[1];
			$document = array();
			$document["name"] = $formName."-".$studentName."- year ".$year;
			$document["comment"] = $comment;
			$document["link"] = "forms.php?route=receive&id=".$formID;
			$document["date"] = $date." at ".$time;
			
			array_push($documents, $document);
		}
		
		$loader = new Twig_Loader_Filesystem('../view/');
        $twig = new Twig_Environment($loader);
		
		$navbar = new navbarController();
		$navbar = $navbar->generateNavbarHtml();
		
		$template = $twig->loadTemplate("history.twig");
		print($template->render(array("navbar"=>$navbar, "documents"=>$documents)));
	}

}


?>
