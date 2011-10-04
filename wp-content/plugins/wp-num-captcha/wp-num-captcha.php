<?php
/*
Plugin Name: wp-num-captcha
Plugin URI: http://www.atgleam.com
Description: Comment Numerical Captcha Plugins
Version: 1.1
Author: Gleam
Author URI: http://www.atgleam.com
*/ 
if (!class_exists('wpNumCaptcha')) {
    session_cache_limiter ('private, must-revalidate');
    if( !isset( $_SESSION ) ) {
        session_start();
    }
    class wpNumCaptcha {
        // adds the captcha to the comment form
        function addCaptchaToCommentForm() {
            global $user_ID;
            // loggin user skiped
            if (isset($user_ID)) {
                return true;
            }
            $rand_a = rand(0,10);
            $rand_b = rand(0,10);
            $form_html = ' <div id="captchaNumDiv"><p><input type="text" name="captcha_num" id="captcha_num" size="15">';
            $form_html .= '<input type="hidden" name="rand_a" value="' .$rand_a .'" /><input type="hidden" name="rand_b" value="' .$rand_b .'" />' ;
            $form_html .= ' <label><span>' . $rand_a .'+' . $rand_b . '=?</span> (required)</label></p></div>' ;
            $form_html .='<script type="text/javascript">var sUrlInput = document.getElementById("comment");var oParent = sUrlInput.parentNode;while(oParent.nodeName != "FORM"){oParent = oParent.parentNode;sUrlInput=sUrlInput.parentNode;};var sSubstitue = document.getElementById("captchaNumDiv");oParent.insertBefore(sSubstitue, sUrlInput);document.getElementById("captcha_num").size=document.getElementById("url").size</script>';
            echo $form_html;
            return true;
        }
        
        function checkCaptchaCommentPost($comment){
            global $user_ID;
            
            if (isset($user_ID)) {
                return($comment);
            }
            
            if (empty($_POST['captcha_num']) || trim($_POST['captcha_num']) == '' ) {
                wp_die( __('Error: Please input Numerical. ', 'num-captcha'));
            }
            
            $captcha_num = trim(strip_tags($_POST['captcha_num']));
            $rand_a = trim(strip_tags($_POST['rand_a']));
            $rand_b = trim(strip_tags($_POST['rand_b']));
            
            if ( is_numeric($_POST['captcha_num']) && ($rand_a +$rand_b ) ==$captcha_num) {
                // ok can continue
                return($comment);
            } else {
                wp_die( __('Error: Numerical Captcha is wrong , Press your browsers back button and try again.', 'num-captcha'));
            }
        }
    }
}

if (class_exists("wpNumCaptcha")) {
  $wp_num_captcha = new wpNumCaptcha();
  add_action('comment_form', array(&$wp_num_captcha, 'addCaptchaToCommentForm'), 1);
  add_filter('preprocess_comment', array(&$wp_num_captcha, 'checkCaptchaCommentPost'), 1);
}
?>