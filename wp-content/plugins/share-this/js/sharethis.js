var headerInformation = [{"back":"","title":"1. Choose Buttons and Options","next":"Select Services"},
{"back":"Button Styles","title":"2. Select Services","next":"Sharing Method"},{"back":"Social Channels","title":"3. Sharing Method","next":"Additional Features"},{"back":"Sharing Method","title":"4. Additional Features","next":"Get your publisher key!"},{"back":"Additional Features","title":"5. Sign In","next":"Final step"},{"back":"Sign In","title":"6. Your Current Configuration","next":""}];

var st_selectedServicesList = [];
// page should be set up only once
var st_widgetVersion;

var st_selectedButtonStyle ="";
var st_isSmallChickletSelected = false;
var st_selectedBarStyle ="";
var st_isShareNowSelected = false;
var st_hoverBarPosition = "left";
var presentStepNumber = 1;

//for share buttons
var st_btnType; 

var PLUGIN_PATH;
var st_sharethisServiceIndex;
var st_publisherKey;
var st_username;
var st_button_state = 1;
var time_interval;
var flgLoaderCompleted = true;
var checkJsLoadingStatus = null;
var loopVar=0;
var st_socialPluginValues = {
	twitter_via_textbox : "",
	twitter_username_textbox : "",
	instagram_textbox : "",
	fbsub_textbox : "",
	twitterfollow_textbox : "",
	pinterestfollow_textbox : "",
	foursquarefollow_textbox : "",
	foursquarefollow_textbox2 : "",
	youtube_textbox : ""
};
	
function st_log() {
	if (jQuery('#st_callesi').val() == 0) {
		_gaq.push(['_trackEvent', 'WordPressPlugin', 'ClosedLoopBetaPublishers', st_publisherKey]);
	}
	_gaq.push(['_trackEvent', 'WordPressPlugin', 'ConfigOptionsUpdated']);
	_gaq.push(['_trackEvent', 'WordPressPlugin', "Type_" + jQuery("#st_current_type").val()]);
	if (st_widgetVersion == "5x") {
		_gaq.push(['_trackEvent', 'WordPressPlugin', "Version_5x"]);
	} else if (st_widgetVersion == "4x") {
		_gaq.push(['_trackEvent', 'WordPressPlugin', "Version_4x"]);
	}
}

jQuery(document).ready(function(){
	jQuery('.wrap').css({'background':'white', 'width':'1002px','padding':'10px','border-radius':'5px'});
	
	removeInterval();
	
	jQuery('#st_cns_settings').find('input').live('click', updateDoNotHash);
	if(jQuery('#st_callesi').val() == 1){
		getGlobalCNSConfig();
	}
	
	toggleExpandCollapse('#headingimgPageList', '#divPageList', 'headingimgPageList_right', 'headingimgPageList_down', '', '');	
	toggleExpandCollapse('#headingAddionalOptions', '#addOptions', 'headingAddionalOptions_right', 'headingAddionalOptions_down', '', '');
	toggleExpandCollapse('#codeToggle', '#codeDiv', 'headingimg_right', 'headingimg_down', '', '');
	
	jQuery('#st_pages_on_top, #st_pages_on_bot').live('click', function() {
		if(jQuery('#st_pages_on_top').attr('checked') != 'checked' && jQuery('#st_pages_on_bot').attr('checked') != 'checked')
			jQuery("#divPageList").find("*").attr("disabled","disabled");
		else
			jQuery("#divPageList").find("*").removeAttr("disabled");
	});

	submitForm();
	PLUGIN_PATH = st_script_vars.plugin_url;
	window.onload  = function (){
		windowLoaded();
		setPostExcerpt();
	}
	
	jQuery("input[name='protocolType']").click(function() {
		var editCode = jQuery('#st_widget').val();
		var selected = jQuery("input[name='protocolType']:checked");
		if(selected[0].value == "https") {
			editCode = editCode.replace('http://w.sharethis.com/button/buttons.js', 'https://ws.sharethis.com/button/buttons.js');
			editCode = editCode.replace('http://s.sharethis.com/loader.js', 'https://ss.sharethis.com/loader.js');
		} else {
			editCode = editCode.replace('https://ws.sharethis.com/button/buttons.js', 'http://w.sharethis.com/button/buttons.js');
			editCode = editCode.replace('https://ss.sharethis.com/loader.js', 'http://s.sharethis.com/loader.js');
		}
		jQuery('#st_widget').val(editCode);
	});

});

function toggleExpandCollapse(elem, displayDiv, classOn, classOff, cssOn, cssOff) {
	jQuery(elem).live('click', function() {
		if(jQuery(displayDiv).css('display') == 'none') {
			jQuery(elem).removeClass(classOn);
			jQuery(elem).addClass(classOff);			
			if(cssOn != '')
				jQuery(elem).css(cssOn);
		} else {			
			jQuery(elem).removeClass(classOff);
			jQuery(elem).addClass(classOn);			
			if(cssOff != '')
				jQuery(elem).css(cssOff);			
		}
			
		jQuery(displayDiv).toggle('slow');
	});
}

function st_signOut(pKey) {
	jQuery('<iframe />', {
		name: 'tempIframe',
		id:   'tempIframe',
		src: '//www.sharethis.com/account/signout.php'
	}).appendTo('body');
	jQuery('#tempIframe').css({'width': '1px', 'height': '1px', 'position': 'absolute', 'top': '-100px'});
	jQuery('#st_pkey').val(pKey);
	jQuery('#st_widget').val(jQuery('#st_widget').val().replace(/publisher:"(.*?)"/,'publisher:"'+pKey+'"'));
	jQuery('#st_user_name').val('');
	jQuery("#ak_sharethis").submit();
	
	//Once the signout is done then reload the page.
	jQuery('#tempIframe').load(function() {
		document.location.reload();
	});
}

function setChannelServicesForBars(elemName, searchBarOptionRegex, barDivElement) {
	var o = gtc.getScriptTagObj(elemName);
	var Obj = gtc.parseBarOptions(o, searchBarOptionRegex);
	if((typeof Obj) != "undefined") {
		jQuery('#' + barDivElement).val(Obj.chicklets.items.toString());
		st_selectedServicesList = Obj.chicklets.items;
		jQuery("#st_services").val(Obj.chicklets.items.toString());
		if("h_options" == searchBarOptionRegex) {
			removeHoverbar();
			scriptLoading('hoverbarStyle');
		} else if("p_options" == searchBarOptionRegex) {
			removePulldownbar();
			scriptLoading("pulldownStyle");		
		}
	}
}

function manageBarsOnSave() {
	var str1 = jQuery('#st_widget').val().replace(/\n/g, "");
	var hb_matches = str1.match(/sharethis\.widgets\.hoverbuttons/);
	var pb_matches = str1.match(/sharethis\.widgets\.pulldownbar/);
	var sn_matches = str1.match(/sharethis\.widgets\.serviceWidget/);
	var so_matches = str1.match(/stLight\.options/);
	
	if( !(hb_matches || pb_matches) && !sn_matches && !so_matches) {
		jQuery("#preview").show();
		jQuery("#preview").addClass("wp_st_error_message");
		jQuery("#preview").html("At least one button or bar style option code should be present");
		location.href = "#wp_st_header";	
		return false;
	} else {
		if(jQuery("#preview").hasClass("wp_st_error_message")){
			jQuery("#preview").removeClass("wp_st_error_message");
		}
		jQuery("#page_list_error").css('display', 'none');
		return true;
	}
		
	var arrButtonType = ['_small', '_large', '_vcount', '_hcount'];
	
	if(jQuery.inArray(jQuery('#st_current_type').val(), arrButtonType ) >= 0 && !so_matches) {
		jQuery('#preview').css('display', 'none');
		jQuery('#st_current_type').val('_none');
		jQuery('#selectSizeType').removeClass('wp_st_show');	
		
		if(jQuery('#chickletStyle').hasClass("selected")) 
			jQuery('#chickletStyle').removeClass('selected');
		else if(jQuery('#hcountStyle').hasClass("selected")) 
			jQuery('#hcountStyle').removeClass('selected');
		else if(jQuery('#vcountStyle').hasClass("selected")) 
			jQuery('#vcountStyle').removeClass('selected');
			
		jQuery('#donotcopy').attr('disabled', 'true');
		jQuery('#hashaddress').attr('disabled', 'true');
	}
	
	if(jQuery('#st_selected_bar').val() == 'hoverbarStyle' && !hb_matches) {
		st_selectedBarStyle = '';
		jQuery('#st_selected_bar').val('');
		removeHoverbar();
		updatePreviewArrow();
		updateUI();
		jQuery('#hoverbarStyle').removeClass('selected');
	} else if(jQuery('#st_selected_bar').val() == 'pulldownStyle' && !pb_matches) {
		st_selectedBarStyle = '';
		jQuery('#st_selected_bar').val('');
		removePulldownbar();
		updatePreviewArrow();
		updateUI();
		jQuery('#pulldownStyle').removeClass('selected');
	}	
	return true;
}

