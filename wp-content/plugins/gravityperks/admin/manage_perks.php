<?php

class GWPerksPage {

	public static function load_page() {

		self::load_perk_pointers();
		self::license_manager_form_action();

		add_action( 'admin_print_footer_scripts', array( __class__, 'output_tb_resize_script' ), 11 );

		$is_install      = gwget( 'view' ) == 'install' && current_user_can( 'install_plugins' );
		$installed_perks = GWPerks::get_installed_perks();

		wp_enqueue_style( 'gf_tooltip', GFCommon::get_base_url() . '/css/tooltip.css', null, GFCommon::$version );
		wp_print_styles( 'gf_tooltip' );

		?>

		<?php
		if ( wp_script_is( 'gform_tooltip_init', 'registered' ) ) {
			wp_print_scripts( 'gform_tooltip_init' );
		} elseif ( wp_script_is( 'gf_tooltip_init', 'registered' ) ) {
			wp_print_scripts( 'gf_tooltip_init' );
		}
		?>

		<script type="text/javascript">

			jQuery(document).ready(function($){

				// handle tabs
				var tab = <?php echo $is_install ? '"install"' : 'window.location.hash'; ?>;
				/*if(tab)
					toggleTabs(false, tab);*/

				$('h2.nav-tab-wrapper a').click(function(event){
					event.preventDefault();
					toggleTabs($(this));
				});

				// handle ajax activate/deactivate

				$(document).on('click', 'a.activate, a.deactivate, a.uninstall', function(event){
					event.preventDefault();

					var link = $(this ),
						confirmMessage = link.data( 'confirm-message' );

					if( confirmMessage && ! confirm( confirmMessage ) ) {
						return;
					}

					var spinner = gperk.ajaxSpinner( link, gperk.baseUrl + '/images/ajax-loader-trans.gif' );

					$.post(ajaxurl, {
						request_url: link.attr('href'),
						action: 'gwp_manage_perk',
						gwp_manage_perk: '<?php echo wp_create_nonce( 'gwp_manage_perk' ); ?>'
					}, function(response){
						spinner.destroy();
						var response = $.parseJSON(response);
						if(response['success']) {
							link.parents('.perk-listing').after(response['listing_html']);
							link.parents('.perk-listing').remove();
							jQuery( ".gf_tooltip" ).tooltip( {
								show: 500,
								hide: 1000,
								content: function () {
									return jQuery(this).prop('title');
								}
							} );
						}
					});

				});

				$(document).on('gperks_toggle_tabs', function() {
					sortPerks();
				});

				$( '.gp-unregistered.gf_tooltip' )
					.tooltip( 'option', {
						position: {
							my: 'left bottom',
							at: 'center-26 top-10'
						},
						tooltipClass: 'arrow-bottom gp-unregistered'
					} );

			});

			function toggleTabs(elem, tab) {

				// assume tab is passed
				if(arguments.length == 2) {
					var link = jQuery('a.nav-tab[href="' + tab + '"]');
				} else {
					var link = jQuery(elem);
					var tab = link.attr('href')
				}

				jQuery('h2.nav-tab-wrapper a').removeClass('nav-tab-active');
				link.addClass('nav-tab-active');

				jQuery('div.wrap .tab-container').hide();
				jQuery(tab).show();

				jQuery(document).trigger('gperks_toggle_tabs', tab);
			}

			function sortPerks() {
				jQuery('div#manage.perks div.perk-listing').each(function(){
					var perkListing = jQuery(this);
					if(perkListing.hasClass('active')) {
						perkListing.appendTo('div.gp-active-perks');
					} else {
						perkListing.appendTo('div.gp-inactive-perks');
					}
				});
			}

			function showLicenseSplash() {
				jQuery('#install .perk-listings').animate({'opacity': '0.3'}, 500, function(){
					jQuery('#need-license-splash').fadeIn();
				});
			}

		</script>

		<div class="wrap">

			<?php self::display_license_manager(); ?>

			<div class="icon32" id="icon-themes"><br></div>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab <?php echo ! $is_install ? 'nav-tab-active' : ''; ?>" href="#manage">Manage Perks</a>
				<?php if ( current_user_can( 'install_plugins' ) ) : ?>
					<a class="nav-tab <?php echo $is_install ? 'nav-tab-active' : ''; ?>" href="#install">Install Perks</a>
				<?php endif; ?>
			</h2>

			<div id="manage" class="perks plugins tab-container" <?php echo $is_install ? 'style="display:none;"' : ''; ?> >

				<?php
				if ( ! empty( $installed_perks ) ) {

					$active_perks   = array();
					$inactive_perks = array();

					foreach ( $installed_perks as $perk_file => $perk_data ) {
						if ( is_plugin_active( $perk_file ) ) {
							$active_perks[ $perk_file ] = $perk_data;
						} else {
							$inactive_perks[ $perk_file ] = $perk_data;
						}
					}

					if ( ! empty( $active_perks ) ) {
						?>

						<h3 class="gp-inline-header"><?php _e( 'Active Perks', 'gravityperks' ); ?></h3>
						<div class="gp-active-perks perk-listings">
							<?php
							foreach ( $active_perks as $perk_file => $perk_data ) {
								self::get_perk_listing( $perk_file, $perk_data );
							}
							?>
						</div>

						<?php
					}

					if ( ! empty( $inactive_perks ) ) {
						?>

						<h3 class="gp-inline-header"><?php _e( 'Inactive Perks', 'gravityperks' ); ?></h3>
						<div class="gp-inactive-perks perk-listings">
							<?php
							foreach ( $inactive_perks as $perk_file => $perk_data ) {
								self::get_perk_listing( $perk_file, $perk_data );
							}
							?>
						</div>

						<?php
					}

					unset( $perk_file );
					unset( $perk_data );

				} else {
					?>

					<div class="no-perks-installed">
						<?php _e( "You don't have any perks installed.", 'gravityperks' ); ?><br>
						<a onclick="jQuery('a[href=\'#install\']').click();return;"><?php _e( "Let's go install some perks!", 'gravityperks' ); ?></a>
					</div>

					<?php
				}
				?>

			</div>

			<?php
			if ( current_user_can( 'install_plugins' ) ) {
				self::install_page( $is_install );
			}
			?>

		</div>

		<?php

	}

