 <style>


input[type=text],textarea{
    width:500px;
    padding:5px;
}

input{
   padding: 7px; 
}
</style>
 
<div class="wrap">
    <div class="icon32" id="icon-categories"><br></div>
<h2>Categories <a href='admin.php?page=file-manager/categories' class="button-secondary">add new</a></h2><br>

<div style="margin-left:10px;float: left;width:47%"> 
<table cellspacing="0" class="widefat fixed">
    <thead>
    <tr>
    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
  
    <th style="" class="manage-column column-media" id="media" scope="col">Category</th>    
    <th style="" class="manage-column column-media" id="media" scope="col">Shortcode</th>        
       
    </tr>
    </thead>

    <tfoot>
    <tr>
    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
 
    <th style="" class="manage-column column-media" id="media" scope="col">Category</th>       
    <th style="" class="manage-column column-media" id="media" scope="col">Shortcode</th>       
    </tr>
    </tfoot>

    <tbody class="list:post" id="the-list">
    <?php 
    function wpdm_render_cats($parent="",$level=0){
    if($categories = maybe_unserialize(get_option("_fm_categories",true))){    
    if(is_array($categories)){
    foreach($categories as $id=>$category) {  
        if($category['parent']==$parent){
            $pres = str_repeat("&mdash;", $level);
        ?>
     
    <tr valign="top" class="alternate author-self status-inherit" id="post-8">

                <th class="check-column" scope="row"><input type="checkbox" value="8" name="id[]"></th>
                <td class="column-icon media-icon" style="text-align: left;">                
                    <a title="Edit" href="admin.php?page=file-manager/categories&task=EditCategory&cid=<?php echo $id; ?>">
                    <b><?php echo $pres.' '.$category['title']?></b>
                    </a> 
                    <div class="row-actions"><div class="button-group"><a class="button" href="admin.php?page=file-manager/categories&task=EditCategory&cid=<?php echo $id; ?>">Edit</a><a href="admin.php?page=file-manager/categories&task=DeleteCategory&cid=<?php echo $id?>" onclick="return showNotice.warn();" class="button submitdelete" style="color: #aa0000">Delete Permanently</a></div></div>                    
                </td>
                <td>
                <input type="text" title="copy the code and place it anywhere inside your post or page" value="{wpdm_category=<?php echo $id; ?>}" readonly=readonly onclick="this.select()"  style="width:180px;font-size: 10px;" />
                </td>
                 
                
                
     
     </tr>
     <?php wpdm_render_cats($id,$level+1);}}}}}wpdm_render_cats(); ?>
    </tbody>
</table><br>

<a href="#" class="button" onclick="return wpdm_deleteallcats();">Delete All Categories</a>
</div>
<div style="margin-left:10px;float: right;width:45%;margin-top:-70px">
<form action="" method="post">  
<table cellspacing="0">
    <thead>
    <tr>
    <th style="padding-bottom: 10px" class="manage-column column-author" id="author" scope="col" align="left"><h2><?php echo $_GET['cid']?'Edit':'Add';?> Category</h2></th>
    </tr>
    </thead>
   
   <?php
    $cat = maybe_unserialize(get_option('_fm_categories',true));
    $cat = $cat[$_GET['cid']];
    $cat[template_wraper] = $cat[template_wraper]?$cat[template_wraper]:'<div class="wpdm_category">
    [repeating_block]
</div>';
    
    $cat[template_repeater] = $cat[template_repeater]?$cat[template_repeater]:'<div class="wpdm_package">
    <a href="[page_url]">[thumb]</a><br>
    <b><a href="[page_url]">[title]</a></b><br>                
    [download_count] downloads
                
</div>';
$cid = stripslashes($_GET['cid']);
?> 
    
    <tbody class="list:post" id="the-list">    
    <tr valign="top">
                <td class="author column-author">
                <input style="" type="hidden" name="cid" value="<?php echo $cat?$cid:''; ?>">
                Title:<br>
                <input type="text" style="width: 99%;font-size: 14pt" name="cat[title]" value="<?php echo htmlspecialchars($cat[title]); ?>">
                Description:
                <textarea spellcheck=false style="width: 99%;height:150px" name="cat[content]"><?php echo stripslashes(htmlspecialchars($cat[content])); ?></textarea>
                <br />
                Parent:<br />
                <select name="cat[parent]">
                <option value="">Top Level Category</option>
                <?php wpdm_dropdown_categories('',0,$cat['parent']); ?>
                </select>                
                <br>                
                <br>                
                <input type="submit" value="<?php echo $_GET['cid']?'Update':'Create';?> Category" class="button-primary">
                </td>
                
     </tr>
    </tbody>
</table>
</form> 
</div>

 

</div>
<script language="JavaScript">
<!--
  jQuery('.<?php echo $cid;?>').attr('disabled','disabled');
  
  jQuery(function(){
      jQuery('select').chosen();
  });
  
  function wpdm_deleteallcats(){
      if(!confirm('Are you sure?')) return false;
      location.href='admin.php?page=file-manager/categories&task=delete-all';
  }
  <?php if($_GET['cid']!=''): ?>
  jQuery('.<?php echo $_GET['cid'];?>').attr('disabled','disabled');
  <?php endif; ?>
//-->
</script>