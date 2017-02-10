$(document).ready(function(){
	allowSubmit();
	$("input").keyup(function(){allowSubmit()});
	$("textarea").keyup(function(){allowSubmit()});

	// make everything not editable
	$(".no-edit").attr('contenteditable','false');
	$(".no-edit").children().each(function() {
		attr('contenteditable','false');	
	});

	//Deal with expanding textareas in table
	$('textarea').each(function () {
		  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
		}).on('keyup', function () {
		  this.style.height = 'auto';
		  this.style.height = (this.scrollHeight) + 'px';
		});
		
	//Ajax post request made when form is submitted
	$("form input").click(function(){
		$("form").append($("<input type = 'hidden'>").attr({name:$(this).attr('name'),value:$(this).attr('value')}));
		
	//	$('div').each(function() {
	//		$("form").append($("<textarea type = 'hidden'>").attr({name:$(this).attr('id'),value:$(this).html()}));
	//	});
	});

	$("form").submit(function(){
		var sendData = true; //if this is false, don't send data
		event.preventDefault();
		console.log("Sending stopped to run a script");
		var data = $(this).serializeArray(); 
		console.log("Array serialised");
		console.log(data);

		var jsonData = {};
		var numberOfSections = 0;
		console.log("Go through each section");
		$.each(data, function(){
			console.log("Name: "+this.name);
			numberOfSections += 1;
			console.log("section " + numberOfSections);
			var name = this.name;
			var name = this.name.split("-");
			var type = name[0];
			if (type == "mark"){
				var valid = checkMark(this.value);
				if (!valid){
					sendData = false;
					return;
				}else{
					console.log("Now add the rationale");
					jsonData[this.name]=this.value;  //Sets mark-n value
					var rationaleName = "rationale-" + name[1]; //Gets rationale name
					//also add the rationale
					console.log(rationaleName);
					var rationale = $('#' + rationaleName).html(); //Get rationale div
					// we need to go through the rationale, to make everything
					// non-editable. go through each p
					// get all from the html (this is god awful)
					$(rationale).filter(function(index) {
						return true;
					}).each(function(index, elem) {
						$(elem).removeAttr('contenteditable',false);	
					});
					// now log and save rationale
					console.log(rationale);
					jsonData[rationaleName] = rationale;

				}
			}
			
			jsonData[this.name]=this.value; //Reset the mark value (or input submission comment)
		});
			
		
		if (!sendData){
			displayError("Invalid mark input");
			return;
		}
		// now add the general comments	
		jsonData["documentID"] = $("form").attr("id");
		console.log(numberOfSections);
		jsonData["numberOfSections"] = (numberOfSections) + 1 //number of mark/rationale sections + the general comments section
		
		
		jsonData["comments"] = $('.comments').children().html();
		console.log("Comments: " + jsonData["comments"]);
		console.log(jsonData);
		console.log(jsonData["numberOfSections"]);
 		$.post("forms.php?route=send", jsonData, function(response){
			console.log("Retrieved");
			response = $.trim(response);
			var response = $.parseJSON(response);
			if (response.hasOwnProperty("error")){
				displayError(response["error"]);
				console.log(response["error"]);
			}else{
				console.log("Success");
				displaySuccess(response["success"]);
				location.reload();
			}
		});
	});
	
	//If all inputs and textareas are filled, enable submit button
	function allowSubmit(){
		//Boolean value - true if no input fields are empty
		var inputsFilled = $("input").filter(function(){
			return $.trim($(this).val()).length == 0;
		}).length==0;
		

		//Boolean value - true if no textarea fields are empty
		var textareasFilled = $("textarea").filter(function(){
			return $.trim($(this).val()).length == 0;
		}).length==0;
		
	
		//todo same again for DIVS
		
		var divsFilled = $(".diveditable").filter(function() {
			return $.trim($(this).html()).length == 0;
		}).length==0;
		//If inputsFilled and textareasFilled,
		//remove disabled attribute from Submit input
		if (inputsFilled && textareasFilled && divsFilled){
			console.log("Allowing submit");
			$("input[name='action'][value='Submit']").removeAttr("disabled");
		}else{
			$("input[name='action'][value='Submit']").attr("disabled","disabled");
		}
	}
	
	function checkMark(mark){
		return (parseInt(mark) >= 0 && parseInt(mark) <= 100);
	}
	
	function displayError(e)
	{
		console.log(e);
		var alertDiv = $("#alerts");
		alertDiv.removeClass("alert alert-success alert-dismissable");
		alertDiv.attr("class", "alert alert-danger alert-dismissable");
		alertDiv.html("<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error: </strong>"+ e);
		return;
	}
	
	function displaySuccess(s)
	{
		console.log(s);
		var alertDiv = $("#alerts");
		alertDiv.removeClass("alert alert-success alert-dismissable");
		alertDiv.attr("class", "alert alert-success alert-dismissable");
		alertDiv.html("<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success: </strong>"+ s);
		return;
	}
	
});
