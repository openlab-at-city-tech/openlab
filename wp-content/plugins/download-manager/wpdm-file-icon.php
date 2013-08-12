<?php //print_r( $fileinfo );  ?>
<style type="text/css">
.wdmiconfile{    
    -webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px;
}
</style>


<div class="postbox " id="iconimage">
<div title="Click to toggle" class="handlediv"><br></div><h3 class="hndle"><span>Select Icon </span></h3>
<div class="inside" style="height: 200px;overflow: auto;"> 
 
<br clear="all" />
<div id="w-icons">
 
<?php 
$img = array('jpg','gif','jpeg','png');
$icons =  scandir(dirname(__FILE__).'/icon/');
array_shift($icons);
array_shift($icons);

foreach($icons as $icon): $ext = strtolower(end(explode(".",$icon))); if(in_array($ext,$img)): ?>
<label>
<img class="wdmiconfile" id="<?php echo md5($icon) ?>" src="<?php  echo plugins_url().'/download-manager/icon/'.$icon; ?>" alt="<?php echo $icon ?>" style="padding:5px; margin:1px; float:left; border:#fff 2px solid;height: 32px;width:auto; " />
<input rel="wdmiconfile" style="display:none" <?php if($file['icon']==$icon) echo ' checked="checked" ' ?> type="radio"  name="file[icon]"  class="checkbox"  value="<?php echo $icon; ?>"></label>
<?php endif; endforeach; ?>
</div>
<script type="text/javascript">
//border:#CCCCCC 2px solid


jQuery('#<?php echo md5($file['icon']) ?>').css('border','#008000 2px solid').css('background','#F2FFF2');

jQuery('img.wdmiconfile').live('click',function(){

jQuery('img.wdmiconfile').css('border','#fff 2px solid').css('background','transparent');
jQuery(this).css('border','#008000 2px solid').css('background','#F2FFF2');



});

</script>

 <div class="clear"></div>
</div>
</div>
 