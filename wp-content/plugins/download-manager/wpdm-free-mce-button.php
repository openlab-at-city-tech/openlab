<?php


 
add_filter('mce_external_plugins', "wpdm_tinyplugin_register");
add_filter('mce_buttons', 'wpdm_tinyplugin_add_button', 0);
 
function wpdm_tinyplugin_add_button($buttons)
{
    array_push($buttons, "separator", "wpdm_tinyplugin");
    return $buttons;
}

function wpdm_tinyplugin_register($plugin_array)
{
    $url = plugins_url("download-manager/editor_plugin.js");

    $plugin_array['wpdm_tinyplugin'] = $url;
    return $plugin_array;
}


function wpdm_free_tinymce(){
    global $wpdb;
    if(!isset($_GET['wpdm_action'])||$_GET['wpdm_action']!='wpdm_tinymce_button') return false;
    ?>
<html>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title>Download Contrller &#187; Insert Package or Category</title>
<style type="text/css">
*{font-family: Tahoma !important; font-size: 9pt; letter-spacing: 1px;}
select,input{padding:5px;font-size: 9pt !important;font-family: Tahoma !important; letter-spacing: 1px;margin:5px;}
.button{
    background: #7abcff; /* old browsers */

background: -moz-linear-gradient(top, #7abcff 0%, #60abf8 44%, #4096ee 100%); /* firefox */

background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#7abcff), color-stop(44%,#60abf8), color-stop(100%,#4096ee)); /* webkit */

filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7abcff', endColorstr='#4096ee',GradientType=0 ); /* ie */
-webkit-border-radius: 3px;
-moz-border-radius: 3px;
border-radius: 3px;
border:0px solid #FFF;
color: #FFF;
cursor: pointer;
}
 
.input{
 width: 340px;   
 background: #EDEDED; /* old browsers */

background: -moz-linear-gradient(top, #EDEDED 24%, #fefefe 81%); /* firefox */

background: -webkit-gradient(linear, left top, left bottom, color-stop(24%,#EDEDED), color-stop(81%,#fefefe)); /* webkit */

filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#EDEDED', endColorstr='#fefefe',GradientType=0 ); /* ie */
border:1px solid #aaa; 
color: #000;
}
.button-primary{cursor: pointer;}
fieldset{padding: 10px;}
</style> 
<style type="text/css">    
.wpdm-pro legend{
    font-size:10pt;
}
.wpdm-pro .nav a:active,
.wpdm-pro .nav a:hover,
.wpdm-pro .nav a{
    outline:none !important;
}
.wpdm-pro button,
.wpdm-pro input[type=submit],
.wpdm-pro input[type=button],
.wpdm-pro input[type=text]{
    line-height:26px;
    min-height:26px;
    margin-bottom: 10px;
    
}
.wpdm-pro .btn small{
    font-size: 65%;
}
#wpdmcats {
    height:300px;
    overflow: auto;
    border:1px solid #eeeeee;
    border-radius:4px;
    margin: 0px;
    padding: 10px;
}
#wpdmcats li label{
    display: inline;
    font-size:11px;
}
#wpdmcats li{
    list-style: none;
}

.nav-tabs li a{
    text-transform: uppercase;
    font-weight: bold;
}

.drag-drop-inside{
    text-align: center;
    padding:5px;
    border:2px dashed #ddd;
    margin:5px 0px;
}

.tab{
    padding:10px 20px;
    text-decoration: none;
    margin: 0px;
     
    display: block;
    float: left;
}

.tab:first-child{
    border-right:0px
}
.tab.active{ 
    background:#ffffff;
    
}

#qbtn b{
    font-weight: normal;
}
#qbtn input,
#qbtn textarea{
    margin:0px;
    margin-bottom: 10px;
}

.tab-pane{
    background: #ffffff;
    padding:10px;
}

