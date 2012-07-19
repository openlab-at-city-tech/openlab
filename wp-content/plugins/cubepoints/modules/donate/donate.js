function cp_module_donate_do(){
	jQuery.ajax({
		url: cp_donate.ajax_url,
		type: "POST",
		cache: false,
		dataType: "json",
		data: {action: "cp_module_donate_do", recipient: jQuery('#cp_recipient').val(), points: jQuery('#cp_points').val(), message: jQuery('#cp_message').val()},
		success: function(data){
			if(data.success==true){
				Boxy.alert(data.message);
				thebox.hide();
				thebox.unload();
				jQuery('.cp_points_display').html(data.pointsd);
			}
			else{
				Boxy.alert(data.message);
			}
		}
	});
}
function cp_module_donate(){
	confirmation = cp_donate.confirmation;
	thebox = new Boxy('<form name="cp_donate" id="cp_donate" method="post" onsubmit="Boxy.confirm(confirmation,function(){cp_module_donate_do();});return false;"><label for="cp_recipient">'+cp_donate.recipient+':</label><br /><input type="text" id="cp_recipient" name="cp_recipient" style="width:300px;" /><br /><br /><label for="cp_points">'+cp_donate.amount+':</label><br /><input type="text" id="cp_points" name="cp_points" style="width:300px;" /><br /><br /><label for="cp_message">'+cp_donate.message+':</label><br /><textarea id="cp_message" name="cp_message" style="width:300px;height:50px;"></textarea><br /><br /><input type="submit" value="'+cp_donate.donate_points+'" style="width:300px;" /></form>', {title: cp_donate.donate, modal: true});
}