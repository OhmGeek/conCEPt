//TODO - Figure out how to know what the current userId is (perhaps with a small php request)
//     - Figure out how to continuously poll for new messages


var userId = 2 // Get it from php using cookies??
$(document).ready(function(){ 
		loadUsers();
	});

function loadUsers(){
	var peopleList = document.getElementById("people-list");
	$.getJSON('people.php',{user_id: userId}, function(jsonData){
		var conversations = jsonData['conversations'];
		for (var i = 0; i < conversations.length; i++){
			var conversation = conversations[i];
			var conversationId = conversation['conversationId'];
			var otherId = conversation['otherUserId'];
			var otherFName = conversation['fName'];
			var otherLName = conversation['lName'];
			
			var element = document.createElement("LI");
			var link = document.createElement("A");
			link.setAttribute("id", conversationId+ "-"+otherId);
			link.appendChild(document.createTextNode(otherFName + " " +otherLName));
			element.appendChild(link);
			peopleList.appendChild(element);
		}
	});
}

$(document).on('click', '#people-list a', function(){
	var id = $(this).attr("id");
	var details = id.split("-");
	var conversationId = details[0];
	var otherUserId = details[1];
	document.getElementById("mainHeader").innerHTML = $(this).text();
	console.log(details);
	var data = {user_id: userId, conversation_id: conversationId, other_user_id:otherUserId};
	console.log(data);
	$.getJSON('messages.php', data, function(jsonData){
		console.log("got data");
		console.log(jsonData);
		//Extract data
		var messages = jsonData['messages'];
		//Display messages
		var messageLocation = document.getElementById("message-location");
		messageLocation.innerHTML="";
		for (var x = 0; x<messages.length; x++){
			var message = messages[x];
			var content = message['content'];
			var sent; //boolean, true if this user sent the message
			if (message['to'] == otherUserId){
				sent = true;
			}else{
				sent = false;
			}
			var mainDiv = document.createElement("DIV");
			var container = document.createElement("P");
			if (sent){container.setAttribute("style","text-align:right;")};
			container.appendChild(document.createTextNode(content));
			messageLocation.appendChild(container);
		}
		
		// Add typing area
		var textBox = document.getElementById("text-box");
		var messageBox = document.createElement("DIV");
		messageBox.innerHTML = "<hr><form id='message-form' onsubmit='return false;' action='#' autocomplete='off' class='form-horizontal' style= margin: 10px auto;'>\
						<div class='input-group' style='min-width: 100%;'>\
							<textarea name='message' id='message-box-"+conversationId+"-"+otherUserId+"' placeholder='Type message...' class ='form-control' style='min-width: 100%'></textarea>\
								<button id = 'btn-"+conversationId+"-"+otherUserId+"' class='btn btn-block'  type= 'button'>\
								Send\
								</button>\
						</div>\
					</form>"
		textBox.appendChild(messageBox);
	});
});

$(document).on('click', '#message-form button', function(){
	var id = $(this).attr("id");
	id = id.split("-");
	var conversationId = id[1];
	var otherUserId = id[2];
	
	//var textInput = document.getElementById("message-box-"+conversationId+"-"+otherUserId);
	var content = $("#message-box-"+conversationId+"-"+otherUserId).val();
	$("#message-box-"+conversationId+"-"+otherUserId).val("");
	var messageLocation = document.getElementById("message-location");

	var mainDiv = document.createElement("DIV");
	var container = document.createElement("P");
	container.setAttribute("style","text-align:right;");
	container.appendChild(document.createTextNode(content));
	messageLocation.insertBefore(container, messageLocation.childNodes[messageLocation.childNodes.length-1]);
	
	var data = {conversation_id: conversationId, user_from: userId, user_to: otherUserId, message: content};
	console.log(data);
	// Send it so other person can see it
	$.post('messages.php', data, function(jsonData){
		console.log("Message sent");
		console.log(jsonData);
	});
	
});

//polling for messages
setInterval(function(){
	console.log("this is working");
	var container = document.getElementById("text-box");
	var details = container.childNodes[1].childNodes[1].childNodes[1].childNodes[1];
	console.log(details);
	details = details.getAttribute("id");
	details = details.split("-");
	var conversationId = details[2];
	var otherUserId = details[3];
	console.log(conversationId);
	console.log(otherUserId);
	var data = {user_id: userId, conversation_id: conversationId, other_user_id:otherUserId};
	$.getJSON('messages.php', data, function(jsonData){
		console.log("got data");
		console.log(jsonData);
		//Extract data
		var messages = jsonData['messages'];
		//Display messages
		var messageLocation = document.getElementById("message-location");
		messageLocation.innerHTML="";
		for (var x = 0; x<messages.length; x++){
			var message = messages[x];
			var content = message['content'];
			var sent; //boolean, true if this user sent the message
			if (message['to'] == otherUserId){
				sent = true;
			}else{
				sent = false;
			}
			var mainDiv = document.createElement("DIV");
			var t = document.createElement("P");
			if (sent){t.setAttribute("style","text-align:right;")};
			t.appendChild(document.createTextNode(content));
			messageLocation.appendChild(t);
		}
		
	});
}, 3000);

	
