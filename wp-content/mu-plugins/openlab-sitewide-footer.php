<?php

/**
 * Adds 'local environment' tab
 */
function cuny_local_env_flag() {

    if (defined('IS_LOCAL_ENV') && IS_LOCAL_ENV) {
        $env_type = 'local';
        if (defined('ENV_TYPE')) {
            $env_type = ENV_TYPE;
        }
        ?>

        <style type="text/css">
            #local-env-flag {
                position: fixed;
                left: 0;
                top: 35px;
                width: 150px;
                padding: 10px 15px;
                text-align: center;
                background: #600;
                color: #fff;
                font-size: 1em;
                line-height: 1.8em;
                border: 2px solid #666;
                z-index: 1000;
                opacity: 0.7;
            }
        </style>

        <div id="local-env-flag">
            <?php echo esc_html(strtoupper($env_type)) ?> ENVIRONMENT
        </div>

        <?php
    }
}

//add_action( 'wp_footer', 'cuny_local_env_flag' );
//add_action( 'admin_footer', 'cuny_local_env_flag' );

add_action('wp_print_styles', 'cuny_site_wide_navi_styles');

function cuny_site_wide_navi_styles() {
    global $blog_id;
    $sw_navi_styles = set_url_scheme(WPMU_PLUGIN_URL . '/css/sw-navi.css');

    if ($blog_id == 1)
        return;

    wp_register_style('SW_Navi_styles', $sw_navi_styles);
    wp_enqueue_style('SW_Navi_styles');

    //google fonts
    wp_register_style('google-fonts', set_url_scheme('http://fonts.googleapis.com/css?family=Arvo'), $sw_navi_styles);
    wp_enqueue_style('google-fonts');
}

//add_action('wp_head', 'cuny_login_popup_script');
function cuny_login_popup_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            var cpl = jQuery('#cuny-popup-login');
            jQuery("#popup-login-link").show();
            jQuery(cpl).hide();

            jQuery("#popup-login-link").click(function () {
                if ('none' == jQuery(cpl).css('display')) {
                    jQuery(cpl).show();
                    jQuery("#sidebar-user-login").focus();
                } else {
                    jQuery(cpl).hide();
                }

                return false;
            });

            jQuery(".close-popup-login").click(function () {
                jQuery(cpl).hide();
            });
        });
    </script>
    <?php
}

add_action('wp_footer', 'cuny_site_wide_footer');

function cuny_site_wide_footer() {
    global $blog_id;
    switch_to_blog(1);
    $site = site_url();
    restore_current_blog();

    openlab_footer_markup();
    //see explanation below
    openlab_footer_markup(true);
}

/**
 * Markup for openlab footer
 * The placholder variable outputs a set of footer markup (minus any js) for purposes of
 * vertical spacing; some of the themes available to group sites have a max width limit set on the ** * body,so the visible footer is absolutely positioned in order to maintain the end-to-end
 * appearance across all themes. However, on smaller screen sizes, the footer naturally grows in
 * height, and yet the main page container has no idea this is happening. As a result, the footer
 * begins to creep up and hide page elements. The placholder footer is set to visibility: hidden, so
 * it will not be visible to the user, yet will provide the page container the necessary height
 * feedback to keep all page elements above the footer
 * @param type $placholder
 */
