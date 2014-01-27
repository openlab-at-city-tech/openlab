<?php

function wpdm_dir_tree(){
    if(!isset($_GET['task'])||$_GET['task']!='wpdm_dir_tree') return;
    $_POST['dir'] = urldecode($_POST['dir']);
    if( file_exists($root . $_POST['dir']) ) {
	    $files = scandir($root . $_POST['dir']);
	    natcasesort($files);
	    if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		    echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		    // All dirs
		    foreach( $files as $file ) {
			    if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
				    echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
			    }
		    }
		    // All files
		    foreach( $files as $file ) {
			    if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
				    $ext = preg_replace('/^.*\./', '', $file);
				    echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
			    }
		    }
		    echo "</ul>";	
	    }
    }
}

function wpdm_file_browser(){
    if(!isset($_GET['task'])||$_GET['task']!='wpdm_file_browser') return;
    ?>
    <link rel="stylesheet" href="<?php echo plugins_url().'/download-manager/css/jqueryFileTree.css';?>" />
    <style type="text/css">.jqueryFileTree li{line-height: 20px;}</style>
    <div class="wrap">
    <div class="icon32" id="icon-categories"><br></div>
    <h2>Browse Files</h2>
    <div id="tree"></div>    
    <script language="JavaScript">
    <!--
      jQuery(document).ready( function() {
            jQuery('#tree').fileTree({
                root: '<?php echo get_option('_wpdm_file_browser_root',$_SERVER['DOCUMENT_ROOT']); ?>/',
                script: 'admin.php?task=wpdm_dir_tree',
                expandSpeed: 1000,
                collapseSpeed: 1000,
                multiFolder: false
            }, function(file) {
                var sfilename = file.split('/');
                var filename = sfilename[sfilename.length-1];
                jQuery('#serverfiles').html('').append('<li><label><input checked=checked type="checkbox" value="'+file+'" name="file[file]" class="role"> &nbsp; '+filename+'</label></li>');
                tb_remove();
            });
            
            jQuery('#TB_ajaxContent').css('width','630px').css('height','90%');
      });
    //-->
    </script>    
    </div>
    <?php
    die();
}

 



?>