function submitForm(){
	jQuery("#ak_sharethis").submit(function(event) {
		
		//Set the protocol type (http or https) i.e. if user modifies the http or https protocols from the edit box
		var str1 = jQuery('#st_widget').val().replace(/\n/g, "");
		var t = str1.match(/src=(.*?)><\/script>/);
		var whichProtocol = "http";
		if((typeof t) != "undefined" && t != null)
			whichProtocol = gtc.checkProtocolOptions(t[1]);

		jQuery("input[name=protocolType][value=" + whichProtocol + "]").attr('checked', 'checked');

		//Set widgetType (Multipost or Directpost) i.e. if user modifies the swithTo5x option from the edit box
		var widgetType = str1.match(/switchTo5x=(true|false)/);
		if((typeof widgetType) != "undefined" && widgetType != null && "false" == widgetType[1]) {
			jQuery('#st_5xwidget').removeClass('selected');
			jQuery('#st_4xwidget').addClass('selected');
			jQuery('#st_version').val('4x');
		} else {
			jQuery('#st_4xwidget').removeClass('selected');
			jQuery('#st_5xwidget').addClass('selected');		
			jQuery('#st_version').val('5x');
		}
		
		//Set the channel services for bars i.e. if user adds/remove the services from the edit box
		setChannelServicesForBars('st_widget', 'h_options', 'st_hoverbar_services');
		setChannelServicesForBars('st_widget', 'p_options', 'st_pulldownbar_services');
	  
		//Set the pulldownbar scrolling height i.e. if user modifies the height from the edit box
		setScrollpxHeight();
			
		st_getServicesFromSpanTag();
		 
		var isOptionSel = manageBarsOnSave();
		if(!isOptionSel) return false;
		
		event.preventDefault();
		var getform = jQuery( this ),
		url = getform.attr('action');
		
		var postdata = jQuery.post(url, getform.serialize());

		postdata.done(function( data ) {
			jQuery('html, body').animate({scrollTop: '0px'}, 0);
			jQuery("#st_updated").show();
			jQuery('#st_updated').delay(2000).fadeOut();
	  });
	  
	  //Update preview and picker list once the span tags are modified and saved.
	  var servArray = jQuery("#st_services").val().split(',');
	  if((typeof servArray) != "undefined" && servArray != null)
		st_selectedServicesList = servArray;
	  stlib_preview.setupPreview(jQuery("#preview"),st_selectedServicesList);
	  
	  //Button type (_large or _small or hcount or vcount)
	  jQuery('#st_current_type_from_db').val(jQuery('#st_current_type').val());
	  jQuery('#st_tags_from_db').val(jQuery('#st_tags').val());
	  return false;
	});
}	

