<?php
$server_name = "mysql.dur.ac.uk";
$user_name = "wznd85";
$password = "haggis93";
$db_name = "Pwznd85_Messages";

if ($_SERVER["REQUEST_METHOD"]=="GET"){
	// Get request to display possible conversations from a user id
	$user_id = $_GET['user_id'];
	$conversation_id = $_GET['conversation_id'];
	$other_user_id = $_GET['other_user_id'];

	// Connect to database
	$connection = mysqli_connect($server_name, $user_name, $password, $db_name);
	if (!$connection){
		die("Connection failed: ".mysqli_connect_error());
	}

	$query = "SELECT * FROM Messages WHERE conversation_id = $conversation_id;";
	$results = mysqli_query($connection, $query);
	mysqli_close($connection);

	$messages = array();
	while ($row = mysqli_fetch_assoc($results)){
		// Could add timestamp to database to get things in order
		$message = array();
		$messageContent = $row['message'];
		$from = $row['user_from'];
		$to = $row['user_to'];
		$message['content'] = $messageContent;
		$message['from'] = $from;
		$message['to'] = $to;
		array_push($messages,$message);
	}

	$messageList = array();
	$messageList['messages'] = $messages;

	$messageList = json_encode($messageList);
	echo $messageList;
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
	$conversation_id = $_POST['conversation_id'];
	$user_from = $_POST['user_from'];
	$user_to = $_POST['user_to'];
	$content = $_POST['message'];
	
	echo '{"content":"'.$content.'"}';
	// Connect to database
	$connection = mysqli_connect($server_name, $user_name, $password, $db_name);
	if (!$connection){
		die("Connection failed: ".mysqli_connect_error());
	}

	$query = "INSERT INTO Messages (conversation_id, user_from, user_to, message) VALUES ($conversation_id, $user_from, $user_to, '".$content."') ;";
	echo '{"query":"'.$query.'"}';
	$results = mysqli_query($connection, $query);
	mysqli_close($connection);

	echo '{"success":"sucess"}';
}
?>