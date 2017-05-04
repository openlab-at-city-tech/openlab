<?php
/**
 * GMMediaTagsAdmin class
 *
 * @package GMMediaTags
 * @author Giuseppe Mazzapica
 *
 */
class GMMediaTagsAdmin {


	/**
	* Class version
	*
	* @since	0.1.0
	*
	* @access	protected
	*
	* @var	string
	*/
	protected static $version = '0.1.1';



	/**
	* Registered taxonomies names
	*
	* @since	0.1.0
	*
	* @access	public
	*
	* @var	array
	*/
	protected static $registered = array();


	/**
	 * Constructor. Doing nothing
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	function __construct() {
		_doing_it_wrong( 'GMMediaTagsAdmin::__construct', 'GMMediaTagsAdmin Class is intented to be used statically.' );
	}



	/**
	 * Init the plugin backend. Run on 'admin_init' hook
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	static function init() {

		if ( ! defined('GMMEDIATAGSPATH') ) die();

		self::$registered = get_object_taxonomies('attachment');

		add_action( 'wp_ajax_add_media_tag_bulk_tr', array(__CLASS__, 'print_tr') );
		add_action( 'wp_ajax_save_media_tag_bulk', array(__CLASS__, 'save') );
		add_action( 'admin_notices', array(__CLASS__, 'notices') );
		add_action( 'admin_enqueue_scripts', array(__CLASS__, 'add_scripts') );

	}



	/**
	 * Add the javascript and pass json data to it using 'wp_localize_script'. Run on 'admin_enqueue_scripts' hook
	 *
	 * @since	0.1.0
	 *
	 * @param	string	$hook	current admin page
	 * @access	public
	 * @return	null
	 *
	 */
	static function add_scripts( $hook ) {
		if( $hook == 'upload.php' ) {
			wp_enqueue_script( 'suggest' );
			wp_enqueue_script( 'GMMediaTags', GMMEDIATAGSURL . 'includes/GMMediaTags.js', array('jquery'), null, true);
			$vars = array(
				'remove_from_edit'	=> 	__('Remove from bulk edit', 'gmmediatags'),
				'update_error'		=> 	__('Error on update attachments', 'gmmediatags'),
				'assign_terms'		=> 	__('Assign Folders', 'gmmediatags'),
				'ver_html'		=>	wp_create_nonce('add_media_tag_bulk_tr'),
				'ver_save'		=>	wp_create_nonce('save_media_tag_bulk')
			);
			wp_localize_script( 'GMMediaTags', 'gm_mediatags_vars', $vars );
		}
	}




	/**
	 * Output json data from an ajax call
	 *
	 * @since	0.1.0
	 *
	 * @param	array	$data	data to output
	 * @param	string	$error	if not empty funtion output error data
	 * @access	protected
	 * @return	null
	 *
	 */
	protected static function json_out( $data = array(), $error = "" ) {
		header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
		if ( ! empty($error) || empty($data) ) $data = array('bulk_media_tag' => 'error', 'error' => $error);
		die ( json_encode( (object)$data) );
	}




