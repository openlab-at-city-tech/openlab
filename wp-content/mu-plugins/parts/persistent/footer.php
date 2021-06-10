<?php
/**
 * OpenLab footer markup
 */
?>

<div id="openlab-footer" class="oplb-bs <?php echo ($blog_id === 1 ? ' page-table-row' : '') ?><?php echo ($placeholder ? ' placeholder' : ''); ?>" <?php echo ($placeholder ? 'aria-hidden="true" tabindex="-1"' : ''); ?>>
    <div class="oplb-bs">
        <div class="footer-wrapper">
            <div class="container-fluid footer-desktop">
                <div class="ol-footer-row row-footer">
                    <div class="col-sm-12 ol-footer-col">
                        <div class="col-logos ol-footer-col">
                            <h2><span>The OpenLab at City Tech:</span><span>A place to learn, work, and share</span></h2>
                            <div class="logos-wrapper clearfix">
                                <p class="statement semibold"><span class="semibold">The OpenLab</span> is an open-source, digital platform designed to support teaching and learning at City Tech (New York City College of Technology), and to promote student and faculty engagement in the intellectual and social life of the college community.</p>
                                <a class="pull-left citytech-logo" href="http://www.citytech.cuny.edu/" target="_blank"><img class="img-responsive" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/ctnyc_seal.png" alt="New York City College of Technology" border="0" /></a>
                                <a class="pull-left cuny-logo " href="http://www.cuny.edu/" target="_blank"><img class="img-responsive" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/cuny_logo.png" alt="City University of New York" border="0" /></a>
                            </div>
                        </div>
                        <div class="ol-footer-row row-copyright hidden-xs hidden-sm ol-footer-col">
                            <div class="col-sm-24 ol-footer-col">
                                <p><span><a class="no-deco roll-over-color" href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span> <span class="horiz-divider">|</span> <span><a class="no-deco roll-over-color" href="http://www.cuny.edu" target="_blank">City University of New York</a></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="horiz-bar-wrapper"><div class="horiz-bar"></div></div>
                    <div class="col-sm-8 col-links semibold ol-footer-col">
                        <h2 class="first-header">Support</h2>
                        <a class="no-deco roll-over-color" href="<?php echo $site; ?>/blog/help/openlab-help/">Help</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/contact-us/">Contact Us</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/terms-of-service/">Terms of Use</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/credits/">Credits</a>

						<h2 class="second-header">Accessibility</h2>
						<p class="statement">Our goal is to make the OpenLab accessible for all users.</p>
						<p class="statement"><a class="deco roll-over-color" href="<?php echo esc_attr( $accessibility_link ) ?>">Learn more about accessibility on the OpenLab</a></p>

                    </div>
                    <div class="horiz-bar-wrapper"><div class="horiz-bar"></div></div>
                    <div class="col-sm-3 col-copyright ol-footer-col">
                        <h2>Copyright</h2>
                        <h3 class="third-header"><a href="https://creativecommons.org/licenses/by-nc-sa/3.0/">Creative Commons</a></h3>
                        <ul>
                            <li>- Attribution</li>
                            <li>- NonCommercial</li>
                            <li>- ShareAlike</li>
                        </ul>
                        <a href="https://creativecommons.org/licenses/by-nc-sa/3.0/"><img src="<?php echo bp_root_domain() ?>/wp-content/mu-plugins/css/images/by-nc-sa.png" alt="Creative Commons" /></a>
                    </div>
                </div>
                <div class="ol-footer-row row-copyright hidden-md hidden-lg">
                    <div class="col-sm-24 ol-footer-col">
                        <p><span>&copy; <a class="no-deco roll-over-color" href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span> <span class="horiz-divider">|</span> <span><a class="no-deco roll-over-color" href="http://www.cuny.edu" target="_blank">City University of New York</a></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (!$placeholder): ?>
        <a class="visible-xs" id="go-to-top" href="#"><span class="fa fa-chevron-circle-up"></span><br />top</a>
    <?php endif; ?>
</div>

<?php if (!$placeholder): ?>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T5XJ92C" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <script type="text/javascript">

		<?php if ( openlab_load_google_analytics() ) : ?>
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
		<?php endif; ?>

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
            var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScroll > 250) {
                jQuery('#go-to-top').css('display', 'block');
            } else {
                jQuery('#go-to-top').css('display', 'none');
            }

        }

        //detection of bootstrap breakpoints
        function isBreakpoint(alias) {
            return jQuery('.device-' + alias).is(':visible');
        }

    </script>
<?php endif; ?>
