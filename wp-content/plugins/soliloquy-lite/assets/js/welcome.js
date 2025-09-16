(function ($) {
	$(function () {
		$('.soliloquy-am-plugins-wrap').on(
			'click',
			'.soliloquy-am-plugins-install',
			function (e) {
				e.preventDefault();
				var $this = $(this),
					url = $this.data('url'),
					basename = $this.data('basename');
					spinner = $this.parent().find('.spinner'),
					message = $(this)
					.parent()
					.parent()
					.find('.soliloquy-am-plugins-status');
				var install_opts = {
					url: soliloquy_welcome.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'soliloquy_install_partner',
						nonce: soliloquy_welcome.install_nonce,
						basename: basename,
						download_url: url,
					},
					success: function (response) {
						$this.text(soliloquy_welcome.deactivate)
							.removeClass('soliloquy-am-plugins-install')
							.addClass('soliloquy-am-plugins-deactivate');

							$(message).text(soliloquy_welcome.active);

						// Trick here to wrap a span around he last word of the status
						var heading = $(message),
							word_array,
							last_word,
							first_part;

						word_array = heading.html().split(/\s+/); // split on spaces
						last_word = word_array.pop(); // pop the last word
						first_part = word_array.join(' '); // rejoin the first words together
						spinner.css('visibility', 'hidden');

						heading.html(
							[
								first_part,
								' <span>',
								last_word,
								'</span>',
							].join(''),
						);
						// Proc
					},
					error: function (xhr, textStatus, e) {
						console.log(e);
					},
				};
				console.log(spinner);
				spinner.css('visibility', 'visible');
				$.ajax(install_opts);
			},
		);
		$('.soliloquy-am-plugins-wrap').on(
			'click',
			'.soliloquy-am-plugins-activate',
			function (e) {
				e.preventDefault();
				var $this = $(this),
					url = $this.data('url'),
				 	basename = $this.data('basename'),
					spinner = $this.parent().find('.spinner'),
					message = $(this)
					.parent()
					.parent()
					.find('.soliloquy-am-plugins-status');
				var activate_opts = {
					url: soliloquy_welcome.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'soliloquy_activate_partner',
						nonce: soliloquy_welcome.activate_nonce,
						basename: basename,
						download_url: url,
					},
					success: function (response) {
						$this.text(soliloquy_welcome.deactivate)
							.removeClass('soliloquy-am-plugins-activate')
							.addClass('soliloquy-am-plugins-deactivate');

						$(message).text(soliloquy_welcome.active);
						// Trick here to wrap a span around he last word of the status
						var heading = $(message),
							word_array,
							last_word,
							first_part;

						word_array = heading.html().split(/\s+/); // split on spaces
						last_word = word_array.pop(); // pop the last word
						first_part = word_array.join(' '); // rejoin the first words together
						spinner.css('visibility', 'hidden');
						heading.html(
							[
								first_part,
								' <span>',
								last_word,
								'</span>',
							].join(''),
						);
						location.reload(true);
					},
					error: function (xhr, textStatus, e) {
						console.log(e);
					},
				};
				console.log(spinner);
				spinner.css('visibility', 'visible');
				$.ajax(activate_opts);
			},
		);
		$('.soliloquy-am-plugins-wrap').on(
			'click',
			'.soliloquy-am-plugins-deactivate',
			function (e) {
				e.preventDefault();
				var $this = $(this),
					url = $this.data('url'),
					basename = $this.data('basename'),
					spinner = $this.parent().find('.spinner'),
					message = $(this)
					.parent()
					.parent()
					.find('.soliloquy-am-plugins-status');
				var deactivate_opts = {
					url: soliloquy_welcome.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'soliloquy_deactivate_partner',
						nonce: soliloquy_welcome.deactivate_nonce,
						basename: basename,
						download_url: url,
					},
					success: function (response) {
						$this.text(soliloquy_welcome.activate)
							.removeClass('soliloquy-am-plugins-deactivate')
							.addClass('soliloquy-am-plugins-activate');

						$(message).text(soliloquy_welcome.inactive);
						// Trick here to wrap a span around he last word of the status
						var heading = $(message),
							word_array,
							last_word,
							first_part;

						word_array = heading.html().split(/\s+/); // split on spaces
						last_word = word_array.pop(); // pop the last word
						first_part = word_array.join(' '); // rejoin the first words together
						spinner.css('visibility', 'hidden');
						heading.html(
							[
								first_part,
								' <span>',
								last_word,
								'</span>',
							].join(''),
						);
						location.reload(true);
					},
					error: function (xhr, textStatus, e) {
						console.log(e);
					},
				};
				console.log(spinner);
				spinner.css('visibility', 'visible');
				$.ajax(deactivate_opts);
			},
		);
	});
})(jQuery);
