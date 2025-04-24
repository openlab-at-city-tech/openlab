jQuery(window).bind('load', function()
{ 
	moppm_has_elementor_class = jQuery('.htmega-login-form-wrapper');
	
	if(moppm_has_elementor_class.length){

        var moppm_log_pass =  document.getElementsByName('login_password');
        
        moppm_log_pass[0].setAttribute("name","moppm_user_password");

	    var moppm_log_pass =  document.getElementsByName('login_username');
	    moppm_log_pass[0].setAttribute("name","moppm_user_name");

       	var moppm_form_id = jQuery('form').attr('id');
		moppm_form_id = '#'+moppm_form_id ;
       	jQuery(moppm_form_id).removeAttr('action');
	
		var moppm_pwd = document.getElementsByName("moppm_user_password");
		moppm_pwd = moppm_pwd[0];
		var element = document.getElementById(moppm_pwd.id);
		
		element.setAttribute("id","moppm_user_password");

		var moppm_user = document.getElementsByName("moppm_user_name");
		moppm_user = moppm_user[0];
		var element = document.getElementById(moppm_user.id);
		element.setAttribute("id","moppm_user_name");


		var moppm_input =  document.getElementsByTagName('input');
        var moppm_on_submit = moppm_input[3].getAttribute('id');
        moppm_on_submit = '#' +moppm_on_submit;

		jQuery('#moppm_user_password').keypress(function (e) {
			if (e.which == 13) {//Enter key pressed		
			   e.preventDefault();
			   moppm_elementor();
			}			
		});
		jQuery(moppm_on_submit).click(function(){
  				if (e.which == 13) {//Enter key pressed		
			   e.preventDefault();
			   moppm_elementor();
			}	
			});
		 jQuery('#moppm_user_name').keypress(function (e){
		 	if (e.which == 13) {//Enter key pressed	
		 	e.preventDefault();
		 	   moppm_elementor();
		 	}
		 });
		 jQuery('.htmega-login-form-wrapper' ).on( 'submit', moppm_form_id, function(e) { 	
		 	if (e.which == 13) {//Enter key pressed	
		 	e.preventDefault();
		 	   moppm_elementor();
		 	}
		 });
		function moppm_elementor(){
			jQuery(moppm_form_id).submit();	
		 }

	}

});