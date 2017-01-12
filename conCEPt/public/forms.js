$(document).ready(function(){
	$("input").keyup(function(){allowSubmit()});
	$("textarea").keyup(function(){allowSubmit()});

	
	//Deal with expanding textareas in table
	$('textarea').each(function () {
		  this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
		}).on('keyup', function () {
		  this.style.height = 'auto';
		  this.style.height = (this.scrollHeight) + 'px';
		  console.log(this.style.height);
		});
		
	//Ajax post request made when form is submitted
	$("form").submit(function(){
		$data = $(this).serializeArray();
		$jsonData = {};
		$.each($data, function(){
			$jsonData[this.name] = this.value
		});
		
/* 		$.post("index.php", jsonData, function(response){
			if response.hasOwnProperty("error"){
				//alert with response["error"];
			}else{
				//alert with response["success"];
			}
		} */
	});
	
	//If all inputs and textareas are filled, enable submit button
	function allowSubmit(){
		//Boolean value - true if no input fields are empty
		var inputsFilled = $("input").filter(function(){
			return $.trim($(this).val()).length == 0;
		}).length==0;
		
		console.log(inputsFilled);
		
		//Boolean value - true if no textarea fields are empty
		var textareasFilled = $("textarea").filter(function(){
			return $.trim($(this).val()).length == 0;
		}).length==0;
		
		console.log(textareasFilled);
		
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