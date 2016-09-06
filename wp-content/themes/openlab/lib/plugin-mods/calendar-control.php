<?php

/**
 * Calendar control
 * Hooks into Events Organiser and BuddyPress Event Organiser
 */
function openlab_custom_calendar_assets() {

    $key = 'AIzaSyDQrCvCLzpXoahl68dVJmfBxemu36CUsTM';

    wp_deregister_script('eo_GoogleMap');
    wp_register_script('eo_GoogleMap', '//maps.googleapis.com/maps/api/js?key=' . $key . '&sensor=false&language=' . substr(get_locale(), 0, 2));
}

add_action('wp_enqueue_scripts', 'openlab_custom_calendar_assets', 999);
add_action('admin_enqueue_scripts', 'openlab_custom_calendar_assets', 999);

/**
 * Right now there doesn't seem to be a good way to delineate the event detail screen from the other actions
 * @return boolean
 */
function openlab_eo_is_event_detail_screen() {
    if (!empty(buddypress()->action_variables) && !bp_is_action_variable('ical') && !bp_is_action_variable('upcoming', 0) && !bpeo_is_action('new') && !bpeo_is_action('edit')) {
        return true;
    } else {
        return false;
    }
}

/**
 * Retrieving the event detail obj outside of the EO's loop
 * This will, in most cases, be used in instances where the object has to exist
 * EO and BP EO already have checks in place to handle non-existing event detail pages
 * @return type
 */
function openlab_eo_get_single_event_query_obj() {
    $obj_out = array();

    // Set up query args
    $query_args = array();
    $query_args['suppress_filters'] = true;
    $query_args['orderby'] = 'none';
    $query_args['post_status'] = array('publish', 'pending', 'private', 'draft', 'future', 'trash');

    // this is a draft with no slug
    if (false !== strpos(bp_current_action(), 'draft-')) {
        $query_args['post__in'] = (array) str_replace('draft-', '', bp_action_variable());

        // use post slug
    } else {
        $query_args['name'] = bp_action_variable();
    }

    // query for the event
    $event = eo_get_events($query_args);

    $obj_out = $event[0];

    return $obj_out;
}

/**
 * Custom control over what events provide for editing
 * @param array $args
 * @return string
 */
function openlab_control_event_post_type($args) {

    $args['supports'] = array('title', 'editor', 'author', 'excerpt', 'custom-fields');

    return $args;
}

add_filter('eventorganiser_event_properties', 'openlab_control_event_post_type');

/* * *
 * Preventing the creation of dedicated venue pages
 */

function openlab_control_venue_taxonomy($event_category_args) {

    $event_category_args['rewrite'] = false;

    return $event_category_args;
}

add_filter('eventorganiser_register_taxonomy_event-venue', 'openlab_control_venue_taxonomy');

function openlab_control_event_action_links($links) {
    global $post;

    //modifying links for sitewide calendar
    //making sure back button goes back to sitewide calendar
    //taking out actions links (there's some complicated issues there)
    if ($post->post_type === 'event' && !bp_current_action()) {
        $links = array();
        $back_link = get_permalink(get_page_by_path('about/calendar'));
        $links['back'] = "<a href='$back_link'>← Back</a>";
    }

    return $links;
}

add_filter('bpeo_get_the_single_event_action_links', 'openlab_control_event_action_links');

/**
 * Pointing to custom templates in OpenLab theme folder
 * @param type $stack
 * @return type
 */
function openlab_add_eventorganiser_custom_template_folder($stack) {

    $custom_loc = get_stylesheet_directory() . '/event-organiser';

    array_unshift($stack, $custom_loc);

    return $stack;
}

add_filter('eventorganiser_template_stack', 'openlab_add_eventorganiser_custom_template_folder');

/**
 * Redirects to control calendar page access
 * @param type $wp
 * @return type
 */
