<?php

/**
 * Customizations for google-calendar-events (Simple Calendar).
 */

/**
 * Keeps the "Powered by Simple Calendar" credit optional on every calendar.
 *
 * Simple Calendar 4.3.2 and later show the credit on calendars created after its cutoff
 * date unless a Pro license is active. Site admins can still turn it on per calendar.
 */
add_filter( 'simcal_is_powered_by_mandatory', '__return_false' );
