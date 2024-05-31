(function($){
	$(document).ready(function(){
		$('#ol-print-this-page').on( 'click', function(e){
			e.preventDefault();
			window.print();
		});
	});
})(jQuery)
