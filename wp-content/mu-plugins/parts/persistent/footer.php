<?php
/**
 * OpenLab footer markup
 */
?>

<div id="openlab-footer" class="oplb-bs<?php echo ($placeholder ? ' placeholder' : ''); ?>">
    <div class="oplb-bs">
        <div class="footer-wrapper">
            <div class="container-fluid footer-desktop">
                <div class="row row-footer">
                    <div class="col-sm-12 col-logos">
                        <h2><span>OPENLAB at City Tech:</span><span>A place to learn, work, and share</span></h2>
                        <div class="logos-wrapper clearfix">
                            <p class="statement semibold"><span class="semibold">OpenLab</span> is an open-source, digital platform designed to support teaching and learning at New York City College of Technology (NYCCT), and to promote student and faculty engagement in the intellectual and social life of the college community.</p>
                            <a class="pull-left citytech-logo" href="http://www.citytech.cuny.edu/" target="_blank"><img class="img-responsive" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/ctnyc_seal.png" alt="New York City College of Technology" border="0" /></a>
                            <a class="pull-left cuny-logo " href="http://www.cuny.edu/" target="_blank"><img class="img-responsive" src="<?php echo bp_root_domain(); ?>/wp-content/mu-plugins/css/images/cuny_logo.png" alt="City University of New York" border="0" /></a>
                        </div>
                    </div>
                    <div class="horiz-bar-wrapper"><div class="horiz-bar"></div></div>
                    <div class="col-sm-8 col-links semibold">
                        <h2>Support</h2>
                        <a class="no-deco roll-over-color" href="<?php echo $site; ?>/blog/help/openlab-help/">Help</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/contact-us/">Contact Us</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/terms-of-service/">Terms of Use</a> <span class="horiz-divider">|</span> <a class="no-deco roll-over-color" href="<?php echo $site; ?>/about/credits/">Credits</a>
                    </div>
                    <div class="horiz-bar-wrapper"><div class="horiz-bar"></div></div>
                    <div class="col-sm-2 col-share">
                        <h2>Share</h2>
                        <a class="rss-link" href="<?php echo $site . "/activity/feed/" ?>">RSS</a>
                        <a class="google-plus-link" href="https://plus.google.com/share?url=<?= $url ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
                                return false;">Google +</a>
                    </div>
                </div>
                <div class="row row-copyright">
                    <div class="col-sm-24">
                        <p><span>&copy; <a class="no-deco roll-over-color" href="http://www.citytech.cuny.edu/" target="_blank">New York City College of Technology</a></span> <span class="horiz-divider">|</span> <span><a class="no-deco roll-over-color" href="http://www.cuny.edu" target="_blank">City University of New York</a></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="visible-xs" id="go-to-top" href="#"><span class="fa fa-chevron-circle-up"></span><br />top</a>
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