</style>
<script type="text/javascript" src="<?php echo includes_url('/js/jquery/jquery.js'); ?>"></script>
<script type="text/javascript" src="<?php echo includes_url('/js/jquery/jquery.form.js'); ?>"></script>
<script type="text/javascript" src="<?php echo includes_url('/js/tinymce/tiny_mce_popup.js'); ?>"></script>
<script type='text/javascript' src='<?php echo includes_url('/js/plupload/plupload.js');?>'></script>
<script type='text/javascript' src='<?php echo includes_url('/js/plupload/plupload.html5.js');?>'></script>
<script type='text/javascript' src='<?php echo includes_url('/js/plupload/plupload.flash.js');?>'></script>
<script type='text/javascript' src='<?php echo includes_url('/js/plupload/plupload.silverlight.js');?>'></script>
<script type='text/javascript' src='<?php echo includes_url('/js/plupload/plupload.html4.js');?>'></script>
                <script type="text/javascript">
                    
                    jQuery(function(){  
                                      
                    var s_title ='', s_desc = '';
                    jQuery('#addtopost').click(function(){
                     
                    var win = window.dialogArguments || opener || parent || top;       
                            
                    if(jQuery('#s_title').is(":checked")) s_title = ' title="true" ';
                    if(jQuery('#s_desc').is(":checked")) s_desc = ' desc="true" ';
                    var shadow = jQuery('#shadow').val();  
                    var template = ' template="'+jQuery('#template').val()+' '+shadow+'" ';
                    if(jQuery('#template').val()=='') template = "";
                    win.send_to_editor('[wpdm_file id='+jQuery('#fl').val()+s_title+s_desc+template+']');
                    tinyMCEPopup.close();
                    return false;                   
                    });
                    jQuery('#addtopostc').click(function(){              
                    var win = window.dialogArguments || opener || parent || top;

                    win.send_to_editor('{wpdm_category='+jQuery('#flc').val()+'}');
                    tinyMCEPopup.close();
                    return false;                   
                    });

                   jQuery('#addtoposte').click(function(){
                    var win = window.dialogArguments || opener || parent || top;

                    win.send_to_editor(jQuery('#esc').val());
                    tinyMCEPopup.close();
                    return false;
                    });
                              
                });
                </script>
                 
</head>
<body>    

<a href="#scode" class="tab active">Insert ShortCode</a><a href="#qbtn" class="tab">Quick Add</a> 
<div style="clear: both;"></div>        
<div id="scode"  class="tab-pane">
<fieldset><legend>Embed File</legend> 
<input type="checkbox" id="s_title" value="1"> <label for="s_title">Show Title</label>
<input type="checkbox" id="s_desc" value="1"> <label for="s_desc">Show Description</label> <br/>
Template: <select id="template">
<option value="">None</option>
<option value="facebook">Facebook</option>
<option value="bluebox">Blue Box</option> 
</select>
<br />
Drop Shadow Effect: <select id="shadow">
<option value="">None</option>
<option value="drop-shadow raised">Raised</option>
<option value="drop-shadow lifted">Lifted</option>
<option value="drop-shadow curved curved-hz-2">Curved</option>
</select>
<br/>
<br/>

    <select class="button input" id="fl">
    <?php
    $res = $wpdb->get_results("select * from ahm_files", ARRAY_A); 
    foreach($res as $row){
    ?>
    
    <option value="<?php echo $row['id']; ?>"><?php echo stripcslashes($row['title']); ?></option>
    
    
    <?php    
        
    }
?>
    </select>    
    <input type="submit" id="addtopost" class="button button-primary" name="addtopost" value="Insert into post" />
</fieldset>   <br>
<fieldset><legend>Embed Category</legend>
    <select class="button input" id="flc">
    <?php
    wpdm_dropdown_categories();
    ?>
    </select>
    <input type="submit" id="addtopostc" class="button button-primary" name="addtopost" value="Insert into post" />
</fieldset>   <br>
<fieldset><legend>Additional Short-codes</legend>
    <select class="button input" id="esc">
    <option value="[wpdm_all_packages]">All Downloads (Data Table)</option>
    <option value="[wpdm_tree]">All Downloads (Tree View)</option>
    </select>
    <input type="submit" id="addtoposte" class="button button-primary" name="addtopost" value="Insert into post" />
