<?php wp_nonce_field('b2s_security_nonce', 'b2s_security_nonce'); ?>
<div class="b2s-container">
    <div class="col-md-offset-3 col-lg-offset-4 col-md-6 col-lg-4 col-xs-12">
        <div class="panel panel-group">
            <div class="panel-body">
                <form method="post" action="#" id="prgLogin" enctype="multipart/form-data">
                    <!--Logo-->
                    <a target="_blank" href="https://www.pr-gateway.de">
                        <img class="img-responsive" src="<?php echo esc_url(plugins_url('/assets/images/prg/prg_logo.png', B2S_PLUGIN_FILE)); ?>" alt="pr-gateway">
                    </a>
                    <!--Form-->
                    <div id="prgLoginInfoFail" class="panel panel-group panel-danger" style="display: none;">
                        <div class="panel-body">
                            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> 
                            <?php esc_html_e('Login failed. Please check your username and a password!', 'blog2social') ?>
                        </div>
                    </div>
                    <div id="prgLoginInfoSSL" class="panel panel-group panel-danger" style="display: none;">
                        <div class="panel-body">
                            <span class="glyphicon glyphicon-remove glyphicon-danger"></span> 
                            <?php esc_html_e('Login failed. Please check your server settings. OpenSSL must be enabled on.', 'blog2social') ?>
                        </div>
                    </div>
                    <input type="text" name="username" id="username" placeholder="<?php esc_attr_e('E-Mail or Username', 'blog2social') ?>" required class="form-control input-lg"/>
                    <input type="password" class="form-control input-lg" id="password" placeholder="<?php esc_attr_e('Password', 'blog2social') ?>" required />
                    <input type="hidden" name="postId" id="postId" value="<?php echo (int) $_GET['postId']; ?>">
                    <input type="hidden" name="token" value="<?php echo esc_attr(base64_encode(time())); ?>"/>
                    <button type="submit" name="submit" id="prgLoginBtn" class="btn btn-lg btn-primary btn-block"><?php esc_html_e('Sign in', 'blog2social') ?></button>
                    <div>
                        <a target="_blank" href="http://prg.li/pr-gateway-connect-registration"><?php esc_html_e('create account', 'blog2social') ?></a> or <a target="_blank" href="https://www.pr-gateway.de/component/users/?view=reset"><?php esc_html_e('reset password', 'blog2social') ?></a>
                    </div>
                </form>
                <!--Register-->
                <div class="form-info">
                    <hr>
                    <h3><?php esc_html_e('Test PR-Gateway for free', 'blog2social') ?></h3>
                    <b>
                        <?php esc_html_e('1x publish press release', 'blog2social'); ?> <br> 
                        <?php esc_html_e('1x publish report', 'blog2social'); ?> <br>
                        <?php esc_html_e('1x promote event', 'blog2social'); ?> <br>
                    </b>
                    <br><br>
                    <?php esc_html_e('The press distribution PR gateway automatically publish your press releases and events with one click.Publish your message over 250 portals.', 'blog2social') ?>
                    <br>
                    <br>
                    <a class="btn btn-lg btn-warning btn-block" target="_blank" href="http://prg.li/pr-gateway-connect-registration"><?php esc_html_e('Start your 14-Day Free Trial', 'blog2social') ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4"></div>
</div>
