<?php
global $wpdb;
$did = '';
$dl = isset($_REQUEST['download'])?(int)$_REQUEST['download']:0;
if($dl>0){
    @header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
    $data = $wpdb->get_row("select * from ahm_files where id='$dl'",ARRAY_A);
    if($data['access']=='member'&&!is_user_logged_in()){
        $wpdm_login_msg = get_option('wpdm_login_msg')?get_option('wpdm_login_msg'):'Login Required';
        die("<div style='padding:20px 30px;background:#fff'><a href='".get_option('siteurl')."/wp-login.php'  style=\"background:url('".get_option('siteurl')."/wp-content/plugins/download-manager/l24.png') no-repeat;padding:3px 12px 12px 28px;font:bold 10pt verdana;\">".$wpdm_login_msg."</a></div>");
    }
    if(isset($_POST['password'])&&isset($data['password'])&&$_POST['password']==$data['password']){
        $did = uniqid();
        file_put_contents(dirname(__FILE__).'/cache/'.$did,serialize($data)); 
        die($did);
    }else if(isset($_POST['password'])&&isset($data['password'])&&$_POST['password']!=$data['password']){
        die('error');
    }
?>
<style>
 
input,form,p{
    font-size:9pt;    
}
form{text-align:center;}
</style>
<?php

if($data){
    
    echo "<div style='min-weight:300px;min-height:200px;padding:30px;background:#fff;color:#000'><h1><nobr>{$data['title']}</nobr></h1><br/><p>".wpautop(stripcslashes($data['description']))."</p>";
        /*
        if($_POST&&$data[password]==''){
        echo "<script>
                    window.opener.location.href='$_SERVER[HTTP_REFERER]'; self.close();</script>"; die(); }
        */            
        ?>        
        <form method="post">
            <?php 
                
                if(isset($_POST['password'])&&isset($data['password'])&&$_POST['password']==$data['password']){
                    
                    
                     
                    
                    mysql_query("update ahm_files set `download_count`=`download_count`+1 where id='{$data['id']}'");
                    echo "Please Wait... Download starting in a while...
                    </form>
                    
                    <script>
                    window.opener.location.href='".get_option('siteurl')."/?wpdmact=process&did={$did}'; 
                    self.close();
                    </script>
                    ";     
                                        
                    die();
                } else {
                    if($data['password']!=''){
                        if($_POST['password']!=$data['password']&&count($_POST)>0) echo "<span style='color:red'>Wrong password!</span><br>";
                ?>
                Enter Password: <input type="password" id="pass" size="10" name="password" />  
           <?php }else{?>
           <input type="hidden" id="pass" name="password" value="" /> 
           <?php }}
           
            ?>
        <input type="button" onclick="validate_pass()" value="Download"/>
        <div id="err" style="color: red;"></div>
        </form>
        <script language="JavaScript">
        <!--
          function validate_pass(){
              jQuery.post("<?php echo home_url('/'); ?>",{'download':'<?php echo $dl; ?>','password':jQuery('#pass').val()},function(res){                  
                  if(res=='error') jQuery('#err').html('Password not matched');
                  else
                  location.href='<?php echo get_option('siteurl'); ?>/?wpdmact=process&did='+res; 
                  
              });
          }
        //-->
        </script>
        </div>
        <?php
        die();
   }

else{
    echo "Error!";
}


    die();
}
?>