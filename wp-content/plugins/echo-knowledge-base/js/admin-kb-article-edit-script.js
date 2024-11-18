jQuery(document).ready(function($) {

	/** EPKB Post Visibility Text */
	$(document).on('click','.edit-post-post-visibility__toggle',function(){
		// INITIAL LOAD: Delay the code so that to let GT load it's HTML so that we can target it.
		setTimeout(function(){
			$('.editor-post-visibility__dialog-fieldset').append('<span class="epkb-visibility-info-text"><i class="epkbfa epkbfa-exclamation-circle"></i>Access Manager Protected <a href="https://www.echoknowledgebase.com/documentation/?top-category=access-manager" target="_blank"><i class="epkbfa epkbfa-external-link"></i></a></span>');
		}, 100);

	});

	//For Classic Editor
	$(document).on('click','.edit-visibility',function(){
		$('#post-visibility-select').append('<span class="epkb-visibility-info-text"><i class="epkbfa epkbfa-exclamation-circle"></i>Access Manager Protected <a href="https://www.echoknowledgebase.com/documentation/?top-category=access-manager" target="_blank"><i class="epkbfa epkbfa-external-link"></i></a></span>');
	});
	$(document).on('click','.cancel-post-visibility',function(){
		$('#post-visibility-select .epkb-visibility-info-text').remove();
	});
	/** EPKB Post Visibility Text END*/

});