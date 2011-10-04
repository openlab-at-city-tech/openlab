var U_BP_Forum_Attachment = function(meta_id){
	var $ = jQuery;
	var meta_id = meta_id || '';
	var vars = ubpfattach_vars;
	var plugin_id = vars.plugin_id;
	var plugin = $('#'+plugin_id);
	var upload_form = $('#'+plugin_id+'-form');
	var choose_button = $('#'+plugin_id+'-button');
	var progress_bar = $('#'+plugin_id+'-process');
	var message_box = $('#'+plugin_id+'-message');
	var upload_info = $('#'+plugin_id+'-info');
	var filelist = $('#'+plugin_id+'-list');
	var max_num = Math.max(1, Number(vars.max_num));
	var file_count = 0;
	var file_field;
	
	var reset_uploader = function(){
		if( typeof file_field=='object' && file_field.length )
			file_field.remove();
		
		if( file_count < max_num ) {
			choose_button.show();
			file_field = $('<input type="file" name="file">').appendTo( choose_button ).change( do_upload );
		} else {
			choose_button.remove();
		}
		upload_info.show();
		progress_bar.hide();
	}
	
	var do_upload = function(){
		choose_button.hide();
		upload_info.hide();
		message_box.hide();
		progress_bar.show();
		
		upload_form.append( file_field ).submit();
	}
	
	var show_error = function(msg, error){
		message_box.html(msg).filter(function(){
			if( error ) {
				$(this).addClass('err');
			}else{
				$(this).removeClass('err');
			}
			return true;
		}).fadeIn();
	}
	
	var upload_complete = function(r){
		show_error( r.message, false );
		add_filelist(meta_id, true, r.url, r.filename, r.thumbnail_url, r.thumbnail_filename);
	}
	
	var upload_error = function(msg){
		show_error(msg, true);
		reset_uploader();
	}
	
	var add_filelist = function(_meta_id, _update_meta, url, filename, thumbnail_url, thumbnail_filename){
		thumbnail_url = thumbnail_url || '';
		thumbnail_filename = thumbnail_filename || '';
		
		var file_type = filename.substr(filename.lastIndexOf('.')+1);
		var is_image = (file_type=='jpg'||file_type=='jpeg'||file_type=='gif'||file_type=='png') ? true : false;
		
		var html = '';
		html += '<span class="'+plugin_id+'-attachments-container '+plugin_id+'-attachments-'+file_count+'">';
		html += '<input type="hidden" class="url"					value="'+url+'"					name="'+plugin_id+'-attachments['+filename+'][url]" />';
		html += '<input type="hidden" class="filename" 				value="'+filename+'"			name="'+plugin_id+'-attachments['+filename+'][filename]" />';
		html += '<input type="hidden" class="thumbnail_url"			value="'+thumbnail_url+'"		name="'+plugin_id+'-attachments['+filename+'][thumbnail_url]" />';
		html += '<input type="hidden" class="thumbnail_filename"	value="'+thumbnail_filename+'"	name="'+plugin_id+'-attachments['+filename+'][thumbnail_filename]" />';
		html += '</span>';
		
		plugin.append(html);
		
		var tr = '';
		tr += '<tr>';
		if( is_image ){
		tr += '	<td class="thumb"><img src="'+(thumbnail_url ? thumbnail_url : url)+'" class="thumb"></td>';
		}else{
		tr += '	<td class="thumb empty"></td>';
		}
		tr += '	<td class="filename">'+filename+'</td>';
		tr += '	<td class="links"></td>';
		tr += '</tr>';
		$tr = $(tr);
		
		filelist.append($tr).show().find('tr').removeClass('even').end().find('tr:even').addClass('even');
		
		if( is_image ){
			var insert_button = $('<a href="#" class="insert-attachment">'+vars.insert_link+'</a>');
			$tr.find('td.links').append(insert_button, '<span class="pipe"> | </span>');
			$.data(insert_button[0], 'index', file_count);
			insert_button.click( insert_into_editor );
		}
		var delete_button = $('<a href="#" class="delete-attachment">'+vars.delete_link+'</a>');
		$tr.find('td.links').append(delete_button);
		$.data(delete_button[0], 'index', file_count);
		$.data(delete_button[0], 'meta_id', _meta_id);
		delete_button.click( delete_file );
		
		if( _meta_id && _update_meta ) 
			update_meta(_meta_id);
		
		file_count++;
		
		reset_uploader();
	}
	
	var insert_into_editor = function(){
		var index = $.data(this, 'index');
		var url = $('span.'+plugin_id+'-attachments-'+index+' input.url').val();
		var html = '<img src="'+url+'">';
		
		var editor;
		if( typeof tinyMCE=='object' && $('textarea.theEditor').length ){
			var id = $('textarea.theEditor').attr('id');
			if( typeof tinyMCE.get(id)=='object' && $('textarea.theEditor').css('display')=='none' ) 
				editor = tinyMCE.get(id);
		}
		if( editor ){
			editor.execCommand('mceInsertContent', false, html);
		}else{
			var ta = $('textarea#topic_text');
			if( !ta.length ) ta = $('textarea#reply_text');
			if( !ta.length ) ta = $('textarea#post_text');
			if( ta.length ){
				var val = ta.val();
				val = val=='' ? html : val+'\n\n'+html;
				ta.val(val);
			}
		}
		return false;
	}
	
	var delete_file = function(){
		if( !confirm(vars.delete_confirm) )
			return false;
		
		var index = $.data(this, 'index');
		var meta_id = $.data(this, 'meta_id');
		var fields = $('span.'+plugin_id+'-attachments-'+index);
		var filename = fields.find('input.filename').val();
		var thumbnail_filename = fields.find('input.thumbnail_filename').val();
		
		fields.remove();
		$(this).parents('tr:eq(0)').remove();
		show_error(vars.processing, true);
		filelist.addClass('processing');
		
		if( meta_id ){
			var args = {
				action: 'ubpfattach_ajax',
				action_scope: 'delete_file_n_update_meta',
				_ajax_nonce: vars.nonce,
				meta_id: meta_id+'|'+filename
			}
			$.post(vars.ajaxurl, args, function(r){
				show_error(vars.delete_success, false);
				reset_filelist();
			});
		}else{
			var args = {
				action: 'ubpfattach_ajax',
				action_scope: 'delete_unattached_files',
				_ajax_nonce: vars.nonce,
				filename: filename,
				thumbnail_filename: thumbnail_filename
			}
			$.post(vars.ajaxurl, args, function(r){
				show_error(vars.delete_success, false);
				filelist.removeClass('processing');
				//console.log(r);
			});
		}
		return false;
	}
	
	var get_meta = function(meta_id){
		if( meta_id=='' ) 
			return;
		var args = {
			action: 'ubpfattach_ajax',
			action_scope: 'get_meta',
			_ajax_nonce: vars.nonce,
			meta_id: meta_id
		}
		$.post(vars.ajaxurl, args, get_meta_lst, 'json');
	}
	
	var get_meta_lst = function(res){
		if(typeof res=='object'){
			var is_older_meta = false;
			for( var i in res ){
				i = i.toString();
				if( i=='0' || Number(i)>0 ) 
					is_older_meta = true;
					
				var r = res[i];
				add_filelist(meta_id, false, r.url, r.filename, r.thumbnail_url, r.thumbnail_filename);
			}
			
			// for under version 1.2
			if( is_older_meta ){
				update_meta(meta_id);
			}
		}else{
			filelist.hide();
		}
	}
	
	var update_meta = function(meta_id){
		if( meta_id=='' ) 
			return;
		
		var files = {};
		$('span.ubpfattach-attachments-container').each(function(){
			var fields = $(this).find('input');
			files[fields.eq(1).val()] = {
				'url': fields.eq(0).val(),
				'filename': fields.eq(1).val(),
				'thumbnail_url': fields.eq(2).val(),
				'thumbnail_filename': fields.eq(3).val()
			};
		});
		
		var args = {
			action: 'ubpfattach_ajax',
			action_scope: 'update_meta',
			_ajax_nonce: vars.nonce,
			meta_id: meta_id,
			files: files,
		}
		
		$.post(vars.ajaxurl, args, function(r){
			//console.log(r);
		});
	}
	
	var reset_filelist = function(){
		file_count = 0;
		filelist.find('tr').remove();
		filelist.removeClass('processing');
		get_meta(meta_id);
	}
	
	this.upload_complete = upload_complete;
	this.upload_error = upload_error;
	
	$('#forum-topic-form div.submit').appendTo('#forum-topic-form');
	
	reset_uploader();
	reset_filelist();
	
	//console.log(meta_id);
}

