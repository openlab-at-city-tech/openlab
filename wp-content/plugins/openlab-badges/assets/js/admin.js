(function($){
	$(document).ready(function(){
		$('a.badge-delete').click(function(e){
			return window.confirm( OpenLabBadgesAdmin.deleteConfirm );
		});
	});
}(jQuery));
