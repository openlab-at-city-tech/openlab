jQuery( document ).ready(function() {
	jQuery('#mtdml_download').on('click', function(){
		//jQuery('#mtdml_form').fadeOut();
		jQuery('#mtdml_form .loading').fadeIn('fast');
		ShowDownloadMessage();
	});
	function ShowDownloadMessage()
	{
		 jQuery('#mtdml_form .loading').fadeOut();
		 window.addEventListener('focus', HideDownloadMessage, false);
	}

	function HideDownloadMessage(){
		window.removeEventListener('focus', HideDownloadMessage, false);
		//jQuery('#mtdml_form').fadeIn();
	}
});