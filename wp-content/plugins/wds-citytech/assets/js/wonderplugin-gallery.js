(function($){
	$(document).ready(function(){
		$('#wonderplugin-add-folder').remove();

		// Cool plugin
		$('#wonderplugin-add-video').click(function(){
			setTimeout(
				function() {
					$('.wonderplugin-dialog-form td').each(function(){
						var $td = $(this);
						var $button = $td.find('input[type="button"]');
						if ( 0 !== $button.length ) {
							$button.remove();

							var regExp = / or ?$/
							var tdContents = $td.html().replace( regExp, '' )
							$td.html( tdContents );
						}
					});
				},
				50
			);
		});
	});
}(jQuery))
