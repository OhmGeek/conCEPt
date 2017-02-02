<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$marker = $_POST['Marker_ID'];
		$student = $_POST['Student_ID'];
		$isSupervisor = $_POST['isSupervisor'];
		if(strcmp($marker,"")==0 or strcmp($student,"")==0 or strcmp($isSupervisor,"")==0) {
			echo "Error : One of the fields seem to be empty. Please try again.";
			exit();
		}
		
		$servername = "mysql.dur.ac.uk";
		$username = "dcs8s04";
		$password = "when58";
		$dbname = 'Idcs8s04_conCEPt';
		
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
     		die("Connection failed: " . $conn->connect_error);
		}
				 
		$sql = "INSERT INTO `Idcs8s04_conCEPt`.`MS` (`Marker_ID`, `Student_ID`, `isSupervisor`) VALUES ('$marker', '$student', '$isSupervisor');" ;
		$result = $conn->query($sql);
		$conn->close();
		echo "The query has been made successfully!";
		
}
?>