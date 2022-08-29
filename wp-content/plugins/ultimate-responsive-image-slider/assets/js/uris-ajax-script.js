function uris_clone_run(post_id){
	var uris_clone_nonce = jQuery("#uris_clone_nonce").val();
	console.log(uris_clone_nonce);
	if(confirm("Are you sure want to create clone of this slider?")){
		var UrisformData = {
			'action': 'uris_clone_slider',
			'ursi_clone_post_id': post_id,
			'uris_clone_nonce': uris_clone_nonce
		};
		
		jQuery.ajax({
			type: "post",
			dataType: "json",
			url: uris_ajax_object.ajax_url,
			data: UrisformData,
			success: function(response){
				//console.log('Got this from the server: ' + response);
				//jQuery('.uris-clone-success').show().fadeOut(4000, 'linear');
				jQuery('.uris-clone-success').show();
			}
		});
	}
}