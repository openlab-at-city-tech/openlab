(function($){
	$(document).ready(function(){
		$('#ol-print-this-page').click(function(e){
			e.preventDefault();
			window.print();
		});
	});
})(jQuery)