function setScrollpxHeight() {
	var str1 = jQuery('#st_widget').val().replace(/\n/g, "");
	var t = str1.match(/\"scrollpx\":[\s\"\']{0,}(\d+)[\s\"\']{0,}/);
	if(t != null) {
		jQuery('#st_pulldownbar_scrollpx').val(t[1]);
		jQuery('#selectScrollHeight_id').val(t[1]);
	}
}

/*
Below function collects services from modified span tags.
i.e. if services are added or modified then accordingly update the st_services hidden textbox before saving into database.
*/
function st_getServicesFromSpanTag() {
	var spanTags = jQuery('#st_tags').val();
	var arrSpanTags = spanTags.split('</span>');
	var service = '';
	for(var i=0;i<arrSpanTags.length;i++) {
		var matches = arrSpanTags[i].match(/='st_(.*?)'/);
		if (matches!=null && typeof(matches[1])!="undefined"){
			var pos = matches[1].indexOf('_');
			if('' != service)
				service += ',';
			
			if(pos != -1)
				service += matches[1].substring(0,pos);	
			else
				service += matches[1]
		}
	}
	
	if(service.length > 0)
		jQuery('#st_services').val(service);
}

function checkButtonJsStatus(){  
	if(typeof(stbuttons) != "undefined"){ 
		clearInterval(checkJsLoadingStatus);
		jQuery("#showLoadingStatus").hide();
		jQuery("#wp_st_outerContainer").fadeIn(2000);
		if(jQuery("#freshInstalation").val() == 0){
			moveToNext(5);
			enableLeftArrow();	
		}
	 }
} 

function windowLoaded(){
	checkJsLoadingStatus = setInterval(function(){
		checkButtonJsStatus()
	},500);
	jQuery(".stButton").remove();
	jQuery("#wp_st_savebutton").hide();
	jQuery("#st_configure_pulldown").hide();
	disableLeftArrow();	
	
	jQuery(".wp_st_navSlideDot").click(function(){
		//var isBtnBarSelected =true;
		var isBtnBarSelected = validateUserSelection();		
		if(presentStepNumber == 5 && isBtnBarSelected == false){
			// This will enable user to navigate back to previous steps without error message
			jQuery("#errorMessage").hide();
			enableRightArrow();
			jQuery(".wp_st_nextText").html("Next : ");
			moveToPrevious(2);
			return true;
		}
		
		if(isBtnBarSelected == true){			
			var selectedDot = jQuery(this).attr("value");
			if(selectedDot > 1){
			    enableLeftArrow();
				enableRightArrow();
				jQuery(".wp_st_nextText").html("Next : ");
				moveToNext((selectedDot-1));
			}else if(selectedDot == 1){
			   	enableRightArrow();
				jQuery(".wp_st_nextText").html("Next : ");
				moveToPrevious(2);
			}
		}
	});
	
	jQuery("#edit").click(function(){
		jQuery("#errorMessage").hide();		
		jQuery(".wp_st_nextText").html("Next : ");
		moveToPrevious(2);
		enableRightArrow();
		return false;
	});
	
	checkUserNameAndPubId();

	if(jQuery("#login_key").html() == "" || jQuery("#login_key").html() == "undefined"){
		getPublisherInfo();	
	}
	
	makeHeadTag();
	
	/**
	* Retrive service values from database
	*/
	var selectedServicesList = jQuery("#st_services").val();
	st_selectedServicesList = selectedServicesList.split(',');
	
	/**
	* Set up preview for buttons
	*/
	stlib_preview.setupPreview(jQuery("#preview"),st_selectedServicesList);
	
	
	/**
	* Retrives social button value saved in database and map old button configuration to new ones
	*/
	st_btnType = jQuery("#st_current_type").val();	
	if(st_btnType == "chicklet" || st_btnType == "chicklet2"){
		st_btnType = "_small";
	}else if(st_btnType == "classic"){
		st_btnType = "_small";
		st_selectedServicesList = ["sharethis"];
		stlib_preview.replace("preview",st_selectedServicesList);
		stlib_preview.updateOpt("preview", {icon:'',layout:'h',label:false});
	}else if(st_btnType == "_buttons"){
		st_btnType = "_large";
	}
	
	var buttonType = st_btnType.substring(1);
	var _buttonSize = "";
	if(buttonType == "" || buttonType == "undefined" || buttonType == "none"){
		st_selectedButtonStyle = "";
	}else{
		if(buttonType == "small" || buttonType == "large"){
			if(buttonType == "small"){
				st_isSmallChickletSelected = true;
				_buttonSize = "16x16";
			}else{
				st_isSmallChickletSelected = false;
				_buttonSize = "32x32";
			}
			stbuttons.changeSize(_buttonSize);
			var radioButtons = jQuery('#selectSizeType input:radio');
			for(var i=0; i<radioButtons.length; i++){
				radioButtons[i].checked = false;
				if(radioButtons[i].value == _buttonSize){
					radioButtons[i].checked = true;
				}
			}
			jQuery("#chickletStyle").addClass("selected");
			st_selectedButtonStyle = "chickletStyle";
		}else{
			jQuery("#"+buttonType+"Style").addClass("selected");
			st_selectedButtonStyle = buttonType+"Style";
		}
		selectStyle(st_selectedButtonStyle);
	}
	
	/**
	* Retrives social bar value saved in database
	*/
	var tag = jQuery('#st_widget').val();
	
	var _scrollpx;
	var _pulldownLogo;
	if (tag.match(/new sharethis\.widgets\.hoverbuttons/)){
		if(tag.toLowerCase().indexOf("right") >= 0){
			st_hoverBarPosition = "right";
		}else if(tag.toLowerCase().indexOf("left") >= 0){
			st_hoverBarPosition = "left";
		} 
		flgLoaderCompleted = true;
	     scriptLoading("hoverbarStyle");
		 jQuery("#hoverbarStyle").addClass("selected");
	}else if (tag.match(/new sharethis\.widgets\.pulldownbar/)){
		_scrollpx = jQuery('#st_pulldownbar_scrollpx').val();
		_pulldownLogo = jQuery('#st_pulldownbar_logo').val();
		jQuery('#selectScrollHeight_id').val(_scrollpx); 
		jQuery('#pulldown_optionsTextbox_id').val(_pulldownLogo);
		flgLoaderCompleted = true;
		scriptLoading("pulldownStyle");
		jQuery("#pulldownStyle").addClass("selected");
	}
	
	// ShareNow is deprecated. If publisher has saved only ShareNow settings in database, he will get the following message
	if (st_btnType== "_none" && ! (tag.match(/new sharethis\.widgets\.hoverbuttons/) || tag.match(/new sharethis\.widgets\.pulldownbar/))){
		jQuery("#preview").hide();
		jQuery("#errorMessage").html("<span style='font-size:14px'>Please note, we have deprecated the ShareNow Widget.</span>");
	}

	
	/**
	* Retrive widget version from database
	*/
	st_widgetVersion = jQuery("#st_version").val();
	
	/**
	* Click handler for share buttons
	*/
	jQuery(".jqBtnStyle").bind("click",function(event){
		if(jQuery(this).hasClass("selected")){
			jQuery(this).removeClass("selected");
			st_selectedButtonStyle = "";
		}else{
			jQuery(".jqBtnStyle").removeClass("selected");
			jQuery(this).addClass("selected");
			st_selectedButtonStyle = jQuery(this).attr("id");
			jQuery('#donotcopy').removeAttr('disabled');
			jQuery('#hashaddress').removeAttr('disabled');	
			st_selectedServicesList = jQuery.unique(st_selectedServicesList);
		}
		selectStyle(st_selectedButtonStyle);
	});
	
	jQuery("input[name='selectSize_type']").change(function(){
		if( jQuery(this).val() == "16x16"){
			st_btnType = "_small";
		}else{
			st_btnType = "_large";
		}
		jQuery("#st_current_type").val(st_btnType);
	})
	
	/**
	* Click handler for share bars
	*/
	jQuery(".jqBarStyle").bind('click',function(event){
		if(flgLoaderCompleted == true){
			removeBars();
			if(jQuery(this).hasClass("selected")){
				jQuery(this).removeClass("selected");
				st_selectedBarStyle = "";
				updateUI();
				jQuery("#st_pulldownConfig").hide();
				jQuery("#st_selected_bar").val(st_selectedBarStyle);
				hideBarsPreview();
			}else{
				jQuery("#st_pulldownConfig").hide();
				jQuery(".jqBarStyle").removeClass("selected");
				jQuery(this).addClass("selected");
				scriptLoading(jQuery(this).attr("id"));
			}
		}
	});
	
	jQuery("input[name='selectDock_type']").change(function() {
		st_hoverBarPosition = jQuery(this).val();
		updatePreviewArrow();
	});
	
	jQuery("#st_configure_pulldown").click(function(){		
		jQuery("#wp_st_slidingContainer").hide();
		jQuery("#st_pulldownConfig").toggle("slow");
		location.href = "#st_pulldownConfig";
	});
	
		
	/**
	* Retrive copynshare configuration from database
	*/
	checkCopyNShare();
	
	/**
	* Sharing button hover and out functionality 
	*/
	jQuery(".wp_st_styleLink").mouseover(function () {
		if(jQuery(this).hasClass('jqBtnStyle')){
			changeHoverView(this, 'over');
		}else if((flgLoaderCompleted == true) && (jQuery(this).hasClass('hoverbarStyle') || jQuery(this).hasClass('pulldownStyle'))){
			changeHoverView(this, 'over');
		}else{
			return false;
		}
	});
	
	jQuery(".wp_st_styleLink").mouseout(function () {
		changeHoverView(this, 'out');
	});
	
	/**	
	* For multipost and direct post selection
	*/
	jQuery("p.wp_st_post_heading").click(function(event){
		jQuery("p.wp_st_post_heading").removeClass("selected");
		jQuery(this).addClass("selected");
		var code=jQuery('#st_widget').val();
		if(event.target.id == "st_5xwidget"){
			st_widgetVersion = "5x";
			jQuery('#st_widget').val(code.replace('switchTo5x=false','switchTo5x=true'));
		}else if(event.target.id == "st_4xwidget"){
			st_widgetVersion = "4x";
			jQuery('#st_widget').val(code.replace('switchTo5x=true','switchTo5x=false'));
		}
		jQuery("#st_version").val(st_widgetVersion);
	});		
	
	/**
	* Retrive addition serivce parameters from database
	*/
	UpdateSocialPluginValues();
}

function UpdateSocialPluginValues(){
    if(st_btnType != "_none"){
		var tags=jQuery('#st_tags').val();
		var matches=tags.match(/st_via='(\w*)'/); 
		if (matches!=null && typeof(matches[1])!="undefined"){
			st_socialPluginValues["twitter_via_textbox"] = matches[1];
		} 
		
		var matches2=tags.match(/st_username='(\w*)'(.*)class='(st_twitter\w*)'/); 
		if (matches2!=null && typeof(matches2[1])!="undefined"){
			st_socialPluginValues["twitter_username_textbox"] = matches2[1];
		} 
		
		var matchInstagram = tags.match(/st_username='(\w*)'(.*)class='(st_instagram\w*)'/);
		if(matchInstagram != null && typeof(matchInstagram[1]) != "undefined"){
			st_socialPluginValues["instagram_textbox"] = matchInstagram[1];
		}
		
		var matchFbSubscribe = tags.match(/st_username='(\w*)'(.*)class='(st_fbsub\w*)'/);
		if(matchFbSubscribe != null && typeof(matchFbSubscribe[1]) != "undefined"){
			st_socialPluginValues["fbsub_textbox"] = matchFbSubscribe[1];
		}
		
		var matchTwFollow = tags.match(/st_username='(\w*)'(.*)class='(st_twitterfollow\w*)'/);
		if(matchTwFollow != null && typeof(matchTwFollow[1]) != "undefined"){
			st_socialPluginValues["twitterfollow_textbox"] = matchTwFollow[1];
		}
		
		var matchPinFollow = tags.match(/st_username='(\w*)'(.*)class='(st_pinterestfollow\w*)'/);
		if(matchPinFollow != null && typeof(matchPinFollow[1]) != "undefined"){
			st_socialPluginValues["pinterestfollow_textbox"] = matchPinFollow[1];
		}
		
		var matchFSFollow = tags.match(/st_username='(\w*)'(.*)st_followId='(\w*)'(.*)class='(st_foursquarefollow\w*)'/);
		if(matchFSFollow != null && typeof(matchFSFollow[1]) != "undefined"){
			st_socialPluginValues["foursquarefollow_textbox"] = matchFSFollow[1];
			st_socialPluginValues["foursquarefollow_textbox2"] = matchFSFollow[2];
		}
		
		var matchYTSubscribe = tags.match(/st_username='(\w*)'(.*)class='(st_youtube\w*)'/);
		if(matchYTSubscribe != null && typeof(matchYTSubscribe[1]) != "undefined"){
			st_socialPluginValues["youtube_textbox"] = matchYTSubscribe[1];
		}
	}else{
		var tags=jQuery('#st_widget').val();
		if (tags.match(/new sharethis\.widgets\.hoverbuttons/)){	
			var viaMatch = tags.match(/"st_via":"(\w*)"/);
			if (viaMatch!=null && typeof(viaMatch[1])!="undefined"){
				st_socialPluginValues["twitter_via_textbox"] = viaMatch[1];
			}
			
			var usernameMatch = tags.match(/"st_username":"(\w*)"/);
			if (usernameMatch!=null && typeof(usernameMatch[1])!="undefined"){
				st_socialPluginValues["instagram_textbox"] = usernameMatch[1];
			}
		}	
	} 
}

function checkUserNameAndPubId(){
	st_publisherKey = jQuery("#st_pkey").val();
	st_username = jQuery("#st_user_name").val();
	updateUserLoginInfo();
}	
	
function updateUserLoginInfo(){
	if(st_publisherKey.toLowerCase().indexOf("wp") == -1 && st_publisherKey != "" && st_publisherKey != "undefined"){
		jQuery("#pbukeyContainer").show();
		jQuery("#login_key").html(st_publisherKey);
		jQuery("#loginFrame").hide();
		jQuery(".wp_st_login_message").show();
	}else{
		jQuery("#pbukeyContainer").hide();
	}
	if(st_username != "" && st_username != "undefined"){
		jQuery("#usernameContainer").show();
		jQuery("#login_name").html(st_username);
	}else{
		jQuery("#usernameContainer").hide();
	}	
	jQuery("#st_pkey").val(st_publisherKey);
	jQuery("#st_user_name").val(st_username);
}	
	
function checkForLoginCredentials(){
	if(st_publisherKey=='' || st_publisherKey.toLowerCase().indexOf("wp") != -1 || st_publisherKey=='undefined'){
		time_interval = setInterval(function(){  
			getPublisherInfo();
		},2000);	
	}
}	
	
/**
* JSONP Request called on closing the external-login iframe
*/
function getPublisherInfo(){
	 jQuery.ajax({
		url: '//www.sharethis.com/get-publisher-info.php?callback=?',
		type: "GET",
		dataType: "jsonp",
		jsonpCallback: "parsePublisherInfo"
	});
}


function parsePublisherInfo(response){ 
	if(response.publisher_id == "" || response.publisher_id == "undefined"){
	
	}else{
		st_publisherKey = response.publisher_id;
		st_username = response.publisher_name;
		updateUserLoginInfo();
		clearInterval(time_interval);
	}
}		
	
function checkHoverBar(){
	if(st_selectedBarStyle == "hoverbarStyle"){
		checkHoverBarPosition();	
	}
}

function checkHoverBarPosition(){
		var radiobuttons = jQuery('#hoverbar_selectDock input:radio');
		for(var i=0; i<radiobuttons.length; i++){
			radiobuttons[i].checked = false;
			radiobuttons[i].disabled = false;
			if(radiobuttons[i].value == st_hoverBarPosition){
				radiobuttons[i].checked = true;
			}	
		}
		jQuery("#sthoverbuttons").removeClass();
		if(st_hoverBarPosition == "right"){
			jQuery("#sthoverbuttons").addClass("sthoverbuttons-pos-right");
		}else if(st_hoverBarPosition == "left"){
			jQuery("#sthoverbuttons").addClass("sthoverbuttons-pos-left");
		}

}

function changeHoverView(obj, mouse_event) {
	var text = jQuery(obj).attr("id");
	if(mouse_event == 'out'){
		jQuery(".wp_st_hoverState."+text+",.wp_st_hoverState2."+text).removeClass("wp_st_show");
	}else{
		jQuery(".wp_st_hoverState."+text+",.wp_st_hoverState2."+text).addClass("wp_st_show");
	}
}	

function updatePreview() {
	st_selectedServicesList = stlib_picker.getServices("mySPicker");
	stlib_preview.replace("preview", st_selectedServicesList);
	jQuery("#st_services").val(st_selectedServicesList);
}

function scriptLoading(barStyle){
	if(flgLoaderCompleted == false){
		return false;
	}
	flgLoaderCompleted = false;
	if(barStyle=="hoverbarStyle"){
		jQuery('#hoverBarImage').hide();
		jQuery("#hoverbarLoadingImg").show();
		jQuery.getScript(PLUGIN_PATH+"libraries/get-hoverbuttons-new.js",function(data){ 
			hoverbuttons.stgOptions.position =  st_hoverBarPosition;
			var st_hoverbuttons_widget = new sharethis.widgets.hoverbuttons(hoverbuttons.stgOptions);
			checkHoverBarPosition();
			st_selectedBarStyle = barStyle;
			selectStyle(st_selectedBarStyle);
			try {
			// Commented for adding a delay 
				stMini.initWidget();
				updateHoverBarChicklets();
				hoverbuttons.updateWidget();
				jQuery("#hoverbarLoadingImg").hide();
				jQuery('#hoverBarImage').show();
			} catch (e) {
			// This is for missing stMini.initWidget function ;
				setTimeout(function(){
					stMini.initWidget();
					updateHoverBarChicklets();
					hoverbuttons.updateWidget();
					jQuery("#hoverbarLoadingImg").hide();
					jQuery('#hoverBarImage').show();
				},3000);
			}
			flgLoaderCompleted = true;
			removePulldownbar();
		},'script');
	  }else if(barStyle=="pulldownStyle"){
		jQuery('#pullDownBarImage').hide();
		jQuery("#pulldownLoadingImg").show();
		jQuery.getScript(PLUGIN_PATH+"libraries/get-pulldown-new.js",function(data){ 
			var st_pulldown_widget = new sharethis.widgets.pulldownbar(pulldown.stgOptions);
			jQuery("#st_configure_pulldown").show();
			st_selectedBarStyle = barStyle;
			selectStyle(st_selectedBarStyle);
			try {
				// Commented for adding a delay 
				stPullDown.initWidget();
				updatePulldownBarChicklets();
				pulldown.updateWidget();
				jQuery("#pulldownLoadingImg").hide();
				jQuery('#pullDownBarImage').show();
			} catch (e) {
				setTimeout(function(){
					stPullDown.initWidget();
					updatePulldownBarChicklets();
					pulldown.updateWidget();
					jQuery("#pulldownLoadingImg").hide();
					jQuery('#pullDownBarImage').show();					
				},3000);
			}
			flgLoaderCompleted = true;
			removeHoverbar();
		},'script'
		);
	  }
}

function selectStyle(obj) {
	jQuery("#st_selected_bar").val(st_selectedBarStyle);
	var text = obj;
	stlib.getButtonConfig.style = text;
	try {
		if (text == "hcountStyle") {
			stlib_preview.updateOpt("preview", {icon:'hcount',label:true});
		} else if (text == "vcountStyle") {
			stlib_preview.updateOpt("preview", {icon:'vcount',label:true});
		} else if (text == "chickletStyle"){
			var radioButtons = jQuery('#selectSizeType input:radio');
			for (var i=0; i<radioButtons.length; i++) {
				if (jQuery('#selectSizeType input:radio')[i].checked) {
					stbuttons.changeSize(jQuery('#selectSizeType input:radio')[i].value);
					if (radioButtons[i].value == "16x16"){
						st_isSmallChickletSelected = true;
					}else {
						st_isSmallChickletSelected = false;
					}
				}
			}
		}	
	} catch (err) {}
	updatePreviewArrow();
	updateUI();
	setButtonType();
}

function setPageView() {		
	jQuery("#mySPicker").show();
	stlib_picker.setupPicker(jQuery("#mySPicker"), st_selectedServicesList, function(response){
		if(response.action == "add") {
				//ga_log('Get Sharing Tools', 'step3', response.service);
		}
		if (response.action!="add") {						
			updatePreview();
			if(st_selectedBarStyle == "hoverbarStyle"){
				updateHoverBarChicklets();hoverbuttons.updateWidget();
			}else if(st_selectedBarStyle == "pulldownStyle"){
				updatePulldownBarChicklets();pulldown.updateWidget();
			}								
		}
		if (response.action!="move") {						
			//gaLog('picker-stbuttons', response.action, response.service);
		}
	}, [], [], {showNative:true});
	updatePreview();
}

function setButtonType(){
	if(st_selectedButtonStyle == "hcountStyle"){
		st_btnType = "_hcount";
	}else if(st_selectedButtonStyle == "vcountStyle"){
		st_btnType = "_vcount";
	}else if(st_selectedButtonStyle == "chickletStyle"){
		if(st_isSmallChickletSelected == true){
			st_btnType = "_small";
		}else{
			st_btnType = "_large";
		}
	}else{
		st_btnType = "_none";
	}
	jQuery("#st_current_type").val(st_btnType);
}


function updateUI(){
	if(st_selectedButtonStyle == "chickletStyle"){
		jQuery('#selectSizeType').addClass('wp_st_show');
	}else{
		jQuery('#selectSizeType').removeClass('wp_st_show');
	}
	if(st_selectedBarStyle == "hoverbarStyle"){
		jQuery("#hoverbar_selectDock").show();
	}else{
		jQuery("#hoverbar_selectDock").hide();
	}
	if(st_selectedButtonStyle == ""){
		jQuery("#preview").hide();
	}else{
		jQuery("#preview").show();
	}
}

function updatePreviewArrow(){
	hideBarsPreview();
	if(st_selectedBarStyle == "hoverbarStyle"){
		if(st_hoverBarPosition == "left"){
			jQuery("#barPreview1").show();
		}else{
			jQuery("#barPreview2").show();
		}
		
	}else if(st_selectedBarStyle == "pulldownStyle"){
		jQuery("#barPreview3").show();
	}
}

function hideBarsPreview(){
	jQuery("#barPreview3").hide();
	jQuery("#barPreview1").hide();
	jQuery("#barPreview2").hide();
}

/**
* Updates preview for the hoverbar with the selected services 
*/
function updateHoverBarChicklets(){
	if(typeof(hoverbuttons.stgOptions.chicklets)=='undefined') {
		hoverbuttons.stgOptions.chicklets = {};
	}	
	hoverbuttons.stgOptions.chicklets.items = removeSocialPluginsFromBar();
	jQuery("#st_hoverbar_services").val(removeSocialPluginsFromBar());
}

/**
* Updates preview for the pulldown with the selected services 
*/
function updatePulldownBarChicklets(){
	if(typeof(pulldown.stgOptions.chicklets)=='undefined') {
		pulldown.stgOptions.chicklets = {};
	}
	pulldown.stgOptions.chicklets.items = removeSocialPluginsFromBar();
	jQuery("#st_pulldownbar_services").val(removeSocialPluginsFromBar());
}


function removeSocialPluginsFromBar(){
	var jCounter=0;	
	var chickletServicesArray = st_selectedServicesList;
	var newchickletServicesArray = new Array();
	if(st_selectedBarStyle == "pulldownStyle"){
		for(var i=0; i<chickletServicesArray.length; i++){
			// Skip social services from bar
			if(jQuery.trim(chickletServicesArray[i]) != 'plusone' && jQuery.trim(chickletServicesArray[i]) != 'fblike' && jQuery.trim(chickletServicesArray[i]) != 'fbrec'&& jQuery.trim(chickletServicesArray[i]) != 'fbsend'&& jQuery.trim(chickletServicesArray[i]) != 'fbsub'&& jQuery.trim(chickletServicesArray[i]) != 'foursquaresave'&& jQuery.trim(chickletServicesArray[i]) != 'foursquarefollow'&& jQuery.trim(chickletServicesArray[i]) != 'youtube'&& jQuery.trim(chickletServicesArray[i]) != 'pinterestfollow'&&
			jQuery.trim(chickletServicesArray[i]) != 'twitterfollow'&& jQuery.trim(chickletServicesArray[i]) != 'instagram') {
				newchickletServicesArray[jCounter] = jQuery.trim(chickletServicesArray[i]);
				jCounter++;
			}
		}			
	}else if(st_selectedBarStyle == "hoverbarStyle"){
		for(var i=0; i<chickletServicesArray.length; i++){
		// Skip social services from bar, instagram is part of hoverbar 
			if(jQuery.trim(chickletServicesArray[i]) != 'plusone' && jQuery.trim(chickletServicesArray[i]) != 'fblike' && jQuery.trim(chickletServicesArray[i]) != 'fbrec'&& jQuery.trim(chickletServicesArray[i]) != 'fbsend'&& jQuery.trim(chickletServicesArray[i]) != 'fbsub'&& jQuery.trim(chickletServicesArray[i]) != 'foursquaresave'&& jQuery.trim(chickletServicesArray[i]) != 'foursquarefollow'&& jQuery.trim(chickletServicesArray[i]) != 'youtube'&& jQuery.trim(chickletServicesArray[i]) != 'pinterestfollow' && jQuery.trim(chickletServicesArray[i]) != 'twitterfollow') {
				newchickletServicesArray[jCounter] = jQuery.trim(chickletServicesArray[i]);
				jCounter++;
			}
		}
	}
	return newchickletServicesArray;
}

function removeHoverbar(){
	jQuery('#sthoverbuttons').remove();
}

function removePulldownbar(){
	jQuery('#stpulldown').remove();
	if (typeof(stPullDown) != "undefined") {
		if(sharethis.utilities.domUtilities.removeListenerCompatible(window, "scroll", stPullDown.onScrollEvent) == false){
			sharethis.utilities.domUtilities.removeListenerCompatible(document, "scroll", stPullDown.onScrollEvent);
		}
	}
	jQuery("#st_configure_pulldown").hide();
}

function removeBars() {
	removeHoverbar();
	removePulldownbar();
}

function disableLeftArrow(){
	jQuery(".wp_st_leftarrow").hide();
	jQuery(".wp_st_backText").hide();
	jQuery(".wp_st_backTitle").hide();
}

function enableLeftArrow(){
	jQuery(".wp_st_leftarrow").show();
	jQuery(".wp_st_backText").show();
	jQuery(".wp_st_backTitle").show();
}

function disableRightArrow(){
    jQuery(".wp_st_rightarrow").hide();
	jQuery(".wp_st_nextText").hide();
	jQuery(".wp_st_nextTitle").hide();
}

function enableRightArrow(){
    jQuery(".wp_st_rightarrow").show();
	jQuery(".wp_st_nextText").show();
	jQuery(".wp_st_nextTitle").show();
}

function checkSpecialServices(){
	var splHtml = getNativeConfigOptions(st_selectedServicesList,"callbackFunction(this)");
	jQuery("#st_splServiceContainer").html(splHtml);
	if(jQuery("#st_native_config").find('.wp_st_buttonCodeGeneratorConfig').length != 0){
		jQuery("#st_splServiceContainer").show();
	}else{
		jQuery("#st_splServiceContainer").hide();
	}
}

function callbackFunction(obj){
	var textBoxId = jQuery(obj).attr("id");
	jQuery(obj).val(jQuery.trim(jQuery(obj).val()));
	st_socialPluginValues[textBoxId] = jQuery.trim(jQuery(obj).val());
}

function getNativeConfigOptions(services,callback) {
	var html = "";
	html += "<div";
	html += " class='wp_st_socialPluginContainer' style='clear: both;text-align: left;'>";
	html += "<h2 class='wp_st_buttonCodeHeading'>We noticed that you picked some buttons which require a bit more information.</h2>";
	html += "<ol id='st_native_config'>";
	
	for (s=0;s<services.length;s++) {
		if (services[s] == "pinterestfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Pinterest follow username</label>";
			html += "<input id='pinterestfollow_textbox' type='textbox' value='"+st_socialPluginValues["pinterestfollow_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "instagram") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Instagram Username</label>";
			html += "<input id='instagram_textbox' type='textbox' value='"+st_socialPluginValues["instagram_textbox"]+"' name='instagram[username]' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "googleplusadd") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Google Add Profile ID</label>";
			html += "<input id='googleplusadd_textbox' type='textbox' value='' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: '113842823840690472625'</label>";
			html += "</div>";
		} else if (services[s] == "googleplusfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Google Follow page ID</label>";
			html += "<input id='googleplusfollow_textbox' type='textbox' value='' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: '110924060789171264851'</label>";
			html += "</div>";
		} else if (services[s] == "youtube") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Youtube subscribe Username</label>";
			html += "<input id='youtube_textbox' type='textbox' value='"+st_socialPluginValues["youtube_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "linkedinfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>LinkedIn Follow Profile ID</label>";
			html += "<input id='linkedinfollow_textbox' type='textbox' value='' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter '207839' for profile id</label>";
			html += "</div>";
		} else if (services[s] == "twitterfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Twitter follow Username</label>";
			html += "<input id='twitterfollow_textbox' type='textbox' name='twitterfollow[via]' value='"+st_socialPluginValues["twitterfollow_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "fbsub") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Facebook subscribe Username</label>";
			html += "<input id='fbsub_textbox' type='textbox' value='"+st_socialPluginValues["fbsub_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for profile name</label>";
			html += "</div>";
		} else if (services[s] == "foursquaresave") {
			//html += "<li>For Foursquare Save button</li>";
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>For Foursquare Save button</label>";
			html += "Please make sure your website contains the needed hCard or OpenGraph location metadata so that foursquare knows which place your webpage is referring to. Click <a href='https://foursquare.com/business/brands/offerings/savetofoursquare/tester' target='_blank'>here</a> for more information.";
			html += "</div>";
		} else if (services[s] == "foursquarefollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Foursquare follow Username</label>";
			html += "<input id='foursquarefollow_textbox'  style='width:15%;' type='textbox' value='"+st_socialPluginValues["foursquarefollow_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label style='margin-right:30px;' class='wp_st_defaultCursor'>Ex: nytimes</label>";
			html += "<label style='margin-right:20px;'>Profile id</label>";
			html += "<input id='foursquarefollow_textbox2' style='width:15%;' type='textbox' value='"+st_socialPluginValues["foursquarefollow_textbox2"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label style='margin-right:0px;' class='wp_st_defaultCursor'>Ex: 1234567</label>";
			html += "</div>";
		}else if(services[s] == "twitter"){
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Twitter Via</label>";
			html += "<input id='twitter_via_textbox' type='textbox' value='"+st_socialPluginValues["twitter_via_textbox"]+"' name='twitter[via]' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Username to attribute tweet to (defaults to @ShareThis)</label>";
			html += "</div>";
			
			if(st_btnType != "_none"){
				html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
				html += "<label class='leftLabel'>Twitter Username</label>";
				html += "<input id='twitter_username_textbox' type='textbox' value='"+st_socialPluginValues["twitter_username_textbox"]+"' name='twitter[username]' data-value=''";
				if (callback) {
					html += " onblur='"+callback+"'";
				}
				html += "><label class='wp_st_defaultCursor'>Twitter will recommend users follow this account after they tweet</label>";
				html += "</div>";
			}
		}
	}
	html += "</ol>";
	html += "</div>";
	return html;
}

function generateCopyNShare(){
	jQuery('#copynshareSettings').val(getCopyNShare());
}

//get settings for copy and share   RS
function getCopyNShare(){
	var retval = '';
	if(jQuery('#st_callesi').val() == 0){		
		if(jQuery(jQuery('#st_cns_settings').find('input')[0]).is(':checked')){
			retval += ', "doNotCopy": false';
		}else{
			retval += ', "doNotCopy": true';
		}
		if(jQuery(jQuery('#st_cns_settings').find('input')[1]).is(':checked')){
			retval += ', "hashAddressBar": true';
		}else{
			retval += ', "hashAddressBar": false';
		}
		
		if(jQuery(jQuery('#st_cns_settings').find('input')[0]).is(':checked') || jQuery(jQuery('#st_cns_settings').find('input')[1]).is(':checked')){
			retval += ', "doNotHash": false';
		}else{
			retval += ', "doNotHash": true';
		}
	}
	return retval;
}

function checkCopyNShare(){	
	if(jQuery('#st_current_type').val() == '_none') {
		jQuery('#donotcopy').attr('disabled', 'true');
		jQuery('#hashaddress').attr('disabled', 'true');	
	}
	
	var tag=jQuery('#st_widget').val();
	if (tag.match(/("|)doNotHash("|):(\s)*false/)){
		if (tag.match(/("|)doNotCopy("|):(\s)*false/)){
			jQuery(jQuery('#st_cns_settings').find('input')[0]).attr("checked","checked").val(true);
		}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).removeAttr("checked").val(false);
    	}
		if (tag.match(/("|)hashAddressBar("|):(\s)*false/)){
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).removeAttr("checked").val(false);
		}else{
			jQuery(jQuery('#st_cns_settings').find('input')[1]).attr("checked","checked").val(true);
    	}
	}else if (tag.match(/("|)doNotHash("|):(\s)*true/)){
		jQuery('#st_cns_settings').find('input').each(function( index ){
			jQuery(this).removeAttr("checked").val(false);
		});
	}
	jQuery('#copynshareSettings').val(getCopyNShare());
}

function updateDoNotHash()
{
	jQuery('#st_callesi').val(0);
	generateCopyNShare();
}

function getGlobalCNSConfig()
{
	try {
		odjs((("https:" == document.location.protocol) ? "https://wd-edge.sharethis.com/button/getDefault.esi?cb=cnsCallback" : "http://wd-edge.sharethis.com/button/getDefault.esi?cb=cnsCallback"));
	} catch(err){
		cnsCallback(err);
	}
}

/**
 * Converts given string to boolean.
 *
 * @param str 
 *   Which string to convert to boolean
 */
function to_boolean(str) {
	return str === true || jQuery.trim(str).toLowerCase() === 'true'; 
}

function cnsCallback(response) 
{
	if((response instanceof Error) || (response == "" || (typeof(response) == "undefined"))){
    	// Setting default config
    	response = '{"doNotHash": true, "doNotCopy": true, "hashAddressBar": false}';
    	response = jQuery.parseJSON(response);
    }
	
	//var obj = response;
	var obj = {
			doNotHash: to_boolean(response.doNotHash),
			doNotCopy: to_boolean(response.doNotCopy),
			hashAddressBar: to_boolean(response.hashAddressBar)
	};
	
	if(obj.doNotHash === false || obj.doNotHash === "false"){
    	if(obj.doNotCopy === true || obj.doNotCopy === "true"){
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).removeAttr("checked");
    	}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).attr("checked",true);
    	}
    	if(obj.hashAddressBar === true || obj.hashAddressBar === "true"){
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).attr("checked",true);
    	}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).removeAttr("checked");
    	}    		
	}else{
		jQuery('#st_cns_settings').find('input').each(function( index ){
			jQuery(this).removeAttr("checked");
		});
	}
}

