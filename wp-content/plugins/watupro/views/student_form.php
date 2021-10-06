<?php // deprecated file? ?>
<div class="wrap watupro-wrap" style="width:500px;">
    <h2><?php _e(ucfirst($action) . " Student", 'watupro'); ?></h2>
    
    <form method="post" onsubmit="return validate(this);">
    <div class="postbox">
        <?php if(!empty($student->ID)):?>
            <h3 class="hndle"><span><?php _e('Date registered', 'watupro') ?></span></h3>
            <div class="inside"><p><?php e($student->regdate)?></p></div>
        <?php endif;?>
    </div>
    <div class="postbox">    
        <h3 class="hndle"><span><?php _e('Student name', 'watupro') ?></span></h3>
        <div class="inside">            
            <p><input type="text" name="name" value="<?php echo stripcslashes(@$student->name);?>" size="60"></p>
        </div>
    </div>
    <div class="postbox">    
        <h3 class="hndle"><span><?php _e('Student email', 'watupro') ?></span></h3>
        <div class="inside">            
            <p><input type="text" name="email" value="<?php echo stripcslashes(@$student->email);?>" size="60"></p>
        </div>
    </div>
    
    <div class="postbox">    
        <h3 class="hndle"><span><?php _e((empty($student->ID)?'':'Change').'Password', 'watupro') ?></span></h3>
        <div class="inside">            
            <p><input type="password" name="password" size="60"></p>
        </div>
    </div>    
        
    <div class="postbox">    
         <h3 class="hndle"><span><?php _e('Optional Notes', 'watupro') ?></span></h3>
        <div class="inside">            
            <p><textarea name="notes" rows="8" cols="80"><?php echo stripcslashes(@$student->notes);?></textarea></p>
        </div>        
    </div>
    
    <div class="postbox">    
        <div class="inside">    
            <p align="center"><input type="submit" name="ok" value="<?php _e('Save Student', 'watupro') ?>" />
            <input type="button" value="<?php _e('Back To Students', 'watupro') ?>" onclick="window.location='admin.php?page=watupro/students.php'" /></p>
        </div>   
    </div> 
    
    </form>
</div>

<script type="text/javascript">
function validate(frm)
{
    if(frm.name.value=="")
    {
        alert("Please enter name");
        frm.name.focus();
        return false;
    }
    
    if(frm.email.value=="" || frm.email.value.indexOf("@")<1 || frm.email.value.indexOf(".")<1)
    {
        alert("Please enter valid email");
        frm.email.focus();
        return false;
    }
    
    <?php if(empty($student->ID)):?>
    if(frm.password.value=='')
    {
        alert("Please enter password");
        frm.password.focus();
        return false;
    }
    <?php endif;?>
    
    return true;
}
</script>