</fieldset>
</div>
<div id="qbtn"  class="tab-pane" style="display: none;"> 
<fieldset>
<legend>Add New Package</legend>
    <form action="admin.php?page=file-manager/add-new-package" id="wpdmpack" method="post">
     
    <input type="hidden" id="act" name="file[access]" value="guest" />
    <input type="hidden" name="action" value="save_wpdm_file" />
    <input type="hidden" name="wpdmtask" value="create" />

    <div class="row-fluid">    
    <b>Title:</b><br>
    <input type="text" size="40" name="file[title]" /><br>
    <b>Description:</b><br>
    <textarea cols="50" rows="3" class="span12" name="file[description]"></textarea><br>
    <div>
    <b>Download Link Label:</b><br>
    <input type="text" id="act" style="max-width: 100%;"  name="file[link_label]" value="Download" />
    </div>
     
    <div style="clear: both;"></div>
<div>
 
 
</div>    
<div class="postbox " id="upload_meta_box">
<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Upload file from PC</span></h3>
<div class="inside">
<input type="hidden" name="file[file]" value="<?php echo $file['file']; ?>" id="wpdmfile" />  
<div id="currentfiles">
<div class="cfile" id="cfl"> 
<?php if($file['file']!=''){ 
if(file_exists(UPLOAD_DIR.'/'.$file['file']))    
$filesize = number_format(filesize(UPLOAD_DIR.'/'.$file['file'])/1025,2);    
else if(file_exists($file['file']))    
$filesize = number_format(filesize($file['file'])/1025,2);    
?>
 
<div style="float: left"><strong><?php echo  basename($file['file']); ?></strong><br/><?php echo $filesize; ?> KB</div> <a href='#' id="dcf" title="Delete Current File" style="float: right;height:32px;"><img src="<?php echo plugins_url('/download-manager/images/error.png'); ?>" /></a>
<?php } else echo "<span style='font-weight:bold;color:#ddd'>No file uploaded yet!</span>"; ?>  
<div style="clear: both;"></div>
</div>
 <div style="clear: both;"></div>


<?php if($file['file']!=''): ?>
<script type="text/javascript">


jQuery('#dcf').click(function(){  
     if(!confirm('Are your sure?')) return false;
     jQuery('#cfl').fadeTo('slow',0.3);
     jQuery.post('admin-ajax.php',{action:'delete_file',file:'<?php echo $file['id']; ?>'},function(res){
        jQuery('#cfl').slideUp();      
        jQuery('#wpdmfile').val('');
     });     
     return false;
     
     
});



</script>


<?php endif; ?>



</div>
 
