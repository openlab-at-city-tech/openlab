<!--Header-->
<?php $prgInfo = get_option('B2S_PLUGIN_PRG_' . B2S_PLUGIN_BLOG_USER_ID);?>
<div class="col-md-12 del-padding-left">
    <div class="col-md-9 del-padding-left del-padding-right">
<?php if (isset($_GET['prgLogout'])) { ?>
            <div class="panel panel-group">
                <div class="panel-body">
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> <?php esc_html_e('You are signed out of PR-Gateway!', 'blog2social') ?>
                </div>
            </div>
        <?php } ?>
<?php if (isset($_GET['prgShip'])) { ?>
            <div class="panel panel-group">
                <div class="panel-body">
                    <span class="glyphicon glyphicon-ok glyphicon-success"></span> 
                    <?php if (isset($_GET['type']) && (int) $_GET['type'] == 1) { ?>
                        <?php esc_html_e('Your message will now be sent over PR gateway to the press portals!', 'blog2social') ?> <br>
                        <?php esc_html_e('See all publications for your message live on ', 'blog2social') ?> <a target="_blank" href="https://www.pr-gateway.de/presseverteiler/presseverteiler-funktionen"> https://www.pr-gateway.de </a>
                    <?php } else { ?>          
                        <?php esc_html_e('Your message has been saved by PR-Gateway as a draft!', 'blog2social') ?>
    <?php } ?>
                </div>
            </div>
<?php } ?> 
        <div id="prgShipInvalidData" class="panel panel-group panel-danger" style="display: none;">
            <div class="panel-body">
                <span class="glyphicon glyphicon-remove glyphicon-danger"></span>
<?php esc_html_e('Unfortunately, your request cannot be processed by Blog2Social. Please try again!', 'blog2social') ?>
            </div>
        </div>
        <div id="prgShipFail" class="panel panel-group panel-danger" style="display: none;">
            <div class="panel-body">
                <span class="glyphicon glyphicon-remove glyphicon-danger"></span>
<?php esc_html_e('Your message was not successfully transmitted. Please try again!', 'blog2social') ?>
            </div>
        </div>
        <div class="panel panel-group b2s-nonce-check-fail" style="display: none;">
            <div class="panel-body">
                <span class="glyphicon glyphicon-remove glyphicon-danger"></span> <?php esc_html_e('The link you followed has expired. Please refresh your page.', 'blog2social'); ?>
            </div>
        </div>
<?php if (isset($title) && !empty($title)) { ?>
            <div  class="panel panel-group">
                <div class="panel-body">
                    <b><?php esc_html_e('Post', 'blog2social') ?>:</b> <?php echo esc_html($title); ?>
                </div>
            </div>
        <?php } ?>

<?php if (isset($_GET['page']) && $_GET['page'] != 'prg-ship' && !isset($prgInfo['B2S_PRG_ID'])) { ?>
            <div class="panel panel-group">
                <div class="panel-body">
    <?php esc_html_e('PR-Gateway offers a paid online distribution service for submitting press releases, articles and social media news to more than 250 news sites, special interest websites and social news sites. If your blog posts provide trade or industry information or expert articles (no advertising), you may submit them to PR-Gateway to turn them into valid online press releases or online articles and select a specific choice of websites and services to publish your post.', 'blog2social'); ?>    
                    <a target="_blank" href="http://prg.li/pr-gateway-connect-registration"><?php esc_html_e('Register here to open your PR-Gateway account.', 'blog2social'); ?></a>
                </div>
            </div>
<?php } ?> 

    </div>
    <div class="col-md-3 del-padding-left">         
        <div class="pull-right hidden-xs hidden-sm padding-bottom-10 padding-left-5">
            <div class="btn-group hidden-sm hidden-xs">
                <a target="_blank" href="http://www.pr-gateway.de">
                    <img class="prg-logo pull-right img-responsive " src="<?php echo esc_url(plugins_url('/assets/images/prg/prg_logo.png', B2S_PLUGIN_FILE)); ?>" alt="pr-gateway">
                </a>
            </div>
        </div>
        <div class="pull-right">
<?php if (isset($prgInfo['B2S_PRG_ID']) && !empty($prgInfo['B2S_PRG_ID']) && isset($prgInfo['B2S_PRG_TOKEN']) && !empty($prgInfo['B2S_PRG_TOKEN'])) { ?>
                <div class="btn-group text-center">
                    <a href="#" id="prgLogoutBtn" class="btn btn-sm btn-warning"><?php esc_html_e('Logout', 'blog2social') ?></a>
                </div>
<?php } ?>
        </div>
        <div class="visible-sm visible-xs">
            <div class="prg-padding-bottom-30"></div>
        </div>       
    </div>
</div>
<div class="clearfix"></div>
<!--Header-->
<div class="prg-loading-area" style="display: none;">
    <br>
    <div class="prg-loader-impulse prg-loader-impulse-md"></div>
    <div class="clearfix"></div>
<?php esc_html_e('Loading...', 'blog2social') ?>
</div>