	/**
	 * Bulk save on ajax the terms
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	static function save() {
		error_reporting(0);
		if ( ! defined('DOING_AJAX') ) die();
		if ( ! isset($_POST['formData']) ) self::json_out(false, 'formData_error');
		parse_str ( $_POST['formData'], $form_data );

		$referer = isset($form_data['_wp_http_referer']) ? $form_data['_wp_http_referer'] : null;
		$nonce = isset($_POST['media_tag_ver']) ? $_POST['media_tag_ver'] : null;
		$screen = isset($form_data['screen']) ? $form_data['screen'] : null;
		if ( empty($nonce) || empty($referer) || empty($screen) ) self::json_out(false, 'security error 10');
		if ( ! wp_verify_nonce($_POST['media_tag_ver'], 'save_media_tag_bulk') ) self::json_out(false, 'security error 20');
		$from = parse_url( $referer );
		$admin_url = admin_url('upload.php');
		if ($_SERVER['HTTPS']) {
			$http = 'https://';
		} else {
			$http = 'http://';
		}
		if ( ! $from ) self::json_out(false, 'security error 30');
		if ( ! isset($from['path']) ) self::json_out(false, 'security error 40');
		if ( $admin_url !=  $http.$_SERVER['HTTP_HOST'] . $from['path'] ) self::json_out(false, 'security error 50' );
		if ( $screen != 'upload' ) self::json_out(false, 'security error 60');

		$orig_qv = array();
		if ( isset($from['query']) ) parse_str($from['query'], $orig_qv);
		$taxonomies = isset($form_data['tax_input']) ? $form_data['tax_input'] : array();
		$clean_taxonomies = isset($form_data['clean_tax']) ? $form_data['clean_tax'] : array();
		$toclean = array_keys($clean_taxonomies);
		$attachments = isset($_POST['attachments']) ? $_POST['attachments'] : array();

		if ( ( empty($taxonomies) && empty($clean_taxonomies) ) || empty($attachments) ) self::json_out( array( 'bulk_media_tag' => 'none' ) );
		$errors = 0;
		$done = null;
		$tax_objs = array();
		foreach( $taxonomies as $taxonomy => $terms ) {
			if ( empty($terms) || in_array($taxonomy, $toclean) ) continue;
			$tax_obj = get_taxonomy($taxonomy);
			$tax_objs[$taxonomy] = $tax_obj;
			if ( ! current_user_can( $tax_obj->cap->assign_terms ) ) continue;
			$done = 1;
			foreach( $attachments as $attachment ) {
				if ( ! is_array( wp_set_post_terms( $attachment, $terms, $taxonomy, true ) ) ) $errors++;
			}
		}
		if ( ! empty($clean_taxonomies) ) { foreach ($clean_taxonomies as $taxonomy => $val ) {
			if ( $val == 1 ) {
				$tax_obj = isset($tax_objs[$taxonomy]) ? $tax_objs[$taxonomy] : get_taxonomy($taxonomy);
				if ( ! current_user_can( $tax_obj->cap->delete_terms ) ) continue;
				$done = 1;
				foreach( $attachments as $attachment ) {
					if ( ! is_array( wp_set_post_terms( $attachment, array(), $taxonomy, false ) ) ) $errors++;
				}
			}
		} }
		if ( is_null($done) ) self::json_out( array( 'bulk_media_tag' => 'none' ) );
		$location = add_query_arg( array( 'bulk_media_tag' => 'updated' ), $admin_url );
		if ( ! empty($orig_qv) ) $location = add_query_arg( $orig_qv, $location );
		$data = array( 'location' => $location, 'bulk_media_tag' => 'updated');
		self::json_out( $data );
	}




	/**
	 * Print admin notices after update
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	static function notices() {
		global $pagenow;
		if( $pagenow == 'upload.php' &&  isset($_GET['bulk_media_tag']) && ! empty($_GET['bulk_media_tag']) ) {
			$result = $_GET['bulk_media_tag'];
			if ( $result == 'updated' ) {
				echo '<div class="updated"><p>' . __( 'Attachments updated successfully.', 'gmmediatags') . '</p></div>';
			} elseif ( $result == 'none' ) {
				echo '<div class="updated"><p>' . __( 'Nothing to update: no media or no terms selected.', 'gmmediatags') . '</p></div>';
			} else {
				echo '<div class="error"><p>' . __( 'Error on update attachments.', 'gmmediatags') . '</p></div>';
			}
		}

	}




	/**
	 * Output the html markup for the edit ui on ajax call
	 *
	 * @since	0.1.0
	 *
	 * @access	public
	 * @return	null
	 *
	 */
	static function print_tr() {
		if ( ! defined('DOING_AJAX') ) die();
		if ( empty( self::$registered ) ) die();
		if ( ! isset($_POST['media_tag_ver']) || ! wp_verify_nonce($_POST['media_tag_ver'], 'add_media_tag_bulk_tr')) die();
		error_reporting(0);
		$colspan = isset( $_POST['colspan'] ) && intval($_POST['colspan']) ? $_POST['colspan'] : 8;
		$hierarchical_taxonomies = array();
		$flat_taxonomies = array();
		foreach ( self::$registered as $taxonomy_name ) {
			$taxonomy = get_taxonomy( $taxonomy_name );
			$taxonomies_obj[$taxonomy_name] = $taxonomy;
			if ( ! $taxonomy->show_ui ) continue;
			if ( $taxonomy->hierarchical )
				$hierarchical_taxonomies[] = $taxonomy;
			else
				$flat_taxonomies[] = $taxonomy;
		}
		do_action('gm_mediatags_pre_ui_print');
		?>
        <tr id="bulk-edit" class="inline-edit-row inline-edit-row-media inline-edit-media bulk-edit-row bulk-edit-row-media bulk-edit-media inline-editor" style="display: table-row;">
        <td colspan="<?php echo $colspan  ?>" class="colspanchange">
		<fieldset class="inline-edit-col-left">
        	<div class="inline-edit-col">
				<h4><?php _e( 'Bulk Edit', 'gmmediatags' ); ?></h4>
				<div id="bulk-title-div">
					<div id="bulk-titles">
                		<?php
                        if ( isset($_POST['media']) && ! empty($_POST['media']) ) { foreach ( $_POST['media'] as $media ) {
							$title = get_the_title($media);
						?>
                    	<div id="ttle<?php echo $media; ?>">
                        	<a id="_<?php echo $media; ?>" class="ntdelbutton" title="<?php _e('Remove From Bulk Edit', 'gmmediatags'); ?>">X</a><?php echo $title; ?>
                        </div>
                        <?php } } ?>
                	</div>
				</div>
        	</div>
        </fieldset>

		<fieldset class="inline-edit-col-center" style="width:<?php echo empty($flat_taxonomies) ? '64' : '32' ?>%; float:right;"><div class="inline-edit-col">
		<?php foreach ( $hierarchical_taxonomies as $taxonomy ) : ?>
			<span class="title inline-edit-categories-label"><?php echo esc_html( $taxonomy->labels->name ) ?></span>
			<?php if ( current_user_can( $taxonomy->cap->assign_terms ) ) { ?>
            <ul class="cat-checklist <?php echo esc_attr( $taxonomy->name )?>-checklist">
				<?php wp_terms_checklist( null, array( 'taxonomy' => $taxonomy->name ) ); ?>
			</ul>
            <?php } ?>
		<?php endforeach;?>
		</div></fieldset>

        <fieldset class="inline-edit-col-right" style="width:<?php echo empty($hierarchical_taxonomies) ? '64' : '32' ?>%; float:right;"><div class="inline-edit-col">
			<?php foreach($flat_taxonomies as $taxonomy ) : if ( current_user_can( $taxonomy->cap->assign_terms ) ) : ?>
			<label class="inline-edit-tags">
				<span class="title"><?php echo esc_html( $taxonomy->labels->name ) ?></span>
				<textarea cols="22" rows="1" name="tax_input[<?php echo esc_attr( $taxonomy->name )?>]" class="tax_input_<?php echo esc_attr( $taxonomy->name )?>"></textarea>
			</label>
			<?php endif; endforeach;?>
		</div></fieldset>

        <fieldset class="inline-edit-col-full" style="width:100%; float:left;"><div class="inline-edit-col">
        <h4><?php  _e('Remove all terms from: ', 'gmmediatags'); ?></h4>
		<?php
		foreach ( self::$registered as $taxonomy_name ) {
			printf('<label style="float:left; margin-left:6px"><input type="checkbox" name="clean_tax[%s]" value="1"> %s</label>', esc_attr($taxonomy_name), $taxonomies_obj[$taxonomy_name]->labels->name );
		}
		?>
        </div></fieldset>

		<p class="submit inline-assign_media_tag">
			<input name="bulk_assign_media_tag" id="bulk_assign_media_tag" class="button button-primary alignright" value="<?php _e('Update'); ?>" accesskey="s" type="submit">
            <a accesskey="c" class="button-secondary cancel alignright" style="margin-right:20px;"><?php _e('Cancel'); ?></a>
            <input name="media_view" value="list" type="hidden">
			<input name="screen" value="upload" type="hidden">
			<br class="clear">
		</p>
		</td>
        </tr>
        <?php
		do_action('gm_mediatags_ui_printed');
		die();
	}



}
