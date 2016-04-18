(function() {

	// Definition array to hold the individual elements 
	// connected to the various modules of the plugin

	var menu = new Array(); // generale
	var plus = new Array(); // google plus
	var cale = new Array(); // google calendar
	var driv = new Array(); // google drive
	var grou = new Array(); // google groups
	var hang = new Array(); // google hangouts
	var maps = new Array(); // google maps
	var pano = new Array(); // google panoramio
	var tran = new Array(); // google translate
	var yout = new Array(); // google youtube

	var Wmax;

	// I read all the divisions within the wrapper and take the hidden 
	// value of the class as shotcode name as the title and content

	jQuery("#sz-google-hidden-shortcodes div").each(function(index) {

		var shortcode   = jQuery(this).attr('class');
		var width       = jQuery(this).data('width');
		var height      = jQuery(this).data('height');
		var description = jQuery(this).data('description');

		var funzione = function() { 
			win = Wmax.windowManager.open({
				title:description,
				url:ajaxurl + '?action=sz_google_shortcodes&amp;shortcode=' + encodeURIComponent(shortcode) + '&amp;title=' + encodeURIComponent(description),
				width:width,
				height:height,
				inline:1,
				popup_css:false
			});
		};

		if (shortcode.substr(0,8)  == 'sz-gplus')      plus.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,11) == 'sz-calendar')   cale.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,8)  == 'sz-drive')      driv.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,10) == 'sz-ggroups')    grou.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,11) == 'sz-hangouts')   hang.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,7)  == 'sz-maps')       maps.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,12) == 'sz-panoramio')  pano.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,13) == 'sz-gtranslate') tran.push({ text: description, onclick: funzione });
		if (shortcode.substr(0,5)  == 'sz-yt')         yout.push({ text: description, onclick: funzione });
	});

	// Control arrays that contain at least one element and create 
	// the main menu with the corresponding translation of the title

	var selected = false;

	if (plus.length != 0) { menu[0] = { text: 'Google+'         , menu: plus }; selected = true; };
	if (cale.length != 0) { menu[1] = { text: 'Google Calendar' , menu: cale }; selected = true; };
	if (driv.length != 0) { menu[2] = { text: 'Google Drive'    , menu: driv }; selected = true; };
	if (grou.length != 0) { menu[3] = { text: 'Google Groups'   , menu: grou }; selected = true; };
	if (hang.length != 0) { menu[4] = { text: 'Google Hangouts' , menu: hang }; selected = true; };
	if (maps.length != 0) { menu[5] = { text: 'Google Maps'     , menu: maps }; selected = true; };
	if (pano.length != 0) { menu[6] = { text: 'Google Panoramio', menu: pano }; selected = true; };
	if (tran.length != 0) { menu[7] = { text: 'Google Translate', menu: tran }; selected = true; };
	if (yout.length != 0) { menu[8] = { text: 'Google Youtube'  , menu: yout }; selected = true; };

	// Add the button with menus that are created only if there is at 
	// least one element selected, otherwise I do not know the menu

	if (selected == true) {
		tinymce.PluginManager.add('sz_google_mce_button',function(editor,url) {
			Wmax = editor;
			editor.addButton('sz_google_mce_button', {
				text: '',
				icon: 'icon dashicons-editor-code" style="font-family:dashicons;font-size:1.6em',
				type: 'menubutton',
				menu: menu,
			});
		});
	}
})();