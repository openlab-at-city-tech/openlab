<?php
/**
 * Blocks API: WP_Block_Template class
 *
 * @package WordPress
 * @since 5.5.0
 */

/**
 * Class representing a block template.
 */
class WP_Block_Template {

	/**
	 * Type: wp_template or wp_template_part
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Theme.
	 *
	 * @var string
	 */
	public $theme;

	/**
	 * Template slug.
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Id.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Title.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Content.
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * Description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Source of the content. `theme` and `custom` is used for now.
	 *
	 * @var string
	 */
	public $source = 'theme';

	/**
	 * Post Id.
	 *
	 * @var integer|null
	 */
	public $wp_id;

	/**
	 * Template Status.
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Whether a template is, or is based upon, an existing template file.
	 *
	 * @var boolean
	 */
	public $has_theme_file;

	/**
	 * Whether a template is a custom template.
	 *
	 * @var bool
	 */
	public $is_custom = true;
}