function openlab_footer_markup($placeholder = NULL) {
    $site = bp_get_root_domain();
    ?>
    <div id="openlab-footer" class="oplb-bs<?php echo ($placeholder ? ' placeholder' : ''); ?>">
        <div class="oplb-bs">
            <!--footer: desktop-->
            <div class="container-fluid footer-desktop hidden-xs">
                <div class="row row-footer">
                    <div class="col-sm-12 col-logos">
                        <h2>OPENLAB at City Tech: A place to learn, work, and share</h2>
                        <p class="semibold"><a class="pull-left" href="http://www.citytech.cuny.edu/" target="_blank"><img src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/ctnyc_seal.png" alt="Ney York City College of Technology" border="0" /></a>
                            <a class="pull-left" href="http://www.cuny.edu/" target="_blank"><img class="cuny-logo" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/cuny_logo.png" alt="Ney York City College of Technology" border="0" /></a>OpenLab is an open-source, digital platform designed to support teaching and learning at New York City College of Technology (NYCCT), and to promote student and faculty engagement in the intellectual and social life of the college community.</p>
                    </div>
                    <div class="horiz-bar-wrapper"><div class="horiz-bar"></div></div>
                    <div class="col-sm-8 col-links semibold">
                        <h2>Support</h2>
                        <a class="no-deco roll-over-color" href="<?php echo $site; ?>/blog/help/openlab-help/">Help</a> | <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/contact-us/">Contact Us</a> | <a class="no-deco roll-over-color" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a> | <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/terms-of-service/">Terms of Use</a> | <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/credits/">Credits</a>
                    </div>
                    <div class="horiz-bar-wrapper"><div class="horiz-bar"></div></div>
                    <div class="col-sm-2 col-share">
                        <h2>Share</h2>
                        <a class="rss-link" href="<?php echo $site . "/activity/feed/" ?>">RSS</a>
                        <!-- Place this tag in your head or just before your close body tag -->
                        <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                        <a class="google-plus-link" href="https://plus.google.com/share?url={URL}" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
            return false;">Google +</a>
                    </div>
                </div>
                <div class="row row-copyright">
                    <div class="col-sm-24">
                        <p><span>&copy; <a class="no-deco roll-over-color" href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span> | <span><a class="no-deco roll-over-color" href="http://www.cuny.edu" target="_blank">City University of New York</a></span></p>
                    </div>
                </div>
            </div>
            <!--end footer: desktop-->
            <!--footer: mobile-->
            <div class="container-fluid footer-mobile visible-xs">
                <div class="footer-wrapper">
                    <div class="row row-footer">
                        <div class="col-sm-12 col-logos">
                            <h2>OPENLAB at City Tech:<br /><span>A place to learn, work, and share</span></h2>
                            <p><span class="semibold">OpenLab</span> is an open-source, digital platform designed to support teaching and learning at New York City College of Technology (NYCCT), and to promote student and faculty engagement in the intellectual and social life of the college community.</p>
                            <p class="clearfix mobile-logos"><a class="pull-left" href="http://www.citytech.cuny.edu/" target="_blank"><img class="img-responsive" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/ctnyc_seal.png" alt="Ney York City College of Technology" border="0" /></a>
                                <a class="pull-left" href="http://www.cuny.edu/" target="_blank"><img class="cuny-logo img-responsive" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/cuny_logo.png" alt="Ney York City College of Technology" border="0" /></a></p>
                        </div>
                        <div class="row-copyright">
                            <div class="col-sm-24">
                                <p><span>&copy; <a class="no-deco roll-over-color" href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span> | <span><a class="no-deco roll-over-color" href="http://www.cuny.edu" target="_blank">City University of New York</a></span></p>
                            </div>
                        </div>
                        <div class="col-sm-8 col-links semibold">
                            <h2>Support</h2>
                            <ul class="support-list">
                                <li><a class="no-deco roll-over-color" href="<?php echo $site; ?>/blog/help/openlab-help/">Help</a></li>
                                <li><a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/contact-us/">Contact Us</a></li>
                                <li><a class="no-deco roll-over-color" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a></li>
                                <li><a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/terms-of-service/">Terms of Use</a></li>
                                <li><a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/credits/">Credits</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-2 col-share">
                            <h2>Share</h2>
                            <a class="rss-link" href="<?php echo $site . "/activity/feed/" ?>">RSS</a>
                            <!-- Place this tag in your head or just before your close body tag -->
                            <script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
                            <a class="google-plus-link" href="https://plus.google.com/share?url={URL}" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
            return false;">Google +</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end footer: mobile-->
        </div>
        <a id="go-to-top" href="#"><span class="fa fa-chevron-circle-up"></span><br />top</a>
    </div>
    <?php /**
     * Adds divs that can be used for client-side detection of bootstrap breakpoints
     */ ?>
    <div class="device-xs visible-xs"></div>
    <div class="device-sm visible-sm"></div>
    <div class="device-md visible-md"></div>
    <div class="device-lg visible-lg"></div>

    <?php if (!$placeholder): ?>
        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-24214531-1']);
            _gaq.push(['_setDomainName', 'openlab.citytech.cuny.edu']);
            _gaq.push(['_trackPageview']);

            (function () {
                var ga = document.createElement('script');
                ga.type = 'text/javascript';
                ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(ga, s);
            })();

            jQuery(document).ready(function ($) {
                getCurrentScroll();

                //go to top functionality
                $('#go-to-top').on('click', function (e) {
                    e.preventDefault();

                    var offsetHeight = $('#wpadminbar').height() + $('.navbar').height();

                    $.smoothScroll({
                        offset: -offsetHeight
                    });

                });

            });
            jQuery(window).scroll(function ($) {
                getCurrentScroll();
            });

            function getCurrentScroll() {
                //go to top button functionality
                if (isBreakpoint('xs')) {

                    var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

                    if (currentScroll > 250) {
                        jQuery('#go-to-top').css('display', 'block');
                    } else {
                        jQuery('#go-to-top').css('display', 'none');
                    }

                }
            }

            //detection of bootstrap breakpoints
            function isBreakpoint(alias) {
                return jQuery('.device-' + alias).is(':visible');
            }

        </script>
    <?php endif; ?>
    <?php
}

remove_action('init', 'maybe_add_existing_user_to_blog');
add_action('init', 'maybe_add_existing_user_to_blog', 90);

function bbg_debug_queries() {
    if (!is_super_admin()) {
        return;
    }

    if (empty($_GET['debug_queries'])) {
        return;
    }

    global $wpdb;
    echo '<pre>';
    foreach ($wpdb->queries as $q) {
        if ($q[1] > 1) {
            print_r($q);
        }
    }
}

register_shutdown_function('bbg_debug_queries');
