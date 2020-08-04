<?php
/**
 * The template for displaying content of archive page meetings
 *
 * This template can be overridden by copying it to yourtheme/video-conferencing-zoom/content-meeting.php.
 *
 * @author Deepen
 * @since 3.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="dpn-zvc-<?php the_ID(); ?>" class="dpn-zvc-<?php the_ID(); ?>">
    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
	<?php the_excerpt(); ?>
</div>