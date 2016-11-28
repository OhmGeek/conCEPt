<?php
require_once "../vendor/autoload.php";
include "../reportmaker/TableMaker.php";
include "../reportmaker/FileReader.php";

//first, let's connect to the database and query it.
//todo use PDO.
$conn = new mysqli("mysql.dur.ac.uk","nobody","","Pmmgw12_CEP");

if($conn->connect_error) {
    die("Connection failed to the database. Error: " . $conn->connect_error);
} else {

    //connected!
    // now, let's query the form id:
    $form_name = "Form1";
    $sql_to_execute = "SELECT Section.Title, Section.Comment, Section.Mark FROM Section, Form WHERE Section.Form_ID = Form.Form_ID AND Form.Name = \'" . $form_name . "\' ORDER BY Section.Order";

    //todo remove this test code
    $result = $conn->query($sql_to_execute);
    print_r(array($result));

    //now let's go through creating an array to be parsed by the TableMaker:

    while($row = $result->fetch_object()) {
        $section_details = array("section" => $row->Title, "perweight"=>$row->Mark, "specification"=>row->specification);

    }
}

