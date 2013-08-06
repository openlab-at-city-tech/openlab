<style>

input[type=text],textarea{
 
    
}

input{
   padding: 4px 7px;
}
.cfile{margin: 2px;border:3px solid #eeeeee;background: #fafafa;overflow:hidden;padding:5px;margin-bottom: 10px;} 
.dfile{margin: 2px;border:1px solid #800;background: #ffdfdf;overflow:hidden;padding:5px;} 
.cfile img, .dfile img{cursor: pointer;}
.inside{padding:10px !important;}
#editorcontainer textarea{border:0px;width:99.9%;}
#file_uploadUploader {background: transparent url('<?php echo plugins_url('/download-manager/images/browse.png'); ?>') left top no-repeat; }
#file_uploadUploader:hover {background-position: left bottom; }
.frm td{line-height: 30px; border-bottom: 1px solid #EEEEEE; padding:5px; font-size:9pt;font-family: Tahoma;}
 
</style>
 
<div class="wrap metabox-holder has-right-sidebar">
<?php if($_GET['task']=='wpdm_edit_file'){ ?>
    <div class="icon32" id="icon-add-new-file"><br></div>
<h2>Edit Download Package</h2>
<?php } else { ?>
    <div class="icon32" id="icon-add-new-file"><br></div>
<h2>Add New Download Package</h2>

<?php }?>
<form id="wpdmpack" action="" method="post">
<input type="hidden" name="action" value="save_wpdm_file" />
<input type="hidden" name="wpdmtask" value="<?php echo isset($_GET['task'])&&$_GET['task']=='wpdm_edit_file'?'update':'create';?>" />
<input type="hidden" name="id" value="<?php echo $file['id']; ?>" />
<div  style="width: 75%;float:left;">
    
<table cellpadding="5" cellspacing="5" width="100%">
<tr>
 
<td><input style="font-size:16pt;width:100%;color:<?php echo $file['title']?'#000':'#ccc'; ?>" onfocus="if(this.value=='Enter title here') {this.value=''; jQuery(this).css('color','#000'); }" onblur="if(this.value==''||this.value=='Enter title here') {this.value='Enter title here'; jQuery(this).css('color','#ccc');}" type="text" value="<?php echo $file['title']?$file['title']:'Enter title here'; ?>" name="file[title]" /></td>
</tr>

<tr>
<td valign="top"> 
<div id="poststuff" class="postarea">
                <?php wp_editor(stripslashes($file['description']),'file[description]','file[description]', true); ?>
                <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
                </div>
 
</td>
</tr>

 
<tr>
<td> <br>
 
<div  style="width: 48%;float: left;"> 
<div class="postbox " id="file_settings">
<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Package Settings</span></h3>
<div class="inside">
<table cellpadding="5" id="file_settings_table" cellspacing="0" width="100%" class="frm">
<tr id="link_label_row">    
<td width="110px">Link Label:</td>
<td><input size="10" type="text" style="width: 200px" value="<?php echo $file[link_label]?$file[link_label]:'Download'; ?>" name="file[link_label]" />
</td></tr>
<tr id="password_row">
<td>Password:</td>  
<td><input size="10" style="width: 200px" type="text" name="file[password]" value="<?php echo $file[password]; ?>" /></td>
</tr>
<tr id="download_limit_row">
<td>Stock&nbsp;Limit:</td>  
<td><input size="10" style="width: 80px" type="text" name="file[quota]" value="<?php echo $file[quota]; ?>" /></td>
</tr>
 <tr>
<td>Download Count: </td>
<td><input type="text" name="file[download_count]" value="<?php echo $file[download_count]?$file[download_count]:0; ?>" /></td>
</tr>

<tr>
<td>Counter: </td>
<td><select id="counter" style="width: 100px;" name="file[show_counter]">
<option value="0">Hide</option>
<option value="1" <?php if($file['show_counter']!=0) echo 'selected="selected"'; ?> >Show</option>
</select></td>
</tr>
<tr>
<td width="70">Access:</td>
<td><select id="access" style="width: 120px;" name="file[access]">
    <option value="guest">All Visitors</option>
    <option value="member" <?php if($file['access']=='member') echo 'selected'; ?>>Members Only</option>    
    </select>
</td>
</tr>
</table>
<div class="clear"></div>
</div>
</div>


</div> 

<div  style="width: 48%;float: right;height: inherit;">
 <div class="postbox " id="categories_meta_box">
<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Categories</span></h3>
<div class="inside" style="max-height: 150px;overflow: auto;">
<ul>
<?php
 $currentAccesss = maybe_unserialize( $file['category'] );
 if(!is_array($currentAccesss)) $currentAccesss = array();
 wpdm_cblist_categories('',0,$currentAccesss);   
?>
</ul> 

<div class="clear"></div>
</div>
</div>  
 
<?php
 include(dirname(__FILE__).'/wpdm-file-icon.php');
?>
   


</div> 

 
</td>
</tr>
 

<tr>
 
<td align="right">

</td>
</tr>

</table>
</div>
<div style="float: right;width:23%">

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

<div class="postbox " id="action">
<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Add file from server</span></h3>
<div class="inside">


<ul id="serverfiles">


 


</ul>   <br>

<a href="admin.php?page=file-manager&task=wpdm_file_browser" class="thickbox button-secondary">Open File Browser</a>








 <div class="clear"></div>
</div>
</div>


<!--download icon-->











 <div class="clear"></div>
 



<!--end downlaod icon-->



 









<div class="postbox " id="action">
<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Actions</span></h3>
<div class="inside">



 <input type="button" value="&#171; Back" tabindex="9" class="button-secondary button button-large" onclick="location.href='admin.php?page=file-manager'" class="add:the-list:newmeta" name="addmeta" id="addmetasub">

<input style="float: right"  type="reset" value="Reset" tabindex="9" class="button-secondary button button-large" class="add:the-list:newmeta" name="addmeta" id="addmetasub">

<input type="submit" value="<?php echo $_GET['task']=='wpdm_edit_file'?'Update Package':'Create Package'; ?>" accesskey="p" tabindex="5" id="publish" class="button-primary button button-large" name="publish">
 <div class="clear"></div>
 
</div>
</div>

<div class="postbox " id="action">
<h3><span>My Other Items</span></h3>
<div class="inside">
   <a href="http://wpeden.com/wordpress/themes/" style="width:97%;overflow:hidden;margin:5px;background: #fafafa;border: 1px solid #ccc;display: block;float: left;text-align: center;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;" ><h3 style="margin: 0px;background: #ccc;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;border-top-left-radius: 5px;border-top-right-radius: 5px;padding:5px;text-decoration: none;color:#333">Pro Level Themes for Free</h3><span style="display: block;padding: 10px;font-size:14pt;font-family:'Segoe UI Light';line-height: 1.5;color:#008000"><img style="max-width: 100%;height:auto;" src="<?php echo plugins_url('download-manager/images/theme.png'); ?>" /></span></a>
   <a href="http://www.wpdownloadmanager.com/" style="width:97%;overflow:hidden;margin:5px;background: #fafafa;border: 1px solid #ccc;display: block;float: left;text-align: center;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;" ><h3 style="margin: 0px;background: #ccc;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;border-top-left-radius: 5px;border-top-right-radius: 5px;padding:5px;text-decoration: none;color:#333">WordPress Download Manager Pro</h3><img style="max-width: 100%;height:auto;" src="<?php echo plugins_url('download-manager/images/wpdm.png'); ?>" /></a>
   <a href="http://www.wpmarketplaceplugin.com/" style="width:97%;overflow:hidden;margin:5px;background: #fafafa;border: 1px solid #ccc;display: block;float: left;text-align: center;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;" ><h3 style="margin: 0px;background: #ccc;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;border-top-left-radius: 5px;border-top-right-radius: 5px;padding:5px;text-decoration: none;color:#333">WordPress Marketplace Plugin</h3><img style="max-width: 100%;height:auto;" vspace="12" src="<?php echo plugins_url('download-manager/images/wpmp.png'); ?>" /></a>
   <a href="http://wpeden.com/" style="width:97%;overflow:hidden;margin:5px;background: #fafafa;border: 1px solid #ccc;display: block;float: left;text-align: center;-webkit-border-radius: 6px;-moz-border-radius: 6px;border-radius: 6px;" ><h3 style="margin: 0px;background: #ccc;-webkit-border-top-left-radius: 5px;-webkit-border-top-right-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-topright: 5px;border-top-left-radius: 5px;border-top-right-radius: 5px;padding:5px;text-decoration: none;color:#333">WordPress Themes & Plugins Collection</h3><img style="max-width: 100%;height:auto;" src="<?php echo plugins_url('download-manager/images/wpeden.png'); ?>" /></a>
   
   <div style="clear: both;"></div>
   </div>
</div>   

</div>
 
</form>

</div>
 <div id="w84sv" style="display: none;position: fixed;top:10px;right:10px;padding:15px 30px;border-radius:4px;background: #aa0000;color:#ffffff;font-weight: bold;font-family:'Courier New';z-index:999999;font-size: 12pt;">
 Saving<span style="text-decoration:blink;">...</span>
 </div>
 <div id="svd" onclick="jQuery(this).fadeOut();" style="display: none;position: fixed;top:10px;right:10px;padding:15px 30px;border-radius:4px;background: #00aa00;color:#ffffff;font-weight: bold;font-family:'Courier New';z-index:999999;font-size: 12pt;">  
 </div>
 
  
 
<script language="JavaScript">
<!--
  jQuery(function(){
      jQuery('select').chosen();
  });
  jQuery('#wpdmpack').submit(function(){
      jQuery('#svd').fadeOut(); 
      jQuery('#w84sv').fadeIn();
      jQuery('#publish').attr('disabled','disabled');
      jQuery(this).ajaxSubmit({
          url:'admin-ajax.php',
          beforeSubmit:function(){
               
          },
          success:function(res){
              var msg = '';
              if(res=='updated') msg = '<?php _e('File Updated Successfully!'); ?>';
              else if(parseInt(res)>0){
                  msg = '<?php _e('File Created Successfully! Please wait while redirecting...'); ?>';
                  location.href='admin.php?page=file-manager&task=wpdm_edit_file&id='+res;
              }
              jQuery('#w84sv').fadeOut(); 
              jQuery('#svd').html(msg).fadeIn(); 
              jQuery('#publish').removeAttr('disabled');
          }
      });
      return false;
  });
//-->
</script>       
       