function odjs(scriptSrc,callBack)
{
	this.head=document.getElementsByTagName('head')[0];
	this.scriptSrc=scriptSrc;
	this.script=document.createElement('script');
	this.script.setAttribute('type', 'text/javascript');
	this.script.setAttribute('src', this.scriptSrc);
	this.script.onload=callBack;
	this.script.onreadystatechange=function(){
		if(this.readyState == "complete" || (scriptSrc.indexOf("checkOAuth.esi") !=-1 && this.readyState == "loaded")){
			callBack();
		}
	};
	this.head.appendChild(this.script);
}

function makeHeadTag(){
	var val=jQuery('#st_pkey').val();
	var tag=jQuery('#st_widget').val();
	var reg=new RegExp("(\"*publisher\"*:)('|\")(.*?)('|\")",'gim');
	var b=tag.replace(reg,'$1$2'+val+'$4');
	jQuery('#st_widget').val(b);
}

function removeInterval(){
	if(time_interval == "undefined" || time_interval == null){
			
	}else{
		clearInterval(time_interval);
	}
}

function checkShareThisService(){
	 return jQuery.inArray("sharethis",st_selectedServicesList)
}

function setPullDownConfig(){
	if(st_selectedBarStyle == "pulldownStyle"){
		jQuery("#st_pulldownbar_scrollpx").val(jQuery("#selectScrollHeight_id").val());
		jQuery("#st_pulldownbar_logo").val(jQuery("#pulldown_optionsTextbox_id").val());
	}
}

