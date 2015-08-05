function tzn_option_slider(el) {

	this.section = jQuery(el);
	this.button = this.section.find('.tzn_option_toggle');
	this.panel = this.section.find('.tzn_option_panel');
	
	this.toggle = function() {
		var ctx = this;
		this.panel.slideToggle('fast', function() {
			if (ctx.panel.is(':hidden')) {
				ctx.button.show();
			} else {
				ctx.button.hide();
			}
		});
	}
	
	this.display = function() {
		var $s = this.section.find('.tzn_option_select option:selected');
		this.section.find('.tzn_option_display').html($s.text());
		this.toggle();
	}
	
	this.cancel = function() {
		var $s = this.section.find('.tzn_option_select');
		var $o = this.section.find('.tzn_option_old').first();
		$s.val($o.val());
		this.toggle();
	}
	
	this.section.find('.tzn_option_toggle').click({ctx: this}, function(e) { e.data.ctx.toggle(); });
	this.section.find('.tzn_option_cancel').click({ctx: this}, function(e) { e.data.ctx.cancel(); });
	this.section.find('.tzn_option_save').click({ctx: this}, function(e) { e.data.ctx.display(); });
	
}


jQuery(document).ready(function() {
	jQuery('.tzn_option_section').each(function() {
		new tzn_option_slider(this);
	});
});

function tfk_project_user_add(id) {
	// add new row in user's table
	event.preventDefault();
	var uid = jQuery('#tfk_user_id').val();
	var unm = jQuery('#tfk_user_id :selected').text();
	var pos = jQuery('#tfk_position').val();
	var pnm = jQuery('#tfk_position :selected').text();
	// alert('adding '+uid+' = '+unm+' as '+pnm+' ('+pid+') on project '+id);
	jQuery.get(ajaxurl+'?action=tfk_project_user_add', {'id': id, 'uid': uid, 'pos': pos, 'unm': unm, 'pnm': pnm}, function(data) {
		jQuery('#tfk_project_users tbody').append(data);
	});
}

function tfk_project_user_edit(id, uid) {
	// update data in corresponding row
	event.preventDefault();
	var unm = jQuery('#tfk_user_name').text();
	var pos = jQuery('#tfk_position').val();
	var pnm = jQuery('#tfk_position :selected').text();
	jQuery.get(ajaxurl+'?action=tfk_project_user_edit', {'id': id, 'uid': uid, 'pos': pos, 'unm': unm, 'pnm': pnm}, function(data) {
		jQuery('#tfk_project_users tbody #pos-'+uid).empty().append(data);
	});
}

function tfk_project_user_delete(uid,tmp) {
	// remove line from table of users
	jQuery('#pos-'+uid).remove();
	// add uid in list of users to delete
	if (tmp) {
		var elm = jQuery('#tfk_project_users_delete');
		var elv = elm.val();
		if (elv) {
			elv += ',';
		}
		elv += uid;
		elm.val(elv);
	}
}