<div id="plupload-upload-ui" class="hide-if-no-js">
     <div id="drag-drop-area">
       <div class="drag-drop-inside">
        <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
        <p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
        <p class="drag-drop-buttons"><input id="plupload-browse-button" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
      </div>
     </div>
  </div>
  
  <?php

  $plupload_init = array(
    'runtimes'            => 'html5,silverlight,flash,html4',
    'browse_button'       => 'plupload-browse-button',
    'container'           => 'plupload-upload-ui',
    'drop_element'        => 'drag-drop-area',
    'file_data_name'      => 'async-upload',            
    'multiple_queues'     => false,
    'max_file_size'       => wp_max_upload_size().'b',
    'url'                 => admin_url('admin-ajax.php'),
    'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
    'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
    'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
    'multipart'           => true,
    'urlstream_upload'    => true,

    // additional post data to send to our ajax hook
    'multipart_params'    => array(
      '_ajax_nonce' => wp_create_nonce('photo-upload'),
      'action'      => 'file_upload',            // the ajax action name
    ),
  );

  // we should probably not apply this filter, plugins may expect wp's media uploader...
  $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

  <script type="text/javascript">

    jQuery(document).ready(function($){
     
      // create the uploader and pass the config from above
      var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

      // checks if browser supports drag and drop upload, makes some css adjustments if necessary
      uploader.bind('Init', function(up){
        var uploaddiv = jQuery('#plupload-upload-ui');

        if(up.features.dragdrop){
          uploaddiv.addClass('drag-drop');
            jQuery('#drag-drop-area')
              .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
              .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

        }else{
          uploaddiv.removeClass('drag-drop');
          jQuery('#drag-drop-area').unbind('.wp-uploader');
        }
      });

      uploader.init();

      // a file was added in the queue
      uploader.bind('FilesAdded', function(up, files){
        //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
        
           

        plupload.each(files, function(file){
          jQuery('#filelist').append(
                        '<div class="file" id="' + file.id + '"><b>' +
 
                        file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                        '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
        });

        up.refresh();
        up.start();
      });
      
      uploader.bind('UploadProgress', function(up, file) {
                      
                jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
            });
 

      // a file was uploaded 
      uploader.bind('FileUploaded', function(up, file, response) {

        // this is your ajax response, update the DOM with it or something...
        //console.log(response);
        //response
        jQuery('#' + file.id ).remove();
        var d = new Date();
        var ID = d.getTime();
        response = response.response;
        var data = response.split("|||");
        jQuery('#wpdmfile').val(data[0]);
        jQuery('#cfl').html('<div style="float: left"><strong>'+data[0]+'</strong><br/>'+data[1]+' KB</div>').slideDown();
                            /*if(response.length>20) nm = response.substring(0,7)+'...'+response.substring(response.length-10);                             
                            jQuery('#currentfiles table.widefat').append("<tr id='"+ID+"' class='cfile'><td><input type='hidden' id='in_"+ID+"' name='files[]' value='"+response+"' /><img id='del_"+ID+"' src='<?php echo plugins_url(); ?>/download-manager/images/minus.png' rel='del' align=left /></td><td>"+response+"</td><td width='40%'><input style='width:99%' type='text' name='wpdm_meta[fileinfo]["+response+"][title]' value='"+response+"' onclick='this.select()'></td><td><input size='10' type='text' id='indpass_"+ID+"' name='wpdm_meta[fileinfo]["+response+"][password]' value=''> <img style='cursor: pointer;float: right;margin-top: -3px' class='genpass' onclick=\"return generatepass('indpass_"+ID+"')\" title='Generate Password' src=\"<?php echo plugins_url('download-manager/images/generate-pass.png'); ?>\" /></td></tr>");
                            jQuery('#'+ID).fadeIn();
                            jQuery('#del_'+ID).click(function(){
                                if(jQuery(this).attr('rel')=='del'){
                                jQuery('#'+ID).removeClass('cfile').addClass('dfile');
                                jQuery('#in_'+ID).attr('name','del[]');
                                jQuery(this).attr('rel','undo').attr('src','<?php echo plugins_url(); ?>/download-manager/images/add.png').attr('title','Undo Delete');
                                } else if(jQuery(this).attr('rel')=='undo'){
                                jQuery('#'+ID).removeClass('dfile').addClass('cfile');
                                jQuery('#in_'+ID).attr('name','files[]');
                                jQuery(this).attr('rel','del').attr('src','<?php echo plugins_url(); ?>/download-manager/images/minus.png').attr('title','Delete File');
                                }
                                
                                
                            });  */
                           

      });

    });   

  </script>
  <div id="filelist"></div>

 <div class="clear"></div>
<!--<input type="file" id="file_upload" name="media"/>-->

 <div class="clear"></div>
</div>
</div>
    
    <input type="submit" class="button btn btn-success" value="Insert into post" /> 
    <div id="sving" style="float: right;margin-right:10px;padding-left: 20px;background:url('<?php echo admin_url('images/loading.gif');?>') left center no-repeat;display: none;">Please Wait...</div>
    </div>       
    </form>    
</fieldset>
</div>

  <script type="text/javascript">
  jQuery('#wpdmpack').submit(function(){
      
      jQuery('#sving').fadeIn();
      jQuery('#publish').attr('disabled','disabled');
      jQuery(this).ajaxSubmit({
          url:'admin-ajax.php',
          beforeSubmit:function(){
               
          },
          success:function(res){
              var msg = '';
              
              jQuery('#sving').fadeOut(); 
              var win = window.dialogArguments || opener || parent || top;                
                    
                    win.send_to_editor('[wpdm_file id='+res+']');
                    tinyMCEPopup.close();
                    return false;
          }
      });
      return false;
  });
  
  jQuery('.tab').click(function(){
         jQuery('.tab-pane').hide();
         jQuery('.tab').removeClass('active');
         jQuery(this).addClass('active');
          
         jQuery(jQuery(this).attr('href')).show();
         return false;
     });
  </script>

</body>    
</html>
    
    <?php
    
    die();
}
 

add_action('init', 'wpdm_free_tinymce');