function setHeaderValues(indexNumber){
	jQuery(".wp_st_navSlideDot").removeClass("wp_st_slideSelected");
	jQuery("#navDotSlide"+(indexNumber+1)).addClass("wp_st_slideSelected");
    jQuery("#wp_st_stepfooter").html("Step "+(indexNumber+1)+" of 6");
	jQuery(".wp_st_backTitle").html(headerInformation[indexNumber].back); 
	jQuery("#wp_st_slideTitle").html(headerInformation[indexNumber].title);
	jQuery(".wp_st_nextTitle").html(headerInformation[indexNumber].next);
}	


function generateCode(){
	makeTags();
	setHoverBarPosition();	
	setPullDownConfig();
}

function makeTags(){
	var tags = "";
	if(st_selectedButtonStyle != "_none" && st_selectedButtonStyle != ""){
		tags = generateSpanTags(st_btnType,st_selectedServicesList);
	}
	jQuery('#st_services').val(st_selectedServicesList);
	jQuery('#st_tags').val(tags);
}


function setHoverBarPosition(){
	jQuery("#st_hoverbar_position").val(st_hoverBarPosition);	
}

function generateSpanTags(type,svcList) {
	var buttonType =type;
	if (type != "_vcount" && type != "_hcount"  && type != "_large") {
	 buttonType = "";
	}
	var html = "";
	var a = 0;
	
	var str = jQuery('#st_tags_from_db').val().replace(/<\/span>(\n+)/g, "</span>");//Get saved span tags from database
	var obj = jQuery('<div/>').html(str).contents();//Convert string of span tags into object
	var arrClassName = new Array();
		
	for (var i=0; i<svcList.length; i++) {
		
		var title;
		if (stlib_picker._all_services[svcList[i]])
			title = stlib_picker._all_services[svcList[i]].title;
		else if (stlib_picker._all_native_services[svcList[i]])
			title = stlib_picker._all_native_services[svcList[i]].title;
		
		var spanTag = "<span";
		// Add extra info if native services
		if (svcList[i] == "fbsub") {
			if (st_socialPluginValues["fbsub_textbox"] && st_socialPluginValues["fbsub_textbox"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["fbsub_textbox"]+"'";
			}
		} else if (svcList[i] == "foursquarefollow") {
			if (st_socialPluginValues["foursquarefollow_textbox"] && st_socialPluginValues["foursquarefollow_textbox2"] && st_socialPluginValues["foursquarefollow_textbox"] != "" && st_socialPluginValues["foursquarefollow_textbox2"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["foursquarefollow_textbox"]+"' st_followId='"+st_socialPluginValues["foursquarefollow_textbox2"]+"'";
			}
		} else if (svcList[i] == "pinterestfollow") {
			if (st_socialPluginValues["pinterestfollow_textbox"] && st_socialPluginValues["pinterestfollow_textbox"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["pinterestfollow_textbox"]+"'";
			}
		} else if (svcList[i] == "instagram") {
			if (st_socialPluginValues["instagram_textbox"] && st_socialPluginValues["instagram_textbox"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["instagram_textbox"]+"'";
			}
		} else if (svcList[i] == "googleplusfollow" || svcList[i] == "googleplusadd") {
			if (jQuery("#"+svcList[i]+"_textbox").val() && jQuery("#"+svcList[i]+"_textbox").val() != "") {
				spanTag+=" st_followId='"+jQuery("#"+svcList[i]+"_textbox").val()+"'";
			}
		} else if (svcList[i] == "twitterfollow") {
			if (st_socialPluginValues["twitterfollow_textbox"] && st_socialPluginValues["twitterfollow_textbox"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["twitterfollow_textbox"]+"'";
			}
		} else if (svcList[i] == "youtube") {
			if (st_socialPluginValues["youtube_textbox"] && st_socialPluginValues["youtube_textbox"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["youtube_textbox"]+"'";
			}
		} else if (svcList[i] == "linkedinfollow") {
			if (jQuery("#"+svcList[i]+"_textbox").val() && jQuery("#"+svcList[i]+"_textbox").val() != "") {
				spanTag+=" st_followId='"+jQuery("#"+svcList[i]+"_textbox").val()+"'";
			}
		}else if (svcList[i] == "twitter"){
			if (st_socialPluginValues["twitter_via_textbox"] && st_socialPluginValues["twitter_via_textbox"] != "") {
				spanTag+=" st_via='"+st_socialPluginValues["twitter_via_textbox"]+"'";
			}	
				
			if (st_socialPluginValues["twitter_username_textbox"] && st_socialPluginValues["twitter_username_textbox"] != "") {
				spanTag+=" st_username='"+st_socialPluginValues["twitter_username_textbox"]+"'";	
			}	
		}
		
		if(jQuery('#st_current_type_from_db').val() != st_btnType) {
			if(jQuery.inArray( 'st_' + svcList[i] + buttonType, arrClassName ) <= 0) {
				spanTag+=" class='st_" + svcList[i] + buttonType + "' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>'></span>\n";
				arrClassName.push('st_' + svcList[i] + buttonType);
				//spanTag+=" class='st_" + svcList[i] + buttonType + "' ";
			} else {
				spanTag = "";
			}
		} else {
			if(i > 0) a += 2;
			spanTag+= st_modifiedSpanTag(obj[i], svcList[i], buttonType);
		}
		html += spanTag;
	}
	return html;
}

/*
Below function gets attributes from modified span tags saved in database
*/
function st_modifiedSpanTag(spanObj, servList, btnType) {
	var spanTags = '';
	
	//if((typeof spanObj) != "undefined" && spanObj.nodeName != 'SPAN') 
	//	spanObj = spanObj.nextSibling;
		
	if((typeof spanObj) != "undefined" && (typeof spanObj.attributes) != "undefined" && spanObj.attributes.length > 0) {
		for(var j=0;j<spanObj.attributes.length;j++) {
			if(spanObj.className.indexOf(servList) == -1) continue;
			if(jQuery.trim(spanObj.attributes[j].nodeName) == 'st_url' || 
				jQuery.trim(spanObj.attributes[j].nodeName) == 'st_title' || 
				jQuery.trim(spanObj.attributes[j].nodeName) == 'st_image' || 
				jQuery.trim(spanObj.attributes[j].nodeName) == 'st_summary' ||
				jQuery.trim(spanObj.attributes[j].nodeName) == 'st_msg' ||
				jQuery.trim(spanObj.attributes[j].nodeName) == 'st_native' ||
				jQuery.trim(spanObj.attributes[j].nodeName) == 'displayText' ||
				jQuery.trim(spanObj.attributes[j].nodeName) == 'class') {
				spanTags += " " + jQuery.trim(spanObj.attributes[j].nodeName) + "='" + jQuery.trim(spanObj.attributes[j].nodeValue) + "'";
			}
		}
	}
		
	if(spanTags == '')
		spanTags += " st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>' class='st_" + servList + btnType + "'";
		
	return spanTags+"></span>\n";
}

function checkAdditionalOptions(){
	var additonalServicesHtml = getAdditionalOptions(st_selectedServicesList);
	jQuery("#st_additional_options").html(additonalServicesHtml);
	jQuery("#st_additional_options").show();
}
 
function getAdditionalOptions(services){
	var html="",
		html1="",
		scheme = ("https:" == document.location.protocol) ? "https://ws" : "http://w";
		showSelectOptionTitle = false;
	html1="<h1 style='font-size:16px;'>Your Selected Options:</h1>"
	html1+="<ul class='wp_st_additional_opts_list'>";

	if(jQuery('#st_callesi').val() == 0){
		html+="<li><span style='position:relative;top:7px;left:12px;'>CopyNShare </span><span class='value'>Selected</span></li>";
		showSelectOptionTitle = true
	}	
   for(s=0;s<services.length;s++){
	 if(services[s] == "twitter"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_via_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Twitter Via </span><span class='value'>"+st_socialPluginValues[services[s]+"_via_textbox"]+"</span></li>";
			showSelectOptionTitle = true
		}
		if(jQuery.trim(st_socialPluginValues[services[s]+"_username_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Twitter Username </span><span class='value'>"+st_socialPluginValues[services[s]+"_username_textbox"]+"</span></li>";
			showSelectOptionTitle = true
		}
	  }else if(services[s]=="pinterestfollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Pinterest Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true
	   }
	  }else if (services[s] == "instagram"){
	  	if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Instagram Badge Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true			
		}
	  }else if (services[s] == "youtube"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Youtube Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true
		}	
	  }else if (services[s] == "linkedinfollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Linkedin Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true
		}	
	  }else if (services[s] == "twitterfollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Twitter Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true			
		}	
	  }else if (services[s] == "fbsub"){
	  if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Facebook Subscribe Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true
		}	
	  }else if (services[s] == "foursquaresave"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Foursquare Save Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true
		}	
	  }else if (services[s] == "foursquarefollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='"+scheme+".sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Foursquare Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
			showSelectOptionTitle = true			
		}	
	  } 
	}
	
	html+="</ul>";
	
	if(showSelectOptionTitle) {
		return html1 + html;
	}
	
	return html;
 }
 
