(function($){
	$(document).ready(function(){
		$('input[name="ofsearch"]').wrap('<label style="display:inline"></label>').before('<span class="sr-only">Search Terms</span>');
		$('select[name="ofcategory"]').wrap('<label style="display:inline"></label>').before('<span class="sr-only">Select Category</span>').find('option').css('color', '#000');
		$('select[name="ofpost_tag"]').wrap('<label style="display:inline"></label>').before('<span class="sr-only">Select Tag</span>');
	});
}(jQuery))
