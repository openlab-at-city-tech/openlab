<?php

/**
 * @author Alex Rabe
 *
 */

/**
 * @param int $galleryID
 */
function nggallery_sortorder($galleryID = 0){
	global $wpdb, $ngg, $nggdb;

	$action_status = array('message' => '', 'status' => 'ok');

	if ($galleryID == 0) return;

	$galleryID = (int) $galleryID;

	if (isset ($_POST['updateSortorder']))  {
		check_admin_referer('ngg_updatesortorder');
		// get variable new sortorder
        if (!empty($_POST['sortorder']))
            $sortArray = explode(',', $_POST['sortorder']);
		if (is_array($sortArray))
		{
			$neworder = array();
			foreach($sortArray as $pid) {
				$neworder[] = (int) $pid;
			}
			$sortindex = 1;
			foreach($neworder as $pic_id) {
				$wpdb->query("UPDATE $wpdb->nggpictures SET sortorder = '$sortindex' WHERE pid = $pic_id");
				$sortindex++;
			}
			C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');

			do_action('ngg_gallery_sort', $galleryID);

			nggGallery::show_message(__('Sort order changed','nggallery'));
		}
	}

	// look for presort args
	$presort = isset($_GET['presort']) ? $_GET['presort'] : false;
	$dir = ( isset($_GET['dir']) && $_GET['dir'] == 'DESC' ) ? 'DESC' : 'ASC';
	$sortitems = array('pid', 'filename', 'alttext', 'imagedate');
	// ensure that nobody added some evil sorting :-)
	if (in_array( $presort, $sortitems) )
		$picturelist = $nggdb->get_gallery($galleryID, $presort, $dir, false);
	else
		$picturelist = $nggdb->get_gallery($galleryID, 'sortorder', $dir, false);

	//this is the url without any presort variable
	$clean_url = 'admin.php?page=nggallery-manage-gallery&amp;mode=sort&amp;gid=' . $galleryID;
	//if we go back , then the mode should be edit
	$back_url  = 'admin.php?page=nggallery-manage-gallery&amp;mode=edit&amp;gid=' . $galleryID;

	// In the case somebody presort, then we take this url
	if ( isset($_GET['dir']) || isset($_GET['presort']) )
		$base_url = $_SERVER['REQUEST_URI'];
	else
		$base_url = $clean_url;

?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			if ($(this).data('ready')) return;

			// Enable sorting
			$(".jqui-sortable").sortable({items: 'div.imageBox'});

			$('#sortGallery').submit(function(e){
				var serial = "";
				var $images = $('div.imageBox');
				for (var i=0; i<$images.length; i++) {
					var image_id = $images[i].id.split('-').pop();
					if (serial.length > 0) serial = serial + ',';
					serial = serial + image_id;
				}
				$('input[name=sortorder]').val(serial);
			});

			// Listen for events in other frames
			if (window.Frame_Event_Publisher) {
				Frame_Event_Publisher.listen_for('attach_to_post:manage_galleries attach_to_post:manage_images', function(){
					window.location.href = window.location.href.toString();
				});
			}

			$(this).data('ready', true);
		});


	</script>

	<?php if ($action_status['message']!='') : ?>
		<div id="message" class="<?php echo ($action_status['status']=='ok' ? 'updated' : $action_status['status']); ?> fade">
			<p><strong><?php echo $action_status['message']; ?></strong></p>
		</div>
	<?php endif; ?>

	<div class="wrap ngg_gallery_sort">

		<div class="ngg_page_content_header"><img src="<?php  echo(C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')); ?>"><h3><?php _e('Sort Gallery', 'nggallery') ?></h3>
		</div>

		<div class='ngg_page_content_main'>

			<form id="sortGallery" method="POST" action="<?php echo $clean_url ?>" accept-charset="utf-8">
				
				<div class="tablenav">
					<div class="alignleft actions">
						<?php wp_nonce_field('ngg_updatesortorder') ?>
						<input class="button-primary action" type="submit" name="updateSortorder" value="<?php _e('Update Sort Order', 'nggallery') ?>" />
					</div>
					<div class="alignright actions">
						<a href="<?php echo nextgen_esc_url( $back_url ); ?>" class="button-primary"><?php _e('Back to gallery', 'nggallery'); ?></a>
					</div>
				</div>
				<input name="sortorder" type="hidden" />
				<ul class="subsubsub">
					<li><?php _e('Presort', 'nggallery') ?> :</li>
					<li><a href="<?php echo esc_attr(remove_query_arg('presort', $base_url)); ?>" <?php if ($presort == '') echo 'class="current"'; ?>><?php _e('Unsorted', 'nggallery') ?></a> |</li>
					<li><a href="<?php echo esc_attr(add_query_arg('presort', 'pid', $base_url)); ?>" <?php if ($presort == 'pid') echo 'class="current"'; ?>><?php _e('Image ID', 'nggallery') ?></a> |</li>
					<li><a href="<?php echo esc_attr(add_query_arg('presort', 'filename', $base_url)); ?>" <?php if ($presort == 'filename') echo 'class="current"'; ?>><?php _e('Filename', 'nggallery') ?></a> |</li>
					<li><a href="<?php echo esc_attr(add_query_arg('presort', 'alttext', $base_url)); ?>" <?php if ($presort == 'alttext') echo 'class="current"'; ?>><?php _e('Alt/Title text', 'nggallery') ?></a> |</li>
					<li><a href="<?php echo esc_attr(add_query_arg('presort', 'imagedate', $base_url)); ?>" <?php if ($presort == 'imagedate') echo 'class="current"'; ?>><?php _e('Date/Time', 'nggallery') ?></a> |</li>
					<li><a href="<?php echo esc_attr(add_query_arg('dir', 'ASC', $base_url)); ?>" <?php if ($dir == 'ASC') echo 'class="current"'; ?>><?php _e('Ascending', 'nggallery') ?></a> |</li>
					<li><a href="<?php echo esc_attr(add_query_arg('dir', 'DESC', $base_url)); ?>" <?php if ($dir == 'DESC') echo 'class="current"'; ?>><?php _e('Descending', 'nggallery') ?></a></li>
				</ul>
			</form>
			<div id="debug" style="clear:both"></div>
			<div class='jqui-sortable'>
				<?php
				if($picturelist) {
					foreach($picturelist as $picture) {
						?>
						<div class="imageBox" id="pid-<?php echo $picture->pid ?>">
							<div class="imageBox_theImage" style="background-image:url('<?php echo nextgen_esc_url( $picture->thumbURL ); ?>')"></div>
							<div class="imageBox_label"><span><?php echo esc_html( stripslashes($picture->alttext) ); ?></span></div>
						</div>
						<?php
					}
				}
				?>
			</div>

		</div>

	</div>

<?php
}
?>