function validateUserSelection(){
	if(st_selectedButtonStyle == "" && st_selectedBarStyle== ""){
		jQuery("#preview").show();
		jQuery("#preview").addClass("wp_st_error_message");
		jQuery("#preview").html("Please select any of the button style or bar style");
		location.href = "#wp_st_header";
		return false;
	} else {
		if(jQuery("#preview").hasClass("wp_st_error_message")){
			jQuery("#preview").removeClass("wp_st_error_message");
		}
		jQuery("#page_list_error").css('display', 'none');
		return true;
	}
}

function hideAll(){
	removeInterval();
	jQuery("#st_step1").hide();
	jQuery("#st_step2").hide();
	jQuery("#st_step3").hide();
	jQuery("#st_step4").hide();
	jQuery("#st_step5").hide();
	jQuery("#st_step6").hide();
	jQuery("#st_splServiceContainer").hide();
	jQuery("#wp_st_slidingContainer").hide();
	jQuery("#st_pulldownConfig").hide();
	jQuery("#wp_st_savebutton").hide();
	jQuery("#edit").hide();
}

function moveToPrevious(stepNumber){
	jQuery("#errorMessage").hide();
	if(stepNumber == 2){
		setPreviousValues("#st_step1",stepNumber);
		disableLeftArrow();
		setScrollpxHeight();	
	}else if(stepNumber == 3){
		setPreviousValues("#st_step2",stepNumber);
		setPageView();
	}else if(stepNumber == 4){
		setPreviousValues("#st_step3",stepNumber);
		checkSpecialServices();
		if(st_sharethisServiceIndex == -1){
			jQuery("#st_step3").hide();
		}
	}else if(stepNumber == 5){
		setPreviousValues("#st_step4",stepNumber);
		jQuery(".wp_st_nextText").html("Next : ");
		enableRightArrow();
	}else if(stepNumber == 6){
		setPreviousValues("#st_step5",stepNumber);
		enableRightArrow();
		checkForLoginCredentials();
	}
	
	if(stepNumber == 6 || stepNumber == 4)
		jQuery('#lastStep').css('padding-bottom', '20px');
	else
		jQuery('#lastStep').css('padding-bottom', '0px');	
}

