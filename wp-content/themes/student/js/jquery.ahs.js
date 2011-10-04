jQuery(document).ready(function($) {

	var themedir = $('#themedir').val();
	
	$('#contactform').submit(function(){
		$.get(themedir+'/inc/ajax_email.php?email='+$('#contactform #email').val()+'&name='+$('#contactform #name').val()+'&comment='+$('#contactform #comment').val(), function(data) {
			if (data!='ERROR') $('#write_to_me').html(data);
			else $('#error').html('<p>Please fill out all of the fields.</p>');
		});
	});

});