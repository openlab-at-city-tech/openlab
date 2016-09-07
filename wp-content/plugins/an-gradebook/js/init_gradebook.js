(function($){
		$(document).ready(function(){
		var _x = document.querySelector('a.toplevel_page_an_gradebook');	
		_x.setAttribute('href',_x.getAttribute('href') + '#courses');
		var _x = document.querySelector('[href="admin.php?page=an_gradebook"]:not(.toplevel_page_an_gradebook)');
		_x.setAttribute('href',_x.getAttribute('href') + '#courses');
		var _x = document.querySelector('[href$="an_gradebook_settings"]');
		_x.setAttribute('href',_x.getAttribute('href') + '#settings');	
	});
})(jQuery);