function setPreviousValues(id,number){
	 hideAll();
	 jQuery(id).show();
	 setHeaderValues((number-2));
	 st_button_state = (number-1);
	 
	 if(st_button_state != 3) {
		jQuery('#addOptDiv').css('display','none');
		jQuery('#addOptDivSep').css('display','none');
		jQuery('#addOptions').css('border-top', 'none');
	 } else {
		jQuery('#addOptDiv').css('display','block');
		jQuery('#addOptDivSep').css('display','block');
		jQuery('#addOptions').css('border-top', '1px solid #DFDFDF');
	 }
}

function moveToNext(stepNumber){
	presentStepNumber = stepNumber;
	if(stepNumber == 1){
		var isBtnBarSelected = validateUserSelection();
		if(isBtnBarSelected == true){
			setNextValues("#st_step2",stepNumber);
			enableLeftArrow();
			setPageView();
		}
	}else if(stepNumber == 2){
		st_sharethisServiceIndex = checkShareThisService();
		setNextValues("#st_step3",stepNumber);
		UpdateSocialPluginValues();		
		checkSpecialServices();
		if(st_sharethisServiceIndex == -1){
				jQuery("#st_step3").hide();
		}
	}else if(stepNumber == 3){
		setNextValues("#st_step4",stepNumber);
		checkCopyNShare();
	}else if(stepNumber == 4){
		
		var isBtnBarSelected = validateUserSelection();
		if(isBtnBarSelected == true) {	
			setNextValues("#st_step5",stepNumber);
			jQuery(".wp_st_nextText").html("Almost Done : ");
			checkForLoginCredentials();
		}
	}else if(stepNumber == 5){
		checkAdditionalOptions();
		generateCode();
		setNextValues("#st_step6",stepNumber);
		jQuery("#wp_st_savebutton").show();
		jQuery("#wp_st_savebutton").attr("disabled", false);
		jQuery("#edit").show();
		disableRightArrow();
		
		gtc.getTheCode();
	}
	
	if(stepNumber == 5 || stepNumber == 3)
		jQuery('#lastStep').css('padding-bottom', '20px');
	else
		jQuery('#lastStep').css('padding-bottom', '0px');
}

function setNextValues(id,number){
	 hideAll();
	 jQuery(id).show();
	 setHeaderValues((number));
	 st_button_state = (number+1);
	 
	 if(st_button_state != 3) {
		jQuery('#addOptDiv').css('display','none');
		jQuery('#addOptDivSep').css('display','none');
		jQuery('#addOptions').css('border-top', 'none');
	 } else {
		jQuery('#addOptDiv').css('display','block');
		jQuery('#addOptDivSep').css('display','block');
		jQuery('#addOptions').css('border-top', '1px solid #DFDFDF');
	 }
}

