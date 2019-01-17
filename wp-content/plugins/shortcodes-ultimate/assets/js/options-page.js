// Wait DOM
jQuery(document).ready(function($) {
	// ########## About screen ##########
	$('.su-demo-video').magnificPopup({
		type: 'iframe',
		callbacks: {
			open: function() {
				// Change z-index
				$('body').addClass('su-mfp-shown');
			},
			close: function() {
				// Change z-index
				$('body').removeClass('su-mfp-shown');
			}
		}
	});
	// ########## Custom CSS screen ##########
	$('.su-custom-css-originals a').magnificPopup({
		type: 'iframe',
		callbacks: {
			open: function() {
				// Change z-index
				$('body').addClass('su-mfp-shown');
			},
			close: function() {
				// Change z-index
				$('body').removeClass('su-mfp-shown');
			}
		}
	});
	// Enable ACE editor
	if ($('#sunrise-field-custom-css-editor').length > 0) {
		var editor = ace.edit('sunrise-field-custom-css-editor'),
			$textarea = $('#sunrise-field-custom-css').hide();
		editor.getSession().setValue($textarea.val());
		editor.getSession().on('change', function() {
			$textarea.val(editor.getSession().getValue());
		});
		editor.getSession().setMode('ace/mode/css');
		editor.setTheme('ace/theme/monokai');
		editor.getSession().setUseWrapMode(true);
		editor.getSession().setWrapLimitRange(null, null);
		editor.renderer.setShowPrintMargin(null);
		editor.session.setUseSoftTabs(null);
	}
	// ########## Add-ons screen ##########
	var addons_timer = 0;
	$('.su-addons-item').each(function() {
		var $item = $(this),
			delay = 300;
		$item.click(function(e) {
			window.open($(this).data('url'));
			e.preventDefault();
		});
		addons_timer = addons_timer + delay;
		window.setTimeout(function() {
			$item.addClass('animated bounceIn').css('visibility', 'visible');
		}, addons_timer);
	});
	// ########## Examples screen ##########
	// Disable all buttons
	$('#su-examples-preview').on('click', '.su-button', function(e) {
		if ($(this).hasClass('su-example-button-clicked')) alert(su_options_page.not_clickable);
		else $(this).addClass('su-example-button-clicked');
		e.preventDefault();
	});
	var open = $('#su_open_example').val(),
		nonce = $('#su_examples_nonce').val(),
		$example_window = $('#su-examples-window'),
		$example_preview = $('#su-examples-preview');
	$('.su-examples-group-title, .su-examples-item').each(function() {
		var $item = $(this),
			delay = 200;
		if ($item.hasClass('su-examples-item')) {
			$item.on('click', function(e) {
				var id = $(this).data('id');
				$item.magnificPopup({
					type: 'inline',
					alignTop: true,
					callbacks: {
						open: function() {
							// Change z-index
							$('body').addClass('su-mfp-shown');
						},
						close: function() {
							// Change z-index
							$('body').removeClass('su-mfp-shown');
							$example_preview.html('');
						}
					}
				});
				var su_example_preview = $.ajax({
					url: ajaxurl,
					type: 'get',
					dataType: 'html',
					data: {
						action: 'su_example_preview',
						id: id,
						nonce: nonce
					},
					beforeSend: function() {
						if (typeof su_example_preview === 'object') su_example_preview.abort();
						$example_window.addClass('su-ajax');
						$item.magnificPopup('open');
					},
					success: function(data) {
						$example_preview.html(data);
						$example_window.removeClass('su-ajax');
					}
				});
				e.preventDefault();
			});
			// Open preselected example
			if ($item.data('id') === open) $item.trigger('click');
		}
	});
	$('#su-examples-window').on('click', '.su-examples-get-code', function(e) {
		$(this).hide();
		$(this).parent('.su-examples-code').children('textarea').slideDown(300);
		e.preventDefault();
	});
	// ########## Cheatsheet screen ##########
	$('.su-cheatsheet-switch').on('click', function(e) {
		$('body').toggleClass('su-print-cheatsheet');
		e.preventDefault();
	});
});