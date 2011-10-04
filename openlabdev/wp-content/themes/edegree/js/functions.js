/* <![CDATA[ */
	jQuery.noConflict();
	
	jQuery(document).ready(function($){	
		//jQuery("#tabs").tabs();
        $('#globalnav ul').superfish({
        	delay: 200,
        	speed: 'fast',
        	autoArrows: false
        }); 

		$("#sidebar h2 a").click(function () {
			var itemlist = $(this).parents("li:first");
			itemlist.toggleClass("side-switch");
			itemlist.find("ul").slideToggle("fast");
			return false;
			
        });
	});

function printCopyrightYears(startYear) {
	if(!startYear)
		var startYear=2009
	var d=new Date(); 
	yr=d.getFullYear();
	if (yr!=startYear) {
		document.write(startYear+"-"+yr);
	} else {
		document.write(startYear);
	}	
}

clearDefault = function(obj) {
		if(obj.defaultValue == obj.value) obj.value = ''; 
}

restoreDefault = function(obj) {
		if(obj.value == '') obj.value = obj.defaultValue;
}

function optformValidate(form) {
	var error = '';
	var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	var objEmail = form.opt_email;
	
	if(form.opt_name.value == '' || form.opt_name.value == form.opt_name.defaultValue) {
		error += "Please enter your name\n";
		form.opt_name.value = '';
		form.opt_name.focus();
		return false;
	}
	if(objEmail.value == '' || objEmail.value == objEmail.defaultValue) {
		error += "Please enter your email\n";
		objEmail.value= '';
		objEmail.focus();
		return false;
		
	} else if(!filter.test(objEmail.value)) {
		error += "Please enter a valid email\n";
		objEmail.value = '';
		objEmail.focus();
	}
	
	if(error != '') {
		alert(error);
		return false;	
	} else {
		return true;
	}
}
/* ]]> */