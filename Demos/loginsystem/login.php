<?php
/**
 * Created by PhpStorm.
 * User: ryan
 * Date: 07/11/16
 * Time: 20:57
 */
$current_user = $_SERVER['REMOTE_USER'];
$conn = new mysqli("mysql.dur.ac.uk", "nobody", "", "Pdcl0www_userdata");

if ($conn->connect_error) {
    die("Connection failed due to: " . $conn->connect_error);
} else {
    echo "Connected successfully \n";
    $sql_to_execute = "SELECT firstnames,surname,current_staff FROM UserDetails WHERE username=\"" . $current_user . "\"";
    echo "\n";
    echo $sql_to_execute;
    $result = $conn->query($sql_to_execute);
    print_r(array($result));
    if (!$result) {
        die($conn->error);
    }
    while ($row = $result->fetch_object()) {
        if ($row->firstnames) {
            echo "FName: " . $row->firstnames;
        } else if ($row->surname) {
            echo "LName: " . $row->surname;
        } else if ($row->current_staff) {
            echo "Current staff: " . $row->$current_staff;
        }
    }
    print_r($user_arr);

}

$conn->close();