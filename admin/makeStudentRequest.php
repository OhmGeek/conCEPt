
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$student = $_POST['Student_ID'];
		$Fname = $_POST['Fname'];
		$Lname = $_POST['Lname'];
		$year = $_POST['Year_Level'];	
		if(strcmp($student,"")==0 or empty($_POST['Lname']) or strcmp($Fname,"")==0 or strcmp($year,"")==0) {
			echo "error";
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
				 
		$sql = "INSERT INTO `Idcs8s04_conCEPt`.`Student` (`Student_ID`, `Fname`, `Lname`, `Year_Level`) VALUES ('$student', '$Fname', '$Lname', '$year');" ;
		$result = $conn->query($sql);
		$conn->close();
		echo "The query has been made successfully!";
		
}
?>
