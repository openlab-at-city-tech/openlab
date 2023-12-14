<?php
/**
 * @var int $index
 * @var string $class
 * @var Image $image
 */

use Imagely\NGG\DataTypes\Image;

if ( ! isset( $id ) ) {
	$id = 'ngg-image-' . $index;
}
?>
<div id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>" 
					<?php
					if ( isset( $image->style ) ) {
						echo $image->style;}
					?>
>