<?php
//offers various php database-related services for the admin pages
require_once(__DIR__ . '/../vendor/autoload.php');

// deal with the odd installation we have going on
$base = dirname($_SERVER['PHP_SELF']);

if (ltrim($base, '/')) {
    $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
}

	
	
	
// main routing
// this will respond to requests sent to it by various webpage elements and functions
$router = new \Klein\Klein();

$router->respond('GET', '/test', function () {
    echo 'Hello World';
});


$router->respond('POST', '/Staff_makeStudent', function(){
	//make a new student, return the result state of the request
	
	//ensure that the given data is valid
	if(!strcmp($_POST["Student_ID"], "")){
		return json_encode(array("error" => "Student ID was not set"));
	} 
	else if(!strcmp($_POST['Lname'],"")){
		return json_encode(array("error" => "Surname was not set"));
	} 
	else if(!strcmp($_POST["Fname"], "")){
		return json_encode(array("error" => "Forename was not set"));
	}
	else if(!strcmp($_POST["Year_Level"], "")){
		return json_encode(array("error" => "Year of study was not set"));
	}
	$student = $_POST['Student_ID'];
	$Fname = $_POST['Fname'];
	$Lname = $_POST['Lname'];
	$year = $_POST['Year_Level'];
	
	$servername = "mysql.dur.ac.uk";
	$username = "dcs8s04";
	$password = "when58";
	$dbname = 'Idcs8s04_conCEPt';
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		return json_encode(array("error" => "Connection failed: " . $conn->connect_error));
	}
	//perform the sql query		 
	$sql = "INSERT INTO `Idcs8s04_conCEPt`.`Student` (`Student_ID`, `Fname`, `Lname`, `Year_Level`) VALUES ('$student', '$Fname', '$Lname', '$year');" ;
	$result = $conn->query($sql);
	if($result){
			return json_encode(array("success" => "Student has been created successfully"));
	}
	else{
		return json_encode(array("error" => "Student could not be created (username already exists)"));
	}
	$conn->close();
	
});


$router->respond('POST', '/Staff_makeMarker', function(){	
	if(!strcmp($_POST["Marker_ID"], "")){
		return json_encode(array("error" => "Marker ID was not set"));
	}
	else if (!strcmp($_POST['Lname'], "")){
		return json_encode(array("error" => "Forename was not set"));
	}
	else if (!strcmp($_POST["Fname"], "")){
		return json_encode(array("error" => "Surname was not set"));
	}
	else{
		$marker = $_POST['Marker_ID'];
		$Fname = $_POST['Fname'];
		$Lname = $_POST['Lname'];
		$servername = "mysql.dur.ac.uk";
		$username = "dcs8s04";
		$password = "when58";
		$dbname = 'Idcs8s04_conCEPt';
		
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
			return json_encode(array("error" => "Connection failed: " . $conn->connect_error));
		}
				 
		$sql = "INSERT INTO `Idcs8s04_conCEPt`.`Marker` (`Marker_ID`, `Fname`, `Lname`) VALUES ('$marker', '$Fname', '$Lname');" ;
		$result = $conn->query($sql);
		if($result){
			return json_encode(array("success" => "Marker has been created successfully"));
		}
		else{
			return json_encode(array("error" => "Marker could not be created (username already exists)"));
		}
		$conn->close();
		
	}
});

$router->respond('POST', '/get_student', function(){
	$link = mysqli_connect('mysql.dur.ac.uk', 'dcs8s04', 'when58', 'Idcs8s04_conCEPt');
	if(!strcmp($_POST["student_id"], "")){
		return json_encode(array("error"=>"Student ID was not set"));
	}
	else{
		$sql = "SELECT Student_ID AS student_id, Fname AS forename, Lname as surname, Year_Level AS year_of_study FROM Student WHERE Student_ID LIKE '" . $_POST["student_id"] . "%'";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result) == 1){
			$record = mysqli_fetch_assoc($result);
			return json_encode(array("success"=>array("student_id"=>$record["student_id"], "forename"=>$record["forename"], "surname"=>$record["surname"], "year_of_study"=>$record["year_of_study"])));
		}
		else{
			return json_encode(array("error"=>"ID did not identify a student"));
		}
	}
});

$router->respond('POST', '/get_marker', function(){
	$link = mysqli_connect('mysql.dur.ac.uk', 'dcs8s04', 'when58', 'Idcs8s04_conCEPt');
	if(!strcmp($_POST["marker_id"], "")){
		return json_encode(array("error"=>"Marker ID was not set"));
	}
	else{
		$sql = "SELECT Marker_ID AS marker_id, Fname AS forename, Lname as surname FROM Marker WHERE Marker_ID LIKE '" . $_POST["marker_id"] . "%'";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result) == 1){
			$record = mysqli_fetch_assoc($result);
			return json_encode(array("success"=>array("marker_id"=>$record["marker_id"], "forename"=>$record["forename"], "surname"=>$record["surname"])));
		}
		else{
			return json_encode(array("error"=>"ID did not identify a marker"));
		}
	}
});