function openlab_event_page_controller($wp) {

    $redirect_url = '';

    /**
     * For now there are no events pages for members
     * Attempting to go to an events page will redirect to the member's profile page
     */
    if (strpos($wp->request, '/events') !== false && strpos($wp->request, 'members/') !== false) {

        $request_url = $wp->request;
        $redirect_url = explode('/events', $request_url);

        if (is_array($redirect_url)) {
            wp_redirect(get_site_url() . '/' . $redirect_url[0]);
            exit;
        } else {
            wp_redirect(get_site_url());
            exit;
        }
    }

    /**
     * Also controls access to new events interface - if a member is a non-admin and non-mod
     * and the group calendar settings are set to only allow admins and mods the ability to
     * create new events, then the member will be redirected
     */
    if (strpos($wp->request, '/events/') !== false && strpos($wp->request, '/new-event') !== false) {

        $event_create_access = groups_get_groupmeta(bp_get_current_group_id(), 'openlab_bpeo_event_create_access');

        if ($event_create_access === 'admin' && !bp_is_item_admin() && !bp_is_item_mod()) {

            $request_url = $wp->request;
            $redirect_url = explode('/new-event', $request_url);

            if (is_array($redirect_url)) {
                wp_redirect(get_site_url() . '/' . $redirect_url[0]);
                exit;
            } else {
                wp_redirect(get_site_url());
                exit;
            }
        }
    }

    return $redirect_url;
}

add_filter('wp', 'openlab_event_page_controller');

/**
 * Custom control of Event Organiser options
 * @param array $options
 * @return string
 */
function openlab_eventorganiser_custom_options($options) {

    $options[dateformat] = 'mm-dd';

    return $options;
}

add_filter('eventorganiser_options', 'openlab_eventorganiser_custom_options');

/**
 * Adds a title above the description box when editing an event
 */
function openlab_eventorganiser_custom_content_after_title() {

    if (bpeo_is_action('new') || bpeo_is_action('edit')) {
        echo '<h3 class="outside-title"><span class="font-size font-18">Event Description</span></h3>';
    }
}

add_action('edit_form_after_title', 'openlab_eventorganiser_custom_content_after_title');

function openlab_manage_media_buttons($editor_id) {

    if (bp_current_action() === 'events' && $editor_id === 'editor-content') {

        $remove_button = <<<HTML
                <script type="text/javascript">
                jQuery(document).ready(function () {

                                jQuery('#wp-editor-content-media-buttons').remove();

                            });
                </script>
HTML;
        echo $remove_button;
    }
}

add_action('media_buttons', 'openlab_manage_media_buttons');

/**
 * Remove Event Categories
 */
add_filter('eventorganiser_register_taxonomy_event-category', false);

/**
 * Remove Event Tags
 */
add_filter('eventorganiser_register_taxonomy_event-tag', false);

/**
 * Remove plugin action for adding author
 */
remove_action('eventorganiser_additional_event_meta', 'bpeo_list_author');

/**
 * Custom markup for author listing on event detail page
 */
function openlab_bpeo_list_author() {
    $event = get_post(get_the_ID());
    $author_id = $event->post_author;

    $base = __('<strong>Author:</strong> %s', 'bp-event-organiser');

    echo sprintf('<li>' . wp_filter_kses($base) . '</li>', bp_core_get_user_displayname($author_id));
}

add_action('eventorganiser_additional_event_meta', 'openlab_bpeo_list_author', 5);

/**
 * For custom Event Organiser meta boxes
 * In some cases we need to add custom content to the Event Organiser meta boxes,
 * and right now this is the only way (hooks are not available for meta box content
 */
function openlab_handlng_eventorganiser_metaboxes() {
    remove_meta_box('eventorganiser_detail', 'event', 'normal');
    add_meta_box('eventorganiser_detail', __('Event Details', 'eventorganiser'), '_eventorganiser_details_metabox_openlab_custom', 'event', 'normal', 'high');
}

add_action('add_meta_boxes_event', 'openlab_handlng_eventorganiser_metaboxes', 20);

/**
 * Custom meta box for Event Details
 * @global type $wp_locale
 */
