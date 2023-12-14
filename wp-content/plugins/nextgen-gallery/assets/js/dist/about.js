(function ($, nextgen_about) {
	$(function () {
		$('.nextgen-am-plugins-wrap').on(
			'click',
			'.nextgen-am-plugins-install',
			function (e) {
				e.preventDefault();
				var $this = $(this),
					url = $this.data('url'),
					basename = $this.data('basename');
					spinner = $this.parent().find('.spinner'),
					message = $(this)
					.parent()
					.parent()
					.find('.nextgen-am-plugins-status');
				var install_opts = {
					url: nextgen_about.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'nextgen_install_am_plugin',
						nonce: nextgen_about.install_nonce,
						basename: basename,
						download_url: url,
					},
					success: function (response) {
						$this.text(nextgen_about.deactivate)
							.removeClass('nextgen-am-plugins-install')
							.addClass('nextgen-am-plugins-deactivate');

							$(message).text(nextgen_about.active);

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
		$('.nextgen-am-plugins-wrap').on(
			'click',
			'.nextgen-am-plugins-activate',
			function (e) {
				e.preventDefault();
				var $this = $(this),
					url = $this.data('url'),
				 	basename = $this.data('basename'),
					spinner = $this.parent().find('.spinner'),
					message = $(this)
					.parent()
					.parent()
					.find('.nextgen-am-plugins-status');
				var activate_opts = {
					url: nextgen_about.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'nextgen_activate_am_plugin',
						nonce: nextgen_about.activate_nonce,
						basename: basename,
						download_url: url,
					},
					success: function (response) {
						$this.text(nextgen_about.deactivate)
							.removeClass('nextgen-am-plugins-activate')
							.addClass('nextgen-am-plugins-deactivate');

						$(message).text(nextgen_about.active);
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
		$('.nextgen-am-plugins-wrap').on(
			'click',
			'.nextgen-am-plugins-deactivate',
			function (e) {
				e.preventDefault();
				var $this = $(this),
					url = $this.data('url'),
					basename = $this.data('basename'),
					spinner = $this.parent().find('.spinner'),
					message = $(this)
					.parent()
					.parent()
					.find('.nextgen-am-plugins-status');
				var deactivate_opts = {
					url: nextgen_about.ajax,
					type: 'post',
					async: true,
					cache: false,
					dataType: 'json',
					data: {
						action: 'nextgen_deactivate_am_plugin',
						nonce: nextgen_about.deactivate_nonce,
						basename: basename,
						download_url: url,
					},
					success: function (response) {
						$this.text(nextgen_about.activate)
							.removeClass('nextgen-am-plugins-deactivate')
							.addClass('nextgen-am-plugins-activate');

						$(message).text(nextgen_about.inactive);
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
})(jQuery, nextgen_about);
