<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle Category Icon and upload, draft category features.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Config_Category {
	
	public $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;

	public function __construct() {

		$kb_id = EPKB_KB_Handler::get_current_kb_id();

		$this->kb_id = empty( $kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $kb_id;

		// handle Category icon on KB Category screen
		$taxonomy = EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_id );
		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'display_draft_fields' ), 99 );
		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'display_category_fields' ), 99 );
		add_action( "{$taxonomy}_add_form_fields", array( $this, 'display_draft_fields' ), 97 );
		add_action( "{$taxonomy}_add_form_fields", array( $this, 'display_category_fields' ), 99 );
		add_action( "edit_{$taxonomy}", array( $this, 'save_category_fields' ), 10, 2 );
		add_action( "created_{$taxonomy}", array( $this, 'save_category_fields' ), 10, 2 );
		add_filter( 'term_name', array( $this, 'category_name_filter' ), 10, 2 );
	}

	/**
	 * On Category edit page display icon upload feature
	 * @param $category
	 */
	public function display_draft_fields( $category ) {

		// show only on full edit for created categories. Hide for add new category column.
		$is_draft = false;
		if ( is_object( $category ) ) {
			$categories_data = self::get_category_data_option( $this->kb_id );
			$is_draft = ! empty( $categories_data[$category->term_id] ) && ! empty( $categories_data[$category->term_id]['is_draft'] );
		} 		 ?>
		
		<tr class="form-field epkb-term-options-wrap">
			<th scope="row"><label><?php esc_html_e( 'Draft', 'echo-knowledge-base' ); ?></label></th>
			<td><label><input type="checkbox" name="epkb_category_is_draft" <?php checked( $is_draft ); ?>> <span><?php echo esc_html__( 'Do not list categories on the KB Main Page or sidebars. It\'s important to note that categories ' .
                                                                'and their articles will still be accessible through direct URLs and other means. This feature is not a security measure, but rather a convenience feature that helps to ' .
	                                                            'prepare content for publication.', 'echo-knowledge-base' ); echo ' <strong>' . esc_html__( 'It is not intended to control access to KB content like our Access Manager plugin.', 'echo-knowledge-base' ) .
                     ' <a href="https://www.echoknowledgebase.com/documentation/hiding-categories/" target="_blank">' . esc_html__( 'Learn More', 'echo-knowledge-base' ) . '</a>' .
           '</strong>'; ?></span></label></td>
		</tr><?php
	}

	/**
	 * On Category edit page display icon upload feature
	 * @param $category
	 */
	public function display_category_fields( $category ) {

		$main_page_layout = epkb_get_instance()->kb_config_obj->get_value( $this->kb_id, 'kb_main_page_layout' );

		if ( $main_page_layout == 'Sidebar' ) { ?>
			<div class="epkb-term-options-message">
			<i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i>
			<p><?php esc_html_e( 'Sidebar Layout does not use icons for categories.', 'echo-knowledge-base'); ?></p>
			</div><?php
			return;
		}

		$category_level = $this->get_level( $category );

		// display icon on/off information but only for layouts that can have icons turned off
		if ( $main_page_layout == EPKB_Layout::GRID_LAYOUT ) {
			$location = $this->get_grid_icon_location( $this->kb_id );
		} else if ( $main_page_layout == EPKB_Layout::CATEGORIES_LAYOUT && $category_level > 1 ) {
			$location = '';
		} else {
			$location = epkb_get_instance()->kb_config_obj->get_value( $this->kb_id, 'section_head_category_icon_location' );
		}

		$is_new_category = ! is_object( $category );

		// if icons disabled just show turn on/off link
		if ( $location == 'no_icons' ) {

			$message = esc_html__( 'Category Icons are disabled', 'echo-knowledge-base' );

		    if ( $is_new_category ) {
			    self::category_icon_message( 'epkb-icons-are-disabled', $message , $this->get_on_off_icons_link(),
				                                'Turn Category Icons ON. See Categories & Articles Module settings.' );
			} else {    ?>
				<tr class="form-field epkb-term-options-wrap">
				<th scope="row">
					<label><?php esc_html_e( 'Category Icon', 'echo-knowledge-base' ); ?></label>
				</th>
				<td><?php self::category_icon_message( 'epkb-icons-are-disabled', $message , $this->get_on_off_icons_link(),
												'Turn Category Icons ON. See Categories & Articles Module settings.' ); ?></td>
			    </tr><?php
			}
			return;
		}

		// not all categories have icons
		switch( $main_page_layout ) {
			case 'Tabs':
				$category_icon_message = esc_html__( 'Only sub-categories in Tabs Layout have icons.', 'echo-knowledge-base' );
				break;
			case 'Sidebar':
				$category_icon_message = esc_html__( 'Sidebar Layout does not use icons for categories.', 'echo-knowledge-base' );
				break;
			case 'Categories':
				$category_icon_message = '';
				break;
			default:
				$category_icon_message = esc_html__( 'Only top-level categories have icons visible.', 'echo-knowledge-base' );
				break;
		}

		$categories_data = self::get_category_data_option( $this->kb_id );
		
		if ( ! $is_new_category  && ! empty( $categories_data[$category->term_id] ) ) {
			$active_icon_name =empty( $categories_data[$category->term_id]['name'] ) ? EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME : $categories_data[$category->term_id]['name'];
			$active_icon_type = empty( $categories_data[$category->term_id]['type'] ) ? EPKB_Icons::DEFAULT_CATEGORY_TYPE : $categories_data[$category->term_id]['type'];
			$active_image_id = empty( $categories_data[$category->term_id]['image_id'] ) ? EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID : $categories_data[$category->term_id]['image_id'];
			$active_image_size = empty( $categories_data[$category->term_id]['image_size'] ) ? EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE : $categories_data[$category->term_id]['image_size'];
		} else {
			$active_icon_name = EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME;
			$active_icon_type = EPKB_Icons::DEFAULT_CATEGORY_TYPE;
			$active_image_id = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID;
			$active_image_size = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE;
		}		?>

		<<?php echo $is_new_category ? 'div' : 'tr'; ?> class="form-field epkb-term-options-wrap" >
			<<?php echo $is_new_category ? 'div' : 'th'; ?> scope="row">
				<label><?php esc_html_e( 'Category Icon', 'echo-knowledge-base' ); ?></label>
			</<?php echo $is_new_category ? 'div' : 'th'; ?>>
			<<?php echo $is_new_category ? 'div' : 'td'; ?>>				<?php

				self::category_icon_message( 'epkb-icons-are-enabled', $category_icon_message, '', '');        ?>
                
				<div class="epkb-categories-icons epkb-categories-icons--visible">
					<div class="epkb-categories-icons__tabs-header">
						<div class="epkb-categories-icons__button <?php echo ( $active_icon_type == 'font' ) ? 'epkb-categories-icons__button--active' : ''; ?>" id="epkb_font_icon" data-type="font">
							<?php esc_html_e( 'Font Icon', 'echo-knowledge-base' ); ?>
						</div>
							<div class="epkb-categories-icons__button <?php echo ( $active_icon_type == 'image' ) ? 'epkb-categories-icons__button--active' : ''; ?>" id="epkb_image_icon"
							    data-type="image"><?php esc_html_e( 'Image Icon', 'echo-knowledge-base' ); ?>
							</div>
					</div>
					<div class="epkb-categories-icons__tab-body epkb-categories-icons__tab-body--font
					  <?php echo ( $active_icon_type == 'font' ) ? 'epkb-categories-icons__tab-body--active' : ''; ?>"><?php EPKB_Icons::get_icons_pack_html( true, $active_icon_name ); ?></div>
					<div class="epkb-categories-icons__tab-body epkb-categories-icons__tab-body--image
							<?php echo ( $active_icon_type == 'image' ) ? 'epkb-categories-icons__tab-body--active' : ''; ?>"><?php $this->display_image_block( $active_image_id, $active_image_size ); ?></div>
					
					<input type="hidden" name="epkb_head_category_icon_type" id="epkb_head_category_icon_type" value="<?php echo esc_attr( $active_icon_type ); ?>">
					<input type="hidden" name="epkb_head_category_icon_name" id="epkb_head_category_icon_name" value="<?php echo esc_attr( $active_icon_name ); ?>">
					<input type="hidden" name="epkb_head_category_icon_image" id="epkb_head_category_icon_image" value="<?php echo esc_attr( $active_image_id ); ?>">
					<input type="hidden" name="epkb_head_category_level" id="epkb_head_category_level" value="<?php echo esc_attr( $category_level ); ?>">
					<input type="hidden" name="epkb_head_category_template" id="epkb_head_category_template" value="<?php echo esc_attr( $main_page_layout ); ?>">
				</div>

			</<?php echo $is_new_category ? 'div' : 'td'; ?>>
		</<?php echo $is_new_category ? 'div' : 'tr'; ?>> <?php 
	}

	public static function category_icon_message( $class, $message , $url , $urlText ) {
		if ( empty( $message ) ) {
			return;
		}		?>
		<div class="epkb-term-options-message <?php echo esc_attr( $class ); ?>">
			<i class="epkbfa epkbfa-info-circle" aria-hidden="true"></i>
			<p>				<?php
				echo esc_html( $message );
				if ( ! empty( $url ) ) {   ?>
					<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php esc_html_e( $urlText, 'echo-knowledge-base' ); ?></a>				<?php
				}   ?>
			</p>
		</div>	<?php
	}
	
	/**
	 * On Category Edit screen display image selected (if any)
	 * @param string $image_id
	 * @param string $image_size
	 */
	private function display_image_block( $image_id = '', $image_size = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE ) {
		$image_url = wp_get_attachment_image_url( $image_id, $image_size ); ?>

		<div class="epkb-category-image">
			<div class="epkb-category-image__dropdown">
				<label>
					<span><?php esc_html_e( 'Image Size:', 'echo-knowledge-base' ); ?></span>
					<select id="epkb_head_category_thumbnail_size" name="epkb_head_category_thumbnail_size">
						<option value="full" <?php selected( 'full', $image_size); ?>><?php esc_html_e('Full', 'echo-knowledge-base' ); ?></option><?php
						if ( $sizes = $this->get_image_sizes() ) {
							foreach ( $sizes as $key => $val ) { 
								$width = empty( $val['width'] ) ? '' : $val['width'];
								$height = empty( $val['height'] ) ? '' : $val['height'];
								
								if ( $width && $height ) {
									$dimension = $width . 'x' . $height;
								} else {
									$dimension = $width . $height;
								}
								echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $image_size ) . '>' . esc_html( ucwords( esc_html__( $key, 'echo-knowledge-base' ) ) ) . ' (' . esc_html( $dimension ) . 'px)</option>';
							}
						} ?>
					</select>
				</label>
			</div>
			<div class="epkb-category-image__button <?php echo $image_url ? 'epkb-category-image__button--have-image' : 'epkb-category-image__button--no-image'; ?>"
			        style="<?php echo $image_url ? 'background-image: url(' . esc_url( $image_url ) . ');' : ''; ?>" data-title="<?php esc_attr_e('Choose Category Icon', 'echo-knowledge-base'); ?>">
				<i class="epkbfa ep_font_icon_plus"></i>
				<i class="epkbfa epkbfa-pencil"></i>
			</div>
			<div class="epkb-category-image__text">
				<ul>
					<li><?php
						esc_html_e( 'The size of all image icons on the front-end is controlled in the visual Editor', 'echo-knowledge-base' );					?>
					</li>
					<li><?php esc_html_e( 'This image should match the given setting for image icon size. If you choose a larger image, the image will be compressed to the icon size. This may cause unnecessary ' .
					              'load time on the front-end.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'For example, if you set the icon size to 50px, an image size of 50x50 will work the best.', 'echo-knowledge-base' ); ?></li>
				</ul>
			</div>
		</div>		<?php
	}

	/**
	 * Called by front-end layout code, get icon data or default from icons data array in the right format
	 *
	 * @param $term_id
	 * @param $categories_data
	 * @param string $default_icon_name
	 *
	 * @return array
	 */
	public static function get_category_icon( $term_id, $categories_data ) {
		$result = array(
			'type' => EPKB_Icons::DEFAULT_CATEGORY_TYPE, 
			'name' => EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME,
			'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
			'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
			'image_alt' => '',
			'image_title' => '',
			'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
			'color' => '#000000',
			'is_draft' => false
		);


		if ( ! empty( $categories_data[ $term_id]) ) {
			$result = array_merge( $result, $categories_data[ $term_id] );
		}
		
		if (strpos( $result['name'], 'epkbfa' ) === false) {
			$result['name'] = str_replace( 'fa-', 'epkbfa-', $result['name'] );
		}

		// image might have been updated so get the latest version if it is not demo data
		if ( ! empty( $result['image_id'] ) && get_post_status( $result['image_id'] ) ) {
			$image_url = wp_get_attachment_image_url( $result['image_id'], $result['image_size'] );
			$result['image_thumbnail_url'] = empty($image_url) ? '' : $image_url;
			$result['image_alt'] = get_post_meta( $result['image_id'], '_wp_attachment_image_alt', TRUE );
			$result['image_alt'] = empty($result['image_alt']) ? '' : $result['image_alt'];
			$result['image_title'] = get_the_title( $result['image_id'] );
		}

		if ( ! empty( $result['image_id'] ) && ! get_post_status( $result['image_id'] ) ) {

			$result['image_id'] = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID;
			$result['image_size'] = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE;
			$result['image_thumbnail_url'] = Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG;
			$result['image_alt'] = '';
			$result['image_title'] = '';
		}

		return $result;
	}

	/**
	 * Save Taxonomy Icon
	 *
	 * @param $term_id
	 * @param $tt_id
	 */
	public function save_category_fields( $term_id, $tt_id ) {

		$icon_type = EPKB_Utilities::get( 'epkb_head_category_icon_type' );
		$icon_name = EPKB_Utilities::get( 'epkb_head_category_icon_name' );
		$icon_image_id = EPKB_Utilities::get( 'epkb_head_category_icon_image' );
		$icon_image_size = EPKB_Utilities::get( 'epkb_head_category_thumbnail_size' );
		$is_draft = ! empty( EPKB_Utilities::get( 'epkb_category_is_draft' ) );

		$image_url = '';
		$image_alt = '';
		$image_title = '';

		if ( empty( $icon_type ) || ! isset( $icon_name ) || ! isset( $icon_image_id ) || ! in_array( $icon_type, array('image', 'font' ) ) ) {
			return;
		}

		// icon type = image, font
		$icon_type = ( $icon_type == 'image' ) && empty( $icon_image_id ) ? 'font' : $icon_type;

		if ( $icon_type == 'image' ) {
			$icon_name = EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME;
			$image_url = wp_get_attachment_image_url( $icon_image_id, $icon_image_size );
			$image_alt = get_post_meta( $icon_image_id, '_wp_attachment_image_alt', TRUE );
			$image_alt = empty( $image_alt ) ? '' : $image_alt;
			$image_title = get_the_title( $icon_image_id );
		} else {
			$icon_image_id = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID;
		} 

		$categories_icon_data = self::get_category_data_option( $this->kb_id );
		$previous_categories_icon_data = empty( $categories_icon_data[$term_id] ) ? [] : $categories_icon_data[$term_id];

		$categories_icon_data[$term_id] = array(
			'type' => $icon_type,
			'name' => $icon_name,
			'image_id' => $icon_image_id,
			'image_size' => $icon_image_size,
			'image_alt' => $image_alt,
			'image_title' => $image_title,
			'image_thumbnail_url' => empty( $image_url ) ? '' : $image_url,
			'color' => '#000000',    // FUTURE
			'is_draft' => $is_draft
		);

		// mark category
		if ( array_diff( $categories_icon_data[$term_id], $previous_categories_icon_data ) ) {
			update_term_meta( $term_id, EPKB_Icons::CATEGORY_ICON_USER_CHANGED_FLAG, 1 );
		}

		EPKB_Utilities::save_kb_option( $this->kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icon_data );
	}

	/**
	 * What image sizes the user can choose from.
	 * @return array
	 */
	private function get_image_sizes() {
		//$wais = &$GLOBALS['_wp_additional_image_sizes'];

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			//if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			if ( in_array( $_size, array('thumbnail', 'medium') ) ) {
				$sizes[ $_size ] = array(
					'width'  => get_option( "{$_size}_size_w" ),
					'height' => get_option( "{$_size}_size_h" ),
					'crop'   => (bool) get_option( "{$_size}_crop" ),
				);
			} else {
				continue;
			}

		/*	elseif ( isset( $wais[$_size] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $wais[ $_size ]['width'],
					'height' => $wais[ $_size ]['height'],
					'crop'   => $wais[ $_size ]['crop'],
				);
			} */

			// size registered, but has 0 width and height
			if ( ( $sizes[ $_size ]['width'] == 0 ) && ( $sizes[ $_size ]['height'] == 0) ) {
				unset( $sizes[$_size] );
			}
		}

		return $sizes;
	}

	/**
	 * Find level of currently edited category.
	 *
	 * @param $category
	 * @param int $level
	 * @return int
	 */
	private function get_level($category, $level = 1) {
		
		if ( $level > 2 ) {
			return 3;
		}
		
		if ( empty($category->parent) ) {
			return $level;
		} else {

			$level++;
			$category = EPKB_Core_Utilities::get_kb_category_unfiltered( $this->kb_id, $category->parent );
			if ( empty($category) ) {
				return 3;
			}
			
			return $this->get_level($category, $level);
		}
	}

	private function get_grid_icon_location( $kb_id ) {
		if ( function_exists( 'elay_get_instance' ) && isset( elay_get_instance()->kb_config_obj ) ) {
			return elay_get_instance()->kb_config_obj->get_value( $kb_id, 'grid_category_icon_location' );
		}
		return '';
	}

	// open Editor for user to switch on/off icons
	private function get_on_off_icons_link() {
		return admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id ) . '&page=epkb-kb-configuration#settings__main-page' );
	}

	/**
	 * Retrieve icon settings for all Categories; ignore images that do not exist
	 * @param $kb_id
	 * @return array - empty if not found
	 */
	public static function get_category_data_option( $kb_id ) {

		$categories_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Icons::CATEGORIES_ICONS, array(), true );
		if ( empty( $categories_data ) ) {
			return array();
		}

		foreach( $categories_data as $term_id => $categories_icon ) {
			$categories_data[$term_id] = array(
				'type' => empty( $categories_icon['type'] ) ? EPKB_Icons::DEFAULT_CATEGORY_TYPE : $categories_icon['type'],
				'name' => empty( $categories_icon['name'] ) ? EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME : $categories_icon['name'],
				'image_id' => empty( $categories_icon['image_id'] ) ? EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID : $categories_icon['image_id'],
				'image_size' => empty( $categories_icon['image_size'] ) ? EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE : $categories_icon['image_size'],
				'image_alt' => empty( $categories_icon['image_alt'] ) ? '' : $categories_icon['image_alt'],
				'image_title' => empty( $categories_icon['image_title'] ) ? '' : $categories_icon['image_title'],
				'image_thumbnail_url' => empty( $categories_icon['image_thumbnail_url'] ) ? Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG : $categories_icon['image_thumbnail_url'],
				'color' => empty( $categories_icon['color'] ) ? '#000000' : $categories_icon['color'],
				'is_draft' => ! empty( $categories_icon['is_draft'] )
			);

			// check if image exists (stored in post meta data)
			if ( $categories_data[$term_id]['image_id'] != EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID && $categories_data[$term_id]['type'] == 'image'
				 && ! get_post_status( $categories_data[$term_id]['image_id'] ) ) {
				$categories_data[$term_id]['image_id'] = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID;
				$categories_data[$term_id]['image_size'] = EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE;
				$categories_data[$term_id]['image_thumbnail_url'] = Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG;
				$categories_data[$term_id]['image_alt'] = '';
				$categories_data[$term_id]['image_title'] = '';
			}
		}

		return $categories_data;
	}

	/**
	 * Check and remove:
	 * - images that do not exist
	 * - terms that do not exist
	 * @param $kb_id
	 * @return void
	 */
	public static function remove_missing_terms_and_images_from_categories_icons( $kb_id ) {

		// get icons excluding images that do not exist
		$categories_data = self::get_category_data_option( $kb_id );
		if ( empty( $categories_data ) ) {
			return;
		}

		$categories_data_filtered = [];
		foreach ( $categories_data as $term_id => $icon_data ) {

			if ( ! term_exists( $term_id ) ) {
				continue;
			}

			$categories_data_filtered[$term_id] = $icon_data;
		}

		EPKB_Utilities::save_kb_option( $kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_data_filtered );
	}

	// add 'Draft' to category name if it is draft
	function category_name_filter( $tag_name, $tag ) {

		if ( ! is_object( $tag ) ) {
			return $tag_name;
		}

		$kb_id = EPKB_KB_Handler::get_kb_id_from_category_taxonomy_name( $tag->taxonomy );

		// not kb taxonomy
		if ( is_wp_error( $kb_id ) ) {
			return $tag_name;
		}

		// check draft
		$categories_data = self::get_category_data_option( $kb_id );
		if ( ! empty( $categories_data[$tag->term_id] ) && ! empty( $categories_data[$tag->term_id]['is_draft'] ) ) {
			$tag_name .= ' - ' . esc_html__( 'Draft', 'echo-knowledge-base' );
		}

		return $tag_name;
	}

	/**
	 * Backward capability.
	 */
	public static function get_category_icons_option( $kb_id ) {
		return self::get_category_data_option( $kb_id );
	}
}