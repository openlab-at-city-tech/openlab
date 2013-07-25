// version 1.0 - original version
// version 1.1 - Updated with function fixes and for WordPress 3.2 / jQuery 1.6
// version 1.2 - Update to work when DISABLED is specified for changes in version 8.5
jQuery(document).ready(function () {
	// function to check or uncheck all when 'checkall' box it toggled
	jQuery('input[name="checkall"]').click(function () {
		var checked_status = this.checked;
		jQuery('input[class="' + this.value + '"]').each(function () {
			if (jQuery().jquery >= '1.6') {
				if (jQuery(this).prop('disabled') === false) {
					this.checked = checked_status;
				}
			} else {
				if (jQuery(this).attr('disabled') === false) {
					this.checked = checked_status;
				}
			}
		});
	});
	// function to check or uncheck 'checkall' box when individual boxes are toggled
	jQuery('input[class^="checkall"]').click(function () {
		var checked_status = true;
		jQuery('input[class="' + this.className + '"]').each(function () {
			if ((this.checked === true) && (checked_status === true)) {
				checked_status = true;
			} else {
				checked_status = false;
			}
			// jQuery 1.6.1 introduced in WordPress 3.2
			// following can be simplified when WordPress 3.2 is minimum requirement
			if (jQuery().jquery >= '1.6') {
				jQuery('input[value="' + this.className + '"]').prop('checked', checked_status);
			} else {
				jQuery('input[value="' + this.className + '"]').attr('checked', checked_status);
			}
		});
	});
	// function to check or uncheck 'checkall' box when page is loaded
	var checked_status = true;
	jQuery('input[class^="checkall"]').each(function () {
		if ((this.checked === true) && (checked_status === true)) {
			checked_status = true;
		} else {
			checked_status = false;
		}
		// jQuery 1.6.1 introduced in WordPress 3.2
		// following can be simplified when WordPress 3.2 is minimum requirement
		if (jQuery().jquery >= '1.6') {
			jQuery('input[value="' + this.className + '"]').prop('checked', checked_status);
		} else {
			jQuery('input[value="' + this.className + '"]').attr('checked', checked_status);
		}
	});
});