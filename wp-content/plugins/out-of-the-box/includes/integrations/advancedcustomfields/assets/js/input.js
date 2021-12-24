(function ($) {

	var data = {};
	var $input = null;
	var $table;

	function initialize_field($field) {
		$input = $field.find('input[data-name="id"]');
		$table = $field.find('.wpcp-acf-items-table');
		read_data();

		_init_buttons($field);
	}

	function read_data() {
		try {
			data = JSON.parse($input.val());
		} catch (e) {
			data = {}
		}
		render_entries()
	}

	function save_data() {
		$input.val(JSON.stringify(data));
		render_entries()
	}

	function _init_buttons($field) {

		$($field).on('click', '.wpcp-acf-add-item', function () {
			window.addEventListener("message", callback_handler)
			tb_show("Select files", '#TB_inline?&inlineId=oftb-embedded&height=450&width=800');

			// Update Z-index to force the popup over the Builder
			$('#TB_window, #TB_overlay').css('z-index', 99999999);
		})

		$($field).on('click', '.wpcp-acf-remove-item', function () {
			var row = $(this).parents('tr');
			delete data[row.data('entry-id')];
			save_data();
		})

	}

	function render_entries() {

		var $tbody = $table.find('tbody');
		$tbody.empty();

		if (Object.entries(data).length === 0) {
			$tbody.append('<tr><td></td><td>No files added</td><td></td><td></td></tr>');
			return;
		}

		for (const [key, entry] of Object.entries(data)) {
			$tbody.append('<tr data-entry-id="' + key + '" data-account-id="' + entry.account_id + '"><td>' + ((entry.icon_url) ? '<img src="' + entry.icon_url + '" style="height:18px; width:18px;"/>' : '') + '</td><td>' + entry.name + ((entry.size) ? ' (' + entry.size + ')' : '') + '</td><td style="max-width:300px;overflow:hidden;white-space:nowrap;text-overflow: ellipsis;">' + entry.entry_id + '</td><td>' + ((entry.direct_url) ? '<a href="' + entry.direct_url + '" target="_blank" class="button button-secondary button-small">View</a>&nbsp;' : '') + ((entry.download_url) ?
				'<a href="' + entry.download_url + '" target="_blank" class="button button-secondary button-small">Download</a>&nbsp;' : '') + '<a href="#" class="wpcp-acf-remove-item button button-secondary button-small">&#10006;</a></td></tr>');
		}
	}

	function callback_handler(event) {

		if (event.origin !== window.location.origin) {
			return;
		}

		if (typeof event.data !== 'object' || event.data === null || typeof event.data.action === 'undefined') {
			return;
		}

		if (event.data.action !== 'wpcp-select-entry') {
			return;
		}

		if (event.data.slug !== 'outofthebox') {
			return;
		}

		data[event.data.entry_id] = {
			account_id: event.data.account_id,
			entry_id: event.data.entry_id,
			name: event.data.entry_name,
			size: '',
			direct_url: '',
			download_url: '',
			shortlived_download_url: '',
			shared_url: '',
			embed_url: '',
			thumbnail_url: '',
			icon_url: '',
		}

		window.removeEventListener("message", callback_handler)

		save_data();
	}

	if (typeof acf.add_action !== 'undefined') {

		acf.add_action('ready_field/type=OutoftheBox_Field', initialize_field);
		acf.add_action('append_field/type=OutoftheBox_Field', initialize_field);


	} else {

		$(document).on('acf/setup_fields', function (e, postbox) {

			// find all relevant fields
			$(postbox).find('.field[data-field_type="OutoftheBox_Field"]').each(function () {

				// initialize
				initialize_field($(this));

			});

		});

	}

})(jQuery);