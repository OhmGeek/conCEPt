$(document).ready(
	$("input").on("keyup", allowSubmit());
	$("textarea").on("keyup"), allowSubmit());
	
	//If all inputs and textareas are filled, enable submit button
	function allowSubmit(){
		//Boolean value - true if no input fields are empty
		var inputsFilled = $("input").filter(function(){
			return $.trim($(this).val()).length == 0;
		}).length==0;
		
		//Boolean value - true if no textarea fields are empty
		var textareasFilled = $("textarea").each(function(){
			return $.trim($(this).val()).length == 0;
		}).length==0;
		
		//If inputsFilled and textareasFilled,
		//remove disabled attribute from Submit input
		if (inputsFilled && textareasFilled){
			$("input[name='submit']").removeAttr("disabled");
		}
	}
	
);