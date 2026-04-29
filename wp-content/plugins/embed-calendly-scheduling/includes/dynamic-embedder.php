<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Renders dynamic embedder shortcode
 */
class EMCS_Dynamic_Embedder
{
    private $atts = array();
    private $events = array();

    public function __construct($atts = [])
    {
        $this->atts = $atts;

        if (!class_exists('EMCS_Event_Types')) {
            include_once(EMCS_EVENT_TYPES . 'event-types.php');
        }

        $events = EMCS_Event_Types::get_event_types();

        if (!empty($events) && is_array($events)) {
            $this->events = $events;
        }
    }

    private function enqueue_assets()
    {

        wp_enqueue_script(
            'emcs-dynamic-embedder',
            EMCS_URL . 'assets/js/dynamic-embedder.js',
            [],
            filemtime(EMCS_DIR . 'assets/js/dynamic-embedder.js'),
            true
        );

        wp_register_style(
            'emcs-dynamic-embedder',
            false,
            [],
            filemtime(__FILE__)
        );

        wp_enqueue_style('emcs-dynamic-embedder');

        wp_localize_script(
            'emcs-dynamic-embedder',
            'emcsDynamic',
            [
                'formHeight' => !empty($this->atts['form_height']) ? $this->atts['form_height'] : '400px'
            ]
        );

        $css = "

        .emcs-dynamic-wrapper {
            display:block;
        }

        .emcs-event-buttons {
            margin-bottom:20px;
        }

        .emcs-event-button {
            cursor:pointer;
            margin-right:10px;
            margin-bottom:10px;
            padding:8px 14px;
        }

        .emcs-event-display {
            min-width:320px;
        }

        ";

        wp_add_inline_style('emcs-dynamic-embedder', $css);
    }

    /**
     * Render embedder
     */
    public function render()
    {

        if (empty($this->events)) {
            return esc_html__('No Calendly event types found.', 'embed-calendly-scheduling');
        }

        // enqueue only when rendering
        $this->enqueue_assets();

        $form_height = !empty($this->atts['form_height'])
            ? intval($this->atts['form_height']) . 'px'
            : '400px';

        $cookie_banner = !empty($this->atts['hide_cookie_banner'])
            ? intval($this->atts['hide_cookie_banner'])
            : 0;

        ob_start();
?>
        <div class="emcs-dynamic-wrapper">
            <?php if (count($this->events) > 1): ?>
                <div class="emcs-event-buttons">

                    <?php

                    foreach ($this->events as $event):

                        $name = !empty($event->name) ? sanitize_text_field($event->name) : '';
                        $url  = !empty($event->url) ? esc_url($event->url) : '';

                        if (empty($url)) continue;

                    ?>

                        <button
                            class="emcs-event-button"
                            data-event-url="<?php echo esc_attr($url); ?>"
                            data-form-height="<?php echo esc_attr($form_height); ?>"
                            data-hide-cookie="<?php echo esc_attr($cookie_banner); ?>">

                            <?php echo esc_html($name); ?>

                        </button>

                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="emcs-event-display">

                <?php

                $first_event = reset($this->events);

                if (!empty($first_event->url)) {

                    $shortcode = sprintf(
                        '[calendly url="%s" type="1" form_height="%s" hide_cookie_banner="%d"]',
                        esc_url($first_event->url),
                        esc_attr($form_height),
                        esc_attr($cookie_banner)
                    );

                    echo do_shortcode($shortcode);
                }

                ?>
            </div>
        </div>

<?php
        return ob_get_clean();
    }
}
