<?php 
function watupro_modules() {
	global $wp_filesystem;
	
	$form_url = "admin.php?page=watupro_modules";
	$form_url = wp_nonce_url($form_url, 'watupro_modules_nonce'); 
	$output = $error = '';	
	
	$fields = array("module");
	
	if(!empty($_POST['upload'])) {
		if(get_option('watupro_sandbox_mode')) wp_die("Sorry, this feature is disabled for security reasons");		
		
		// define the module
		if($_FILES['module']['name']=='i.zip') $module = "Intelligence";		
		if($_FILES['module']['name']=='reports.zip') $module = "Reporting";
		if($_FILES['module']['name']=='watupro-play.zip') wp_die("WatuPRO Play is a plugin, not a module. Please install it from your <a href='plugins.php'>Plugins</a> page.");
		if(empty($module)) wp_die("Incorrect module file");

		// destination folder
		if($module == 'Intelligence') $context = WP_PLUGIN_DIR."/watupro";
		if($module == 'Reporting') $context = WP_PLUGIN_DIR."/watupro/modules";
		
		$form_fields = array("module");
		$method = "";
		
		if(!watupro_filesystem_init($form_url, $method, $context, $form_fields)) return false; 
		
		$error = unzip_file($_FILES['module']['tmp_name'], $context);
	}
	
	if(@file_exists(get_stylesheet_directory().'/watupro/modules.php')) require get_stylesheet_directory().'/watupro/modules.php';
	else require WATUPRO_PATH."/views/modules.php";
}


/**
 * Initialize Filesystem object
 *
 * @param str $form_url - URL of the page to display request form
 * @param str $method - connection method
 * @param str $context - destination folder
 * @param array $fields - fileds of $_POST array that should be preserved between screens
 * @return bool/str - false on failure, stored text on success
 **/
 // Thanks to http://www.webdesignerdepot.com/2012/08/wordpress-filesystem-api-the-right-way-to-operate-with-local-files/
function watupro_filesystem_init($form_url, $method, $context, $fields = null) {
    global $wp_filesystem;
    
    
    /* first attempt to get credentials */
    if (false === ($creds = request_filesystem_credentials($form_url, $method, false, $context, $fields))) {
        
        /**
         * if we comes here - we don't have credentials
         * so the request for them is displaying
         * no need for further processing
         **/
        return false;
    }
    
    /* now we got some credentials - try to use them*/        
    if (!WP_Filesystem($creds)) {
        
        /* incorrect connection data - ask for credentials again, now with error message */
        request_filesystem_credentials($form_url, $method, true, $context);
        return false;
    }
    
    return true; //filesystem object successfully initiated
}