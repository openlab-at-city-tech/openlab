<?php

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/**
 * nggallery_admin_overview()
 *
 * Add the admin overview the dashboard style
 * @return NULL
 */
function nggallery_admin_overview()
{
    $action_status = array('message' => '', 'status' => 'ok');

    M_Gallery_Display::enqueue_fontawesome();

    if ($action_status['message']!='') { ?>
        <div id="message" class="<?php echo ($action_status['status']=='ok' ? 'updated' : $action_status['status']); ?> fade">
            <p><strong><?php echo $action_status['message']; ?></strong></p>
        </div>
    <?php } ?>

    <div class="wrap about-wrap ngg_overview">

        <div class="ngg_page_content_header">
            <img src="<?php  echo(C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')); ?>" alt=""/>
            <h3><?php echo _e( 'Welcome to NextGEN Gallery', 'nggallery' ); ?></h3>
        </div>

        <?php /* Disabled 2020-10-16 - the wizard is broken and needs to be fixed or replaced
        <div class="about-text" id="ngg-gallery-wizard">
            <span><?php echo __("Need help getting started? ", 'nggallery')?></span>
            <?php echo ' <a data-ngg-wizard="nextgen.beginner.gallery_creation_igw" class="ngg-wizard-invoker button-primary" href="' . esc_url(add_query_arg('ngg_wizard', 'nextgen.beginner.gallery_creation_igw')) . '">' . __('Launch Gallery Wizard', 'nggallery') . '</a>'; ?>
        </div> */ ?>

        <div class='ngg_page_content_menu'>
            <a href="javascript:void(0)" data-id="welcome-link"><?php _e( 'Welcome', 'nggallery' ); ?></a>
            <a href="javascript:void(0)" data-id="videos-link" style="display:none;"><?php _e( 'More Videos', 'nggallery'); ?></a>
            <?php
            $found = FALSE;
            if (defined('NEXTGEN_GALLERY_PRO_PLUGIN_BASENAME')
            ||  defined('NGG_PRO_PLUGIN_BASENAME')
            ||  defined('NGG_PLUS_PLUGIN_BASENAME'))
                $found = TRUE;
            if (!$found) { ?>
                <a href="javascript:void(0)" data-id="ngg-vs-pro-link"><?php _e( 'NextGEN Basic vs Pro', 'nggallery'); ?></a>
            <?php } ?>

            <?php if (wp_get_theme() != "Imagely"): ?>
            <a href="javascript:void(0)" data-id="genesis-link"><?php _e( 'Imagely Themes', 'nggallery'); ?></a>
            <?php endif ?>
            <?php if (!$found) { ?>
                <a href="javascript:void(0)" data-id="pro-link"><?php _e('Unlock More', 'nggallery'); ?></a>
            <?php } ?>
        </div>

        <div class='ngg_page_content_main'>

            <div data-id="welcome-link">

                <div class="about-text"><strong><?php printf( __( "Congrats! You're now running the most popular WordPress gallery plugin of all time.")) ?></strong><br><?php printf( __( "To get started, watch our two minute intro below.")) ?>
                </div>

                <div class="headline-feature feature-video">
                <iframe width="1050"
                    height="590"
                    src="https://www.youtube.com/embed/ZAYj6D5XXNk"
                    frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media;"
                    allowfullscreen></iframe>
                </div>

            </div>

            <div data-id="videos-link" style="display:none;">

                <p class="about-text"><?php printf( __( 'We have a growing list of video tutorials to get you started. Watch some below or head over to <a href="%s" target="_blank">NextGEN Gallery University on YouTube</a> to see all available vidoes.', 'nggallery' ), esc_url( 'https://www.youtube.com/playlist?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM' ) ); ?>
                </p>

                <div class="headline-feature feature-video">
                    <iframe width="1280" height="720" src="https://www.youtube.com/embed/4Phvmm3etnw?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM" frameborder="0" allowfullscreen></iframe>
                </div>

                <div class="feature-section two-col">
                    <div class="col">
                        <div class="headline-feature feature-video">
                            <iframe width="1280" height="720" src="https://www.youtube.com/embed/h_HrKpkI90w?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col">
                        <div class="headline-feature feature-video">
                            <iframe width="1280" height="720" src="https://www.youtube.com/embed/58u9AjMJqJM?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                <div class="feature-section two-col">
                    <div class="col">
                        <div class="headline-feature feature-video">
                            <iframe width="1280" height="720" src="https://www.youtube.com/embed/OfxWM59fb_Y?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="col">
                        <div class="headline-feature feature-video">
                            <iframe width="1280" height="720" src="https://www.youtube.com/embed/BiFjDYIaZZw?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM" frameborder="0" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                <p class="about-text"><?php printf( __( 'Want more? Head over to <a href="%s" target="_blank">NextGEN Gallery University on YouTube</a>.', 'nggallery' ), esc_url( 'https://www.youtube.com/playlist?list=PL9cmsdHslD0vIcJjBggJ-XMjtwvqrRgoM' ) ); ?>
                </p>

            </div>

            <div data-id="pro-link">
                <h2><?php _e( 'Upgrade to NextGEN Pro!' ); ?></h2>
                <p class="about-text"><span style="font-weight: bold;"><?php _e( 'The most powerful gallery system ever built for WordPress. ', 'nggallery' ); ?></span><br><?php _e( 'Gorgeous new gallery displays, image protection, full screen lightbox, commenting and social sharing for individual images, proofing, ecommerce, digital downloads, and more.', 'nggallery' ); ?></p>
                <p class="about-text"><a style='background-color: #9ebc1b' href='<?php print esc_attr(M_Marketing::get_utm_link('https://www.imagely.com/wordpress-gallery-plugin/nextgen-pro/', 'overviewunlockmore', 'getnextgenpro')); ?>' target='_blank' class="button-primary ngg-pro-upgrade"><?php _e( 'Get NextGEN Pro Now', 'nggallery' ); ?></a></p>

                <div class="feature-section">
                    <iframe src="https://www.youtube.com/embed/ePnYGQX0Lf8" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>

            <div data-id="ngg-vs-pro-link" id="ngg-basic-vs-pro">
                <style>
                    #ngg-basic-vs-pro {
                        width: 100%;
                    }
                    #ngg_page_content #ngg-basic-vs-pro h2 {
                        margin-top: 0;
                    }
                    #ngg-basic-vs-pro table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    #ngg-basic-vs-pro th {
                        font-weight: 600;
                        font-size: 18px;
                        line-height: 36px;
                        padding: 15px;
                        border: 1px solid #DDDDDD;
                        vertical-align: middle;
                        text-align: center;
                    }

                    #ngg-basic-vs-pro tbody th {
                        text-align: left;
                        background-color: rgb(241,241,241);
                        padding-left: 25px;
                    }

                    #ngg-basic-vs-pro td {
                        border: 1px solid #DDDDDD;
                        padding: 30px;
                        vertical-align: top;
                        width: 33%;
                        font-size: 18px;
                        line-height: 24px;
                    }

                    #ngg_page_content #ngg-basic-vs-pro th h2 {
                        text-align: center;
                        text-transform: none;
                    }

                    #ngg_page_content #ngg-basic-vs-pro thead th h2 {
                        margin: 0;
                        padding: 0;
                    }

                    #ngg-basic-vs-pro td:nth-of-type(2) {
                        background-color: rgb(243, 249, 254);
                    }

                    #ngg-basic-vs-pro td p {
                        margin: 0;
                        padding: 0;
                        font-size: 18px;
                        line-height: 24px;
                    }

                    #ngg-basic-vs-pro tbody td.ngg-features-column {
                        text-align: center;
                    }

                    #ngg-basic-vs-pro th.ngg-empty-th {
                        border: none;
                        background: inherit;
                    }

                    #ngg-basic-vs-pro td i {
                        margin-right: 3px;
                    }
                    #ngg-basic-vs-pro i.ngg-features-none {
                        color: gray;
                    }
                    #ngg-basic-vs-pro i.ngg-features-full {
                        color: rgb(158, 188, 27);
                    }

                    #ngg-basic-vs-pro table h1, #ngg-basic-vs-pro table h2,
                    #ngg-basic-vs-pro table h3, #ngg-basic-vs-pro table h4,
                    #ngg-basic-vs-pro table h5, #ngg-basic-vs-pro table h6 {
                        margin: 0;
                        padding: 0;
                    }
                </style>
                <h2><?php print __('NextGEN Basic vs Pro'); ?></h2>

                <p>
                    <?php print __('Get the most out of NextGEN Gallery by upgrading to Pro and unlocking all the powerful features.', 'nggallery'); ?>
                </p>

                <?php
                M_Marketing::enqueue_blocks_style();
                $stupid = [
                    __('Gallery Types', 'nggallery') => [
                        __('Filmstrip Gallery',     'nggallery') => [TRUE, FALSE],
                        __('Masonry Gallery',       'nggallery') => [TRUE, FALSE],
                        __('Mosaic Gallery',        'nggallery') => [TRUE, FALSE],
                        __('Tiled Gallery',         'nggallery') => [TRUE, FALSE],
                        __('Film Gallery',          'nggallery') => [TRUE, FALSE],
                        __('Blogstyle Gallery',     'nggallery') => [TRUE, FALSE]
                    ],
                    __('Ecommerce', 'nggallery') => [
                        __('Ecommerce',                   'nggallery') => [TRUE, FALSE],
                        __('Paid Digital Downloads',      'nggallery') => [TRUE, FALSE],
                        __('Coupons',                     'nggallery') => [TRUE, FALSE],
                        __('Price Lists',                 'nggallery') => [TRUE, FALSE],
                        __('Automated Tax Calculations',  'nggallery') => [TRUE, FALSE],
                        __('Automated Print Fulfillment', 'nggallery') => [TRUE, FALSE],
                        __('Proofing',                    'nggallery') => [TRUE, FALSE]
                    ],
                    __('Interface', 'nggallery') => [
                        __('Hover Captions',        'nggallery') => [TRUE, FALSE],
                        __('Lazy Loading',          'nggallery') => [TRUE, FALSE],
                        __('Infinite Scroll',       'nggallery') => [TRUE, FALSE]
                    ],
                    __('Image Upload / Processing', 'nggallery') => [
                        __('Lightroom Plugin',       'nggallery') => [TRUE, FALSE]
                    ],
                    __('Other', 'nggallery') => [
                        __('Digital Downloads',         'nggallery') => [TRUE, FALSE],
                        __('Image Protection',          'nggallery') => [TRUE, FALSE],
                        __('Image Commenting',          'nggallery') => [TRUE, FALSE],
                        __('Image Deeplinking',         'nggallery') => [TRUE, FALSE],
                        __('Full-Screen Lightbox',      'nggallery') => [TRUE, FALSE],
                        __('Image Social Sharing',      'nggallery') => [TRUE, FALSE],
                        __('Lightbox Slideshow',        'nggallery') => [TRUE, FALSE]
                    ]
                ]; ?>

                <table cellspacing="0" cellpadding="0" border="0">
                    <thead>
                        <tr>
                            <th class="ngg-empty-th"></th>
                            <th>
                                <h2><?php print __('NextGEN Pro', 'nggallery'); ?></h2>
                            </th>
                            <th>
                                <h2><?php print __('NextGEN Basic', 'nggallery'); ?></h2>
                            </th>
                        </tr>
                    </thead>

                    <?php foreach ($stupid as $block_label => $block) { ?>
                        <tbody>
                            <tr>
                                <th colspan="3"><h3><?php print $block_label; ?></h3></th>
                            </tr>
                            <?php foreach ($block as $label => $supports) { ?>
                                <tr>
                                    <td>
                                        <?php print $label; ?>
                                    </td>
                                    <td class="ngg-features-column">
                                        <?php if ($supports[0]) { ?>
                                            <i class="fa fa-check ngg-features-full"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-times ngg-features-none"></i>
                                        <?php } ?>
                                    </td>
                                    <td class="ngg-features-column">
                                        <?php if ($supports[1]) { ?>
                                            <i class="fa fa-check ngg-features-full"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-times ngg-features-none"></i>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    <?php } ?>

                    <tfoot>
                        <tr>
                            <th colspan="3">
                                <div class="wp-block-columns has-background">
                                    <div class="wp-block-column is-vertically-aligned-center"
                                         style="flex-basis:66.66%">
                                        <h3>
                                            <a href="<?php print esc_attr(M_Marketing::get_utm_link('https://www.imagely.com/nextgen-gallery/', 'nextgenbasicvspro', 'unlockpowerfulfeatures')); ?>"
                                               target="_blank"
                                               rel="noreferrer noopener">Get NextGEN Pro Today and Unlock all the Powerful Features</a>
                                        </h3>
                                        <p>
                                            <strong>Bonus:</strong> NextGEN Gallery users get 20% off the regular price using in the link above.
                                        </p>
                                    </div>
                                    <div class="wp-block-column is-vertically-aligned-center"
                                         style="flex-basis:33.33%">
                                        <div class="wp-block-buttons">
                                            <div class="wp-block-button">
                                                <a class="wp-block-button__link has-text-color has-background no-border-radius"
                                                   href="<?php print esc_attr(M_Marketing::get_utm_link('https://www.imagely.com/nextgen-gallery/', 'nextgenbasicvspro', 'getnextgenpro')); ?>"
                                                   style="background-color: #9ebc1b; color: #ffffff"
                                                   target="_blank"
                                                   rel="noreferrer noopener">Get NextGEN Pro</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </tfoot>
                </table>

            </div>
            <?php if (wp_get_theme() != "Imagely"): ?>
            <div data-id="genesis-link">

                <h2><?php _e( 'WordPress Themes for Photographers by Imagely' ); ?></h2>
                <p class="about-text"><?php _e( 'Meet the new series of Genesis child themes by Imagely: gorgeous, responsive image-centric themes for photographers or anyone with visually rich websites.', 'nggallery' ); ?></p>
                <h3 class="about-text"><?php _e( 'CLICK TO LEARN MORE:', 'nggallery' ); ?></h3>

                <?php
                $presets = [
                    'minimum',
                    'pano',
                    'lens',
                    'pixelated',
                    'artisan',
                    'nomad',
                    'bloggist',
                    'micro',
                    'artisandark',
                    'nomaddark',
                    'microdark',
                    'light'
                ];

                $imgs_per_column = 2;
                $current = 1;
                foreach ($presets as $ndx => $preset) {
                    if ($current === 1) { ?>
                        <div class="feature-section two-col">
                    <?php } ?>
                    <div class="col">
                        <a href="<?php print esc_attr(M_Marketing::get_utm_link('https://www.imagely.com/wordpress-photography-themes/', 'imagelythemes', $preset)); ?>" target="_blank">
                            <img src="https://f001.backblazeb2.com/file/photocrati-demos/<?php print esc_attr($preset); ?>.png"
                                 alt="<?php print esc_attr($preset); ?>"
                                 title="<?php print esc_attr($preset); ?>"/>
                        </a>
                    </div>
                    <?php if ($current === $imgs_per_column) { ?>
                        </div>
                    <?php } ?>
                    <?php
                    if ($current === $imgs_per_column) {
                        $current = 1;
                    } else {
                        $current++;
                    } ?>
                <?php } ?>
            </div>
            <?php endif ?>

        </div> <!-- /.ngg_page_content_main -->
    
    </div> <!-- /.wrap -->

    <?php
    return NULL;
}