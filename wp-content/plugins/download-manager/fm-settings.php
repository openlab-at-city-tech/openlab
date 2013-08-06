 <style>
.wrap *{
    font-family: Tahoma;
    letter-spacing: 1px;
}

input[type=text],textarea{
    width:500px;
    padding:5px;
}

input{
   padding: 7px; 
}
</style>
 
<div class="wrap">
    <div class="icon32" id="icon-options-general"><br></div>
<h2>Settings</h2>

<form action="" method="post" enctype="multipart/form-data">
 
<table cellpadding="5" cellspacing="5">
           
<tr>
<td>Minimum User Access Level:</td>
<td><select name="access">
    <option value="level_10">Administrator</option>    
    <option value="level_5" <?php echo $access=='level_5'?'selected':''?>>Editor</option>    
    <option value="level_2" <?php echo $access=='level_2'?'selected':''?>>Author</option>    
    </select>
</td>
</tr>

<tr>
<td>Login Required Message:</td>
<td>
<input type="text" name="wpdm_login_msg" value="<?php echo get_option('wpdm_login_msg',true); ?>" size="40">
</td>
</tr>


<tr>
<td>Download Link Icon:</td>
<td>
<input type="file" name="icon">
 | Current Icon: <img src="<?php echo plugins_url(); ?>/download-manager/icon/download.png" />
</td>
</tr>

 

<tr>
<td valign="top"></td>
<td align="right">

<input type="button" value="&#171; back" tabindex="9" class="button-secondary" onclick="location.href='admin.php?page=file-manager'" class="add:the-list:newmeta" name="addmeta" id="addmetasub">

<input type="reset" value="reset" tabindex="9" class="button-secondary" class="add:the-list:newmeta" name="addmeta" id="addmetasub">

<input type="submit" value="save" accesskey="p" tabindex="5" id="publish" class="button-primary" name="publish">
</td>
</tr>

</table>


</form>

</div>