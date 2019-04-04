;'use strict';

if (!String.prototype.trim) {(function() {
	var r = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;
	String.prototype.trim = function() {
		return this.replace(r, '');
	};
})();};

if (!String.prototype.esc_html) {(function() {
	var r = /[&<>"'\/]/g;
	var entity_map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;'};
	String.prototype.esc_html = function() {
		return this.replace(r, function from_entity_map(s) {
			return entity_map[s];
		});
	};
})();};

if (!String.prototype.capitalize) {
	String.prototype.capitalize = function() {
		return this.charAt(0).toUpperCase() + this.slice(1);
	}
};

jQuery(document).ready(function($) {



	$('#wplnst-tabs-nav').find('a.nav-tab').click(function() {
		$('#wplnst-tabs-nav').find('a').removeClass('nav-tab-active');
		$('.wplnst-tab').removeClass('wplnst-tab-active');
		var id = $(this).attr('id').replace('-tab', '');
		$('#' +  id).addClass('wplnst-tab-active');
		$(this).addClass('nav-tab-active');
		var action = $('#wplnst-form').attr('action').split('#top#');
		$('#wplnst-form').attr('action', action[0] + '#top#' + id);
	});

	if ($('.wplnst-tab-default').length) {
		var active_tab = window.location.hash.replace('#top', '').replace('#wplnst-', '');
		if ('' === active_tab || active_tab === '#_=_') {
			active_tab = $('.wplnst-tab-default').attr('id').replace('wplnst-', '');
		}
		$('#wplnst-' + active_tab).addClass('wplnst-tab-active');
		$('#wplnst-' + active_tab + '-tab').addClass('nav-tab-active');
		$('#wplnst-' + active_tab + '-tab').click();
		if ('general' == active_tab && 0 == $('#wplnst-scan-id').val()) {
			$('#tx-name').focus();
		}
	}

	$('.wplnst-scan-delete').click(function() {
		if (confirm($('#wplnst-scans').attr('data-confirm-delete'))) {
			$(this).attr('href', $(this).attr('href') + '&confirm=on');
			return true;
		}
		return false;
	});

	$('.wplnst-scan-delete-isolated').click(function() {
		if (confirm($(this).attr('data-confirm-delete'))) {
			$(this).attr('href', $(this).attr('href') + '&confirm=on');
			return true;
		}
		return false;
	});



	$('.wplnst-remove-entry').click(function() {
		return confirm($('#wplnst-results').attr('data-confirm-delete-entry'));
	});

	$('.wplnst-remove-comment').click(function() {
		return confirm($('#wplnst-results').attr('data-confirm-delete-comment'));
	});

	$('.wplnst-remove-bookmark').click(function() {
		return confirm($('#wplnst-results').attr('data-confirm-delete-bookmark'));
	});




	$('.wplnst-scans-bulkactions-top .button.action').click(function() {
		scans_bulkactions($('#bulk-action-selector-top').val());
		return false;
	});

	$('.wplnst-scans-bulkactions-bottom .button.action').click(function() {
		scans_bulkactions($('#bulk-action-selector-bottom').val());
		return false;
	});

	function scans_bulkactions(action) {

		if ('delete' == action) {

			var checked = [];
			$('input[type=checkbox].wplnst-ck-scan-id').each(function() {
				if ($(this).is(':checked')) {
					checked.push($(this).val());
				}
			});

			if (checked.length > 0) {
				if (confirm($('#wplnst-scans').attr('data-confirm-delete-bulk'))) {
					window.location.href = $('#wplnst-scans').attr('data-href-delete').replace('%scan_id%', '-' + checked.join('-')) + '&confirm=on';
				}
			}
		}
	}



	$('#wplnst-filter-button').click(function() {
		var args = '', value;
		var fields = $(this).attr('data-fields').split(',');
		for (var i in fields) {
			value = $('#wplnst-filter-' + fields[i]).val();
			if (typeof value != 'undefined' && '' !== value) {
				args += '&' + fields[i] + '=' + value;
			}
		}
		window.location.href = $(this).attr('data-href') + args;
		return false;
	});



	$('#current-page-selector').keydown(function(e) {
		if (e.keyCode == 13) {
			var paged = parseInt($(this).val(), 10);
			var pages = parseInt($('.total-pages').first().text(), 10);
			window.location.href = $(this).closest('form').attr('action') + ((paged > 1)? '&paged=' + ((paged > pages)? pages : paged) : '');
			return false;
		}
	});



});