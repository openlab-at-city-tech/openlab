<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly ?>
<div class="bookly-collapse-with-arrow">
    <a class="h5<?php if ( $opened ) : ?> bookly-collapsed<?php endif ?>" href="#<?php echo esc_attr( $id ) ?>" data-toggle="bookly-collapse" role="button" aria-expanded="<?php echo esc_attr( $opened?'true':'false' ) ?>"><?php echo esc_html( $title ) ?></a>
    <div id="<?php echo esc_attr( $id ) ?>" class="bookly-collapse<?php if ( $opened ) : ?> bookly-show<?php endif ?>">