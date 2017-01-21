$(document).ready(function(){
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
	$("form").submit(function(){
		$data = $(this).serializeArray();
		$jsonData = {};
		$numberOfSections = -1; //Will ignore general comments section
		$.each($data, function(){
			$jsonData[this.name] = this.value;
			$numberOfSections += 1;
		});
		$jsonData["documentID"] = $("form").attr("id");
		//DON'T KNOW HOW TO GET THE STORE TYPE FROM THE FORM YET
		$jsonData["storeType"] = "save";
		$jsonData["numberOfSections"] = $numberOfSections/2;
		console.log($jsonData);

 		$.post("index.php", $jsonData, function(response){
			if (response.hasOwnProperty("error")){
				//alert with response["error"];
				console.log("Error");
			}else{
				//alert with response["success"];
				console.log("Worked");
				console.log(response);
				response = $.parseJSON(response);
				console.log(response["sections"]);
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
			$("input[name='submit']").removeAttr("disabled");
		}else{
			$("input[name='submit']").attr("disabled","disabled");
		}
	}
	
});