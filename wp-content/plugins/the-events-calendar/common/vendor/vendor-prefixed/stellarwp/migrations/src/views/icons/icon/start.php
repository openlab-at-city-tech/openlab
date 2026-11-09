<?php

/**
 * View: Icon Opening Tag.
 *
 * @since 0.0.1
 * @version 0.0.1
 *
 * @var string[] $classes         Additional classes to add to the svg icon.
 * @var string   $label           The label for the icon.
 * @var bool     $is_aria_hidden  Whether the icon is hidden from screen readers. Default false to show the icon.
 * @var int      $height          The height of the icon. Also used when constructing the viewbox.
 * @var int      $width           The width of the icon. Also used when constructing the viewbox.
 *
 * @package \StellarWP\Migrations
 */
$svg_classes = ['stellarwp-migration-icon'];
if (!empty($classes)) {
    $svg_classes = array_merge($svg_classes, $classes);
}
?>
<svg
	<?php 
if (empty($is_aria_hidden)) {
    ?>
		aria-label="<?php 
    echo esc_attr($label);
    ?>"
	<?php 
} else {
    ?>
		aria-hidden="true"
	<?php 
}
?>
	class="<?php 
echo esc_attr(implode(' ', $svg_classes));
?>"
	height="<?php 
echo esc_attr((string) $height);
?>"
	role="img"
	viewBox="0 0 <?php 
echo esc_attr((string) $width . ' ' . (string) $height);
?>"
	width="<?php 
echo esc_attr((string) $width);
?>"
	xmlns="http://www.w3.org/2000/svg"
>
