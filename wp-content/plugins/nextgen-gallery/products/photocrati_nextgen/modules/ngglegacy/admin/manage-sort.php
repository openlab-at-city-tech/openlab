<?php

/**
 * @author Alex Rabe
 *
 */

/**
 * @param int $galleryID
 */
function nggallery_sortorder($galleryID = 0) {
	global $wpdb, $nggdb;

	$action_status = ['message' => '', 'status' => 'ok'];

	if ($galleryID == 0)
	    return;

	$galleryID = (int) $galleryID;

	if (isset($_POST['updateSortorder']))
	{
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
				$wpdb->query("UPDATE {$wpdb->nggpictures} SET `sortorder` = '{$sortindex}' WHERE `pid` = {$pic_id}");
				$sortindex++;
			}

			C_Photocrati_Transient_Manager::flush('displayed_gallery_rendering');

			do_action('ngg_gallery_sort', $galleryID);

			nggGallery::show_message(__('Sort order changed','nggallery'));
		}
	}

	// look for presort args
	$presort   = isset($_GET['presort']) ? $_GET['presort'] : false;
	$dir       = (isset($_GET['dir']) && $_GET['dir'] == 'DESC' ) ? 'DESC' : 'ASC';
	$sortitems = array('pid', 'filename', 'alttext', 'imagedate');

	// ensure that nobody added some evil sorting :-)
	if (in_array( $presort, $sortitems) )
		$picturelist = $nggdb->get_gallery($galleryID, $presort, $dir, false);
	else
		$picturelist = $nggdb->get_gallery($galleryID, 'sortorder', $dir, false);

	// This is the url without any presort variable
	$clean_url = 'admin.php?page=nggallery-manage-gallery&amp;mode=sort&amp;gid=' . $galleryID;

	// If we go back then the mode should be edit
	$back_url  = 'admin.php?page=nggallery-manage-gallery&amp;mode=edit&amp;gid=' . $galleryID;

	// In the case somebody presort, then we take this url
	if (isset($_GET['dir']) || isset($_GET['presort']))
		$base_url = $_SERVER['REQUEST_URI'];
	else
		$base_url = $clean_url;
    ?>
	<script type="text/javascript">
        (function($) {
            $(function() {
                if ($(this).data('ready'))
                    return;

                // Enable sorting
                $(".jqui-sortable").sortable({
                    items: 'div.imageBox'
                });

                $('#sortGallery').on('submit',function (e) {
                    var serial = "";
                    var $images = $('div.imageBox');
                    for (var i = 0; i < $images.length; i++) {
                        var image_id = $images[i].id.split('-').pop();
                        if (serial.length > 0) {
                            serial = serial + ',';
                        }
                        serial = serial + image_id;
                    }
                    $('input[name=sortorder]').val(serial);
                });

                // Listen for events in other frames
                if (window.Frame_Event_Publisher) {
                    Frame_Event_Publisher.listen_for('attach_to_post:manage_galleries attach_to_post:manage_images', function () {
                        window.location.href = window.location.href.toString();
                    });
                }

                $(this).data('ready', true);
            });
		})(jQuery);
	</script>

	<?php if ($action_status['message'] != '') { ?>
		<div id="message"
             class="<?php print ($action_status['status']=='ok' ? 'updated' : $action_status['status']); ?> fade">
			<p><strong><?php print $action_status['message']; ?></strong></p>
		</div>
	<?php } ?>

	<div class="wrap ngg_gallery_sort">
		<div class="ngg_page_content_header">
            <img src="<?php print(C_Router::get_instance()->get_static_url('photocrati-nextgen_admin#imagely_icon.png')); ?>"/>
            <h3><?php _e('Sort Gallery', 'nggallery') ?></h3>
		</div>

		<div class='ngg_page_content_main'>

			<form id="sortGallery"
                  method="POST"
                  action="<?php print $clean_url ?>" accept-charset="utf-8">
				
				<div class="tablenav">
					<div class="alignleft actions">
						<?php wp_nonce_field('ngg_updatesortorder'); ?>
						<input class="button-primary action"
                               type="submit"
                               name="updateSortorder"
                               value="<?php print __('Update Sort Order', 'nggallery'); ?>"/>
					</div>
					<div class="alignright actions">
						<a href="<?php print nextgen_esc_url( $back_url ); ?>"
                           class="button-primary">
                            <?php _e('Back to gallery', 'nggallery'); ?>
                        </a>
					</div>
				</div>

				<input name="sortorder" type="hidden"/>

				<ul class="subsubsub">
					<li><?php print __('Presort', 'nggallery'); ?>:</li>
					<li><a href="<?php print esc_attr(remove_query_arg('presort', $base_url)); ?>" <?php if ($presort == '') print 'class="current"'; ?>><?php _e('Unsorted', 'nggallery') ?></a> |</li>
					<li><a href="<?php print esc_attr(add_query_arg('presort', 'pid', $base_url)); ?>" <?php if ($presort == 'pid') print 'class="current"'; ?>><?php _e('Image ID', 'nggallery') ?></a> |</li>
					<li><a href="<?php print esc_attr(add_query_arg('presort', 'filename', $base_url)); ?>" <?php if ($presort == 'filename') print 'class="current"'; ?>><?php _e('Filename', 'nggallery') ?></a> |</li>
					<li><a href="<?php print esc_attr(add_query_arg('presort', 'alttext', $base_url)); ?>" <?php if ($presort == 'alttext') print 'class="current"'; ?>><?php _e('Alt/Title text', 'nggallery') ?></a> |</li>
					<li><a href="<?php print esc_attr(add_query_arg('presort', 'imagedate', $base_url)); ?>" <?php if ($presort == 'imagedate') print 'class="current"'; ?>><?php _e('Date/Time', 'nggallery') ?></a> |</li>
					<li><a href="<?php print esc_attr(add_query_arg('dir', 'ASC', $base_url)); ?>" <?php if ($dir == 'ASC') print 'class="current"'; ?>><?php _e('Ascending', 'nggallery') ?></a> |</li>
					<li><a href="<?php print esc_attr(add_query_arg('dir', 'DESC', $base_url)); ?>" <?php if ($dir == 'DESC') print 'class="current"'; ?>><?php _e('Descending', 'nggallery') ?></a></li>
				</ul>
			</form>

			<div id="debug" style="clear:both"></div>
			<div id='ngg-sort-gallery-container' class='jqui-sortable'>
				<?php
				if ($picturelist) {
					foreach($picturelist as $picture) { ?>
						<div class="imageBox"
                             id="pid-<?php print esc_attr($picture->pid); ?>">
							<div class="imageBox_theImage"
                                 style="background-image:url('<?php print nextgen_esc_url($picture->thumbURL); ?>')">
                            </div>
							<div class="imageBox_label">
                                <span><?php print esc_html(stripslashes($picture->alttext)); ?></span>
                            </div>
						</div>
                    <?php }
				} ?>
			</div>

		</div> <!-- /ngg_page_content_main -->

        <?php do_action('ngg_sort_images_marketing_block'); ?>

    </div>

<?php }