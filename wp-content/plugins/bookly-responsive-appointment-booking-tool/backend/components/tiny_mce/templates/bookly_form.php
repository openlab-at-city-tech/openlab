<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\TinyMce\Proxy;
?>
<?php Proxy\Shared::renderBooklyFormHead() ?>
<tr>
    <td>
        <label for="bookly-select-category"><?php esc_html_e( 'Default value for category select', 'bookly' ) ?></label>
    </td>
    <td>
        <select id="bookly-select-category" class="form-control custom-select">
            <option value=""><?php esc_html_e( 'Select category', 'bookly' ) ?></option>
        </select>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-categories">
                <?php esc_html_e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
    </td>
</tr>
<tr>
    <td>
        <label for="bookly-select-service"><?php esc_html_e( 'Default value for service select', 'bookly' ) ?></label>
    </td>
    <td>
        <select id="bookly-select-service" class="form-control custom-select">
            <option value=""><?php esc_html_e( 'Select service', 'bookly' ) ?></option>
        </select>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-services">
                <?php esc_html_e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
        <i><?php esc_html_e( 'Please be aware that a value in this field is required in the frontend. If you choose to hide this field, please be sure to select a default value for it', 'bookly' ) ?></i>
    </td>
</tr>
<tr>
    <td>
        <label for="bookly-select-employee"><?php esc_html_e( 'Default value for employee select', 'bookly' ) ?></label>
    </td>
    <td>
        <select class="form-control custom-select" id="bookly-select-employee">
            <option value=""><?php esc_html_e( 'Any', 'bookly' ) ?></option>
        </select>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-employee">
                <?php esc_html_e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
    </td>
</tr>
<?php Proxy\Shared::renderBooklyFormFields() ?>
<tr>
    <td>
        <label for="bookly-hide-date"><?php esc_html_e( 'Date', 'bookly' ) ?></label>
    </td>
    <td>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-date">
                <?php esc_html_e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
    </td>
</tr>
<tr>
    <td>
        <label for="bookly-hide-week-days"><?php esc_html_e( 'Week days', 'bookly' ) ?></label>
    </td>
    <td>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-week-days">
                <?php esc_html_e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
    </td>
</tr>
<tr>
    <td>
        <label for="bookly-hide-time-range"><?php esc_html_e( 'Time range', 'bookly' ) ?></label>
    </td>
    <td>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="bookly-hide-time-range">
                <?php esc_html_e( 'Hide this field', 'bookly' ) ?>
            </label>
        </div>
    </td>
</tr>