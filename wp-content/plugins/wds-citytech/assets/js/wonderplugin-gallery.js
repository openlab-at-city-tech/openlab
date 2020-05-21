(function($){
	$(document).ready(function(){
		$('#wonderplugin-add-folder').remove();

		// Cool plugin
		$('#wonderplugin-add-video').click(function(){
			var buttonsToRemove = [
				'wonderplugin-dialog-select-mp4',
				'wonderplugin-dialog-select-hdmp4',
				'wonderplugin-dialog-select-webm',
				'wonderplugin-dialog-select-hdwebm',
			];

			setTimeout(
				function() {
					$('.wonderplugin-dialog-form td').each(function(){
						var $td = $(this);
						var $button = $td.find('input[type="button"]');
						if ( 0 !== $button.length ) {
							console.log($button.attr('id'));
							if ( -1 !== buttonsToRemove.indexOf( $button.attr( 'id' ) ) ) {
								$button.remove();

								var regExp = / or ?$/
								var tdContents = $td.html().replace( regExp, '' )
								$td.html( tdContents );
							}
						}
					});
				},
				50
			);
		});
	});
}(jQuery))
