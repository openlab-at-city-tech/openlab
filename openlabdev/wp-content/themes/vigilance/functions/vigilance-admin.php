<?php
	/*
		Class Definition
	*/
	if (!class_exists( 'JestroCore' )) {
		class JestroCore {

			var $themename = "Jestro";
			var $themeurl = "http://thethemefoundry.com/";
			var $shortname = "jestro_themes";
			var $options = array();

			/* PHP4 Compatible Constructor */
			function JestroCore () {
				add_action( 'init', array(&$this, 'printAdminScripts' ));
				add_action( 'admin_menu', array(&$this, 'addAdminPage' ));
			}

			/* Add Custom CSS & JS */
			function printAdminScripts () {
				if ( $_GET['page'] == basename(__FILE__) ) {
					wp_enqueue_style( 'jestro', get_bloginfo( 'template_directory' ).'/functions/stylesheets/admin.css' );
					wp_enqueue_script( 'jestro', get_bloginfo( 'template_directory' ).'/functions/javascripts/admin.js', array( 'jquery' ) );
					wp_enqueue_script( 'farbtastic' );
					wp_enqueue_style( 'farbtastic' );
				}
			}

			/* Process Input and Add Options Page*/
			function addAdminPage() {
				// global $themename, $shortname, $options;
				if ( $_GET['page'] == basename(__FILE__) ) {
					if ( 'save' == $_REQUEST['action'] ) {
						foreach ($this->options as $value) {
							update_option( $value['id'], $_REQUEST[ $value['id'] ] );
						}
						foreach ($this->options as $value) {
							if ( isset( $_REQUEST[ $value['id'] ] ) ) {
								update_option( $value['id'], $_REQUEST[ $value['id'] ]	);
							} else {
								delete_option( $value['id'] );
							}
						}
						header("Location: themes.php?page=".basename(__FILE__)."&saved=true");
						die;
					} else if ( 'reset' == $_REQUEST['action'] ) {
						foreach ($this->options as $value) {
							delete_option( $value['id'] );
						}
						header("Location: themes.php?page=".basename(__FILE__)."&reset=true");
						die;
					}
				}
				add_theme_page($this->themename." Options", $this->themename." Options", 'edit_themes', basename(__FILE__), array(&$this, 'adminPage' ));
			}

			/* Output of the Admin Page */
			function adminPage () {
				// global $themename, $shortname, $options;
				if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>' . $this->themename . __( ' settings saved!', 'vigilance' ) . '</strong></p></div>';
				if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>' . $this->themename . __( ' settings reset.', 'vigilance' ) . '</strong></p></div>'; ?>

<div id="v-options">
	<div class="v-top clear">
		<h1 class="v-trial"><?php echo $this->themename; ?> <?php _e( 'Options', 'vigilance' ); ?></h1>
		<div class="v-logo"><a href="http://thethemefoundry.com/">The Theme Foundry</a></div>
	</div>
	<div id="vop-header"><p><strong><?php _e( 'Need help?', 'vigilance' ); ?></strong> <a href="http://thethemefoundry.com/tutorials/vigilance/"><?php _e( 'Read the tutorials' , 'vigilance' ); ?></a> <?php _e( 'or visit the <a href="http://thethemefoundry.com/forums/">support forums.</a>', 'vigilance' ); ?></p></div>
	<div class="v-notice">
		<h3><?php _e( 'Go PRO!', 'vigilance' ); ?></h3>
		<p><?php _e( 'You are using the free version of Vigilance. Upgrade to Vigilance PRO for extra features, access to dedicated support, priority theme updates, and comprehensive theme tutorials.', 'vigilance' ); ?></p>
		<p><a href="http://thethemefoundry.com/vigilance/"><?php _e( 'Learn more about Vigilance PRO &rarr;', 'vigilance' ); ?></a></p>
	</div>
	<div id="vop-body">
		<form method="post">
<?php
				for ($i = 0; $i < count($this->options); $i++) :
					switch ($this->options[$i]["type"]) :

						case "subhead":
							if ($i != 0) { ?>
		<div class="v-save-button submit">
			<input type="hidden" name="action" value="save" />
			<input class="button-primary" type="submit" value="<?php _e( 'Save changes', 'vigilance' ); ?>" name="save"/>
		</div><!--end v-save-button-->
	</div>
</div><!--end v-option--><?php } ?>
<div class="v-option">
	<h3><?php echo $this->options[$i]["name"]; ?></h3>
	<div class="v-option-body clear">
		<?php $notice = $this->options[$i]["notice"] ?>
		<?php if ($notice != '' ) { ?>
			<p class="notice"><?php echo $notice; ?></p>
		<?php } ?>
						<?php
							break;

					case "checkbox":
						?>
		<?php $pro = $this->options[$i]["pro"] ?>
		<div class="v-field check clear <?php if ($pro == 'true' ) echo 'pro' ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
			<input id="<?php echo $this->options[$i]["id"]; ?>" type="checkbox" name="<?php echo $this->options[$i]["id"]; ?>" value="true"<?php echo (get_settings($this->options[$i]['id'])) ? ' checked="checked"' : ''; ?> />
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
		</div><!--end v-field check-->
						<?php
							break;

						case "radio":
							?>
		<?php $pro = $this->options[$i]["pro"] ?>
		<div class="v-field radio clear <?php if ($pro == 'true' ) echo 'pro' ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
				<?php $pro = $this->options[$i]["pro"] ?>
				<?php
				$radio_setting = get_settings($this->options[$i]['id']);
				$checked = '';
				foreach ($this->options[$i]['options'] as $key => $val) :
					if ($radio_setting != '' &&	$key == get_settings($this->options[$i]['id']) ) {
						$checked = ' checked="checked"';
					} else {
						if ($key == $this->options[$i]['std']){
							$checked = 'checked="checked"';
						}
					}
					?>
				<input type="radio" name="<?php echo $this->options[$i]['id']; ?>" value="<?php echo $key; ?>"<?php echo $checked; ?> /><?php echo $val; ?><br />
				<?php endforeach; ?>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
		</div><!--end v-field radio-->
						<?php
							break;

						case "text":
							?>
		<?php $pro = $this->options[$i]["pro"] ?>
		<div class="v-field text clear <?php if ($pro == 'true' ) echo 'pro' ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
			<input id="<?php echo $this->options[$i]["id"]; ?>" type="text" name="<?php echo $this->options[$i]["id"]; ?>" value="<?php echo stripslashes((get_settings($this->options[$i]["id"]) != '') ? get_settings($this->options[$i]["id"]) : $this->options[$i]["std"]); ?>" />
		</div><!--end v-field text-->
						<?php
							break;

						case "colorpicker":
							?>
		<?php $pro = $this->options[$i]["pro"] ?>
		<div class="v-field colorpicker clear <?php if ($pro == 'true' ) echo 'pro' ?>">

			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]; ?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?><?php if ($pro != 'true' ) { ?> <a href="javascript:return false;" onclick="toggleColorpicker (this, '<?php echo $this->options[$i]["id"]; ?>', 'open', '<?php _e( 'show color picker', 'vigilance' ); ?>', '<?php _e( 'hide color picker', 'vigilance' ); ?>' )"><?php _e( 'show color picker', 'vigilance' ); ?><?php } ?></a></label>
			<div id="<?php echo $this->options[$i]["id"]; ?>_colorpicker" class="colorpicker_container"></div>
			<input id="<?php echo $this->options[$i]["id"]; ?>" type="text" name="<?php echo $this->options[$i]["id"]; ?>" value="<?php echo (get_settings($this->options[$i]["id"]) != '') ? get_settings($this->options[$i]["id"]) : $this->options[$i]["std"]; ?>" />
		</div><!--end v-field colorpicker-->
						<?php
							break;

						case "select":
							?>
		<?php $pro = $this->options[$i]["pro"] ?>
		<div class="v-field select clear <?php if ($pro == 'true' ) echo 'pro' ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]?></span></div>
			<label for="<?php echo $this->options[$i]["id"]; ?>"><?php echo $this->options[$i]["name"]; ?></label>
			<select id="<?php echo $this->options[$i]["id"]; ?>" name="<?php echo $this->options[$i]["id"]; ?>">
				<?php
					foreach ($this->options[$i]["options"] as $key => $val) :
						if (get_settings($this->options[$i]["id"]) == '' || is_null(get_settings($this->options[$i]["id"]))) : ?>
					<option value="<?php echo $key; ?>"<?php echo ($key == $this->options[$i]['std']) ? ' selected="selected"' : ''; ?>><?php echo $val; ?></option>
						<?php else : ?>
					<option value="<?php echo $key; ?>"<?php echo get_settings($this->options[$i]["id"]) == $key ? ' selected="selected"' : ''; ?>><?php echo $val; ?></option>
					<?php
						endif;
					endforeach;
				?>
			</select>
		</div><!--end v-field select-->
						<?php
							break;

						case "textarea":
							?>
		<?php $pro = $this->options[$i]["pro"] ?>
		<div class="v-field textarea clear <?php if ($pro == 'true' ) echo 'pro' ?>">
			<div class="v-field-d"><span><?php echo $this->options[$i]["desc"]?></span></div>
			<label for="<?php echo $this->options[$i]["id"]?>"><?php echo $this->options[$i]["name"]?></label>
			<textarea id="<?php echo $this->options[$i]["id"]?>" name="<?php echo $this->options[$i]["id"]?>"<?php echo ($this->options[$i]["options"] ? ' rows="'.$this->options[$i]["options"]["rows"].'" cols="'.$this->options[$i]["options"]["cols"].'"' : ''); ?>><?php
				echo ( get_settings($this->options[$i]['id']) != '') ? stripslashes(get_settings($this->options[$i]['id'])) : stripslashes($this->options[$i]['std']);
			?></textarea>
		</div><!--end vop-v-field textarea-->
						<?php
							break;

					endswitch;
				endfor;
			?>
					<div class="v-save-button submit">
						<input type="submit" value="<?php _e( 'Save changes', 'vigilance' ); ?>" name="save"/>
					</div><!--end v-save-button-->
				</div>
			</div>
			<div class="v-saveall-button submit">
				<input class="button-primary" type="submit" value="<?php _e( 'Save all changes', 'vigilance' ); ?>" name="save"/>
			</div>
			</form>
			<div class="v-reset-button submit">
				<form method="post">
					<input type="hidden" name="action" value="reset" />
					<input class="v-reset" type="submit" value="<?php _e( 'Reset all options', 'vigilance' ); ?>" name="reset"/>
				</form>
			</div>

			<script type="text/javascript">
				<?php
					for ($i = 0; $i < count($this->options); $i++) :
						if ( ($this->options[$i]['type'] == 'colorpicker' ) && ($this->options[$i]['pro'] != 'true' ) ) :
				?>
						jQuery("#<?php echo $this->options[$i]["id"]; ?>_colorpicker").farbtastic("#<?php echo $this->options[$i]["id"]; ?>");
				<?php
						endif;
					endfor;
				?>
				jQuery( '.colorpicker_container' ).hide();
				jQuery("div.v-field.pro input, div.v-field.pro select, div.v-field.pro textarea").attr("disabled", "disabled");
			</script>
	</div><!--end vop-body-->
</div><!--end v-options-->

			<?php
			}
		}
	}

?>