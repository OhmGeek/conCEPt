$("submit").click(sendData("submit"));
$("save").click(sendData("save"));

function sendData(type){
	var n = $('tr').length - 1; //Number of criteria
	
	var params = '{ "type": ' + type + ', "name": '+documentName;
	
	//Get information from input and textareas using id's
	for (var i = 0; i < n; i++){
		var sectionName = "section"+toString(i+1); 
		
		// get the mark and rationale for the current section
		var m = $("#mark"+toString(i+1)).val; // mark
		var r = $("#rationale"+toString(i+1)).val; //rationale
		
		if (mark !== "" || rationale !== ""){
			// Add non-empty sections to the params
			var section = {mark: m, rationale: m}}
			params += ', '+sectionName+': '+section; 
		}
	}
	
	params += '}'
	
	// Turn params into a JSON object
	params = JSON.parse(text);
	
	
	// ParamsExample = {type: "save", name: "examiner-design", section1: {mark: 57, rationale: textHere},
	//					section4: {mark: 73, rationale: textHere}}
	$.post("sendToDatabase.php", params, function(jsonData){
		// Process respone here (check for errors)
	});
}