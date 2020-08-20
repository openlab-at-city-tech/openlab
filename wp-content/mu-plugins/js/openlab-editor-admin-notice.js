(function($){
	$(document).ready(function(){
		$('.openlab-editor-admin-notice .notice-dismiss').click(function(){
			var nonce = $('#openlab-editor-admin-notice-dismiss-nonce').val();
			$.get( ajaxurl + '?action=openlab_editor_admin_notice_dismiss&_wpnonce=' + nonce );
		});
	});
}(jQuery));
