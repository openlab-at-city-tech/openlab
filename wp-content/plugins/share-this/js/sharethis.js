var headerInformation = [{"back":"","title":"1. Choose Buttons and Options","next":"Select Services"},
{"back":"Button Styles","title":"2. Select Social Channels","next":"Sharing Method"},{"back":"Social Channels","title":"3. Choose a Sharing Method","next":"Additional Features"},{"back":"Sharing Method","title":"4. Additional Features","next":"Get your publisher key!"},{"back":"Additional Features","title":"5. Sign In","next":"Final step"},{"back":"Sign In","title":"6. Your Current Configuration","next":""}];

var st_selectedServicesList = [];
// page should be set up only once
var st_widgetVersion;

var st_selectedButtonStyle ="";
var st_isSmallChickletSelected = false;
var st_selectedBarStyle ="";
var st_isShareNowSelected = false;
var st_hoverBarPosition = "left";

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
	removeInterval();
	jQuery('#st_cns_settings').find('input').live('click', updateDoNotHash);
	if(jQuery('#st_callesi').val() == 1){
		getGlobalCNSConfig();
	}
	submitForm();
	PLUGIN_PATH = st_script_vars.plugin_url;
	window.onload  = function (){
		windowLoaded();
	}
});

function submitForm(){
	jQuery("#ak_sharethis").submit(function(event) {
		event.preventDefault();
		var getform = jQuery( this ),
		url = getform.attr('action');
		
		var postdata = jQuery.post(url, getform.serialize());

		postdata.done(function( data ) {
			jQuery('html, body').animate({scrollTop: '0px'}, 0);
			jQuery("#st_updated").show();
			jQuery('#st_updated').delay(2000).fadeOut();
	  });
	  return false;
	});
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
	jQuery("#st_customize_sharenow").hide();
	jQuery("#st_configure_pulldown").hide();
	disableLeftArrow();	
	
	jQuery(".wp_st_navSlideDot").click(function(){
		var isBtnBarSelected = validateUserSelection();
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
	* Retrive sharenow value from database
	*/
	checkShareNow();
	
	/**
	* Retrive copynshare configuration from database
	*/
	checkCopyNShare();
	
	/**
	* Click handler for sharenow
	*/
	jQuery(".jqShareNow").bind('click',function(event){
		if(flgLoaderCompleted == true){
			if(jQuery(".jqShareNow").hasClass("selected")){
				removeShareNow();
				jQuery(".jqShareNow").removeClass("selected");
				st_isShareNowSelected = false;
				jQuery("#st_customize_sharenow").hide();
				jQuery("#wp_st_slidingContainer").hide();
			}else{
				selectShareNow();
			}
			checkHoverBar();			
		}
		checkHoverBar();
	});
	
	jQuery("#st_customize_sharenow").click(function(){
		jQuery("#st_pulldownConfig").hide();
		jQuery("#wp_st_slidingContainer").toggle("slow");
		location.href = "#wp_st_slidingContainer";
	});
		
	jQuery(".wp_st_sharenowImg").click(function(){
		sharenow.stgOptions.style = jQuery(this).attr('data-value');
		jQuery("#st_sharenow_theme").val(jQuery(this).attr('data-value'));
	});
	
	/**
	* Sharing button hover and out functionality 
	*/
	jQuery(".wp_st_styleLink").mouseover(function () {
		if(jQuery(this).hasClass('jqBtnStyle')){
			changeHoverView(this, 'over');
		}else if((flgLoaderCompleted == true) && (jQuery(this).hasClass('hoverbarStyle') || jQuery(this).hasClass('pulldownStyle') || jQuery(this).hasClass('fbStyle'))){
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
		if(event.target.id == "st_5xwidget"){
			st_widgetVersion = "5x";
		}else if(event.target.id == "st_4xwidget"){
			st_widgetVersion = "4x";
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
		
		var matches2=tags.match(/st_username='(\w*)' class='(st_twitter\w*)'/); 
		if (matches2!=null && typeof(matches2[1])!="undefined"){
			st_socialPluginValues["twitter_username_textbox"] = matches2[1];
		} 
		
		var matchInstagram = tags.match(/st_username='(\w*)' class='(st_instagram\w*)'/);
		if(matchInstagram != null && typeof(matchInstagram[1]) != "undefined"){
			st_socialPluginValues["instagram_textbox"] = matchInstagram[1];
		}
		
		var matchFbSubscribe = tags.match(/st_username='(\w*)' class='(st_fbsub\w*)'/);
		if(matchFbSubscribe != null && typeof(matchFbSubscribe[1]) != "undefined"){
			st_socialPluginValues["fbsub_textbox"] = matchFbSubscribe[1];
		}
		
		var matchTwFollow = tags.match(/st_username='(\w*)' class='(st_twitterfollow\w*)'/);
		if(matchTwFollow != null && typeof(matchTwFollow[1]) != "undefined"){
			st_socialPluginValues["twitterfollow_textbox"] = matchTwFollow[1];
		}
		
		var matchPinFollow = tags.match(/st_username='(\w*)' class='(st_pinterestfollow\w*)'/);
		if(matchPinFollow != null && typeof(matchPinFollow[1]) != "undefined"){
			st_socialPluginValues["pinterestfollow_textbox"] = matchPinFollow[1];
		}
		
		var matchFSFollow = tags.match(/st_username='(\w*)' st_followId='(\w*)' class='(st_foursquarefollow\w*)'/);
		if(matchFSFollow != null && typeof(matchFSFollow[1]) != "undefined"){
			st_socialPluginValues["foursquarefollow_textbox"] = matchFSFollow[1];
			st_socialPluginValues["foursquarefollow_textbox2"] = matchFSFollow[2];
		}
		
		var matchYTSubscribe = tags.match(/st_username='(\w*)' class='(st_youtube\w*)'/);
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
	var url = "http://sharethis.com/get-publisher-info.php?callback=parsePublisherInfo";
	var script = document.createElement('script');
	script.setAttribute('src', url);
	document.getElementsByTagName('head')[0].appendChild(script); 
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
	
function checkShareNow(){
	var tag=jQuery('#st_widget').val();
	if (tag.match(/serviceWidget/)){
		flgLoaderCompleted = true;
		selectShareNow();
		checkHoverBar();
	}	
}

function selectShareNow(){
	jQuery(".jqShareNow").addClass("selected");
	st_isShareNowSelected = true;
	st_hoverBarPosition = "right";
	scriptLoading("fbStyle");
}	

function checkHoverBar(){
	jQuery("#st_sharenow_selected").val(st_isShareNowSelected);
	if(st_selectedBarStyle == "hoverbarStyle"){
		checkHoverBarPosition();	
	}
}

function checkHoverBarPosition(){
	if(st_isShareNowSelected == true){
		hoverbuttons.stgOptions.position = "right";
		var radiobuttons = jQuery('#hoverbar_selectDock input:radio');
		for(var i=0; i<radiobuttons.length; i++){
			radiobuttons[i].checked = false;
			radiobuttons[i].disabled = true;
			if(radiobuttons[i].value == "right"){
				radiobuttons[i].checked = true;
			}
		}
		if(jQuery("#sthoverbuttons").hasClass("sthoverbuttons-pos-left")){
			jQuery("#sthoverbuttons").removeClass("sthoverbuttons-pos-left");
			jQuery("#sthoverbuttons").addClass("sthoverbuttons-pos-right");
		}
	}else{
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
}


function removeShareNow() {
	jQuery('#stservicewidget').remove();
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
		jQuery.getScript("http://sharethis.com/js/new/get-hoverbuttons-new.js",function(data){ 
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
				},1000);
			}
			flgLoaderCompleted = true;
			removePulldownbar();
		},'script');
	  }else if(barStyle=="pulldownStyle"){
		jQuery('#pullDownBarImage').hide();
		jQuery("#pulldownLoadingImg").show();
		jQuery.getScript("http://sharethis.com/js/new/get-pulldown-new.js",function(data){ 
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
				},1000);
				jQuery("#pulldownLoadingImg").hide();
				jQuery('#pullDownBarImage').show();
			}
			flgLoaderCompleted = true;
			removeHoverbar();
		},'script'
		);
	  }
	  if(barStyle=="fbStyle"){
		jQuery('#shareNowImage').hide();
		jQuery("#sharenowLoadingImg").show();
		jQuery.getScript("http://sharethis.com/js/new/get-sharenow-new.js",function(data){ 
			sharenow.stgOptions.style = jQuery("#st_sharenow_theme").val();
			var st_service_widget = new sharethis.widgets.serviceWidget(sharenow.stgOptions);
			jQuery("#st_customize_sharenow").show();
			jQuery("#themeList").find("#st_sharenowImg"+jQuery("#st_sharenow_theme").val()).addClass("selected");
			selectStyle("fbStyle");
			try{
				stServiceWidget = new sharethis.widgets.serviceWidget.framework(); // after serviceWidget.js is loaded.
				jQuery("#sharenowLoadingImg").hide();
				jQuery('#shareNowImage').show();
			}catch (e) {
				setTimeout(function(){
					stServiceWidget = new sharethis.widgets.serviceWidget.framework(); // after serviceWidget.js is loaded.
				},1000);
				jQuery("#sharenowLoadingImg").hide();
				jQuery('#shareNowImage').show();
			}
			flgLoaderCompleted = true;
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
	removePulldownbar()
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
			html += "<label class='leftLabel'>Instagram badge username</label>";
			html += "<input id='instagram_textbox' type='textbox' value='"+st_socialPluginValues["instagram_textbox"]+"' name='instagram[username]' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "googleplusadd") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Google add profile id</label>";
			html += "<input id='googleplusadd_textbox' type='textbox' value='' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: '113842823840690472625'</label>";
			html += "</div>";
		} else if (services[s] == "googleplusfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Google follow page id</label>";
			html += "<input id='googleplusfollow_textbox' type='textbox' value='' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: '110924060789171264851'</label>";
			html += "</div>";
		} else if (services[s] == "youtube") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Youtube subscribe username</label>";
			html += "<input id='youtube_textbox' type='textbox' value='"+st_socialPluginValues["youtube_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "linkedinfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>LinkedIn follow profile id</label>";
			html += "<input id='linkedinfollow_textbox' type='textbox' value='' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter '207839' for profile id</label>";
			html += "</div>";
		} else if (services[s] == "twitterfollow") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Twitter follow username</label>";
			html += "<input id='twitterfollow_textbox' type='textbox' name='twitterfollow[via]' value='"+st_socialPluginValues["twitterfollow_textbox"]+"' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
		} else if (services[s] == "fbsub") {
			html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
			html += "<label class='leftLabel'>Facebook subscribe username</label>";
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
			html += "<label class='leftLabel'>Foursquare follow username</label>";
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
			html += "<label class='leftLabel'>Twitter via username</label>";
			html += "<input id='twitter_via_textbox' type='textbox' value='"+st_socialPluginValues["twitter_via_textbox"]+"' name='twitter[via]' data-value=''";
			if (callback) {
				html += " onblur='"+callback+"'";
			}
			html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
			html += "</div>";
			
			if(st_btnType != "_none"){
				html += "<div class='wp_st_buttonCodeGeneratorConfig'>";
				html += "<label class='leftLabel'>Twitter account username</label>";
				html += "<input id='twitter_username_textbox' type='textbox' value='"+st_socialPluginValues["twitter_username_textbox"]+"' name='twitter[username]' data-value=''";
				if (callback) {
					html += " onblur='"+callback+"'";
				}
				html += "><label class='wp_st_defaultCursor'>Example: Enter 'sharethis' for username</label>";
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
			retval += ', doNotCopy: false';
		}else{
			retval += ', doNotCopy: true';
		}
		if(jQuery(jQuery('#st_cns_settings').find('input')[1]).is(':checked')){
			retval += ', hashAddressBar: true';
		}else{
			retval += ', hashAddressBar: false';
		}
		
		if(jQuery(jQuery('#st_cns_settings').find('input')[0]).is(':checked') || jQuery(jQuery('#st_cns_settings').find('input')[1]).is(':checked')){
			retval += ', doNotHash: false';
		}else{
			retval += ', doNotHash: true';
		}
	}
	return retval;
}

function checkCopyNShare(){	
	var tag=jQuery('#st_widget').val();
	if (tag.match(/doNotHash:(\s)*false/)){
		if (tag.match(/doNotCopy:(\s)*false/)){
			jQuery(jQuery('#st_cns_settings').find('input')[0]).attr("checked","checked").val(true);;
		}else{
    		jQuery(jQuery('#st_cns_settings').find('input')[0]).removeAttr("checked").val(false);
    	}
		if (tag.match(/hashAddressBar:(\s)*false/)){
    		jQuery(jQuery('#st_cns_settings').find('input')[1]).removeAttr("checked").val(false);
		}else{
			jQuery(jQuery('#st_cns_settings').find('input')[1]).attr("checked","checked").val(true);;
    	}
	}else if (tag.match(/doNotHash:(\s)*true/)){
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
	if(st_isShareNowSelected == true){
		jQuery("#st_hoverbar_position").val("right");
	}else{
		jQuery("#st_hoverbar_position").val(st_hoverBarPosition);
	}
}

function generateSpanTags(type,svcList) {
	var buttonType =type;
	if (type != "_vcount" && type != "_hcount"  && type != "_large") {
	 buttonType = "";
	}
	var html = "";
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
		spanTag+=" class='st_" + svcList[i] + buttonType + "' st_title='<?php the_title(); ?>' st_url='<?php the_permalink(); ?>'></span>";
		html += spanTag;
	}
	return html;
}

function checkAdditionalOptions(){
	var additonalServicesHtml = getAdditionalOptions(st_selectedServicesList);
	jQuery("#st_additional_options").html(additonalServicesHtml);
	jQuery("#st_additional_options").show();
}
 
 function getAdditionalOptions(services){
	var html="";
	html="<h1>Your Additional Selected Options:</h1>"
	html+="<ul class='wp_st_additional_opts_list'>";
	if(st_widgetVersion != ""){
		var sharingMethod;
		if(st_widgetVersion == "5x"){
			sharingMethod = "Multi-Post";
		}else if(st_widgetVersion == "4x"){
			sharingMethod = "Classic";
		}
		html+="<li><span style='position:relative;top:7px;left:12px;'>Sharing Method</span><span class='value'>"+sharingMethod+"</span></li>";
	}
	if(jQuery('#st_callesi').val() == 0){
		html+="<li><span style='position:relative;top:7px;left:12px;'>CopyNShare </span><span class='value'>Selected</span></li>";
	}	
   for(s=0;s<services.length;s++){
	 if(services[s] == "twitter"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_via_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Twitter Via </span><span class='value'>"+st_socialPluginValues[services[s]+"_via_textbox"]+"</span></li>";
		}
		if(jQuery.trim(st_socialPluginValues[services[s]+"_username_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Twitter Username </span><span class='value'>"+st_socialPluginValues[services[s]+"_username_textbox"]+"</span></li>";
		}
	  }else if(services[s]=="pinterestfollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Pinterest Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
	   }
	  }else if (services[s] == "instagram"){
	  	if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Instagram Badge Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}
	  }else if (services[s] == "youtube"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Youtube Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}	
	  }else if (services[s] == "linkedinfollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Linkedin Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}	
	  }else if (services[s] == "twitterfollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Twitter Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}	
	  }else if (services[s] == "fbsub"){
	  if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Facebook Subscribe Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}	
	  }else if (services[s] == "foursquaresave"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Foursquare Save Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}	
	  }else if (services[s] == "foursquarefollow"){
		if(jQuery.trim(st_socialPluginValues[services[s]+"_textbox"]) != ""){
			html+="<li><span class='wp_st_alignPluginIcons'><img src='http://w.sharethis.com/images/"+services[s]+"_32.png'></img></span><span class='label'>Foursquare Follow Username</span><span class='value'>"+st_socialPluginValues[services[s]+"_textbox"]+"</span></li>";
		}	
	  } 
	}
	html+="</ul>";
	return html;
 }
 
