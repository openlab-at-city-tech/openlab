<?php if (!defined('ABSPATH')) {
    die('No direct access.');
} ?>

<div id="metaslider-ui" class="flex p-6 mb-16">
    <table id="comparison-chart" class="metaslider_feat_table">
        <thead>
            <tr>
                <th>
                    <h1>MetaSlider</h1>
                </th>
                <th colspan="2">
                    <h3><?php esc_html_e('Comparison Chart', 'ml-slider'); ?></h3>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="ms-dark-blue text-white">Features</td>
                <td class="ms-orange text-white"><?php esc_html_e('Free', 'ml-slider');?></td>
                <td class="ms-orange text-white"><?php esc_html_e('Pro', 'ml-slider');?></td>
            </tr>
            <tr>
                <td></td>
                <td class="metaslider_installed_status"><?php esc_html_e('Installed', 'ml-slider');?></td>
                <td class="metaslider_installed_status"><?php echo metaslider_optimize_url("https://www.metaslider.com/upgrade/", esc_html__('Upgrade now', 'ml-slider')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Create unlimited slideshows', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Create and manage as many slideshows as you need.', 'ml-slider');?></p>
                </td>
                <td><div class="dot available"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Regular updates', 'ml-slider');?></h4>
                    <p><?php esc_html_e('We keep MetaSlider compatible with the latest versions of WordPress.', 'ml-slider');?></p>
                </td>
                <td><div class="dot available"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Intelligent image cropping', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Unique Smart Crop functionality ensures your slides are perfectly resized.', 'ml-slider');?></p>
                </td>
                <td><div class="dot available"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Thumbnail navigation', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Allow users to browse your slideshows using thumbnail navigation.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add YouTube, Vimeo and Tiktok slides', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Easily include videos hosted by YouTube, Vimeo or TikTok.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add local video slides', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Create slideshows with videos from your WordPress media library.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add external video slides', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Create slideshows with external videos.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Extra premium themes', 'ml-slider');?></h4>
                    <p><?php esc_html_e('MetaSlider Pro provides stylish and exclusive themes for your slideshows.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add slide layers', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Add layers to your slides with over 50 available transition effects.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add Post Feed slides', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Easily build slides based on your WordPress posts.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add Custom HTML slides', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Design slides using images, HTML and CSS', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Add custom CSS', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Customize your slideshows to fit with your website.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Schedule your slides', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Add a start/end date to individual slides.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Toggle your slide\'s visibility', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Hide any slide, without having to delete them.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
            <tr>
                <td>
                    <h4><?php esc_html_e('Premium support', 'ml-slider');?></h4>
                    <p><?php esc_html_e('Have your specific queries addressed directly by our experts.', 'ml-slider');?></p>
                </td>
                <td><div class="dot unavailable"></div></td>
                <td><div class="dot available"></div></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td class="metaslider_installed_status"><?php esc_html_e('Installed', 'ml-slider');?></td>
                <td class="metaslider_installed_status"><?php echo metaslider_optimize_url("https://www.metaslider.com/upgrade/", esc_html__('Upgrade now', 'ml-slider')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
            </tr>
        </tfoot>
    </table>
</div>
