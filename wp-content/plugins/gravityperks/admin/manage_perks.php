<?php

class GWPerksPage {

    public static function load_page() {

        self::load_perk_pointers();

        add_action( 'admin_print_footer_scripts', array(__class__, 'output_tb_resize_script'), 11 );

        $is_install = gwget('view') == 'install' && current_user_can( 'install_plugins' );
        $installed_perks = GWPerks::get_installed_perks();

        wp_enqueue_style( 'gf_tooltip', GFCommon::get_base_url() . '/css/tooltip.css', null, GFCommon::$version );
        wp_print_styles( 'gf_tooltip' );

        ?>

        <style type="text/css">

            #gwp-header-links { float: right; margin-top: -36px; }
                #gwp-header-links li { display: inline; padding-left: 10px; }

            .gp-active-perks { }

            .perks { padding: 20px 0 0; }

            .perks .manage-perks-intro,
            .perks .install-perks-intro { border-bottom: 1px solid #eee; padding: 0 20px 20px; margin: 0 0 20px; }
                .perks .install-perks-intro h3 { margin-top: 0; }
            .perks .no-perks-installed { padding: 60px 20px 20px; margin: 0 0 20px; text-align: center; font-size: 24px;
                line-height: 36px; }
                .perks .no-perks-installed a { cursor: pointer; }

            .perks .all-perks-installed { padding: 60px 20px 20px; margin: 0 0 20px; text-align: center; font-size: 24px;
                line-height: 36px; width: 100%; }

            #install.tab-container { position: relative; }
                #install #need-license-splash { background-color: #fff; position: absolute; z-index: 99;
                    top: 100px; left: 50%; margin-left: -25%; width: 44%; padding: 3%;
                    box-shadow: 0 0 100px 100px #fff; }
                #install #need-license-splash .perk-listing { width: auto; }
                #install #need-license-splash .perk-listing .wrap { min-height: 0; }
                #install #need-license-splash h3 { margin: 0 0 18px; }
                #install #need-license-splash p { margin: 0 0 18px; }
                #install #need-license-splash .dismiss-link { float: right; line-height: 22px; }

            .perk-listings {
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
                -webkit-flex-wrap: wrap;
                -ms-flex-wrap: wrap;
                flex-wrap: wrap;
            }
            .perk-listing { background-color: #f4f8fc; box-shadow: 0 0 20px #D8E9FA inset;
                border: 1px solid #eee; float: left; margin: 0 10px 20px; width: 260px;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
            }

                .perk-listing .wrap { padding: 15px; border: 4px solid #fff; min-height: 160px; margin: 0;
                    position: relative; }
                .perk-listing h3 { margin: 0 0 4px; }
                .perk-listing p { margin: 0; padding: 0; }
                .perk-listing span.version { color: #6E9FB5; }
                .perk-listing .actions { margin: 0 0 12px; }

                    .perks .perk-listing a.button-primary { color: #fff; }

                .perk-listing .network-activated-perk { background-color: #fff; padding: 5px 7px; line-height: 1;
                    position: absolute; bottom: 0; right: 15px; border-top-left-radius: 4px; border-top-right-radius: 4px; }
                .perk-listing .network-activated-perk a { background: none repeat scroll 0 0 transparent; display: block;
                    font-size: 10px; height: auto; text-decoration: none; text-indent: 0; text-transform: uppercase; width: auto; }

                .perk-listing .update-available { margin-top: 12px; text-align: center; }

                .qtip-content ul { margin: 0; }

            .perk-listing.install,
            .perk-listing.inactive { background-color: #f7f7f7; box-shadow: 0 0 20px #e7e7e7 inset; padding: 0; }

                .perk-listing.install .actions { position: absolute; bottom: 10px; }
                .perk-listing.install h3 { margin: 0 0 12px; }
                .perk-listing.install span.version,
                .perk-listing.inactive span.version { color: #999; }

            .perk-listing.failed-requirements { background-color: #FFEBE8; box-shadow: 0 0 20px #FCCCC8 inset; }

                .perk-listing.failed-requirements span.version { color: #999; }
                .perk-listing.failed-requirements a { color: #7F564D; }
                .perk-listing.failed-requirements a.gp-requirements { background-image: url(<?php echo GWPerks::get_base_url(); ?>/images/icon-exclamation.png);
                    text-indent: -999em; display: inline-block; height: 16px; }

            .forms_page_gwp_perks .gwp_buy_license-pointer .wp-pointer-arrow { left: auto; right: 50px; }
            .forms_page_gwp_perks .gwp_register_license-pointer .wp-pointer-arrow { left: auto; right: 44px; }
            .forms_page_gwp_perks .gwp_get_support-pointer .wp-pointer-arrow { left: auto; right: 27px; }

        </style>

        <?php 
        if( wp_script_is( 'gform_tooltip_init', 'registered' ) ) {
			wp_print_scripts( 'gform_tooltip_init' );
        } else if( wp_script_is( 'gf_tooltip_init', 'registered' ) ) {
        	wp_print_scripts( 'gf_tooltip_init' );
		}
        ?>

        <script type="text/javascript">

            jQuery(document).ready(function($){

                // handle tabs
                var tab = <?php echo $is_install ? '"install"' : 'window.location.hash' ?>;
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
                        gwp_manage_perk: '<?php echo wp_create_nonce('gwp_manage_perk'); ?>'
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

                <?php if( self::show_splash() ): ?>
                    if( tab != 'install' ) {
                        $(document).one('gperks_toggle_tabs.splash', function() {
                            showLicenseSplash();
                        });
                    } else {
                        showLicenseSplash();
                    }
                <?php endif; ?>

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

            function dismissLicenseSplash() {
                jQuery.post( ajaxurl, {
                    pointer: 'need_license_splash',
                    action: 'dismiss-wp-pointer'
                });
                jQuery('#need-license-splash').fadeOut(function(){
                    jQuery('#install .perk-listings').animate({'opacity': '1.0'}, 500);
                });
            }

        </script>

        <div class="wrap">

            <div class="icon32" id="icon-themes"><br></div>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo !$is_install ? 'nav-tab-active' : ''; ?>" href="#manage">Manage Perks</a>
                <?php if( current_user_can( 'install_plugins' ) ): ?>
                    <a class="nav-tab <?php echo $is_install ? 'nav-tab-active' : ''; ?>" href="#install">Install Perks</a>
                <?php endif; ?>
            </h2>

            <?php self::display_header_links(); ?>

            <?php self::handle_message_code(); ?>

            <div id="manage" class="perks plugins tab-container" <?php echo $is_install ? 'style="display:none;"' : ''; ?> >

                <?php
                if(!empty($installed_perks)) {

                    $active_perks = $inactive_perks = array();
                    foreach($installed_perks as $perk_file => $perk_data) {
                        if( is_plugin_active($perk_file) ) {
                            $active_perks[$perk_file] = $perk_data;
                        } else {
                            $inactive_perks[$perk_file] = $perk_data;
                        }
                    }

                    if(!empty($active_perks)) {
                        ?>

                        <h3 class="gp-inline-header"><?php _e('Active Perks', 'gravityperks'); ?></h3>
                        <div class="gp-active-perks perk-listings">
                            <?php foreach($active_perks as $perk_file => $perk_data) {
                                self::get_perk_listing($perk_file, $perk_data);
                            } ?>
                        </div>

                        <?php
                    }

                    if(!empty($inactive_perks)) {
                        ?>

                        <h3 class="gp-inline-header"><?php _e('Inactive Perks', 'gravityperks'); ?></h3>
                        <div class="gp-inactive-perks perk-listings">
                            <?php foreach($inactive_perks as $perk_file => $perk_data) {
                                self::get_perk_listing($perk_file, $perk_data);
                            } ?>
                        </div>

                        <?php
                    }

                    unset($perk_file);
                    unset($perk_data);

                } else {
                    ?>

                    <div class="no-perks-installed">
                        <?php printf( __('You don\'t have any perks installed.<br /> %sLet\'s go install some perks!%s', 'gravityperks'), '<a onclick="jQuery(\'a[href=\\\'#install\\\']\').click();">', '</a>'); ?>
                    </div>

                    <?php
                }
                ?>

            </div>

            <?php
            if( current_user_can( 'install_plugins' ) ) {
                self::install_page( $is_install );
            }
            ?>

        </div>

        <?php

    }

    public static function install_page( $is_active ) {
        ?>

        <div id="install" class="perks plugins tab-container <?php echo self::show_splash() ? 'splash' : ''; ?>" <?php echo $is_active ? '' : 'style="display:none;"'; ?> >

            <?php if( self::show_splash() ):
                $generic_perk = new GWPerk();
                ?>
                <div id="need-license-splash" style="display:none;">
                    <div class="perk-listing">
                        <div class="wrap">
                            <h3><?php _e('Want Access to All Perks? Buy a License!', 'gravityperks'); ?></h3>
                            <p><?php printf( __('Purchase a Gravity Perks license and install as many perks as you\'d like! If you\'ve already purchased a license you can register it via the "Register License" button below.', 'gravityperks'),
                                '<a href="' . GW_REGISTER_LICENSE_URL . '">', '</a>' ); ?></p>
                            <!--<p><?php _e('Keep your Gravity Perks license active for unlimited access to <strong>all current and future</strong> perks . You\'ll also get free automatic upgrades and premium support.', 'gravityperks' ); ?></p>-->
                            <div class="gp-license-splash-actions">
                                <a href="<?php echo GW_BUY_GPERKS_URL; ?>" class="button-primary" target="_blank"><?php _e('Buy License', 'gravityperks'); ?></a>
                                <a href="<?php echo GW_REGISTER_LICENSE_URL; ?>&register=1" class="button-secondary"><?php _e('Register License', 'gravityperks'); ?></a>
                                <a href="javascript:void(0);" onclick="dismissLicenseSplash();" class="dismiss-link"><?php _e('dismiss', 'gravityperks'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="perk-listings">

            <?php
            $available_perks = GWPerks::get_available_perks();
            $i = 0;
            foreach($available_perks as $perk):

                if( !isset($perk->plugin_file) || empty($perk->plugin_file) || GWPerk::is_installed( $perk->plugin_file ) )
                     continue;

                $generic_perk = new GWPerk($perk->plugin_file);

                ?>

                <div class="perk-listing install">
                    <div class="wrap">

                        <h3><?php echo $perk->name; ?> <span class="version">v.<?php echo $perk->version; ?></span></h3>

                        <div class="actions">
                            <?php if( GWPerks::has_valid_license() ): ?>
                                <a href="<?php echo $generic_perk->get_link_for('install'); ?>" class="button"><?php _e('Install Perk', 'gravityperks'); ?></a>
                            <?php else: ?>
                                <a href="<?php echo GW_BUY_GPERKS_URL; ?>" class="button" target="_blank"><?php _e('Buy License', 'gravityperks'); ?></a>
                            <?php endif; ?>
                        </div>

                        <div class="perk-description"><?php echo $perk->sections['description']; ?></div>

                    </div>
                </div>

            <?php endforeach;

            if(!isset($generic_perk)): ?>

                <div class="all-perks-installed">
                    <?php _e('Holy cow. You must really love perks.<br /><strong>You\'ve installed them all</strong>!', 'gravityperks'); ?>
                </div>

            <?php endif;

            unset($perk);
            $i++;
            ?>

            </div> <!-- / perk-listings -->

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

    public static function get_perk_listing($perk_file, $perk_data, $is_ajax = false) {

        $actions = array();
        $is_network_activated = is_plugin_active_for_network($perk_file);
        $is_active = is_plugin_active($perk_file);

        $perk = GWPerk::get_perk( $perk_file );
        if( is_wp_error( $perk ) ) {
            return '';
        }

        if( $is_active ) {

            if(!$is_network_activated)
                $actions['deactivate'] = '<a href="' . $perk->get_link_for('deactivate') . '" class="deactivate">' . __('Deactivate', 'gravityperks') . '</a>';

            if( method_exists( $perk, 'documentation' ) ) {
                
                $documentation = $perk->get_documentation();
                $is_url = is_array( $documentation ) && rgar( $documentation, 'type' ) == 'url';
                $class = $is_url ? '' : 'thickbox';
                $target = $is_url ? '_blank' : '_self';
                
                $actions['documentation'] = '<a class="' . $class . '" target="' . $target . '" title="Gravity Perks Documentation" href="' . $perk->get_link_for('documentation') . '" class="documentation">' . __('Documentation', 'gravityperks') . '</a>';
                
            }

            if(method_exists($perk, 'settings'))
                $actions['settings'] = '<a class="thickbox" title="Gravity Perks Settings" href="' . $perk->get_link_for('settings') . '" class="settings">' . __('Settings', 'gravityperks') . '</a>';

        }
        else {

            $actions['activate'] = '<a href="' . $perk->get_link_for('activate') . '" class="activate">Activate</a>';
            $actions['delete'] = '<a href="' . $perk->get_link_for('delete') . '" class="delete">Delete</a>';

            if( is_callable( array( $perk, 'uninstall' ) ) ) {
                $actions['uninstall'] = sprintf(
                    '<a href="%s" class="uninstall delete gf_tooltip" title="%s" data-confirm-message="%s">%s</a>',
                        $perk->get_link_for( 'uninstall' ),
                        __( '<h6>Uninstall Perk</h6> <em>Uninstalling</em> a perk you will <strong>completely remove its data and all files</strong>. This option is available for some perks which create custom tables or store a significant amount of data in the form meta.', 'gravityperks' ),
                        __( 'Are you sure you want to delete this perk and all of its data?', 'gravityperks' ),
                        __( 'Uninstall', 'gravityperks' )
                );
            }

        }

        $update_info  = $perk->has_update();
        $is_supported = $perk->is_supported();

        $listing_class = $is_active ? 'active' : 'inactive';
        $listing_class .= ! $is_active || $is_supported ? '' : ' failed-requirements';

        $actions = apply_filters( 'gperks_perk_action_links', array_filter( $actions ), $perk_file, $perk_data );
        $actions = apply_filters( "gperks_perk_action_links_$perk_file", $actions, $perk_file, $perk_data );

        if( $is_ajax ) {
            ob_start();
        }

        ?>

        <div class="perk-listing <?php echo $listing_class; ?>">
            <div class="wrap">

                <h3>
                    <?php if($is_active && !$is_supported ): ?>
                        <span class="requirements"><?php $perk->failed_requirements_tooltip( $perk->get_failed_requirements() ); ?></span>
                    <?php endif; ?>
                    <?php echo $perk_data['Name']; ?>
                    <span class="version">v.<?php echo $perk_data['Version']; ?></span></h3>

                <div class="actions">
                    <?php
                    $action_count = count( $actions );
                    $i = 0;
                    foreach ( $actions as $action => $link ) {
                        ++$i;
                        ( $i == $action_count ) ? $sep = '' : $sep = ' | ';
                        echo "<span class='$action'>$link$sep</span>";
                    } ?>
                </div>

                <p class="perk-description"><?php echo gwar($perk_data, 'Description'); ?></p>

                <?php if($is_network_activated): ?>
                    <div class="network-activated-perk">
                        <a href="<?php echo network_admin_url('plugins.php'); ?>" class="tooltip" tooltip="<?php echo esc_attr(__('<h6>Network Activated Perk</h6>This perk is network activated. You can deactivate this perk from the Network Admin Plugins page.', 'gravityperks')); ?>"><?php _e('Network Activated', 'gravityperks'); ?></a>
                    </div>
                <?php endif; ?>

                <?php if($update_info): ?>
                    <div class="update-available">
                        <?php if( GWPerks::has_valid_license() ): ?>
                            <a href="<?php echo $perk->get_link_for('upgrade'); ?>" class="button button-primary">Install Update (v.<?php echo $update_info->new_version; ?>)</a>
                        <?php else: ?>
                            <a class="button button-primary" style="cursor:pointer;" onclick="alert('<?php _e('You must purchase or register your license to take advantage of automatic upgrades.', 'gravityperks'); ?>');">Install Update (v.<?php echo $update_info->new_version; ?>)</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <?php
        return $is_ajax ? ob_get_clean() : false;
    }

    public static function ajax_manage_perk() {

        $request = parse_url(gwpost('request_url'));
        parse_str(gwar($request, 'query'), $request);

        if(empty($request)) {
            GWPerks::json_and_die(array(
                'error' => __('There was an error managing this perk.', 'gravityperks')
                ));
        }

        $action = gwar($request, 'action');
        $plugin = gwar($request, 'plugin');
        $_REQUEST['_wpnonce'] = gwar($request, '_wpnonce');

        // use some of WPs default plugin management functionality
        // @see wp-admin/plugins.php

        switch($action) {

            case 'activate':

                if ( ! current_user_can('activate_plugins') )
                    wp_die(__('You do not have sufficient permissions to activate plugins for this site.', 'gravityperks'));

                check_admin_referer('activate-plugin_' . $plugin);

                $result = activate_plugin($plugin, null, is_network_admin() );

                if ( is_wp_error( $result ) ) {

                    if ( 'unexpected_output' == $result->get_error_code() ) {
                        $error_data = $result->get_error_data();
                    } else {
                        $error_data = $result;
                    }

                    GWPerks::json_and_die(array(
                        'error' => __('There was an error activating this perk.', 'gravityperks'),
                        'error_data' => $error_data
                        ));

                }

                if ( ! is_network_admin() ) {

                    $recent = (array) get_option( 'recently_activated' );
                    unset( $recent[ $plugin ] );
                    update_option( 'recently_activated', $recent );

                }

                $perk_data = GWPerk::get_perk_data( $plugin );
                GWPerks::json_and_die( array('success' => 1, 'listing_html' => self::get_perk_listing($plugin, $perk_data, true) ) );
                break;

            case 'deactivate':

                if ( ! current_user_can('activate_plugins') )
                    wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.', 'gravityperks'));

                check_admin_referer('deactivate-plugin_' . $plugin);

                if ( ! is_network_admin() && is_plugin_active_for_network( $plugin ) ) {
                    GWPerks::json_and_die( array('error' => __('This perk can only be managed from the network admin\'s Plugins page.', 'gravityperks')) );
                }

                deactivate_plugins( $plugin, false, is_network_admin() );

                if ( ! is_network_admin() )
                    update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );

                $perk_data = GWPerk::get_perk_data( $plugin );
                GWPerks::json_and_die( array('success' => 1, 'listing_html' => self::get_perk_listing($plugin, $perk_data, true) ) );
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

                if( $result ) {
                    $response = json_encode( array(
                        'success' => 1,
                        'listing_html' => ''
                    ) );
                } else {
                    $response = __( 'ERROR' );
                }

                die( $response );

                break;
        }

        GWPerks::json_and_die( array('error' => __('There was an error managing this perk.', 'gravityperks')) );

    }

    public static function display_header_links() {

        $header_links = array();

        if( GWPerks::has_valid_license() ) {
            $header_links[] = array( 'label' => __('Get Support', 'gravityperks'), 'href' => GW_SUPPORT_URL, 'target' => '_blank', 'id' => 'gw-get-support' );
            $header_links[] = array( 'label' => __('Settings', 'gravityperks'), 'href' => GW_SETTINGS_URL, 'id' => 'gw-settings' );
        } else {
            $header_links[] = array( 'label' => __('Buy License', 'gravityperks'), 'href' => GW_BUY_GPERKS_URL, 'target' => '_blank', 'id' => 'gw-buy-license' );
            $header_links[] = array( 'label' => __('Register License', 'gravityperks'), 'href' => GW_REGISTER_LICENSE_URL, 'id' => 'gw-register-license' );
        }

        $header_links = apply_filters( 'gperks_header_links', $header_links );
        $links = array();

        foreach( $header_links as $header_link ) {

            $class = gwar($header_link, 'class') ? gwar($header_link, 'class') : 'button-secondary';
            $target = gwar($header_link, 'target') ? 'target="' . gwar($header_link, 'target') . '"' : '';
            $id = gwar($header_link, 'id') ? 'id="' . gwar($header_link, 'id') . '"' : '';

            $links[] = "<a class=\"{$class}\" href=\"{$header_link['href']}\" {$target} {$id} >{$header_link['label']}</a>";
        }

        echo '<ul id="gwp-header-links"><li>' . implode('</li><li>', $links) . '</li></ul>';

    }

    /**
    * Handle showing pointers on Perks admin pages.
    *
    */
    public static function load_perk_pointers() {

        //delete_user_meta( get_current_user_id(), 'dismissed_wp_pointers' );

        GWPerks::dismiss_pointer( 'gwp_welcome' );

        if( GWPerks::has_valid_license() )
            GWPerks::dismiss_pointer( array('gwp_buy_license', 'gwp_register_license') );

        // clear the cache
        wp_cache_delete( get_current_user_id(), 'user_meta' );

        $show_pointer = false;

        foreach( self::get_perk_pointers() as $pointer ) {
            if( !GWPerks::is_pointer_dismissed( $pointer['name'] ) ) {
                $show_pointer = true;
                break;
            }
        }

        if( !$show_pointer )
            return;

        wp_enqueue_style( 'wp-pointer' );
        wp_enqueue_script( 'wp-pointer' );

        add_action( 'admin_print_footer_scripts', array( __class__, 'perk_pointers_script' ) );

    }

    public static function get_perk_pointers() {
        return array(
            array(
                'name' => 'gwp_manage_perks',
                'target' => 'a[href="#manage"]',
                'title' => '<h3>' . __('Manage Perks', 'gravityperks') . '</h3>',
                'content' => '<p>' . __('Welcome to the <strong>Manage Perks</strong> page. Here you can activate/deactivate installed perks, view documentation, modify a perk\\\'s global settings and also delete unwanted perks.', 'gravityperks') . '</p>'
                ),
            array(
                'name' => 'gwp_install_perks',
                'target' => 'a[href="#install"]',
                'title' => '<h3>' . __('Install Perks', 'gravityperks') . '</h3>',
                'content' => '<p>' . __('The <strong>Install Perks</strong> page provides you a complete list of perks available for installation. Just click the <em>Install</em> button on any listed perk to automatically download and install.', 'gravityperks') . '</p>',
                'pending' => 'gwp_manage_perks',
                'on_open' => 'toggleTabs( $(elements.element) );'
                ),
            array(
                'name' => 'gwp_buy_license',
                'target' => 'a#gw-buy-license',
                'title' => '<h3>' . __('Buy a License', 'gravityperks') . '</h3>',
                'content' => '<p>' . __('Buy a license to receive unlimited access to <strong>all perks</strong> along with automatic upgrades and support.', 'gravityperks') . '</p>',
                'pending' => 'gwp_install_perks',
                'position' => array( 'edge' => 'top', 'align' => 'right', 'offset' => '23 0' )
                ),
            array(
                'name' => 'gwp_register_license',
                'target' => 'a#gw-register-license',
                'title' => '<h3>' . __('Register Your License', 'gravityperks') . '</h3>',
                'content' => '<p>' . __('Already purchased a license? Register your license to enable unlimited access to <strong>all perks</strong> along with automatic upgrades and support.', 'gravityperks') . '</p>',
                'pending' => 'gwp_buy_license',
                'position' => array( 'edge' => 'top', 'align' => 'right' )
                ),
            array(
                'name' => 'gwp_get_support',
                'target' => 'a#gw-get-support',
                'title' => '<h3>' . __('Need Help? Get Support!', 'gravityperks') . '</h3>',
                'content' => '<p>' . __('One of the best perks of your Gravity Perks license is premium support! If you\\\'ve got a question or problem, get in touch. We are eager to help!', 'gravityperks') . '</p>',
                'pending' => 'gwp_install_perks',
                'position' => array( 'edge' => 'top', 'align' => 'right' )
                )
            );
    }

    public static function perk_pointers_script() {


        $pointers = array();

        foreach( self::get_perk_pointers() as $pointer ) {

            if( GWPerks::is_pointer_dismissed( $pointer['name'] ) )
                continue;

            $pending = gwar($pointer, 'pending');
            $pointer['action'] = $pending && !GWPerks::is_pointer_dismissed( $pending ) ? '' : ".pointer('open');";

            $dependent_pointer = self::get_pointer_dependency( $pointer['name'] );
            $pointer['on_close'] = $dependent_pointer ? "$('" . $dependent_pointer['target'] . "').pointer('open');" : '';

            $position = gwar($pointer, 'position');
            $pointer['position'] = $position ? $position : array( 'edge' => 'top' );

            $class = gwar($pointer, 'class');
            $pointer['class'] = $class ? $class : $pointer['name'] . '-pointer';

            $pointers[$pointer['name']] = $pointer;

        }

        ?>

        <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready( function($) {
            <?php foreach( $pointers as $pointer ): ?>

                $('<?php echo $pointer['target']; ?>').pointer({
                    content: '<?php echo $pointer['title'] . $pointer['content']; ?>',
                    position: <?php echo json_encode($pointer['position']); ?>,
                    pointerClass: '<?php echo $pointer['class']; ?>',
                    open: function(events, elements) {
                        <?php echo gwar( $pointer, 'on_open' ); ?>
                    },
                    close: function() {
                        gwpDismissPointer( '<?php echo $pointer['name']; ?>' );
                        <?php echo $pointer['on_close']; ?>
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

    public static function get_pointer_dependency($name) {
        foreach( self::get_perk_pointers() as $pointer ) {
            if( isset( $pointer['pending'] ) && $pointer['pending'] == $name && !GWPerks::is_pointer_dismissed( $pointer['name'] ) )
                return $pointer;
        }
        return false;
    }

    public static function show_splash() {
        return false;
        $splash_dismissed = GWPerks::is_pointer_dismissed( 'need_license_splash' );
        return !GWPerks::has_valid_license() && !$splash_dismissed;
    }



    // PERK DISPLAY VIEWS //

    /**
    * Display Perk Documentation
    *
    * Acts as a style wrapper for the actual perk documentation content.
    *
    */
    public static function load_documentation() {

        $perk = GWPerk::get_perk(gwget('slug'));
        $perk->load_perk_data();

        $page_title = sprintf(__('%s Documentation', 'gravityperks'), $perk->data['Name']);

        ?>

        <!DOCTYPE html>
        <html>

        <head>
        <title><?php echo $page_title; ?></title>
        <link rel='stylesheet' id='google-fonts-css'  href='http://fonts.googleapis.com/css?family=Merriweather%3A400%2C700%7CUbuntu%3A300%2C400%2C400italic%2C500italic%2C500%7CAnonymous+Pro%3A400%2C700italic%2C700%2C400italic&#038;ver=3.5' type='text/css' media='all' />
        <?php
            wp_print_styles(array('gwp-admin', 'colors-fresh'));
            wp_print_scripts(array('jquery'));
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
                    <?php if(isset($perk->data['PluginURI'])) { ?>
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

        $perk = GWPerk::get_perk(gwget('slug'));
        $perk->load_perk_data();

        if(isset($_POST['gwp_save_settings'])) {

            $settings = $setting_keys = array();

            if(method_exists($perk, 'register_settings')) {
                $setting_keys = $perk->register_settings($perk);
                if(empty($setting_keys))
                    $setting_keys = array();
            }

            $settings = self::get_submitted_settings($perk, $setting_keys);

            if(!empty($settings)) {
                GWPerk::save_perk_settings($perk->get_id(), $settings);
                $notice = new GWNotice(__('Settings saved successfully.', 'gravityperks'));
            } else {
                $notice = new GWNotice(__('Settings were not saved.', 'gravityperks'), array('class' => 'error'));
            }


        }

        $page_title = sprintf(__('%s Settings', 'gravityperks'), $perk->data['Name']);

        ?>

        <!DOCTYPE html>
        <html>

        <head>
        <title><?php echo $page_title; ?></title>
        <?php
            // Resolves issues with the 3rd party scripts checking for get_current_screen().
            remove_all_actions( 'wp_print_styles' );
            remove_all_actions( 'wp_print_scripts' );
            wp_print_styles(array('gwp-admin', 'wp-admin', 'buttons', 'colors-fresh'));
            wp_print_scripts(array('jquery', 'gwp-admin'));
        ?>
        </head>

        <body class="perk-iframe wp-core-ui">

            <div class="wrap perk-settings">
                <form action="" method="post">
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                    <div class="content">
                        <?php

                        if(isset($notice))
                            $notice->display();

                        $perk->settings();

                        ?>
                    </div>
                    <div class="content-footer">
                        <input type="submit" id="gwp_save_settings" name="gwp_save_settings" class="button button-primary" value="<?php _e('Save Settings', 'gravityperks'); ?>" />
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



    // REVIEW ALL CODE BELOW //






    public static function load_perk_info() {

        // TODO: update perk info page to be a bit fancier, using default Perk plugin info for now
        GWPerks::display_change_log();
        exit;

    }

    public static function process_actions() {

        $action = gwget('action');
        $slug = gwget('slug');

        if(!$action)
            return;

        if($action && $slug && !wp_verify_nonce(gwget('_wpnonce'), $slug)) {
            die(__('Oops! Doesn\'t look like you have permission to do this.', 'gravityperks'));
        }

        if(!in_array($action, array('activate', 'deactivate', 'delete'))) {
            die(__('What exactly are you trying to do?', 'gravityperks'));
        }

        $perks = GWPerks::get_installed_perks();

        foreach($perks as $perk) {
            if($perk->slug == $slug)
                break;
        }

        switch($action) {

        case 'activate':
            //$perk->activate();
            $message = 1;
            break;

        case 'deactivate':
            $perk->deactivate();
            $message = 2;
            break;

        case 'delete':
            $message = $perk->delete() ? 5 : 6;
        }

        wp_redirect(admin_url("admin.php?page=gwp_perks&message=$message"));
    }

    public static function handle_message_code() {

        $message_code = gwget('message');

        if(!$message_code)
            return;

        $message = gwar(GWPerks::$message_codes, $message_code);

        echo "<div id=\"message\" class=\"updated below-h2\"><p>$message</p></div>";

    }

    public static function get_submitted_settings($perk, $setting_keys, $flush_values = false) {

        $settings = array();

        foreach($setting_keys as $setting_key => $setting_children) {

            if(!is_array($setting_children)) {
                $setting_key = $setting_children;
                $key = $perk->get_id() . "_{$setting_key}";
                $settings[$key] = $flush_values ? false : gwpost($key);
            } else {
                $key = $perk->get_id() . "_{$setting_key}";
                $settings[$key] = $flush_values ? false : gwpost($key);
                $settings = array_merge($settings, self::get_submitted_settings($perk, $setting_children, !$settings[$key]));
            }

        }

        return $settings;
    }

}

class GWExecTime {

    public $start_time;
    public $end_time;

    function __construct() {
        $this->start();
    }

    function start() {
        $time = microtime(true);
        $this->start_time = $time;
    }

    function report($content = '', $echo = true ) {
        $time_since = $this->time_since_start();

        $output = $content ? "($content) " : '';
        $output .= 'Execution time: ' . $time_since . ' seconds...<br />';

        if($echo)
            echo $output;

        return $output;
    }

    function time_since_start() {
        $time = microtime(true);
        $this->end_time = $time;
        return round($this->end_time - $this->start_time, 4, PHP_ROUND_HALF_UP);
    }

}