function validateUserSelection(){
	if(st_selectedButtonStyle == "" && st_selectedBarStyle== "" && st_isShareNowSelected == false){
		jQuery("#preview").show();
		jQuery("#preview").addClass("wp_st_error_message");
		jQuery("#preview").html("Please select any of the button style or bar style");
		location.href = "#wp_st_header";
		return false;
	}else{
		if(jQuery("#preview").hasClass("wp_st_error_message")){
			jQuery("#preview").removeClass("wp_st_error_message");
		}
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
	if(stepNumber == 2){
		setPreviousValues("#st_step1",stepNumber);
		disableLeftArrow();
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
}

function setPreviousValues(id,number){
	 hideAll();
	 jQuery(id).show();
	 setHeaderValues((number-2));
	 st_button_state = (number-1);
}

function moveToNext(stepNumber){
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
		checkSpecialServices();
		if(st_sharethisServiceIndex == -1){
				jQuery("#st_step3").hide();
		}
	}else if(stepNumber == 3){
		setNextValues("#st_step4",stepNumber);
	}else if(stepNumber == 4){
		setNextValues("#st_step5",stepNumber);
		jQuery(".wp_st_nextText").html("Almost Done : ");
		checkForLoginCredentials();
	}else if(stepNumber == 5){
		checkAdditionalOptions();
		generateCode();
		setNextValues("#st_step6",stepNumber);
		jQuery("#wp_st_savebutton").show();
		jQuery("#wp_st_savebutton").attr("disabled", false);
		jQuery("#edit").show();
		disableRightArrow();
	}
}

function setNextValues(id,number){
	 hideAll();
	 jQuery(id).show();
	 setHeaderValues((number));
	 st_button_state = (number+1);
}

