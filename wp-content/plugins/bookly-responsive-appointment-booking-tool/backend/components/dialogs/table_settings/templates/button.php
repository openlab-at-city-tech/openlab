<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="col-auto">
    <button type="button" class="btn btn-default w-100 mb-3 bookly-js-table-settings" data-location="<?php echo esc_attr( $location ) ?>" data-table-name="<?php echo esc_attr( $table_name ) ?>" data-table-paging="<?php echo json_encode( Bookly\Lib\Utils\Tables::supportPagination( $table_name ) ) ?>" data-setting-name="<?php echo esc_attr( $setting_name ) ?>"><i class="far fa-fw fa-eye"></i></button>
</div>