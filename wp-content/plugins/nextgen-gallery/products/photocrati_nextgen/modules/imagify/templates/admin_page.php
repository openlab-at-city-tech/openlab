<div class="wrap" id="ngg_page_content" >

    <div class="ngg_page_content_header"><img src="<?php  echo(C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')); ?>"><h3><?php esc_html_e($i18n->title) ?></h3>
    </div>

    <div class='ngg_page_content_main'>

        <div id='imagify_upgrade_page' class="wrap nextgen_plus">

            <div class="about-text">
                    <?php if ($is_imagify_activated): ?>
                        <?php esc_html_e($i18n->confirmation); ?>
                    <?php elseif ($imagify_install_url): ?>
                        <div class="imagify-content">
                            <p class="imagify-main-message"><?php esc_html_e($i18n->message); ?></p>
                            <p class="imagify-information"><?php esc_html_e($i18n->more_message); ?><br>
                                <?php print '<a target="_blank" href="' . $imagify_plugin_url . '">' . $i18n->imagify_plugin_link . '</a>'; ?><br> 
                                <?php print '<a target="_blank" href="' . $imagify_website_url . '">' . $i18n->imagify_website_link . '</a>'; ?><br>
                                <?php print '<a target="_blank" href="' . $imagify_review_url . '">' . $i18n->imagify_review_link . '</a>'; ?><br>
                            </p>
                            <p><?php print '<a class="button-primary" href="' . $imagify_install_url . '">' . $i18n->button . '</a>'; ?></p>
                            <p class="imagify-third-party"><?php esc_html_e($i18n->third_party_message); ?></p>
                        </div>
                        <div class="feature-section">
                            <img src="https://f001.backblazeb2.com/file/nextgen-gallery/imagify_preview.png"></iframe>
                        </div>

                        <div class="clear"></div>

                    <?php endif ?>    

            </div>

        </div>

    </div> 



</div>