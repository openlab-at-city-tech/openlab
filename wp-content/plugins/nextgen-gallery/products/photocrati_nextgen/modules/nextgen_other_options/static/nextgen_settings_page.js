jQuery(function($){

    $('select.select2').select2();
    $('label.tooltip, span.tooltip').tooltip();

	/**** LIGHTBOX EFFECT TAB ****/
    $('#lightbox_library').on('change', function() {
        var value   = $(this).find(':selected').val();
        var id      = 'lightbox_library_'+value;
        $('.lightbox_library_settings').each(function(){
            if ($(this).attr('id') != id) $(this).fadeOut('fast');
        });
        $('#'+id).fadeIn();
    }).trigger('change');

	/**** WATERMARK TAB ****/

	// Configure the watermark customization link
	$('#watermark_customization').attr('rel', 'watermark_'+$('#watermark_source').val()+'_source');

	// Configure the button to switch from watermark text to image
	$('#watermark_source').on('change', function() {
		$('#'+$('#watermark_customization').attr('rel')).css('display', '').addClass('hidden');
		if (!$('#'+$(this).val()).hasClass('hidden')) {
			$('#'+$(this).val()).removeClass('hidden');
		}
		$('#watermark_customization').attr('rel', 'watermark_'+$('#watermark_source').val()+'_source').trigger('click');
	});

    // Don't show any Watermark fields unless Watermarks are enabled
    $('#watermark_source').on('change', function() {
        var value = $(this).val();

        $('.watermark_field').each(function(){
            if (value == 0) {
                $(this).fadeOut().addClass('hidden');
            }
            else {
                $(this).fadeIn().removeClass('hidden');
            }
        });
    }).trigger('change');


    // sends the current settings to a special ajax endpoint which saves them, regenerates the url, and then reverts
    // to the old settings. this submits the form and forces a refresh of the image through the time parameter
    $('#nextgen_settings_preview_refresh').on('click', function(event) {
        event.preventDefault();

        var form = $(this).parents('form:first');
        var self = $(this);
        var orig_html = $(self).html();

        $(self).attr('disabled', 'disabled').html('Processing...');
        $('body').css('cursor', 'wait');

        $.ajax({
            type: form.attr('method'),
            url: $(this).data('refresh-url'),
            data: form.serialize()+"&action=get_watermark_preview_url",
            dataType: 'json',
            success: function(data) {
                var img = self.prev();
                var src = data.thumbnail_url;
                queryPos = src.indexOf('?');
                if (queryPos != -1) {
                    src = src.substring(0, queryPos);
                }

                img.attr('src', src + '?' + new Date().getTime());
                $(self).prop('disabled', false).html(orig_html);
                $('body').css('cursor', 'default');
            },
            error: function(xob, err, code) {        
                $(self).prop('disabled', false).html(orig_html);
                $('body').css('cursor', 'default');
            }
        });
        
        return false;
    });

});