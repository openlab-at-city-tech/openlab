<?php
/**
 * File to display feedback form.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<body>
	<style>
		.moppm_modal{
			display:flex;
			justify-content:center;
			align-items:center;
		}

		.moppm_modal_content{
			font-size:13px;
			display:flex;
			flex-direction:column;
			align-items:left;
			margin: 10px 10%;
		}

		.moppm-modal-footer{
			display:flex;
			justify-content:right;
			/* gap:10px; */
		}

		.moppm_feedback{
			margin: 8px 0;
			font-weight:500;
		}

		hr{
			margin:0;
		}

	</style>

	<div id="moppm_feedback_modal" class="moppm_modal">

		<div class="moppm_modal-content">
			<h3 style="margin: 3%; text-align:center;"><b>Please give us your feedback</b><span class="moppm_wpns_close dashicons dashicons-no" style="cursor: pointer"></span>
			</h3>
			<hr>
			<form name="f" method="post" action="" id="moppm_mmp_feedback">

				<?php wp_nonce_field( 'moppm_feedback' ); ?>
				<input type="hidden" name="option" value="moppm_feedback"/>
				<div class="moppm_modal_content">
					<p style="font-weight:500">Please let us know why you are deactivating the plugin. Your feedback will help us make it better for you and other users</p>
					<div>
						<div class="moppm_feedback">
							<input type="radio" id="moppm_fbk_1" name="moppm_feedback" value="I'll reactivate it later" required>
							<label for="moppm_fbk_1">I'll reactivate it later</label>
						</div>
						<div class="moppm_feedback">
							<input type="radio" id="moppm_fbk_2" name="moppm_feedback" value="The plugin is not working" required>
							<label for="moppm_fbk_2">The plugin is not working</label>
						</div>
						<div class="moppm_feedback">
							<input type="radio" id="moppm_fbk_3" name="moppm_feedback" value="I could not understand how to use it" required>
							<label for="moppm_fbk_3">I could not understand how to use it</label>
						</div>
						<div class="moppm_feedback">
							<input type="radio" id="moppm_fbk_4" name="moppm_feedback" value="specific_feature" required>
							<label for="moppm_fbk_4">looking for specific feature</label>
						</div>
						<div class="moppm_feedback">
							<input type="radio" id="moppm_fbk_5" name="moppm_feedback" value="It_is_not_what_I_am_looking_for" required>
							<label for="moppm_fbk_5">It's not what I am looking for</label>
						</div>
						<div class="moppm_feedback">
							<input type="radio" id="moppm_fbk_6" name="moppm_feedback" value="other" required>
							<label for="moppm_fbk_6">Other</label>
						</div>
					</div>
					<br>                        
					<div>
						<div>
							<input type="hidden" id="query_mail" name="query_mail" required value="<?php echo esc_attr( $email ); ?>" readonly="readonly"/>
							<input type="hidden" name="edit" id="edit" onclick="editName()" value=""/>
							</label>
						</div>
						<textarea id="moppm_query_feedback" name="moppm_query_feedback" rows="2" style="width:100%" placeholder="Tell us!" hidden></textarea>
						<textarea id="moppm_query_feedback_specific_feature" name="moppm_query_feedback_specific_feature" rows="2" style="width:100%" placeholder="Please tell us about what you are looking for " hidden></textarea>
						<input type="checkbox" name="moppm_get_reply" value="reply">Do not reply</input>
						<br>
						<input type="checkbox" name="moppm_send_conf" value="send_configuration">Send Plugin Configuration with plugin feedback</input>
						<br>                  
					</div>
					<br>
					<div class="moppm-modal-footer">
						<input type="submit" name="miniorange_feedback_submit" class="button button-primary button-large"style="background-color:#224fa2; padding: 1% 3% 1% 3%;color: white;cursor: pointer;" value="Submit & Deactivate"/>
						<span width="30%">&nbsp;&nbsp;</span>
						<input type="button" name="moppm_skip_feedback"
							style="background-color:#224fa2; padding: 1% 3% 1% 3%;color: white;cursor: pointer;" value="Skip" onclick="document.getElementById('moppm_feedback_form_close').submit();"/>
					</div>
				</div>	
				<br>
			</form>
			<form name="f1" method="post" action="" id="moppm_feedback_form_close">
				<?php wp_nonce_field( 'moppm_feedback' ); ?>
				<input type="hidden" name="option" value="moppm_skip_feedback"/>
			</form>
		</div>
	</div>

	<script>

		jQuery("[name='moppm_feedback']").change((e)=>{

		if(jQuery("#moppm_fbk_6").is(":checked") || jQuery("#moppm_fbk_4").is(":checked")){
			jQuery("#moppm_query_feedback").show();
			jQuery("#moppm_query_feedback").prop("required",true);
		}
		else{
			jQuery("#moppm_query_feedback").hide();
			jQuery("#moppm_query_feedback").prop("required",false);
		}

		if(jQuery("#moppm_fbk_5").is(":checked")){
			jQuery("#moppm_query_feedback_specific_feature").show();
			jQuery("#moppm_query_feedback_specific_feature").prop("required",true);
		}
		else{
			jQuery("#moppm_query_feedback_specific_feature").hide();
			jQuery("#moppm_query_feedback_specific_feature").prop("required",false);
		}
		});
		jQuery("[type='radio']").show();

		jQuery('#deactivate-password-policy-manager').click(function () {

			var moppm_modal = document.getElementById('moppm_feedback_modal');

			var span = document.getElementsByClassName("moppm_wpns_close")[0];

			moppm_modal.style.display = "flex";
			document.querySelector("#moppm_query_feedback").focus();
			span.onclick = function () {
				moppm_modal.style.display = "none";
			}

			window.onclick = function (event) {
				if (event.target == moppm_modal) {
					moppm_modal.style.display = "none";
				}
			}
			return false;

		});
	</script>

