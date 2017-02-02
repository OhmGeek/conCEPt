
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$marker = $_POST['Marker_ID'];
		$Fname = $_POST['Fname'];
		$Lname = $_POST['Lname'];	
		if(strcmp($marker,"")==0 or empty($_POST['Lname']) or strcmp($Fname,"")==0) {
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
				 
		$sql = "INSERT INTO `Idcs8s04_conCEPt`.`Marker` (`Marker_ID`, `Fname`, `Lname`) VALUES ('$marker', '$Fname', '$Lname');" ;
		$result = $conn->query($sql);
		$conn->close();
		echo "The query has been made successfully!";
		
}
?>