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
	});

	$("form").submit(function(){
		event.preventDefault();
		console.log("STOPPED SENDING");
		var data = $(this).serializeArray();
		console.log(data);
		var jsonData = {};
		var numberOfSections = 0;
		$.each(data, function(){
			console.log(this);
			numberOfSections += 1;
			console.log(this.name);
			console.log(this.value);
			jsonData[this.name]=this.value;
		});
		//jsonData["sections"] = sections;

		jsonData["documentID"] = $("form").attr("id");
		//DON'T KNOW HOW TO GET THE STORE TYPE FROM THE FORM YET
		jsonData["numberOfSections"] = Math.floor((numberOfSections+1)/2)
		console.log(jsonData);
	
 		$.post("index.php?route=send", jsonData, function(response){
			console.log(response);
			if (response.hasOwnProperty("error")){
				//alert with response["error"];
				console.log(response["error"]);
			}else{
				//alert with response["success"];
				console.log(response["success"]);
				console.log(response.hasOwnProperty("success"));
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
		
	

		//If inputsFilled and textareasFilled,
		//remove disabled attribute from Submit input
		if (inputsFilled && textareasFilled){
			console.log("Allowing submit");
			$("input[name='action'][value='Submit']").removeAttr("disabled");
		}else{
			$("input[name='action'][value='Save']").attr("disabled","disabled");
		}
	}
	
});