	public static function get_uninstalled_perk_listing( $perk ) {

		$generic_perk  = new GWPerk( $perk->plugin_file, $perk->ID );
		$is_registered = GWPerks::is_perk_registered( $perk->ID );

		?>

		<div class="perk-listing <?php echo $is_registered ? 'registered' : 'install'; ?>">
			<div class="wrap">

				<h3><?php echo $perk->name; ?> <span class="version">v.<?php echo $perk->version; ?></span></h3>

				<?php if ( $perk->documentation ) : ?>
					<div class="actions">
						<a href="<?php echo $perk->documentation; ?>" target="_blank" title="<?php printf( __( '%s Documentation', 'gravityperks' ), $perk->name ); ?>">
							<?php _e( 'View Documentation', 'gravityperks' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<div class="perk-description"><?php echo $perk->sections['description']; ?></div>

				<div class="actions actions-buttons">
					<?php if ( GWPerks::has_valid_license() ) : ?>

						<?php if ( GWPerks::has_available_perks() || GWPerks::is_perk_registered( $perk->ID ) ) : ?>

							<a href="<?php echo $generic_perk->get_link_for( 'install' ); ?>" class="button"><?php _e( 'Install Perk', 'gravityperks' ); ?></a>

							<?php if ( GWPerks::is_perk_registered( $perk->ID ) ) : ?>
								<a href="<?php echo $generic_perk->get_link_for( 'deregister' ); ?>" class="button"><?php _e( 'Deregister', 'gravityperks' ); ?></a>
							<?php endif; ?>

						<?php else : ?>

							<a href="<?php echo GWPerks::get_license_upgrade_url(); ?>" class="button" target="_blank"><?php _e( 'Upgrade License', 'gravityperks' ); ?></a>

						<?php endif; ?>

					<?php else : ?>

						<a href="<?php echo GW_BUY_URL; ?>" class="button" target="_blank"><?php _e( 'Buy License', 'gravityperks' ); ?></a>

					<?php endif; ?>
				</div>

			</div>
		</div>
		<?php

	}

	public static function install_page( $is_active ) {

		$available_perks = GWPerks::get_available_perks();

		$unregistered_perks = array();
		$registered_perks   = array();

		foreach ( $available_perks as $perk ) {

			if ( ! isset( $perk->plugin_file ) || empty( $perk->plugin_file ) || GWPerk::is_installed( $perk->plugin_file ) ) {
				continue;
			}

			if ( GWPerks::is_perk_registered( $perk->ID ) ) {
				$registered_perks[] = $perk;

				continue;
			}

			$unregistered_perks[] = $perk;

		}

		?>

		<div id="install" class="perks plugins tab-container" <?php echo $is_active ? '' : 'style="display:none;"'; ?> >

			<?php if ( empty( $available_perks ) && GravityPerks::get_api_status() !== 200 ) : ?>

				<div class="install-perks-api-message">
					<?php echo GravityPerks::get_api_error_message(); ?>
				</div>

			<?php else : ?>

				<?php
				if ( ! empty( $registered_perks ) ) {
					?>

					<h3 class="gp-inline-header"><?php _e( 'Registered Perks', 'gravityperks' ); ?></h3>
					<div class="gp-registered-perks perk-listings">
						<?php
						foreach ( $registered_perks as $perk ) {
							self::get_uninstalled_perk_listing( $perk );
						}
						?>
					</div>

					<?php
				}
				?>

				<?php
				if ( ! empty( $unregistered_perks ) ) {
					?>

					<h3 class="gp-inline-header"><?php _e( 'Unregistered Perks', 'gravityperks' ); ?></h3>
					<div class="gp-unregistered-perks perk-listings">
						<?php
						foreach ( $unregistered_perks as $perk ) {
							self::get_uninstalled_perk_listing( $perk );
						}
						?>
					</div>

					<?php
				}
				?>

				<?php if ( empty( $unregistered_perks ) && empty( $registered_perks ) ) : ?>

					<div class="all-perks-installed">
						<?php _e( 'Holy cow. You must really love perks.<br /><strong>You\'ve installed them all</strong>!', 'gravityperks' ); ?>
					</div>

				<?php endif; ?>

			<?php endif; ?>

		</div>

		<?php
	}

	public static function output_tb_resize_script() {
		?>

		<script type="text/javascript">

			var thickDims, tbWidth, tbHeight;
			jQuery(document).ready(function($) {

				thickDims = function() {
					var tbWindow = $('#TB_window'), H = $(window).height(), W = $(window).width(), w, h;

					w = (tbWidth && tbWidth < W - 90) ? tbWidth : W - 90;
					h = (tbHeight && tbHeight < H - 60) ? tbHeight : H - 60;

					if(w > 800)
						w = 800;

					if ( tbWindow.size() ) {
						tbWindow.width(w).height(h);
						$('#TB_iframeContent').width(w).height(h - 27);
						tbWindow.css({'margin-left': '-' + parseInt((w / 2),10) + 'px'});
						if ( typeof document.body.style.maxWidth != 'undefined' )
							tbWindow.css({'top':'30px','margin-top':'0'});
					}
				};

			});

		</script>

		<?php
	}

	public static function get_perk_listing( $perk_file, $perk_data, $is_ajax = false ) {

		$actions              = array();
		$is_network_activated = is_plugin_active_for_network( $perk_file );
		$is_active            = is_plugin_active( $perk_file );
		$available_perks      = GWPerks::get_available_perks();

		$perk = GWPerk::get_perk( $perk_file );
		if ( is_wp_error( $perk ) ) {
			return '';
		}

		if ( $is_active ) {

			$documentation = $perk->get_documentation();
			$is_url        = ( is_array( $documentation ) && rgar( $documentation, 'type' ) == 'url' ) || strpos( (string) $documentation, 'http' ) === 0;
			$class         = $is_url ? '' : 'thickbox';
			$target        = $is_url ? '_blank' : '_self';

			$actions['documentation'] = '<a class="' . $class . '" target="' . $target . '"
	title="' . sprintf( __( '%s Documentation', 'gravityperks' ), $perk_data['Name'] ) . '"
	href="' . $perk->get_link_for( 'documentation' ) . '"
	class="documentation">' . __( 'Docs', 'gravityperks' ) . '</a>';

			if ( ! $is_network_activated && current_user_can( 'activate_plugins' ) ) {
				$actions['deactivate'] = '<a href="' . $perk->get_link_for( 'deactivate' ) . '" class="deactivate">' . __( 'Deactivate', 'gravityperks' ) . '</a>';
			}

			if ( $perk->has_method( 'settings', '' ) ) {
				$actions['settings'] = sprintf( '<a class="thickbox settings" title="Gravity Perks Settings" href="%s">%s</a>', $perk->get_link_for( 'settings' ), __( 'Settings', 'gravityperks' ) );
			}
		} else {

			if ( ! empty( $available_perks[ $perk_file ] ) && $available_perks[ $perk_file ]->documentation ) {
				$documentation            = $available_perks[ $perk_file ]->documentation;
				$actions['documentation'] = '<a target="_blank" title="' . sprintf( __( '%s Documentation', 'gravityperks' ), $perk_data['Name'] ) . '"
href="' . $documentation . '" class="documentation">' . __( 'Docs', 'gravityperks' ) . '</a>';
			}

			if ( current_user_can( 'activate_plugins' ) ) {
				$actions['activate'] = '<a href="' . $perk->get_link_for( 'activate' ) . '" class="activate">Activate</a>';
			}

			if ( current_user_can( 'delete_plugins' ) ) {
				$actions['delete'] = '<a href="' . $perk->get_link_for( 'delete' ) . '" class="delete">Delete</a>';
			}

			if ( is_callable( array( $perk, 'uninstall' ) ) && current_user_can( 'delete_plugins' ) ) {
				$actions['uninstall'] = sprintf(
					'<a href="%s" class="uninstall delete" data-confirm-message="%s">%s</a>',
					$perk->get_link_for( 'uninstall' ),
					__( 'Are you sure you want to delete this perk and all of its data?', 'gravityperks' ),
					__( 'Uninstall', 'gravityperks' )
				);
			}
		}

		$update_info     = $perk->has_update();
		$is_supported    = $perk->is_supported();
		$is_unregistered = false;

		$listing_class  = $is_active ? 'active' : 'inactive';
		$listing_class .= ! $is_active || $is_supported ? '' : ' perk-error failed-requirements';

		$available_perks  = GWPerks::get_available_perks();
		$perk_info        = rgar( $available_perks, $perk->basename );
		$license_data     = GWPerks::get_license_data();
		$registered_perks = rgar( $license_data, 'registered_perks', array() );

		if ( ! GWPerks::is_unlimited() && $perk_info && array_search( $perk_info->ID, $registered_perks ) === false ) {
			$is_unregistered = true;
			$listing_class  .= ' perk-error unregistered';
		}

		$actions = apply_filters( 'gperks_perk_action_links', array_filter( $actions ), $perk_file, $perk_data );
		$actions = apply_filters( "gperks_perk_action_links_$perk_file", $actions, $perk_file, $perk_data );

		$tooltip = '';

		if ( $is_unregistered ) {
			$tooltip .= __( '<b>This perk is unregistered.</b><br>You will not receive updates for it.', 'gravityperks' );
		}

		if ( $is_active && ! $is_supported ) {
			$tooltip .= $perk->failed_requirements_tooltip( $perk->get_failed_requirements() );
		}

		if ( $is_ajax ) {
			ob_start();
		}

		?>

		<div class="perk-listing <?php echo $listing_class; ?>">
			<div class="wrap">

				<?php if ( ! empty( $tooltip ) ) : ?>
					<span class="gp-unregistered tooltip gf_tooltip" title="<?php echo esc_attr( $tooltip ); ?>"></span>
				<?php endif; ?>

				<h3>
					<?php echo $perk_data['Name']; ?>
					<span class="version">v.<?php echo $perk_data['Version']; ?></span></h3>

				<div class="actions">
					<?php
					$action_count = count( $actions );
					$i            = 0;
					foreach ( $actions as $action => $link ) {
						if ( $action === 'activate' && $is_unregistered && ! GWPerks::has_available_perks() ) {
							$action_count--;
							continue;
						}

						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo "<span class='$action'>$link$sep</span>";
					}
					?>
				</div>

				<p class="perk-description"><?php echo gwar( $perk_data, 'Description' ); ?></p>

				<?php if ( $is_unregistered ) : ?>
					<div class="actions-buttons register-perk">
						<?php if ( GWPerks::has_valid_license() ) : ?>
							<?php if ( GWPerks::has_available_perks() ) : ?>
								<a class="button button-primary" href="
								<?php
								echo esc_html( wp_nonce_url( add_query_arg( array(
									'page'              => 'gwp_perks',
									'gwp_register_perk' => $perk_info->ID,
								), admin_url( 'admin.php' ) ), 'gwp_register_perk' ) );
								?>
																		">Register Perk</a>
							<?php else : ?>
								<a class="button button-primary" href="<?php echo GWPerks::get_license_upgrade_url(); ?>" target="_blank">Upgrade License to Register</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $update_info ) : ?>
					<div class="actions-buttons update-available">
						<?php if ( GWPerks::has_valid_license() && ! $is_unregistered ) : ?>
							<a href="<?php echo $perk->get_link_for( 'upgrade' ); ?>" class="button button-primary">Install Update (v.<?php echo $update_info->new_version; ?>)</a>
						<?php else : ?>
							<a class="button button-primary" style="cursor:pointer;" onclick="alert('<?php _e( 'You must purchase or register your license to take advantage of automatic upgrades.', 'gravityperks' ); ?>');">Install Update (v.<?php echo $update_info->new_version; ?>)</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $is_network_activated ) : ?>
					<div class="network-activated-perk">
						<a href="<?php echo network_admin_url( 'plugins.php' ); ?>" class="tooltip" tooltip="<?php echo esc_attr( __( '<h6>Network Activated Perk</h6>This perk is network activated. You can deactivate this perk from the Network Admin Plugins page.', 'gravityperks' ) ); ?>"><?php _e( 'Network Activated', 'gravityperks' ); ?></a>
					</div>
				<?php endif; ?>

