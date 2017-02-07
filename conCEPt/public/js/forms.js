$(document).ready(function(){
	allowSubmit();
	$("input").keyup(function(){allowSubmit()});
	$("textarea").keyup(function(){allowSubmit()});

	
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
		
		$('div').each(function() {
			$("form").append($("<textarea type = 'hidden'>").attr({name:$(this).attr('id'),value:$(this).html()}));
		});
	});

	$("form").submit(function(){
		var sendData = true; //if this is false, don't send data
		event.preventDefault();
		console.log("STOPPED SENDING");
		var data = $(this).serializeArray();
		console.log(data);
		var jsonData = {};
		var numberOfSections = 0;
		$.each(data, function(){
			numberOfSections += 1;
			var name = this.name;
			var name = this.name.split("-");
			var type = name[0];
			if (type == "mark"){
				var valid = checkMark(this.value);
				if (!valid){
					sendData = false;
					return;
				}else{
					console.log("else statement");
					jsonData[this.name]=this.value;
				}
			}
			jsonData[this.name]=this.value;
		});
		
		if (!sendData){
			displayError("Invalid mark input");
			return;
		}
		
		jsonData["documentID"] = $("form").attr("id");
		jsonData["numberOfSections"] = Math.ceil((numberOfSections+1)/2)
		
		console.log(jsonData);
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
		var alertDiv = $("#alerts");
		alertDiv.removeClass("alert alert-success alert-dismissable");
		alertDiv.attr("class", "alert alert-danger alert-dismissable");
		alertDiv.html("<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Error: </strong>"+ e);
		return;
	}
	
	function displaySuccess(s)
	{
		var alertDiv = $("#alerts");
		alertDiv.removeClass("alert alert-success alert-dismissable");
		alertDiv.attr("class", "alert alert-success alert-dismissable");
		alertDiv.html("<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><strong>Success: </strong>"+ s);
		return;
	}
	
});
