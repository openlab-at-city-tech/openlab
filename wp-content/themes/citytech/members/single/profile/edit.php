<?php do_action( 'bp_before_profile_edit_content' ) ?>

<?php 
global $bp,$user_ID;
if(is_super_admin( $user_ID )){
	$pgroup=bp_get_current_profile_group_id();
}else{
  $account_type=bp_get_profile_field_data( 'field=Account Type' );
  if($account_type=="Student"){
	  $pgroup="2";
  }elseif($account_type=="Faculty"){
	  $pgroup="3";
  }elseif($account_type=="Alumni"){
	  $pgroup="4";
  }elseif($account_type=="Staff"){
	  $pgroup="5";
  }else{
	  $pgroup="1";
  }
}
$first_name=bp_get_profile_field_data( 'field=First Name' );
$last_name=bp_get_profile_field_data( 'field=Last Name' );
$update_user_first = update_user_meta($user_ID,'first_name',$first_name);
$update_user_last = update_user_meta($user_ID,'last_name',$last_name);

$display_name=bp_get_profile_field_data( 'field=Name' );

if ( bp_has_profile( 'profile_group_id=' . $pgroup ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

<form action="<?php bp_the_profile_group_edit_form_action() ?>" method="post" id="profile-edit-form" class="standard-form <?php bp_the_profile_group_slug() ?>">

	<?php do_action( 'bp_before_profile_field_content' ) ?>

		<h4><?php printf( __( "Edit Profile Information", "buddypress" ), bp_get_the_profile_group_name() ); ?></h4>

		<ul class="button-nav">
			<?php 
			if(is_super_admin( $user_ID )){
				bp_profile_group_tabs(); 
			}
			?>
		</ul>

		<div class="clear"></div>
		<?php if($pgroup!="1"){?>
          <div class="editfield field_1 field_name alt">
          <label for="field_1">Display Name (required)</label>
          <input type="text" value="<?php echo $display_name;?>" id="field_1" name="field_1">
          <p class="description"></p>
          </div>
          <div class="editfield field_241 field_first-name alt">
          <label for="field_241">First Name (required)</label>
          <input type="text" value="<?php echo $first_name;?>" id="field_241" name="field_241">
          <p class="description"></p>
          </div>
          <div class="editfield field_3 field_last-name alt">
          <label for="field_3">Last Name (required)</label>
          <input type="text" value="<?php echo $last_name;?>" id="field_3" name="field_3">
          <p class="description"></p>
          </div>
        <?php } ?>
        
		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

			<div<?php bp_field_css_class( 'editfield' ) ?>>
    
                    
				
				<?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>
				<?php	if(bp_get_the_profile_field_name()=="Name"){ ?>
					<label for="<?php bp_the_profile_field_input_name() ?>"><?php echo "Display Name"; ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
				<?php	}else{ ?>
					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
				<?php	}      ?>

					<input type="text" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" value="<?php bp_the_profile_field_edit_value() ?>" />

				<?php endif; ?>

				<?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
					<textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_edit_value() ?></textarea>

				<?php endif; ?>

				<?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : 
					$style="";	
					if(bp_get_the_profile_field_name()=="Account Type" && !is_super_admin( $user_ID ) || $account_type){
						//$style="style='display:none;'";	
					}?>

					<label <?php echo $style;?> for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
					<select <?php echo $style;?> name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>">
						<?php bp_the_profile_field_options() ?>
					</select>

				<?php endif; ?>

				<?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>

					<label for="<?php bp_the_profile_field_input_name() ?>"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>
					<select name="<?php bp_the_profile_field_input_name() ?>" id="<?php bp_the_profile_field_input_name() ?>" multiple="multiple">
						<?php bp_the_profile_field_options() ?>
					</select>

					<?php if ( !bp_get_the_profile_field_is_required() ) : ?>
						<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e( 'Clear', 'buddypress' ) ?></a>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

					<div class="radio">
						<span class="label"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></span>

						<?php bp_the_profile_field_options() ?>

						<?php if ( !bp_get_the_profile_field_is_required() ) : ?>
							<a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name() ?>' );"><?php _e( 'Clear', 'buddypress' ) ?></a>
						<?php endif; ?>
					</div>

				<?php endif; ?>

				<?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

					<div class="checkbox">
						<span class="label"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></span>

						<?php bp_the_profile_field_options() ?>
					</div>

				<?php endif; ?>

				<?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

					<div class="datebox">
						<label for="<?php bp_the_profile_field_input_name() ?>_day"><?php bp_the_profile_field_name() ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ) ?><?php endif; ?></label>

						<select name="<?php bp_the_profile_field_input_name() ?>_day" id="<?php bp_the_profile_field_input_name() ?>_day">
							<?php bp_the_profile_field_options( 'type=day' ) ?>
						</select>

						<select name="<?php bp_the_profile_field_input_name() ?>_month" id="<?php bp_the_profile_field_input_name() ?>_month">
							<?php bp_the_profile_field_options( 'type=month' ) ?>
						</select>

						<select name="<?php bp_the_profile_field_input_name() ?>_year" id="<?php bp_the_profile_field_input_name() ?>_year">
							<?php bp_the_profile_field_options( 'type=year' ) ?>
						</select>
					</div>

				<?php endif; ?>

				<?php do_action( 'bp_custom_profile_edit_fields' ) ?>

				<p class="description"><?php bp_the_profile_field_description() ?></p>
			</div>

		<?php endwhile; ?>

	<?php do_action( 'bp_after_profile_field_content' ) ?>

	<div class="submit">
		<input type="submit" name="profile-group-edit-submit" id="profile-group-edit-submit" value="<?php _e( 'Save Changes', 'buddypress' ) ?> " />
	</div>
	<input type="hidden" name="field_ids" id="field_ids" value="1,3,241,<?php bp_the_profile_group_field_ids() ?>" />
	<?php wp_nonce_field( 'bp_xprofile_edit' ) ?>

</form>

<?php endwhile; endif; ?>

<?php do_action( 'bp_after_profile_edit_content' ) ?>