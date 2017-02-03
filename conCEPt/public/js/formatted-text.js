// go through each form
//
// for each form, go through the editable divs
// and make a new hidden field
//

$(form).each(function(index, elem) {
	$('div').each(function(index, elem) {
		$("form").append($("<textarea type = 'hidden'>").attr({name:$(this).attr('id'),value:$(this).attr('html')}));
	});
});
