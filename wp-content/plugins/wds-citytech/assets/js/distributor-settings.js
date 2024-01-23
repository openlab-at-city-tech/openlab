(function($){
	$(document).ready(function(){
		const $licenseWrap = $('.license-wrap');
		if ( ! $licenseWrap.length ) {
			return;
		}

		$licenseWrap.closest( 'tr' ).remove();
	})
})(jQuery);