//===============GET THE CODE
gtc = new function () {
	this.gtc_st_version = '';
	this.gtc_st_pubid = '';
	this.gtc_st_type = '';
	this.gtc_st_hoverbar_pos = '';
	this.gtc_st_twitter_via = '';
	this.gtc_st_instagram_username = '';
	this.gtc_st_services = '';
	this.gtc_st_hoverbar_services = '';
	this.gtc_st_pulldownbar_scrollpx = '';
	this.gtc_st_pulldownbar_logo = '';
	this.gtc_st_pulldown_services = '';
	this.gtc_st_pulldownbar_logo = '';
	this.gtc_st_current_type = '';
	this.gtc_st_selected_bar = '';
	this.gtc_st_copyAndShare = '';
	
	this.initGetTheCode = function(){
		this.gtc_st_version = this.clearString(jQuery('#st_version').val());
		this.gtc_st_pubid = this.clearString(jQuery('#st_pkey').val());
		this.gtc_st_type = this.clearString(jQuery('#st_type').val());
		this.gtc_st_hoverbar_pos =this.clearString(jQuery('#st_hoverbar_position').val());
		this.gtc_st_twitter_via = this.clearString(jQuery('#twitter_via_textbox').val());
		this.gtc_st_instagram_username = this.clearString(jQuery('#instagram_textbox').val());
		this.gtc_st_services = this.clearString(jQuery('#st_services').val());
		this.gtc_st_hoverbar_services = this.clearString(jQuery('#st_hoverbar_services').val());
		this.gtc_st_pulldownbar_scrollpx = this.clearString(jQuery('#st_pulldownbar_scrollpx').val());
		this.gtc_st_pulldownbar_logo = this.clearString(jQuery('#pulldown_optionsTextbox_id').val());
		this.gtc_st_pulldown_services = this.clearString(jQuery('#st_pulldownbar_services').val());
		this.gtc_st_pulldownbar_logo = this.clearString(jQuery('#st_pulldownbar_logo').val());
		this.gtc_st_current_type = this.clearString(jQuery('#st_current_type').val());
		this.gtc_st_selected_bar = this.clearString(jQuery('#st_selected_bar').val());
			this.gtc_st_copyAndShare = this.clearString(jQuery('#copynshareSettings').val());
	};
	
	this.getSelectedServices = function(selServiceString) {
		arrServices = selServiceString.split(',');
		strServices = '';
		
		if((typeof arrServices) != "undefined") {
			for(var i=0;i<arrServices.length;i++) {
				if(strServices != '')
					strServices += '", "';
					
				strServices += arrServices[i];
			}
		}
		
		return '"'+strServices+'"';
	};
	
	this.getTheCode = function() {
		var scriptCode = '';
		var optionType = 'chickletStyle';
		var styleType = '';
		
		this.initGetTheCode();
		
		if((typeof this.gtc_st_current_type) != undefined && this.gtc_st_current_type.length > 0) {
			if('_hcount' == this.gtc_st_current_type)
				optionType = 'hcountStyle';
			else if('_vcount' == this.gtc_st_current_type)
				optionType = 'vcountStyle';	
			else
				optionType = 'chickletStyle';
		} else
			optionType = 'chickletStyle';
		
		if((typeof this.gtc_st_selected_bar) != undefined && this.gtc_st_selected_bar.length > 0) {
			if('hoverbarStyle' == this.gtc_st_selected_bar)
				styleType = 'hoverbarStyle';
			else if('pulldownStyle' == this.gtc_st_selected_bar)
				styleType = 'pulldownStyle';
		}
		
		scriptCode = this.createCode(optionType, styleType);
		jQuery('#st_widget').val(scriptCode);
	};
	
	this.checkProtocolOptions = function(srcElement) {
		var matches = srcElement.match(/https|http/);
		if(matches[0] == "https") {
			jQuery('#typehttps').attr('checked', 'checked');
			jQuery('#typehttp').removeAttr('checked');
		} else {
			jQuery('#typehttp').attr('checked', 'checked');
			jQuery('#typehttps').removeAttr('checked');
		}
		return matches[0];
	};
	
	this.parseBarOptions = function(scriptTagObj, barOptionType) {
		for(var i=0;i<scriptTagObj.length;i++) {				
			var scriptTag = scriptTagObj[i].innerHTML.replace(/\\"/g,'"');
			var arrScriptTag = scriptTag.split(';');
			
			if(arrScriptTag.length > 2) {
				var pattern = new RegExp(barOptionType);
				if(arrScriptTag[0].match(pattern)) {
					var temp = arrScriptTag[0].split('=');
					return jQuery.parseJSON(JSON.stringify(eval('('+temp[1]+')')));
				}
			}			
		}
	};
	
	this.getScriptTagObj = function(elemId) {
		var str = jQuery('#'+elemId).val().replace(/(\n+)/g, "");
		var obj;
		if(str.match(/\\"/g))
			obj = jQuery('<div/>').html(str).contents();//Convert string of span tags into object	
		else
			obj = jQuery('<div/>').html(str.replace(/"/g,'\\"')).contents();//Convert string of span tags into object	
			
		return obj;
	};
	
	this.getBarOptions = function(styleType) {
		var objEditBoxBarOptions;
		var objDBBarOptions;
		
		var scriptTagEditBoxObj = this.getScriptTagObj('st_widget');
		var scriptTagDBObj = this.getScriptTagObj('st_script_tags_from_db');
			
		if('hoverbarStyle' == styleType) {
			objEditBoxBarOptions = this.parseBarOptions(scriptTagEditBoxObj, "h_options");
			objDBBarOptions = this.parseBarOptions(scriptTagDBObj, "h_options");		
			
			if((typeof objEditBoxBarOptions) != "undefined") {
				objEditBoxBarOptions.position = this.gtc_st_hoverbar_pos;
				
				if((typeof objDBBarOptions) != "undefined" && objEditBoxBarOptions.chicklets_params.instagram.st_username != objDBBarOptions.chicklets_params.instagram.st_username)
					objEditBoxBarOptions.chicklets_params.instagram.st_username = this.gtc_st_instagram_username;
				if((typeof objDBBarOptions) != "undefined" && objEditBoxBarOptions.chicklets_params.twitter.st_via != objDBBarOptions.chicklets_params.twitter.st_via)
					objEditBoxBarOptions.chicklets_params.instagram.st_username = this.gtc_st_twitter_via;
				if(st_selectedServicesList.length != objEditBoxBarOptions.chicklets.items.length) {
					if (st_selectedServicesList instanceof Array) {
						var newServicesCounter=0;	
						var newselectedServicesArray = new Array();
						
						for(var i=0; i<st_selectedServicesList.length; i++){
							if(st_selectedServicesList[i] != 'plusone' && st_selectedServicesList[i] != 'fblike' && st_selectedServicesList[i] != 'fbrec'&& st_selectedServicesList[i] != 'fbsend'&& st_selectedServicesList[i] != 'fbsub'&& st_selectedServicesList[i] != 'foursquaresave'&& st_selectedServicesList[i] != 'foursquarefollow'&& st_selectedServicesList[i] != 'youtube'&& st_selectedServicesList[i] != 'pinterestfollow'&&
							st_selectedServicesList[i] != 'twitterfollow') {
								newselectedServicesArray[newServicesCounter] = st_selectedServicesList[i];
								newServicesCounter++;
							}
						}
						objEditBoxBarOptions.chicklets.items = newselectedServicesArray;
					}else{
						objEditBoxBarOptions.chicklets.items = st_selectedServicesList;
					}	
				}	
					
				
			}
			return objEditBoxBarOptions;
		} else if('pulldownStyle' == styleType) {
			objEditBoxBarOptions = this.parseBarOptions(scriptTagEditBoxObj, "p_options");
			objDBBarOptions = this.parseBarOptions(scriptTagDBObj, "p_options");		
			
			if((typeof objEditBoxBarOptions) != "undefined") {
				if((typeof objDBBarOptions) != "undefined" && objEditBoxBarOptions.scrollpx != objDBBarOptions.scrollpx)
					objEditBoxBarOptions.scrollpx = this.gtc_st_pulldownbar_scrollpx;
				else if(objEditBoxBarOptions.scrollpx != jQuery('#selectScrollHeight_id').val() && jQuery('#selectScrollHeight_id').val() != "") {
					objEditBoxBarOptions.scrollpx = jQuery('#selectScrollHeight_id').val();
					jQuery('#st_pulldownbar_scrollpx').val(jQuery('#selectScrollHeight_id').val());
				} 
				if(st_selectedServicesList.length != objEditBoxBarOptions.chicklets.items.length) {
					if (st_selectedServicesList instanceof Array) {
						var newServicesCounter=0;	
						var newselectedServicesArray = new Array();
						
						for(var i=0; i<st_selectedServicesList.length; i++){
							if(st_selectedServicesList[i] != 'plusone' && st_selectedServicesList[i] != 'fblike' && st_selectedServicesList[i] != 'fbrec'&& st_selectedServicesList[i] != 'fbsend'&& st_selectedServicesList[i] != 'fbsub'&& st_selectedServicesList[i] != 'foursquaresave'&& st_selectedServicesList[i] != 'foursquarefollow'&& st_selectedServicesList[i] != 'youtube'&& st_selectedServicesList[i] != 'pinterestfollow'&&
							st_selectedServicesList[i] != 'twitterfollow'&& st_selectedServicesList[i] != 'instagram') {
								newselectedServicesArray[newServicesCounter] = st_selectedServicesList[i];
								newServicesCounter++;
							}
						}
						objEditBoxBarOptions.chicklets.items = newselectedServicesArray;
					}else{
						objEditBoxBarOptions.chicklets.items = st_selectedServicesList;
					}	
					
				}
			}
			return objEditBoxBarOptions;
		}
		
		if("sharebar" == styleType) {
			return objEditBoxBarOptions;
		}
	};
	
	this.createCode = function(optionType, styleType, isSharebarSelected) {
		var switchTo5x = 'true';
		var jsScriptCode = '';
		var copyAndShare = '';
		var temp = '';
		var selected = jQuery("input[name='protocolType']:checked");
		var barOpt;
		var objStlightOpt;
		
		if('5x' != this.gtc_st_version)
			switchTo5x = 'false';
		
		if('' != this.gtc_st_copyAndShare)
			copyAndShare = this.gtc_st_copyAndShare;
			
		//===================================	
		var str1 = jQuery('#st_widget').val().replace(/(\n+)/g, "");
		var scriptTagEditBoxObj1;
		if(str1.match(/\\"/g))
			scriptTagEditBoxObj1 = jQuery('<div/>').html(str1).contents();//Convert string of span tags into object	
		else
			scriptTagEditBoxObj1 = jQuery('<div/>').html(str1.replace(/"/g,'\\"')).contents();//Convert string of span tags into object

		for(var i=0;i<scriptTagEditBoxObj1.length;i++) {
			var scriptTag1 = scriptTagEditBoxObj1[i].innerHTML.replace(/\\"/g,'"');
			var arrScriptTag1 = scriptTag1.split(';');
			
			if(arrScriptTag1.length > 2) {
				var pattern1 = new RegExp("stLight.options\\((.*)\\)");
				var matches = arrScriptTag1[0].match(pattern1);
				if(matches) {
					if(copyAndShare != "")
						temp = matches[1].replace(/\}/, copyAndShare + "}");
					else
						temp = matches[1];
					objStlightOpt = jQuery.parseJSON(JSON.stringify(eval('('+temp+')')));
				}
			}			
		}
		
		//==================================
		
		jsScriptCode += '<script charset="utf-8" type="text/javascript">var switchTo5x='+switchTo5x+';</script>\n';
		if(selected[0].value == "https")
			jsScriptCode += '<script charset="utf-8" type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>\n';
		else
			jsScriptCode += '<script charset="utf-8" type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>\n';
		
		if(this.gtc_st_current_type == '_none')
			objStlightOpt = null;
			
		if(objStlightOpt) {
			objStlightOpt.publisher = this.gtc_st_pubid;
			jsScriptCode += '<script charset="utf-8" type="text/javascript">stLight.options('+JSON.stringify(objStlightOpt)+');var st_type="'+this.gtc_st_type+'";</script>\n';
		} else if(!objStlightOpt && this.gtc_st_current_type != '_none')
			jsScriptCode += '<script charset="utf-8" type="text/javascript">stLight.options({"publisher":"'+this.gtc_st_pubid+'"});var st_type="'+this.gtc_st_type+'";</script>\n';
		
		if('hoverbarStyle' == styleType || 'pulldownStyle' == styleType) {
			if(selected[0].value == "https")
				jsScriptCode += '<script charset="utf-8" type="text/javascript" src="https://ss.sharethis.com/loader.js"></script>\n';
			else
				jsScriptCode += '<script charset="utf-8" type="text/javascript" src="http://s.sharethis.com/loader.js"></script>\n';
		}
		
		if('hoverbarStyle' == styleType || 'pulldownStyle' == styleType) {
			barOpt = this.getBarOptions(styleType);
			if('hoverbarStyle' == styleType) {
				jsScriptCode += '<script charset="utf-8" type="text/javascript">\n';
				if((typeof barOpt) == "undefined")
					barOpt = '{ "publisher":"'+this.gtc_st_pubid+'", "position": "'+this.gtc_st_hoverbar_pos+'", "chicklets_params": {"twitter":{"st_via":"'+this.gtc_st_twitter_via+'" }, "instagram" :{"st_username":"'+this.gtc_st_instagram_username+'" } }, "chicklets": { "items": ['+this.getSelectedServices(this.gtc_st_hoverbar_services)+'] } }\n';
				else {
					barOpt.publisher = this.gtc_st_pubid;
					barOpt = JSON.stringify(barOpt);			
				}
				jsScriptCode += 'var h_options=' + barOpt;
				jsScriptCode += ';var st_hover_widget = new sharethis.widgets.hoverbuttons(h_options);\n';
			} else {
				jsScriptCode += '<script charset="utf-8" type="text/javascript">\n';
				if((typeof barOpt) == "undefined") {
					var logoStr = '';
					barOpt = '{ "publisher": "'+this.gtc_st_pubid+'", "scrollpx": "'+this.gtc_st_pulldownbar_scrollpx+'", "ad": { "visible": false}, "chicklets": { "items": ['+this.getSelectedServices(this.gtc_st_pulldown_services)+']} '+logoStr+'}\n';
				} else {
					barOpt.publisher = this.gtc_st_pubid;
					barOpt = JSON.stringify(barOpt);
				}
				jsScriptCode += 'var p_options=' + barOpt;
				jsScriptCode += ';var st_pulldown_widget = new sharethis.widgets.pulldownbar(p_options);\n';
			}
				
			jsScriptCode += '</script>\n';
		} 
		
		return jsScriptCode;
	};
	
	this.clearString = function(val) {
		return jQuery.trim(val);
	};
}

function setPostExcerpt() {
	//	Disable the post excerpt checkbox if buttons are not selected on top/bottom
	if (!document.getElementById('st_posts_on_top').checked && !document.getElementById('st_posts_on_bot').checked) {
		document.getElementById('st_post_excerpt').disabled = true;
	}else{
		document.getElementById('st_post_excerpt').disabled = false;
	}
}