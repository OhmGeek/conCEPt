<?php
$server_name = "mysql.dur.ac.uk";
$user_name = "wznd85";
$password = "haggis93";
$db_name = "Pwznd85_Messages";

$conversationsList = array();
// Get request to display possible conversations from a user id
$user_id = $_GET['user_id'];

// Connect to database
$connection = mysqli_connect($server_name, $user_name, $password, $db_name);
if (!$connection){
	die("Connection failed: ".mysqli_connect_error());
}

$query = "SELECT * FROM Conversation JOIN User ON (user_id = user_1 OR user_id = user_2) WHERE NOT(user_id = $user_id);";
$results = mysqli_query($connection, $query);
mysqli_close($connection);

$conversations = array();
while ($row = mysqli_fetch_assoc($results)){
	$other_user_id = $row['user_id'];
	$other_user_f_name = $row['first_name'];
	$other_user_l_name = $row['last_name'];
	$conversation_id = $row["conversation_id"];
	
	$conversation = array();
	$conversation["conversationId"] = $conversation_id;
	$conversation["otherUserId"] = $other_user_id;
	$conversation["fName"] = $other_user_f_name;
	$conversation["lName"] = $other_user_l_name;
	array_push($conversations, $conversation);
}
$conversationsList["conversations"] = $conversations;

$conversationsList = json_encode($conversationsList);
echo $conversationsList;
?>