			</div>
		</div>

		<?php
		return $is_ajax ? ob_get_clean() : false;
	}

	public static function ajax_manage_perk() {

		$request = parse_url( gwpost( 'request_url' ) );
		parse_str( gwar( $request, 'query' ), $request );

		if ( empty( $request ) ) {
			GWPerks::json_and_die(array(
				'error' => __( 'There was an error managing this perk.', 'gravityperks' ),
			));
		}

		$action               = gwar( $request, 'action' );
		$plugin               = gwar( $request, 'plugin' );
		$_REQUEST['_wpnonce'] = gwar( $request, '_wpnonce' );

		// use some of WPs default plugin management functionality
		// @see wp-admin/plugins.php

		switch ( $action ) {

			case 'activate':
				if ( ! current_user_can( 'activate_plugins' ) ) {
					wp_die( __( 'You do not have sufficient permissions to activate plugins for this site.', 'gravityperks' ) );
				}

				check_admin_referer( 'activate-plugin_' . $plugin );

				$result = activate_plugin( $plugin, null, is_network_admin() );

				if ( is_wp_error( $result ) ) {

					if ( 'unexpected_output' == $result->get_error_code() ) {
						$error_data = $result->get_error_data();
					} else {
						$error_data = $result;
					}

					GWPerks::json_and_die(array(
						'error'      => __( 'There was an error activating this perk.', 'gravityperks' ),
						'error_data' => $error_data,
					));

				}

				if ( ! is_network_admin() ) {

					$recent = (array) get_option( 'recently_activated' );
					unset( $recent[ $plugin ] );
					update_option( 'recently_activated', $recent );

				}

				$perk_data = GWPerk::get_perk_data( $plugin );
				GWPerks::json_and_die( array(
					'success'      => 1,
					'listing_html' => self::get_perk_listing( $plugin, $perk_data, true ),
				) );
				break;

			case 'deactivate':
				if ( ! current_user_can( 'activate_plugins' ) ) {
					wp_die( __( 'You do not have sufficient permissions to deactivate plugins for this site.', 'gravityperks' ) );
				}

				check_admin_referer( 'deactivate-plugin_' . $plugin );

				if ( ! is_network_admin() && is_plugin_active_for_network( $plugin ) ) {
					GWPerks::json_and_die( array( 'error' => __( 'This perk can only be managed from the network admin\'s Plugins page.', 'gravityperks' ) ) );
				}

				deactivate_plugins( $plugin, false, is_network_admin() );

				if ( ! is_network_admin() ) {
					update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );
				}

				$perk_data = GWPerk::get_perk_data( $plugin );
				GWPerks::json_and_die( array(
					'success'      => 1,
					'listing_html' => self::get_perk_listing( $plugin, $perk_data, true ),
				) );
				break;

			case 'uninstall':
				if ( ! current_user_can( 'delete_plugins' ) ) {
					wp_die( __( 'You do not have sufficient permissions to delete plugins for this site.', 'gravityperks' ) );
				}

				check_admin_referer( 'uninstall-plugin_' . $plugin );

				deactivate_plugins( $plugin, true );

				$perk = GWPerk::get_perk( $plugin );
				$perk->uninstall();

				$result = delete_plugins( array( $plugin ) );

				if ( $result ) {
					$response = json_encode( array(
						'success'      => 1,
						'listing_html' => '',
					) );
				} else {
					$response = __( 'ERROR' );
				}

				die( $response );

				break;
		}

		GWPerks::json_and_die( array( 'error' => __( 'There was an error managing this perk.', 'gravityperks' ) ) );

	}

	public static function display_license_manager() {
		if ( ! GWPerks::can_manage_license() ) {
			return;
		}

		if ( isset( $GLOBALS['GWP_LICENSE_NOTICE'] ) && $GLOBALS['GWP_LICENSE_NOTICE'] instanceof GWNotice ) {
			$GLOBALS['GWP_LICENSE_NOTICE']->display();
		}

		?>
		<div class="manage-menus">
			<?php
			if ( GWPerks::has_valid_license() ) :
				$license_data          = GWPerks::get_license_data();
				$registered_perk_count = is_array( $license_data['registered_perks'] ) ? count( $license_data['registered_perks'] ) : 0;
				?>
				<span class="dashicons dashicons-yes"></span>
				<strong>Gravity Perks <?php echo $license_data['price_name']; ?></strong>
				<?php
				if ( $license_data['perk_limit'] !== 0 ) {
					echo $registered_perk_count . ' / ' . $license_data['perk_limit'] . ' perks registered';
				}
				?>
				<?php if ( ! defined( 'GPERKS_LICENSE_KEY' ) || ! GPERKS_LICENSE_KEY ) : ?>
					| <a href="
					<?php
					echo esc_html( wp_nonce_url( add_query_arg( array(
						'page'                   => 'gwp_perks',
						'gwp_deactivate_license' => 1,
					), admin_url( 'admin.php' ) ), 'gwp_deactivate_license' ) );
					?>
								"
							onClick="return confirm('<?php _e( 'Are you sure you wish to deactivate your Gravity Perks license on this site?' ); ?>');">Deactivate</a>
				<?php endif; ?>
				<?php if ( $license_data['price_id'] != GW_PRICE_ID_PRO && $license_data['price_id'] != GW_PRICE_ID_LEGACY_UNLIMITED ) : ?>
				| <a href="<?php echo GWPerks::get_license_upgrade_url(); ?>" target="_blank">Upgrade</a>
				<?php endif; ?>

				| <a href="<?php echo GW_ACCOUNT_URL; ?>" target="_blank">Manage</a>

				| <a href="
				<?php
				echo esc_html( wp_nonce_url( add_query_arg( array(
					'page'              => 'gwp_perks',
					'gwp_flush_license' => 1,
				), admin_url( 'admin.php' ) ), 'gwp_flush_license' ) );
				?>
							">Refresh</a>

				| <a href="https://gravitywiz.com/documentation/license-faq/" style="text-decoration: none;" target="_blank"><span class="dashicons dashicons-editor-help"></span></a>
			<?php else : ?>
				<form id="gwp_license" method="post" action="<?php echo remove_query_arg( array( 'gwp_deactivate_license' ) ); ?>">
					<?php wp_nonce_field( 'update', 'gwp_license' ); ?>

					<input type="text" name="gwp_license_key" id="gwp_license_key" autocomplete="off"
						placeholder="Enter your Gravity Perks license..."/>

					<?php if ( GWPerks::has_valid_license() ) : ?>

						<a id="gw-get-support" href="<?php echo GW_SUPPORT_URL; ?>" target="_blank"
						class="button button-secondary"><?php _e( 'Get Support', 'gravityperks' ); ?></a>

					<?php else : ?>

						<a id="gw-buy-license" href="<?php echo GW_BUY_URL; ?>" target="_blank"
						class="button button-secondary"><?php _e( 'Buy License', 'gravityperks' ); ?></a>

					<?php endif; ?>

					<input type="submit" value="Register License" name="gwp_license_submit" id="gwp_license_submit"
						class="button button-primary"/>

				</form>
			<?php endif; ?>
		</div>
		<?php

	}

	public static function license_manager_form_action() {
		if ( ! GWPerks::can_manage_license() ) {
			return;
		}

		if ( gwar( $_POST, 'gwp_license_submit' ) ) {

			$settings = get_site_option( 'gwp_settings' ) ? get_site_option( 'gwp_settings' ) : array();

			check_admin_referer( 'update', 'gwp_license' );

			$settings = array_merge( $settings, array(
				'license_key' => trim( stripslashes( $_POST['gwp_license_key'] ) ),
			) );

			update_site_option( 'gwp_settings', $settings );

			GWPerks::flush_license( true );

			if ( ! GWPerks::has_valid_license() ) {
				if ( GravityPerks::get_api_status() !== 200 ) {
					$GLOBALS['GWP_LICENSE_NOTICE'] = new GWNotice( GravityPerks::get_api_error_message(), array( 'class' => 'inline error gwp-message' ) );
				} else {
					$license_data = GravityPerks::get_license_data();
					$message      = __( 'Oops! That doesn\'t appear to be a valid license.', 'gravityperks' );

					if ( rgar( $license_data, 'activations_left' ) === 0 ) {
						$license_limit = rgar( $license_data, 'license_limit' );

						if ( $license_limit ) {
							$message = sprintf( __( 'Oops! You have reached your license\'s site limit of %d site(s).', 'gravityperks' ), $license_limit );
						} else {
							$message = sprintf( __( 'Oops! You have reached your license\'s site limit.', 'gravityperks' ), $license_limit );
						}

						$message .= sprintf( ' <a href="%s" target="_blank">%s</a>', GravityPerks::get_license_upgrade_url(), __( 'Upgrade Now', 'gravityperks' ) );
					}

					$GLOBALS['GWP_LICENSE_NOTICE'] = new GWNotice( $message, array( 'class' => 'inline error gwp-message' ) );
				}
			}
		} elseif ( gwar( $_GET, 'gwp_deregister_perk' ) ) {
			$perk_id = gwar( $_GET, 'gwp_deregister_perk' );
			check_admin_referer( 'gwp_deregister_perk' );

			GWPerks::flush_license();

			if ( GWPerks::deregister_perk( $perk_id ) ) {
				$GLOBALS['GWP_LICENSE_NOTICE'] = new GWNotice( __( 'Perk successfully deregistered.', 'gravityperks' ), array( 'class' => 'inline notice notice-success gwp-message' ) );
			}
		} elseif ( gwar( $_GET, 'gwp_register_perk' ) ) {
			$perk_id = gwar( $_GET, 'gwp_register_perk' );
			check_admin_referer( 'gwp_register_perk' );

			GWPerks::flush_license();

			if ( GWPerks::register_perk( $perk_id ) ) {
				$GLOBALS['GWP_LICENSE_NOTICE'] = new GWNotice( __( 'Perk successfully registered.', 'gravityperks' ), array( 'class' => 'inline notice notice-success gwp-message' ) );
			}
		} elseif ( gwar( $_GET, 'gwp_deactivate_license' ) ) {

			check_admin_referer( 'gwp_deactivate_license' );

			GravityPerks::get_api()->deactivate_license();

			$settings = get_site_option( 'gwp_settings' ) ? get_site_option( 'gwp_settings' ) : array();
			$settings = array_merge( $settings, array(
				'license_key' => '',
			) );

			update_site_option( 'gwp_settings', $settings );

			GWPerks::flush_license( true );

			$GLOBALS['GWP_LICENSE_NOTICE'] = new GWNotice( __( 'License successfully deactivated.', 'gravityperks' ), array( 'class' => 'inline notice notice-success gwp-message' ) );

		} elseif ( gwar( $_GET, 'gwp_flush_license' ) ) {

			check_admin_referer( 'gwp_flush_license' );

			GWPerks::flush_license( true );

			$GLOBALS['GWP_LICENSE_NOTICE'] = new GWNotice( __( 'License successfully refreshed.', 'gravityperks' ), array( 'class' => 'inline notice notice-success gwp-message' ) );

		}

	}

	/**
	* Handle showing pointers on Perks admin pages.
	*
	*/
	public static function load_perk_pointers() {

		//delete_user_meta( get_current_user_id(), 'dismissed_wp_pointers' );

		GWPerks::dismiss_pointer( 'gwp_welcome' );

		if ( GWPerks::has_valid_license() ) {
			GWPerks::dismiss_pointer( array( 'gwp_buy_license', 'gwp_register_license' ) );
		}

		// clear the cache
		wp_cache_delete( get_current_user_id(), 'user_meta' );

		$show_pointer = false;

		foreach ( self::get_perk_pointers() as $pointer ) {
			if ( ! GWPerks::is_pointer_dismissed( $pointer['name'] ) ) {
				$show_pointer = true;
				break;
			}
		}

		if ( ! $show_pointer ) {
			return;
		}

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		add_action( 'admin_print_footer_scripts', array( __class__, 'perk_pointers_script' ) );

	}

	public static function get_perk_pointers() {
		return array(
			array(
				'name'    => 'gwp_manage_perks',
				'target'  => 'a[href="#manage"]',
				'title'   => '<h3>' . __( 'Manage Perks', 'gravityperks' ) . '</h3>',
				'content' => '<p>' . __( 'Welcome to the <strong>Manage Perks</strong> page. Here you can activate/deactivate installed perks, view documentation, modify a perk\\\'s global settings and also delete unwanted perks.', 'gravityperks' ) . '</p>',
			),
			array(
				'name'    => 'gwp_install_perks',
				'target'  => 'a[href="#install"]',
				'title'   => '<h3>' . __( 'Install Perks', 'gravityperks' ) . '</h3>',
				'content' => '<p>' . __( 'The <strong>Install Perks</strong> page provides you a complete list of perks available for installation. Just click the <em>Install</em> button on any listed perk to automatically download and install.', 'gravityperks' ) . '</p>',
				'pending' => 'gwp_manage_perks',
				'on_open' => 'toggleTabs( $(elements.element) );',
			),
			array(
				'name'     => 'gwp_buy_license',
				'target'   => 'a#gw-buy-license',
				'title'    => '<h3>' . __( 'Buy a License', 'gravityperks' ) . '</h3>',
				'content'  => '<p>' . __( 'Buy a license to receive unlimited access to <strong>all perks</strong> along with automatic upgrades and support.', 'gravityperks' ) . '</p>',
				'pending'  => 'gwp_install_perks',
				'position' => array(
					'edge'   => 'top',
					'align'  => 'right',
					'offset' => '23 0',
				),
			),
			array(
				'name'     => 'gwp_get_support',
				'target'   => 'a#gw-get-support',
				'title'    => '<h3>' . __( 'Need Help? Get Support!', 'gravityperks' ) . '</h3>',
				'content'  => '<p>' . __( 'One of the best perks of your Gravity Perks license is premium support! If you\\\'ve got a question or problem, get in touch. We are eager to help!', 'gravityperks' ) . '</p>',
				'pending'  => 'gwp_install_perks',
				'position' => array(
					'edge'  => 'top',
					'align' => 'right',
				),
			),
		);
	}

	public static function perk_pointers_script() {

		$pointers = array();

		foreach ( self::get_perk_pointers() as $pointer ) {

			if ( GWPerks::is_pointer_dismissed( $pointer['name'] ) ) {
				continue;
			}

			$pending           = gwar( $pointer, 'pending' );
			$pointer['action'] = $pending && ! GWPerks::is_pointer_dismissed( $pending ) ? '' : ".pointer('open');";

			$dependent_pointer  = self::get_pointer_dependency( $pointer['name'] );
			$pointer['on_next'] = $dependent_pointer ? "$('" . $dependent_pointer['target'] . "').pointer('open');" : '';

			$position            = gwar( $pointer, 'position' );
			$pointer['position'] = $position ? $position : array( 'edge' => 'top' );

			$class            = gwar( $pointer, 'class' );
			$pointer['class'] = $class ? $class : $pointer['name'] . '-pointer';

			$pointers[ $pointer['name'] ] = $pointer;

		}

		?>

		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			<?php foreach ( $pointers as $pointer ) : ?>

				$('<?php echo $pointer['target']; ?>').pointer({
					content: '<?php echo $pointer['title'] . $pointer['content']; ?>',
					buttons: function( event, t ) {
						var closeButton = $('<a class="close" href="#">End Tour</a>');
						var nextButton = $('<a class="close next" href="#">Next</a>');

						closeButton.bind( 'click.pointer', function(e) {
							e.preventDefault();

							jQuery.post( ajaxurl, {
								security: '<?php echo wp_create_nonce( 'gwp-dismiss-pointers' ); ?>',
								action: 'gwp_dismiss_pointers'
							});

							t.element.pointer('close');
						});

						nextButton.bind( 'click.pointer', function(e) {
							<?php echo $pointer['on_next']; ?>
							gwpDismissPointer( '<?php echo $pointer['name']; ?>' );
							t.element.pointer('close');
						});

						return closeButton<?php echo $pointer['on_next'] ? '.add(nextButton)' : ''; ?>;
					},
					position: <?php echo json_encode( $pointer['position'] ); ?>,
					pointerClass: 'gwp-pointer <?php echo $pointer['class']; ?>',
					open: function(events, elements) {
						<?php echo gwar( $pointer, 'on_open' ); ?>
					}
				})<?php echo $pointer['action']; ?>;

			<?php endforeach; ?>
		});
		function gwpDismissPointer(name) {
			jQuery.post( ajaxurl, {
				pointer: name,
				action: 'dismiss-wp-pointer'
			});
		}
		//]]>
		</script>

		<?php
	}

	public static function get_pointer_dependency( $name ) {
		foreach ( self::get_perk_pointers() as $pointer ) {
			if ( isset( $pointer['pending'] ) && $pointer['pending'] == $name && ! GWPerks::is_pointer_dismissed( $pointer['name'] ) ) {
				return $pointer;
			}
		}
		return false;
	}



	// PERK DISPLAY VIEWS //

	/**
	* Display Perk Documentation
	*
	* Acts as a style wrapper for the actual perk documentation content.
	*
	*/
	public static function load_documentation() {

		$perk = GWPerk::get_perk( gwget( 'slug' ) );
		$perk->load_perk_data();

		$page_title = sprintf( __( '%s Documentation', 'gravityperks' ), $perk->data['Name'] );

		?>

		<!DOCTYPE html>
		<html>

		<head>
		<title><?php echo $page_title; ?></title>
		<link rel='stylesheet' id='google-fonts-css'  href='https://fonts.googleapis.com/css?family=Merriweather%3A400%2C700%7CUbuntu%3A300%2C400%2C400italic%2C500italic%2C500%7CAnonymous+Pro%3A400%2C700italic%2C700%2C400italic&#038;ver=3.5' type='text/css' media='all' />
		<?php
			wp_print_styles( array( 'gwp-admin', 'colors-fresh' ) );
			wp_print_scripts( array( 'jquery' ) );
		?>
		<script type="text/javascript">
			parent.window.thickDims();
		</script>
		</head>

		<body class="perk-iframe">

			<div class="wrap documentation">
				<h1 class="page-title"><?php echo $page_title; ?></h1>
				<div class="content">
					<?php $perk->display_documentation(); ?>
				</div>
				<div class="content-footer">
					<?php if ( isset( $perk->data['PluginURI'] ) ) { ?>
						<a href="<?php echo $perk->data['PluginURI']; ?>" target="_blank">View this Perk's Home Page</a>
					<?php } ?>
				</div>
			</div>

		</body>
		</html>

		<?php
		exit;
	}

	public static function load_perk_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			die( __( 'You don\'t have permission to access this page.' ) );
		}

		$perk = GWPerk::get_perk( gwget( 'slug' ) );
		$perk->load_perk_data();

		if ( isset( $_POST['gwp_save_settings'] ) ) {

			check_admin_referer( 'gp_save_settings', 'security' );

			$setting_keys = array();

			if ( method_exists( $perk, 'register_settings' ) ) {
				$setting_keys = $perk->register_settings( $perk );
				if ( empty( $setting_keys ) ) {
					$setting_keys = array();
				}
			}

			$settings = self::get_submitted_settings( $perk, $setting_keys );

			if ( ! empty( $settings ) ) {
				GWPerk::save_perk_settings( $perk->get_id(), $settings );
				$notice = new GWNotice( __( 'Settings saved successfully.', 'gravityperks' ) );
			} else {
				$notice = new GWNotice( __( 'Settings were not saved.', 'gravityperks' ), array( 'class' => 'error' ) );
			}
		}

		$page_title = sprintf( __( '%s Settings', 'gravityperks' ), $perk->data['Name'] );

		?>

		<!DOCTYPE html>
		<html>

		<head>
		<title><?php echo $page_title; ?></title>
		<?php
			// Resolves issues with the 3rd party scripts checking for get_current_screen().
			remove_all_actions( 'wp_print_styles' );
			remove_all_actions( 'wp_print_scripts' );
			wp_print_styles( array( 'gwp-admin', 'wp-admin', 'buttons', 'colors-fresh' ) );
			wp_print_scripts( array( 'jquery', 'gwp-admin' ) );
		?>
		</head>

		<body class="perk-iframe wp-core-ui">

			<div class="wrap perk-settings">
				<form action="" method="post">
					<div class="header">
						<h1 class="page-title"><?php echo $page_title; ?></h1>
						<?php
						if ( isset( $notice ) ) :
							$notice->display();
						endif;
						?>
					</div>
					<div class="content">
						<?php echo $perk->get_settings(); ?>
					</div>
					<div class="content-footer">
						<?php wp_nonce_field( 'gp_save_settings', 'security' ); ?>
						<input type="submit" id="gwp_save_settings" name="gwp_save_settings" class="button button-primary" value="<?php _e( 'Save Settings', 'gravityperks' ); ?>" />
					</div>
				</form>
			</div>

			<script type="text/javascript">
			setTimeout('jQuery(".updated").slideUp();', 5000);
			</script>

		</body>
		</html>

		<?php
		exit;
	}

	public static function get_submitted_settings( $perk, $setting_keys, $flush_values = false ) {

		$settings = array();

		foreach ( $setting_keys as $setting_key => $setting_children ) {

			if ( ! is_array( $setting_children ) ) {
				$setting_key      = $setting_children;
				$key              = $perk->get_id() . "_{$setting_key}";
				$settings[ $key ] = $flush_values ? false : gwpost( $key );
			} else {
				$key              = $perk->get_id() . "_{$setting_key}";
				$settings[ $key ] = $flush_values ? false : gwpost( $key );
				$settings         = array_merge( $settings, self::get_submitted_settings( $perk, $setting_children, ! $settings[ $key ] ) );
			}
		}

		return $settings;
	}

}
