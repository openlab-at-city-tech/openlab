<?php
/**
 * Premio.io Recommended Plugins
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (! defined('ABSPATH')) {
    exit;
}
?>
<style>
    a.hide-recommended-btn {
        background: #1da1f4;
        display: block;
        float: right;
        color: #fff;
        text-decoration: none;
        padding: 5px 20px;
        font-size: 18px;
        /* font-weight: bold; */
        border-radius: 4px;
    }
    .ui-dialog-titlebar {
        background: none !important;
    }
    .ui-dialog {
        z-index: 999999;
        text-align: center;
    }
    .ui-dialog-buttonpane {
        border: none;
        background: transparent;
        padding-top: 0;
    }
    .ui-dialog .ui-dialog-buttonset{
        float:none;
        text-align: center;
    }
    .ui-dialog .ui-dialog-buttonpane .ui-button {
        margin: 0 10px;
    }
    .ui-dialog-buttonpane .ui-dialog-buttonset .red-btn,
    .ui-dialog-buttonpane .ui-dialog-buttonset .purple-btn,
    .ui-dialog-buttonpane .ui-dialog-buttonset .gray-btn {
        background-color: #ffffff;
        color: #fff;
        border-color: #1da1f4;
        line-height: 1.4;
        padding: 5px 0;
        height: auto;
        display: inline-block;
        vertical-align: top;
        font-size: 16px;
        min-width: 150px;
        color: #1da1f4;
    }
    .ui-dialog-buttonpane .ui-dialog-buttonset .red-btn {
        background-color: #1da1f4;
        border-color: #1da1f4;
        color: #ffffff;
    }
</style>
<?php
wp_enqueue_style('wp-jquery-ui-dialog');
wp_enqueue_script('jquery-ui-dialog');
// You may comment this out IF you're sure the function exists.
require_once ABSPATH.'wp-admin/includes/plugin-install.php';
remove_all_filters('plugins_api');
$plugins_allowedtags = [
    'a'       => [
        'href'   => [],
        'title'  => [],
        'target' => [],
    ],
    'abbr'    => [ 'title' => [] ],
    'acronym' => [ 'title' => [] ],
    'code'    => [],
    'pre'     => [],
    'em'      => [],
    'strong'  => [],
    'ul'      => [],
    'ol'      => [],
    'li'      => [],
    'p'       => [],
    'br'      => [],
];

$recommended_plugins = [];

// Poptin Plugins
$args = [
    'slug'   => 'poptin',
    'fields' => [
        'short_description' => true,
        'icons'             => true,
        'reviews'           => false,
// excludes all reviews
    ],
];
$data = plugins_api('plugin_information', $args);
if ($data && ! is_wp_error($data)) {
    $recommended_plugins['poptin']       = $data;
    $recommended_plugins['poptin']->name = 'Poptin: Beautiful Pop Ups and Embedded Inline Contact Forms for Your Website';
    $recommended_plugins['poptin']->short_description = 'Pop ups and contact forms builder for your website. Get more sales, leads, and subscribers with beautiful popups & inline forms templates, no coding skills required';
}

// Chatway Plugins
$args = [
    'slug'   => 'chatway-live-chat',
    'fields' => [
        'short_description' => true,
        'icons'             => true,
        'reviews'           => false,
// excludes all reviews
    ],
];
$data = plugins_api('plugin_information', $args);
if ($data && ! is_wp_error($data)) {
    $recommended_plugins['chatway']       = $data;
    $recommended_plugins['chatway']->name = 'Free Live Chat, WordPress Website Chat Plugin, Support Chat App: Chatway';
    $recommended_plugins['chatway']->short_description = 'Live chat with your website’s visitors through your WordPress website. With Chatway – live chat app, you can do just that and much more!';
}

// Chaty Plugins
$args = [
    'slug'   => 'chaty',
    'fields' => [
        'short_description' => true,
        'icons'             => true,
        'reviews'           => false,
// excludes all reviews
    ],
];
$data = plugins_api('plugin_information', $args);
if ($data && ! is_wp_error($data)) {
    $recommended_plugins['chaty']       = $data;
    $recommended_plugins['chaty']->name = 'Chaty: WhatsApp, Facebook Messenger, and Many Other Chat Buttons For Your Website';
    $recommended_plugins['chaty']->short_description = 'Let your visitors contact you via Facebook Messenger, Whatsapp, Telegram, Viber, Email, Phone call, SMS and more with customizable chat & call buttons';
}

