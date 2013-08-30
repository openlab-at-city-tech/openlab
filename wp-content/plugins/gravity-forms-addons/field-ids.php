<?php
header("HTTP/1.1 200 OK");


require_once (preg_replace("/wp-content.*/","wp-blog-header.php",__FILE__));
require_once (preg_replace("/wp-content.*/","/wp-admin/includes/admin.php",__FILE__));

//redirect to the login page if user is not authenticated
auth_redirect();

if(!GFCommon::current_user_can_any(array("gravityforms_edit_forms", "gravityforms_create_form")))
    die(__("You don't have adequate permission to preview forms.", "gravityforms"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="Imagetoolbar" content="No" />
        <title><?php _e("Form Preview", "gravityforms") ?></title>
        <link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/reset.css' type='text/css' />
        <link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/preview.css' type='text/css' />
        <link rel='stylesheet' href='<?php echo GFCommon::get_base_url() ?>/css/forms.css' type='text/css' />
        <style type="text/css">
        	body { height: 100%!important; overflow: auto; }
	        table#input_ids {
	        	width: 99%;
	        	margin: 0 auto!important;
	        }
	        table#input_ids caption#input_id_caption { 
	        	color: #333!important; font-weight: bold;
	        	padding: 1em 0 .5em 0;
	        }
        </style>

    </head>
    <body>
        <?php
        GFDirectory_Admin::show_field_ids();
        ?>
    </body>
</html>