function MS_exists($marker_id, $student_id, $link){
	$sql_query = "SELECT MS_ID FROM MS WHERE Marker_ID = '" . marker_id . "' AND Student_ID = '" . student_id . "';";
	$result = mysqli_query($link, $sql_query);
	if(mysqli_num_rows($result) >= 1){
		return True;
	}
	else{
		return False;
	}
}

function can_add_supervisor($marker_id, $student_id, $link){
	$sql_query = "SELECT MS_ID FROM MS WHERE Student_ID = '" . student_id . "' AND IsSupervisor = '1';";
	$result = mysqli_query($link, $sql_query);
	if(mysqli_num_rows($result) >= 1){
		return False;
	}
	else{
		return True;
	}
}

function can_add_examiner($marker_id, $student_id, $link){
	$sql_query = "SELECT MS_ID FROM MS WHERE Student_ID = '" . student_id . "' AND IsSupervisor = '0';";
	$result = mysqli_query($link, $sql_query);
	if(mysqli_num_rows($result) >= 1){
		return False;
	}
	else{
		return True;
	}
}


$router->respond('POST', '/Staff_makeRelationship', function(){
	$link = mysqli_connect('mysql.dur.ac.uk', 'dcs8s04', 'when58', 'Idcs8s04_conCEPt');
	$student_given = false;
	$supervisor_given = false;
	$examiner_given = false;

	$supervisor_id = "";
	$supervisor_forename = "";
	$supervisor_surname = "";
	
	$examiner_id = "";
	$examiner_forename = "";
	$examiner_surname = "";
	
	$student_id = "";
	$student_forename = "";
	$student_surname = "";
	$student_year_of_study = "";
	$sql_query = "SELECT Student_ID AS student_id, Fname as forename, Lname as surname, Year_Level as year_of_study FROM Student WHERE ";
	if(isset($_POST["student_id"])){
		$student_id = $_POST["student_id"];
		$sql_query = $sql_query . "Student_ID LIKE '" . $student_id . "%' AND ";
		$student_given = true;
	}
	if(isset($_POST["student_forename"])){
		$student_forename = $_POST["student_forename"];
		$sql_query = $sql_query . "Fname = '" . $student_forename . "' AND ";
		$student_given = true;
	}
	if(isset($_POST["student_surname"])){
		$student_surname = $_POST["student_surname"];
		$sql_query = $sql_query . "LName = '" . $student_surname . "' AND ";
		$student_given = true;
	}
	if(isset($_POST["student_year_of_study"])){
		$student_year_of_study = $_POST["student_year_of_study"];
		$sql_query = $sql_query . "Year_Level = '" . $student_year_of_study . "' AND ";
		$student_given = true;
	}
	if($student_given){
		$sql_query = $sql_query . "1;";
		$sql_result_student = mysqli_query($link, $sql_query);
		if(mysqli_num_rows($sql_result_student) == 0){
			return json_encode(array("error" => "No student exists for the given information"));
		}
		else if(mysqli_num_rows($sql_result_student) > 1){
			return json_encode(array("error" => "Information supplied for student was not specific enough"));
		}
		else if(mysqli_num_rows($sql_result_student) == 1){
			while($a = mysqli_fetch_assoc($sql_result_student)) {
				$final_student_id = $a["student_id"];
			}
		}
	}
	else{
		return json_encode(array("error" => "No student's information was given"));
	}
	
	
	$sql_query = "SELECT Marker_ID as examiner_id, Fname as examiner_forename, Lname as examiner_surname FROM Marker WHERE ";
	if(isset($_POST["examiner_id"])){
		$examiner_id = $_POST["examiner_id"];
		$sql_query = $sql_query . "Marker_ID = '" . $examiner_id . "' AND ";
		$examiner_given = true;
	}
	if(isset($_POST["examiner_forename"])){
		$examiner_forename = $_POST["examiner_forename"];
		$sql_query = $sql_query . "Fname = '" . $examiner_forename . "' AND ";
		$examiner_given = true;
	}
	if(isset($_POST["examiner_surname"])){
		$examiner_surname = $_POST["examiner_surname"];
		$sql_query = $sql_query . "Lname = '" . $examiner_surname . "' AND ";
		$examiner_given = true;
	}
	if($examiner_given){
		$sql_query = $sql_query . "1;";
		$sql_result_examiner = mysqli_query($link, $sql_query);
		if(mysqli_num_rows($sql_result_examiner) == 0){
			return json_encode(array('error' => 'Information supplied does not relate to any existing examiner'));
		}
		else if(mysqli_num_rows($sql_result_examiner) > 1){
			return json_encode(array('error' => 'Information supplied for examiner was not specific enough'));
		}
		else if(mysqli_num_rows($sql_result_examiner) == 1){
			while($a = mysqli_fetch_assoc($sql_result_examiner)) {
				$final_examiner_id = $a["examiner_id"];
			}
		}
	}
	
	
	$sql_query = "SELECT Marker_ID as supervisor_id, Fname as supervisor_forename, Lname as supervisor_surname FROM Marker WHERE ";
	if(isset($_POST["supervisor_id"])){
		$supervisor_id = $_POST["supervisor_id"];
		$sql_query = $sql_query . "Marker_ID = '" . $supervisor_id . "' AND ";
		$supervisor_given = true;
	}
	if(isset($_POST["supervisor_forename"])){
		$supervisor_forename = $_POST["supervisor_forename"];
		$sql_query = $sql_query . "Fname = '" . $supervisor_forename . "' AND ";
		$supervisor_given = true;
	}
	if(isset($_POST["supervisor_surname"])){
		$supervisor_surname = $_POST["supervisor_surname"];
		$sql_query = $sql_query . "Lname = '" . $supervisor_surname . "' AND ";
		$supervisor_given = true;
	}
	if($supervisor_given){
		$sql_query = $sql_query . "1;";
		$sql_result_supervisor = mysqli_query($link, $sql_query);
		if(mysqli_num_rows($sql_result_supervisor) == 0){
			return json_encode(array('error' => 'Information supplied does not relate to any existing supervisor'));
		}
		else if(mysqli_num_rows($sql_result_supervisor) > 1){
			return json_encode(array('error' => 'Information supplied for supervisor was not specific enough'));
		}
		else if(mysqli_num_rows($sql_result_supervisor) == 1){
			while($a = mysqli_fetch_assoc($sql_result_supervisor)) {
				$final_supervisor_id = $a["supervisor_id"];
			}
		}
	}
	$super_set = "";
	if(!($examiner_given || $supervisor_given)){
		return json_encode(array("error" => "No information was supplied for markers' information"));
	}
	else{
		if($supervisor_given){
			if(!can_add_supervisor($final_supervisor_id, $final_student_id, $link)){
				return json_encode(array("error" => "Student already has a supervisor"));
			}
			else if(MS_exists($final_supervisor_id, $final_student_id, $link)){
				return json_encode(array("error" => "Given supervisor is already assigned as a marker for the given student"));
			}
		}
		else if($examiner_given){
			if(!can_add_examiner($final_examiner_id, $final_student_id, $link)){
				return json_encode(array("error" => "Student already has an examiner"));
			}
			else if(MS_exists($final_examiner_id, $final_student_id, $link)){
				return json_encode(array("error" => "Given examiner is already assigned as a marker for the given student"));
			}
		}
		$sql_query_marker = "INSERT INTO `MS`(`Marker_ID`, `Student_ID`, `IsSupervisor`) VALUES";
		if($examiner_given){
			$sql_query_marker = $sql_query_marker . "('" .$final_examiner_id . "','" . $final_student_id . "',0)";
		}
		if($examiner_given && $supervisor_given){
			$sql_query_marker = $sql_query_marker . ",";
		}
		if($supervisor_given){
			$sql_query_marker = $sql_query_marker . "('" .$final_supervisor_id . "','" . $final_student_id . "',1)";
		}
		$sql_query_marker = $sql_query_marker . " ON DUPLICATE KEY UPDATE Marker_ID = VALUES(Marker_ID), Student_ID = VALUES(Student_ID), IsSupervisor = VALUES(IsSupervisor);";
		
		
		
		if(mysqli_query($link, $sql_query_marker)){
			/* create a blank form for each base */
			
			if($examiner_given){
				$sql_query_link1 = "INSERT INTO `Form`(`BForm_ID`) SELECT `BForm_ID` FROM `BaseForm`";
				$super_set = $super_set . $sql_query_link1;
				if(mysqli_query($link, $sql_query_link1)){
					/* connect the marker-student pair to the 5 form types (forms previously created but not used) - based on student and marker id */
					$sql_query_link2 = "
					INSERT INTO `MS_Form`(`MS_ID`, `Form_ID`) 
					SELECT (
					SELECT `MS`.`MS_ID`
					FROM `MS`
					LEFT JOIN `MS_Form` ON `MS_Form`.`MS_ID` = `MS`.`MS_ID`
					WHERE `MS_Form`.`MS_ID` IS NULL AND `MS`.`Marker_ID` = '" . $final_examiner_id .
					"' AND `MS`.`Student_ID` = '" . $final_student_id . "'
					) AS MS_ID, Form_ID
					FROM
					(
						SELECT `Form`.`Form_ID`, `Form`.`BForm_ID`
						FROM `Form`
						LEFT JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
						WHERE `MS_Form`.`Form_ID` IS NULL AND `IsMerged` = 0
						GROUP BY `Form`.`BForm_ID`
					) AS Table_B;";
					$super_set = $super_set . $sql_query_link2;
					# return $sql_query_link2;
					if(mysqli_query($link, $sql_query_link2)){
						
						$sql_query_link3 = "INSERT INTO `SectionMarking`(`Sec_ID`, `Form_ID`)(
						SELECT `Section`.`Sec_ID`, `Form`.`Form_ID`
						FROM `Form`
						JOIN `Section` ON `Section`.`BForm_ID` = `Form`.`BForm_ID`
						JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
						JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
						WHERE `MS`.`Marker_ID` = '" . $final_examiner_id . "' AND `MS`.`Student_ID` = '" . $final_student_id . "');";
						# return $sql_query_link3_examiner;
						if(!mysqli_query($link, $sql_query_link3)){
							return json_encode(array("error" => "Forms could not be built after creation"));
						}
					
					}
					else{
						return json_encode(array("error" => "Forms could not be linked after creation"));
					}
				}
				else{
					return json_encode(array("error" => "Forms could not be created after creating relationship"));
				}
			}
			
			if($supervisor_given){
				$sql_query_link1 = "INSERT INTO `Form`(`BForm_ID`) SELECT `BForm_ID` FROM `BaseForm`";
				$super_set = $super_set . $sql_query_link1;
				if(mysqli_query($link, $sql_query_link1)){
					/* connect the marker-student pair to the 5 form types (forms previously created but not used) - based on student and marker id */
					$sql_query_link2 = "
					INSERT INTO `MS_Form`(`MS_ID`, `Form_ID`) 
					SELECT (
					SELECT `MS`.`MS_ID`
					FROM `MS`
					LEFT JOIN `MS_Form` ON `MS_Form`.`MS_ID` = `MS`.`MS_ID`
					WHERE `MS_Form`.`MS_ID` IS NULL AND `MS`.`Marker_ID` = '" . $final_supervisor_id .
					"' AND `MS`.`Student_ID` = '" . $final_student_id . "'
					) AS MS_ID, Form_ID
					FROM
					(
						SELECT `Form`.`Form_ID`, `Form`.`BForm_ID`
						FROM `Form`
						LEFT JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
						WHERE `MS_Form`.`Form_ID` IS NULL AND `IsMerged` = 0
						GROUP BY `Form`.`BForm_ID`
					) AS Table_B;";
					$super_set = $super_set . $sql_query_link2;
					# return $sql_query_link2;
					if(mysqli_query($link, $sql_query_link2)){
						
						$sql_query_link3 = "INSERT INTO `SectionMarking`(`Sec_ID`, `Form_ID`)(
						SELECT `Section`.`Sec_ID`, `Form`.`Form_ID`
						FROM `Form`
						JOIN `Section` ON `Section`.`BForm_ID` = `Form`.`BForm_ID`
						JOIN `MS_Form` ON `MS_Form`.`Form_ID` = `Form`.`Form_ID`
						JOIN `MS` ON `MS`.`MS_ID` = `MS_Form`.`MS_ID`
						WHERE `MS`.`Marker_ID` = '" . $final_supervisor_id . "' AND `MS`.`Student_ID` = '" . $final_student_id . "');";
						# return $sql_query_link3_supervisor;
						if(!mysqli_query($link, $sql_query_link3)){
							return json_encode(array("error" => "Forms could not be built after creation"));
						}
					
					}
					else{
						return json_encode(array("error" => "Forms could not be linked after creation"));
					}
				}
				else{
					return json_encode(array("error" => "Forms could not be created after creating relationship "));
				}
			}
			
			if($examiner_given && $supervisor_given){
				return json_encode(array("success" => "Relationship created successfully between both markers and student"));
			}
			else if($supervisor_given){
				return json_encode(array("success" => "Relationship created successfully between supervisor and student"));
			}
			else if($examiner_given){
				return json_encode(array("success" => "Relationship created successfully between examiner and student"));
			}
		}
		else{
			if($examiner_given && $supervisor_given){
				return json_encode(array("error" => "Relationship could not be created between specified markers and student"));
			}
			else if($supervisor_given){
				return json_encode(array("error" => "Relationship could not be created between specified supervisor and student"));
			}
			else if($examiner_given){
				return json_encode(array("error" => "Relationship could not be created between specified examiner and student"));
			}
		}
	}

});


$router->onHttpError(function ($code, $router) {
    if ($code >= 400 && $code < 500) {
        $router->response()->body(
            'Oh no, a bad error happened that caused a ' . $code . $_SERVER['REQUEST_URI']
        );
    } elseif ($code >= 500 && $code <= 599) {
        error_log('uhhh, something bad happened');
    }
});

$router->dispatch();