// mystickyelements Plugins
$args = [
    'slug'   => 'mystickyelements',
    'fields' => [
        'short_description' => true,
        'icons'             => true,
        'reviews'           => false,
// excludes all reviews
    ],
];
$data = plugins_api('plugin_information', $args);
if ($data && ! is_wp_error($data)) {
    $recommended_plugins['mystickyelements']       = $data;
    $recommended_plugins['mystickyelements']->name = 'All-in-one Floating Contact Form, Call, Chat, and 50+ Social Icon Tabs – My Sticky Elements';
    $recommended_plugins['mystickyelements']->short_description = 'Add floating form and tabs on any side of your website to help your visitors contact you and easily find your Facebook page, YouTube channel, open hours';
}

?>
<div class="wrap mystickyelement-wrap recommended-plugins">
    <h2>
        <?php esc_html_e('Try out our recommended plugins', 'folders'); ?>
        <a class="hide-recommended-btn" href="#" class=""><?php esc_html_e('Hide From Menu', 'folders');?></a>
    </h2>
</div>
<div class="wrap recommended-plugins">
    <div class="wp-list-table widefat plugin-install">
        <div class="the-list">
            <?php
            foreach ((array) $recommended_plugins as $plugin) {
                if (is_object($plugin)) {
                    $plugin = (array) $plugin;
                }

                // Display the group heading if there is one.
                if (isset($plugin['group']) && $plugin['group'] != $group) {
                    if (isset($this->groups[$plugin['group']])) {
                        $group_name = $this->groups[$plugin['group']];
                        if (isset($plugins_group_titles[$group_name])) {
                            $group_name = $plugins_group_titles[$group_name];
                        }
                    } else {
                        $group_name = $plugin['group'];
                    }

                    // Starting a new group, close off the divs of the last one.
                    if (! empty($group)) {
                        echo '</div></div>';
                    }

                    echo '<div class="plugin-group"><h3>'.esc_html($group_name).'</h3>';
                    // Needs an extra wrapping div for nth-child selectors to work.
                    echo '<div class="plugin-items">';

                    $group = $plugin['group'];
                }//end if

                $title = wp_kses($plugin['name'], $plugins_allowedtags);

                // Remove any HTML from the description.
                $description = wp_strip_all_tags($plugin['short_description']);
                $version     = wp_kses($plugin['version'], $plugins_allowedtags);

                $name = wp_strip_all_tags($title.' '.$version);

                $author = wp_kses($plugin['author'], $plugins_allowedtags);
                if (! empty($author)) {
                    // translators: %s: Plugin author.
                    $author = ' <cite>'.sprintf(esc_html__( 'By %s', "folders"), $author).'</cite>';
                }

                $requires_php = isset($plugin['requires_php']) ? $plugin['requires_php'] : null;
                $requires_wp  = isset($plugin['requires']) ? $plugin['requires'] : null;

                $compatible_php = is_php_version_compatible($requires_php);
                $compatible_wp  = is_wp_version_compatible($requires_wp);
                $tested_wp      = ( empty($plugin['tested']) || version_compare(get_bloginfo('version'), $plugin['tested'], '<=') );

                $action_links = [];

                if (current_user_can('install_plugins') || current_user_can('update_plugins')) {
                    $status = install_plugin_install_status($plugin);

                    switch ($status['status']) {
                    case 'install':
                        if ($status['url']) {
                            if ($compatible_php && $compatible_wp) {
                                $action_links[] = sprintf(
                                    '<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                    esc_attr($plugin['slug']),
                                    esc_url($status['url']),
                                    // translators: %s: Plugin name and version.
                                    esc_attr(sprintf(esc_html__('Install %s now', 'folders'), $name)),
                                    esc_attr($name),
                                    esc_html__( 'Install Now', "folders")
                                );
                            } else {
                                $action_links[] = sprintf(
                                    '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                    esc_html__('Cannot Install', 'folders')
                                );
                            }
                        }
                        break;

                    case 'update_available':
                        if ($status['url']) {
                            if ($compatible_php && $compatible_wp) {
                                $action_links[] = sprintf(
                                    '<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                    esc_attr($status['file']),
                                    esc_attr($plugin['slug']),
                                    esc_url($status['url']),
                                    // translators: %s: Plugin name and version.
                                    esc_attr(sprintf(esc_html__('Update %s now', 'folders'), $name)),
                                    esc_attr($name),
                                    esc_html__( 'Update Now', "folders")
                                );
                            } else {
                                $action_links[] = sprintf(
                                    '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                    esc_html__('Cannot Update', 'folders')
                                );
                            }
                        }
                        break;

                    case 'latest_installed':
                    case 'newer_installed':
                        if (is_plugin_active($status['file'])) {
                            $action_links[] = sprintf(
                                '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                esc_html__('Active', 'folders')
                            );
                        } else if (current_user_can('activate_plugin', $status['file'])) {
                            $button_text = esc_html__( 'Activate', "folders");
                            // translators: %s: Plugin name.
                            $button_label = esc_html__('Activate %s', 'folders');
                            $activate_url = add_query_arg(
                                [
                                    '_wpnonce' => wp_create_nonce('activate-plugin_'.$status['file']),
                                    'action'   => 'activate',
                                    'plugin'   => $status['file'],
                                ],
                                network_admin_url('plugins.php')
                            );

                            if (is_network_admin()) {
                                $button_text = esc_html__( 'Network Activate', "folders");
                                // translators: %s: Plugin name.
                                $button_label = esc_html__('Network Activate %s', 'folders');
                                $activate_url = add_query_arg([ 'networkwide' => 1 ], $activate_url);
                            }

                            $action_links[] = sprintf(
                                '<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
                                esc_url($activate_url),
                                esc_attr(sprintf($button_label, $plugin['name'])),
                                $button_text
                            );
                        } else {
                            $action_links[] = sprintf(
                                '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                esc_html__('Installed', 'folders')
                            );
                        }//end if
                        break;
                    }//end switch
                }//end if

                $details_link = self_admin_url(
                    'plugin-install.php?tab=plugin-information&amp;plugin='.$plugin['slug'].'&amp;TB_iframe=true&amp;width=600&amp;height=550'
                );

                $action_links[] = sprintf(
                    '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
                    esc_url($details_link),
                    // translators: %s: Plugin name and version.
                    esc_attr(sprintf(esc_html__( 'More information about %s', "folders"), $name)),
                    esc_attr($name),
                    esc_html__( 'More Details', "folders")
                );

                if (! empty($plugin['icons']['svg'])) {
                    $plugin_icon_url = $plugin['icons']['svg'];
                } else if (! empty($plugin['icons']['2x'])) {
                    $plugin_icon_url = $plugin['icons']['2x'];
                } else if (! empty($plugin['icons']['1x'])) {
                    $plugin_icon_url = $plugin['icons']['1x'];
                } else {
                    $plugin_icon_url = $plugin['icons']['default'];
                }

                /*
                 * Filters the install action links for a plugin.
                 *
                 * @since 2.7.0
                 *
                 * @param string[] $action_links An array of plugin action links. Defaults are links to Details and Install Now.
                 * @param array    $plugin       The plugin currently being listed.
                 */
                $action_links = apply_filters('plugin_install_action_links', $action_links, $plugin);

                $last_updated_timestamp = strtotime($plugin['last_updated']);
                ?>
                <div class="plugin-card plugin-card-<?php echo sanitize_html_class($plugin['slug']); ?>">
                    <?php
                    if (! $compatible_php || ! $compatible_wp) {
                        echo '<div class="notice inline notice-error notice-alt"><p>';
                        if (! $compatible_php && ! $compatible_wp) {
                            esc_html_e('This plugin doesn&#8217;t work with your versions of WordPress and PHP.');
                            if (current_user_can('update_core') && current_user_can('update_php')) {
                                printf(
                                // translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page.
                                    ' '.esc_html__( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.', "folders"),
                                    esc_url(self_admin_url('update-core.php')),
                                    esc_url(wp_get_update_php_url())
                                );
                                wp_update_php_annotation('</p><p><em>', '</em>');
                            } else if (current_user_can('update_core')) {
                                printf(
                                // translators: %s: URL to WordPress Updates screen.
                                    ' '.esc_html__( '<a href="%s">Please update WordPress</a>.', "folders"),
                                    esc_url(self_admin_url('update-core.php'))
                                );
                            } else if (current_user_can('update_php')) {
                                printf(
                                // translators: %s: URL to Update PHP page.
                                    ' '.esc_html__( '<a href="%s">Learn more about updating PHP</a>.', "folders"),
                                    esc_url(wp_get_update_php_url())
                                );
                                wp_update_php_annotation('</p><p><em>', '</em>');
                            }//end if
                        } else if (! $compatible_wp) {
                            esc_html_e('This plugin doesn&#8217;t work with your version of WordPress.', "folders");
                            if (current_user_can('update_core')) {
                                printf(
                                // translators: %s: URL to WordPress Updates screen.
                                    ' '.esc_html__( '<a href="%s">Please update WordPress</a>.', "folders"),
                                    esc_url(self_admin_url('update-core.php'))
                                );
                            }
                        } else if (! $compatible_php) {
                            esc_html_e('This plugin doesn&#8217;t work with your version of PHP.');
                            if (current_user_can('update_php')) {
                                printf(
                                // translators: %s: URL to Update PHP page.
                                    ' '.esc_html__( '<a href="%s">Learn more about updating PHP</a>.', "folders"),
                                    esc_url(wp_get_update_php_url())
                                );
                                wp_update_php_annotation('</p><p><em>', '</em>');
                            }
                        }//end if

                        echo '</p></div>';
                    }//end if
                    ?>
                    <div class="plugin-card-top">
                        <div class="name column-name">
                            <h3>
                                <a href="<?php echo esc_url($details_link); ?>" class="thickbox open-plugin-details-modal">
                                    <?php echo esc_attr($title); ?>
                                    <img src="<?php echo esc_attr($plugin_icon_url); ?>" class="plugin-icon" alt="" />
                                </a>
                            </h3>
                        </div>
                        <div class="action-links">
                            <?php
                            if ($action_links) {
                                echo '<ul class="plugin-action-buttons"><li>'.implode('</li><li>', $action_links).'</li></ul>';
                            }
                            ?>
                        </div>
                        <div class="desc column-description">
                            <p><?php echo esc_attr($description); ?></p>
                            <p class="authors"><?php echo wp_kses($author, $plugins_allowedtags); ?></p>
                        </div>
                    </div>
                    <div class="plugin-card-bottom">
                        <div class="vers column-rating">
                            <?php
                            wp_star_rating(
                                [
                                    'rating' => $plugin['rating'],
                                    'type'   => 'percent',
                                    'number' => $plugin['num_ratings'],
                                ]
                            );
                            ?>
                            <span class="num-ratings" aria-hidden="true">(<?php echo esc_attr(number_format_i18n($plugin['num_ratings'])); ?>)</span>
                        </div>
                        <div class="column-updated">
                            <strong><?php esc_html_e('Last Updated:', "folders"); ?></strong>
                            <?php
                            // translators: %s: Human-readable time difference.
                            printf(esc_html__( '%s ago', "folders"), esc_attr(human_time_diff($last_updated_timestamp)));
                            ?>
                        </div>
                        <div class="column-downloaded">
                            <?php
                            if ($plugin['active_installs'] >= 1000000) {
                                $active_installs_millions = floor(($plugin['active_installs'] / 1000000));
                                $active_installs_text     = sprintf(
                                // translators: %s: Number of millions.
                                    _nx('%s+ Million', '%s+ Million', $active_installs_millions, 'Active plugin installations'),
                                    number_format_i18n($active_installs_millions)
                                );
                            } else if (0 == $plugin['active_installs']) {
                                $active_installs_text = esc_html__('Less Than 10', 'folders');
                            } else {
                                $active_installs_text = number_format_i18n($plugin['active_installs']).'+';
                            }

                            // translators: %s: Number of installations.
                            printf(esc_html__( '%s Active Installations', "folders"), esc_attr($active_installs_text));
                            ?>
                        </div>
                        <div class="column-compatibility">
                            <?php
                            if (! $tested_wp) {
                                echo '<span class="compatibility-untested">'.esc_html__( 'Untested with your version of WordPress', "folders").'</span>';
                            } else if (! $compatible_wp) {
                                echo '<span class="compatibility-incompatible">'.esc_html__( '<strong>Incompatible</strong> with your version of WordPress', "folders").'</span>';
                            } else {
                                echo '<span class="compatibility-compatible">'.esc_html__( '<strong>Compatible</strong> with your version of WordPress', "folders").'</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }//end foreach
            ?>
        </div>
    </div>
    <div id="hide-recommeded-plugins" style="display:none;" title="<?php esc_html_e('Are you sure?', 'folders');?>">
        <p><?php esc_html_e("If you hide the recommended plugins page from your menu, it won't appear there again. Are you sure you'd like to do it?", 'folders');?></p>
    </div>

</div>
<script>
    ( function( $ ) {
        "use strict";
        $(document).ready(function(){
            $('a.hide-recommended-btn').on('click',function(event){
                event.preventDefault();
                $( "#hide-recommeded-plugins" ).dialog({
                    resizable: false,
                    modal: true,
                    draggable: false,
                    height: 'auto',
                    width: 400,
                    open: function (event, ui) {
                        $(".ui-widget-overlay").click(function () {
                            $('#hide-recommeded-plugins').dialog('close');
                        });
                    },
                    buttons: {
                        "Hide it": {
                            click: function () {
                                window.location = "<?php echo esc_url(admin_url('admin.php?page=wcp_folders_settings&hide_folder_recommended_plugin=1&nonce='.wp_create_nonce("folder_recommended_plugin")));?>";
                            },
                            text: 'Hide it',
                            class: 'btn red-btn'
                        },
                        "Keep it": {
                            click: function () {
                                $(this).dialog('close');
                            },
                            text: 'Keep it',
                            class: 'btn alt gray-btn'
                        },
                    }
                });
            });
        });
    })( jQuery );
</script>
