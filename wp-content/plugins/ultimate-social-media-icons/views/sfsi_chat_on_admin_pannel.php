<?php 
global $current_user;
$current_user=wp_get_current_user();

$email = $current_user->user_email;
?>
<div id="sfsi_jivo_offline_chat" style="display:none">
	<a href="" style="float:right;font-size:20px;margin-right:5px;color:#888;text-decoration: none;"  onclick="sfsi_close_offline_chat(event)">X</a>
	<p style="text-align:center" class="heading-text">No chat agent are available, However <span style="text-decoration: underline">we'll still respond quickly</span>.
		<!-- <a target="_blank" href="https://goo.gl/MU6pTN#no-topic-0" >we'll still respond quickly</a> -->
	</p>
	<ul class="tab-changer">
		<li class="tab-link active"><p style="text-align:center"><a href="#sfsi_technical"></a>Technical question<br><span>(for the free plugin)</span></p></li>
		<li class="tab-link"><p style="text-align:center"><a href="#sfsi_sales"></a>Pre-sales question<br><span>(for the Premium plugin)</span></p></li>
	</ul>
	<div class="clear"></div>
	<div class="tabs">
		<div id="sfsi_technical" class="tab-content" style="text-align:center;display:block">
			<h5>Please ask your question in the...</h5>
			<div class="support-forum-green-div">
				<!-- <span style="width: 25%">   </span> -->
				<a target="_blank" href="https://goo.gl/auxJ9C#no-topic-0" class="support-forum-green-bg" >
	                <img src="<?php echo SFSI_PLUGURL ?>images/support.png">
	                <p class="support-forum">Support Forum</p>
	            </a>
	            <span class="sfsi-button-right-side" ><span class="sfsi-button-right-side-icon"></span>Click here</span>
	        </div>
	        <!-- <p class="sfsi-button-right-side" ><span class="sfsi-button-right-side-icon"></span>Click here</p> -->
			<h5>We‘ll respond <span style="text-decoration: underline;"><b>quickly!</b></span></h5>
		</div>
		<div id="sfsi_sales" class="tab-content" style="display:none">
			
			<div style="display:block" class="before_message_sent">

				<!-- <p class="right-message" style="display:none">Please also check the <a href="">FAQ</a></p>	 -->
				<form action="#" method="POST" >
					<?php wp_nonce_field( 'OfflineChatMessage','nonce' ) ?>
					<div>
						<div for="question" class="label">
							Your question:
							<!-- <span class="right-message">Please also check the <a href=""><i>FAQ</i></a></span> -->
						</div>
						<textarea id="question" name="question" placeholder="Your question..."></textarea>
					</div>
					<div>
						<div>
							<div for="email" class="label email">Your email: </div>
							<div>
								<input type="email" name="email" value="<?php echo $email; ?>" placeholder="your@email.com" style="width:60%;float:left">
								<input type="submit" value="Send message" class="submit" style="width:37%;float:right">
							</div>
						</div>
						<div class="clear"></div>
					</div>
				</form>

			</div>
			<div style="display:none" class="after_message_sent">
				<h2>Thank you!</h2>
				<h3>We‘ll get back to you ASAP.</h3>
				<button class="chat_btn" onclick="sfsi_close_offline_chat(event)">Close window</button>
			</div>
		</div>
	</div>

</div>
<!-- Start jivo chat code -->

<script type='text/javascript'>
var sfsi_jivo_init=function(){ var widget_id =window.sfsi_plus_jivo_widget_id= 'heGfAHWfsn';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}};
var sfsi_dummy_chat_icon={};
sfsi_dummy_chat_icon.element=document.createElement('div');
sfsi_dummy_chat_icon.element.id="sfsi_dummy_chat_icon";
sfsi_dummy_chat_icon.element.style="position:fixed; bottom:0;right:10px;width:350px;height:74px;cursor:pointer;background-image:url('<?php echo SFSI_PLUGURL.'images/Chat_with_us_bar_light_green.png' ?>');background-position: -12.5px -11.5px;background-size: 374px 101px;border-top-left-radius: 8px;border-top-right-radius: 8px;";
function sfsi_open_chat(){
	if(window.jivo_api){
		if( window.jivo_api.chatMode()==='online'){
			sfsi_jivo_init();
		}else{
			jQuery('#jivo-iframe-container').remove();
			jQuery('script[src="//code.jivosite.com/script/widget/'+sfsi_plus_jivo_widget_id+'"]').remove();
			jQuery('#sfsi_jivo_offline_chat').show();
		}
	}else{
		sfsi_jivo_init();
	}
	// jQuery(sfsi_dummy_chat_icon.element).html("<p style='text-align: center;font-size: 18px;'>Loading...</p>");
	jQuery(sfsi_dummy_chat_icon.element).hide();
}
sfsi_dummy_chat_icon.element.onclick=sfsi_open_chat;
var jivo_onLoadCallback = function(){
	if(jivo_api.chatMode()==='online'){
		jivo_api.showProactiveInvitation('How can I help you?');
	}else{
		jQuery('#jivo-iframe-container').remove();
		jQuery('script[src="//code.jivosite.com/script/widget/'+sfsi_plus_jivo_widget_id+'"]').remove();
		jQuery('#sfsi_jivo_offline_chat').show();
	}
	// jQuery(sfsi_dummy_chat_icon.element).hide();
};
// sfsi_dummy_chat_icon.heading= document.createElement('p');
// sfsi_dummy_chat_icon.warning= document.createElement('p');
// sfsi_dummy_chat_icon.heading.style="margin: 0;text-align: center;font-size: 18px;margin-top: 5px;"
// sfsi_dummy_chat_icon.warning.style="font-size:11px;text-align: center;margin-bottom: 0;margin-top: 4px;"
// sfsi_dummy_chat_icon.heading.appendChild(document.createTextNode("Questions? Chat with us!"));
// sfsi_dummy_chat_icon.warning.appendChild(document.createTextNode("This will establish connection to the chat servers."));
// sfsi_dummy_chat_icon.element.appendChild(sfsi_dummy_chat_icon.heading);
// sfsi_dummy_chat_icon.element.appendChild(sfsi_dummy_chat_icon.warning);
sfsi_dummy_chat_icon.body=document.getElementsByTagName('body');
if(sfsi_dummy_chat_icon.body.length>0){
	sfsi_dummy_chat_icon.body[0].appendChild(sfsi_dummy_chat_icon.element);
}else{
	document.appendChild(sfsi_dummy_chat_icon.element);
}
</script>

<!-- End jivo chat code -->
