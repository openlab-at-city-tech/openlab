<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use Bookly\Backend\Components\Controls\Buttons;

?>
<?php /** @var array $roles */ ?>
<?php /** @var boolean $has_error */ ?>
<?php /** @var boolean $fixable */ ?>
<?php if ( $has_error ) : ?>
    <div class="float-right mb-3">
        <?php $attrs = array( 'data-ajax' => 'fixRoles', 'data-tool' => 'Roles', 'data-hide-errors-on-success' => 1 ) ?>
        <?php if ( ! $fixable ) {
            $attrs['disabled'] = 'disabled';
        } ?>
        <?php Buttons::render( 'bookly-fix-roles-btn', 'btn-success bookly-js-has-error', 'Fix roles', $attrs ) ?>
    </div>
<?php endif ?>
<?php foreach ( $roles as $role_name => $data ) : ?>
    <?php $role = get_role( $role_name ) ?>
    <h4><?php echo $data['title'] ?></h4>
    <?php if ( ! $role ) : ?>
        <p class="text-danger">Missing</p>
    <?php else : ?>
        <div class="mb-3">
            <?php foreach ( $role->capabilities as $capability => $value ) : ?>
                <div><?php echo esc_html( $capability ) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>
<?php endforeach ?>
