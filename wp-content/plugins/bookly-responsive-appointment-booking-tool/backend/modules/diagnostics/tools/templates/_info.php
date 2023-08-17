<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="w-100 text-center">
    Database version: <?php echo $db ?>
</div>
<?php echo \Bookly\Lib\Utils\Common::html( $php_info );