function _eventorganiser_details_metabox_openlab_custom() {
    global $wp_locale;

    //Sets the format as php understands it, and textual.
    $php_format = eventorganiser_get_option('dateformat');
    if ('d-m-Y' == $php_format) {
        $format = 'dd &ndash; mm &ndash; yyyy'; //Human form
    } elseif ('Y-m-d' == $php_format) {
        $format = 'yyyy &ndash; mm &ndash; dd'; //Human form
    } else {
        $format = 'mm &ndash; dd &ndash; yyyy'; //Human form
    }

    $is24 = eventorganiser_blog_is_24();
    $time_format = $is24 ? 'H:i' : 'g:ia';

    //Get the starting day of the week
    $start_day = intval(get_option('start_of_week'));
    $ical_days = array('SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA');

    //Retrieve event details
    $schedule_arr = eo_get_event_schedule($post->ID);

    $schedule = $schedule_arr['schedule'];
    $start = $schedule_arr['start'];
    $end = $schedule_arr['end'];
    $all_day = $schedule_arr['all_day'];
    $frequency = $schedule_arr['frequency'];
    $schedule_meta = $schedule_arr['schedule_meta'];
    $occurs_by = $schedule_arr['occurs_by'];
    $until = $schedule_arr['until'];
    $include = $schedule_arr['include'];
    $exclude = $schedule_arr['exclude'];

    $venues = eo_get_venues();
    $venue_id = (int) eo_get_venue($post->ID);

    //$sche_once is used to disable date editing unless the user specifically requests it.
    //But a new event might be recurring (via filter), and we don't want to 'lock' new events.
    //See https://wordpress.org/support/topic/wrong-default-in-input-element
    $sche_once = ( 'once' == $schedule || !empty(get_current_screen()->action) );

    if (!$sche_once) {
        $notices = sprintf(
                        '<label for="eo-event-recurrring-notice">%s</label>', __('This is a recurring event. Check to edit this event and its recurrences', 'eventorganiser')
                )
                . ' <input type="checkbox" id="eo-event-recurrring-notice" name="eo_input[AlterRe]" value="yes">';
    } else {
        $notices = '';
    }

    /**
     * Filters the notice at the top of the event details metabox.
     *
     * @param string  $notices The message text.
     * @param WP_Post $post    The corresponding event (post).
     */
    $notices = apply_filters('eventorganiser_event_metabox_notice', $notices, $post);
    if ($notices) {
        //updated class used for backwards compatability see https://core.trac.wordpress.org/ticket/27418
        echo '<div class="notice notice-success updated inline"><p>' . $notices . '</p></div>';
    }

    $date_desc = sprintf(__('Enter date in %s format', 'eventorganiser'), $format);
    $time_desc = $is24 ? __('Enter time in 24-hour hh colon mm format', 'eventorganiser') : __('Enter time in 12-hour hh colon mm am or pm format', 'eventorganiser');
    ?>
    <div class="meta-header"><p>Ensure dates are entered in mm-dd-yyyy format and times in 12 hour format</p></div>
    <div class="eo-grid <?php echo ( $sche_once ? 'onetime' : 'reoccurence' ); ?>">

        <div class="eo-grid-row">
            <div class="eo-grid-4">
                <span class="eo-label" id="eo-start-datetime-label">
                    <?php esc_html_e('Start Date/Time:', 'eventorganiser'); ?>
                </span>
            </div>
            <div class="eo-grid-8 event-date" role="group" aria-labelledby="eo-start-datetime-label">

                <label for="eo-start-date" class="screen-reader-text"><?php esc_html_e('Start Date', 'eventorganiser'); ?></label>
                <input type="text" id="eo-start-date" aria-describedby="eo-start-date-desc" class="ui-widget-content ui-corner-all" name="eo_input[StartDate]" size="10" maxlength="10" value="<?php echo $start->format($php_format); ?>"/>
                <span id="eo-start-date-desc" class="screen-reader-text"><?php echo esc_html($date_desc); ?></span>

                <label for="eo-start-time" class="screen-reader-text"><?php esc_html_e('Start Time', 'eventorganiser'); ?></label>
                <?php
                printf(
                        '<input type="text" id="eo-start-time" aria-describedby="eo-start-time-desc" name="eo_input[StartTime]" class="eo_time ui-widget-content ui-corner-all" size="6" maxlength="8" value="%s"/>', eo_format_datetime($start, $time_format)
                );
                ?>
                <span id="eo-start-time-desc" class="screen-reader-text"><?php echo esc_html($time_desc); ?></span>
            </div>
        </div>

        <div class="eo-grid-row">
            <div class="eo-grid-4">
                <span class="eo-label" id="eo-end-datetime-label">
                    <?php esc_html_e('End Date/Time:', 'eventorganiser'); ?>
                </span>
            </div>
            <div class="eo-grid-8 event-date" role="group" aria-labelledby="eo-end-datetime-label">

                <label for="eo-end-date" class="screen-reader-text"><?php esc_html_e('End Date', 'eventorganiser'); ?></label>
                <input type="text" id="eo-end-date" aria-describedby="eo-end-date-desc" class="ui-widget-content ui-corner-all" name="eo_input[EndDate]" size="10" maxlength="10" value="<?php echo $end->format($php_format); ?>"/>

                <span id="eo-end-date-desc" class="screen-reader-text"><?php echo esc_html($date_desc); ?></span>
                <label for="eo-end-time" class="screen-reader-text"><?php esc_html_e('End Time', 'eventorganiser'); ?></label>
                <?php
                printf(
                        '<input type="text" id="eo-end-time" aria-describedby="eo-end-time-desc" name="eo_input[FinishTime]" class="eo_time ui-widget-content ui-corner-all" size="6" maxlength="8" value="%s"/>', eo_format_datetime($end, $time_format)
                );
                ?>
                <span id="eo-end-time-desc" class="screen-reader-text"><?php echo esc_html($time_desc); ?></span>

                <span>
                    <input type="checkbox" id="eo-all-day"  <?php checked($all_day); ?> name="eo_input[allday]" value="1"/>
                    <label for="eo-all-day">
                        <?php esc_html_e('All day', 'eventorganiser'); ?>
                    </label>
                </span>

            </div>
        </div>

        <div class="eo-grid-row event-date">
            <div class="eo-grid-4">
                <label for="eo-event-recurrence"><?php esc_html_e('Recurrence:', 'eventorganiser'); ?> </label>
            </div>
            <div class="eo-grid-8 event-date">
                <?php
                $recurrence_schedules = array(
                    'once' => __('none', 'eventorganiser'), 'daily' => __('daily', 'eventorganiser'), 'weekly' => __('weekly', 'eventorganiser'),
                    'monthly' => __('monthly', 'eventorganiser'), 'yearly' => __('yearly', 'eventorganiser'), 'custom' => __('custom', 'eventorganiser'),
                );
                ?>
                <select id="eo-event-recurrence" name="eo_input[schedule]">
                    <?php foreach ($recurrence_schedules as $value => $label) : ?>
                        <option value="<?php echo esc_attr($value) ?>" <?php selected($schedule, $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="eo-grid-row event-date reocurrence_row">
            <div class="eo-grid-4"></div>
            <div class="eo-grid-8 event-date">
                <div id="eo-recurrence-frequency-wrapper">
                    <?php esc_html_e('Repeat every', 'eventorganiser'); ?>
                    <label for="eo-recurrence-frequency" class="screen-reader-text"><?php esc_html_e('Recurrence frequency', 'eventorganiser'); ?></label>
                    <input type="number" id="eo-recurrence-frequency" class="ui-widget-content ui-corner-all" name="eo_input[event_frequency]"  min="1" max="365" maxlength="4" size="4" value="<?php echo intval($frequency); ?>" />
                    <span id="eo-recurrence-schedule-label"></span>
                </div>

                <div id="eo-day-of-week-repeat">

                    <span id="eo-days-of-week-label" class="screen-reader-text"><?php esc_html_e('Repeat on days of week:', 'eventorganiser'); ?></span>
                    <span class="eo-days-of-week-text"><?php esc_html_e('on', 'eventorganiser'); ?></span>
                    <ul class="eo-days-of-week" role="group" aria-labelledby="eo-days-of-week-label">
                        <?php
                        for ($i = 0; $i <= 6; $i++) :
                            $d = ($start_day + $i) % 7;
                            $ical_d = $ical_days[$d];
                            $day = $wp_locale->weekday_abbrev[$wp_locale->weekday[$d]];
                            $fullday = $wp_locale->weekday[$d];
                            $schedule_days = ( is_array($schedule_meta) ? $schedule_meta : array() );
                            ?>
                            <li>
                                <input type="checkbox" id="day-<?php echo esc_attr($day); ?>"  <?php checked(in_array($ical_d, $schedule_days), true); ?>  value="<?php echo esc_attr($ical_d) ?>" class="daysofweek" name="eo_input[days][]"/>
                                <label for="day-<?php echo esc_attr($day); ?>" > <abbr aria-label="<?php echo esc_attr($fullday); ?>"><?php echo esc_attr($day); ?></abbr></label>
                            </li>
                            <?php
                        endfor;
                        ?>
                    </ul>
                </div>

                <div id="eo-day-of-month-repeat">
                    <span id="eo-days-of-month-label" class="screen-reader-text"><?php esc_html_e('Select whether to repeat monthly by date or day:', 'eventorganiser'); ?></span>
                    <div class="eo-days-of-month" role="group" aria-labelledby="eo-days-of-month-label">
                        <label for="eo-by-month-day" >
                            <input type="radio" id="eo-by-month-day" name="eo_input[schedule_meta]" <?php checked($occurs_by, 'BYMONTHDAY'); ?> value="BYMONTHDAY=" />
                            <?php esc_html_e('date of month', 'eventorganiser'); ?>
                        </label>
                        <label for="eo-by-day" >
                            <input type="radio" id="eo-by-day" name="eo_input[schedule_meta]"  <?php checked('BYMONTHDAY' != $occurs_by, true); ?> value="BYDAY=" />
                            <?php esc_html_e('day of week', 'eventorganiser'); ?>
                        </label>
                    </div>
                </div>

                <div id="eo-schedule-last-date-wrapper" class="reoccurrence_label">
                    <?php esc_html_e('until', 'eventorganiser'); ?>
                    <label id="eo-repeat-until-label" for="eo-schedule-last-date" class="screen-reader-text"><?php esc_html_e('Repeat this event until:', 'eventorganiser'); ?></label>
                    <input class="ui-widget-content ui-corner-all" name="eo_input[schedule_end]" id="eo-schedule-last-date" size="10" maxlength="10" value="<?php echo $until->format($php_format); ?>"/>
                </div>

                <p id="eo-event-summary" role="status" aria-live="polite"></p>

            </div>
        </div>

        <div id="eo_occurrence_picker_row" class="eo-grid-row event-date">
            <div class="eo-grid-4">
                <?php esc_html_e('Include/Exclude occurrences:', 'eventorganiser'); ?>
            </div>
            <div class="eo-grid-8 event-date">
                <?php submit_button(__('Show dates', 'eventorganiser'), 'hide-if-no-js eo_occurrence_toggle button small', 'eo_date_toggle', false); ?>

                <div id="eo-occurrence-datepicker"></div>
                <?php
                if (!empty($include)) {
                    $include_str = array_map('eo_format_datetime', $include, array_fill(0, count($include), 'Y-m-d'));
                    $include_str = esc_attr(sanitize_text_field(implode(',', $include_str)));
                } else {
                    $include_str = '';
                }
                ?>
                <input type="hidden" name="eo_input[include]" id="eo-occurrence-includes" value="<?php echo $include_str; ?>"/>

                <?php
                if (!empty($exclude)) {
                    $exclude_str = array_map('eo_format_datetime', $exclude, array_fill(0, count($exclude), 'Y-m-d'));
                    $exclude_str = esc_attr(sanitize_text_field(implode(',', $exclude_str)));
                } else {
                    $exclude_str = '';
                }
                ?>
                <input type="hidden" name="eo_input[exclude]" id="eo-occurrence-excludes" value="<?php echo $exclude_str; ?>"/>

            </div>
        </div>

        <?php
        $tax = get_taxonomy('event-venue');
        if (taxonomy_exists('event-venue')) :

            $address_fields = _eventorganiser_get_venue_address_fields();
            $address = array();
            $venue_stored_name = '';

            //check for stored fields when editing
            if (in_array('edit', bp_action_variables())) {

                if ($venue_id && $venue_id > 0) {
                    $venue_obj = get_term_by('id', $venue_id,'event-venue');
                    $venue_stored_name = $venue_obj->name;
                    $address = eo_get_venue_address($venue_id);
                }
            }
            ?>

            <!-- Add New Venue -->
            <div class="eo-grid-row eo-add-new-venue-custom">
                <div class="eo-grid-4">
                    <label for="eo_venue_name"><?php esc_html_e('Venue Name', 'eventorganiser'); ?></label>
                </div>
                <div class="eo-grid-8">
                    <input type="text" name="eo_venue[name]" id="eo_venue_name"  value="<?php echo $venue_stored_name ?>"/>
                </div>

                <?php
                foreach ($address_fields as $key => $label) {
                    printf(
                            '<div class="eo-grid-4">
						<label for="eo_venue_add-%2$s">%1$s</label>
					</div>
					<div class="eo-grid-8">
						<input type="text" name="eo_venue[%2$s]" class="eo_addressInput" id="eo_venue_add-%2$s"  value="%3$s"/>
					</div>', esc_html($label), esc_attr(trim($key, '_'))/* Keys are prefixed by '_' */, (isset($address[trim($key, '_')]) ? $address[trim($key, '_')] : '')
                    );
                }
                ?>

                <div class="eo-grid-4"></div>
            </div>

            <div class="eo-grid-row venue_row <?php
        if (!$venue_id) {
            echo 'novenue';
        }
                ?>">
                <div class="eo-grid-4"></div>
                <div class="eo-grid-8">
                    <div id="eventorganiser_venue_meta" style="display:none;">
                        <input type="hidden" id="eo_venue_Lat" name="eo_venue[latitude]" value="<?php esc_attr(eo_venue_lat($venue_id)); ?>" />
                        <input type="hidden" id="eo_venue_Lng" name="eo_venue[longtitude]" value="<?php esc_attr(eo_venue_lng($venue_id)); ?>" />
                    </div>

                    <div id="venuemap" class="ui-widget-content ui-corner-all gmap3"></div>
                    <div class="clear"></div>
                </div>
            </div>
        <?php endif; //endif venue's supported              ?>

    </div>
    <?php
    // create a custom nonce for submit verification later
    wp_nonce_field('eventorganiser_event_update_' . get_the_ID() . '_' . get_current_blog_id(), '_eononce');
}

/**
 * Save calendar group settings
 */
function openlab_process_group_calendar_settings($group_id) {
    if (!empty($_POST['openlab-bpeo-event-create-access'])) {

        $access_level = sanitize_text_field($_POST['openlab-bpeo-event-create-access']);

        groups_update_groupmeta($group_id, 'openlab_bpeo_event_create_access', $access_level);
    } else {
        groups_delete_groupmeta($group_id, 'openlab_bpeo_event_create_access');
    }
}

add_action('groups_group_settings_edited', 'openlab_process_group_calendar_settings');

function openlab_group_calendar_media_settings($settings, $post) {

    if ($post->post_type === 'event') {

    }

    return $settings;
}

add_filter('media_view_settings', 'openlab_group_calendar_media_settings', 10, 2);

/**
 * Saving some extra meta when inserting a venue
 * This is not utilized yet, but will be, and I wanted to get it set up
 * before users actually start entering venues
 * Also used for venues with no physical address, by setting LatLng to NaN
 * @param type $venue_id
 */
function openlab_bpeo_extra_venue_meta($venue_id) {

    //attaching venue to group posts
    if (!is_admin()) {

        if (isset($_POST['post_ID'])) {

            //check for legacy
            $post_ids = get_metadata('eo_venue', $venue_id, '_postids', true);

            if ($post_ids && !empty($post_ids)) {

                $post_ids[] = $_POST['post_ID'];
                $post_ids = array_unique($post_ids);
            } else {

                $post_ids = array($_POST['post_ID']);
            }

            update_metadata('eo_venue', $venue_id, '_postids', $post_ids);
        }

        //attaching venue to user
        if (isset($_POST['user_ID'])) {

            //check for legacy
            $user_ids = get_metadata('eo_venue', $venue_id, '_userids', true);

            if ($user_ids && !empty($user_ids)) {

                $user_ids[] = $_POST['user_ID'];
                $user_ids = array_unique($user_ids);
            } else {

                $user_ids = array($_POST['user_ID']);
            }

            update_metadata('eo_venue', $venue_id, '_userids', $user_ids);
        }
    }

    //check if venue has physical address
    //if it does not, set Lat and Lng to NaN
    $venue_address = eo_get_venue_meta($venue_id, '_address');

    if (empty($venue_address)) {
        eo_update_venue_meta($venue_id, '_lat', 0.00000);
        eo_update_venue_meta($venue_id, '_lng', 0.00000);
    }
}

add_action('eventorganiser_save_venue', 'openlab_bpeo_extra_venue_meta');
