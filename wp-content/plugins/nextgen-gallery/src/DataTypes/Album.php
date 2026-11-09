<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataMappers\Album as Mapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataStorage\Sanitizer;

/**
 * Album Data Type
 *
 * Represents an album containing galleries in NextGEN Gallery.
 */
class Album extends Model {

	/**
	 * Link target values preserved in the album name and description.
	 *
	 * The album screen's own allowlist permits a[target] (Legacy/admin/album.php:74),
	 * so the model-layer filter must not strip it back out. #495 asked for links that
	 * open in a new tab, which is `_blank`, and that is all this permits:
	 *
	 * - `_parent` and `_top` are excluded because they can escape a frame.
	 * - `_self` is excluded because it is the default, so dropping it renders
	 *   identically, and HTMLPurifier attaches rel="noreferrer noopener" to every
	 *   target it keeps -- which on a same-tab link would suppress the referrer for
	 *   no benefit.
	 *
	 * @var string[]
	 */
	const ALLOWED_FRAME_TARGETS = [ '_blank' ];

	/**
	 * Album description
	 *
	 * @var string
	 */
	public $albumdesc;

	/**
	 * Galleries to exclude
	 *
	 * @var array
	 */
	public $exclude;

	/**
	 * WordPress post ID for extras
	 *
	 * @var int
	 */
	public $extras_post_id;

	/**
	 * Album ID
	 *
	 * @var int
	 */
	public $id;

	/**
	 * ID field name
	 *
	 * @var string
	 */
	public $id_field = 'id';

	/**
	 * Album name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * WordPress page ID
	 *
	 * @var int
	 */
	public $pageid;

	/**
	 * Preview picture ID
	 *
	 * @var int
	 */
	public $previewpic;

	/**
	 * Album slug
	 *
	 * @var string
	 */
	public $slug;

	/**
	 * Sort order of galleries in album
	 *
	 * @var array
	 */
	public $sortorder = [];

	/**
	 * Date when the album was created.
	 *
	 * @var string
	 */
	public $date_created;

	/**
	 * Date when the album was last modified.
	 *
	 * @var string
	 */
	public $date_modified;

	/**
	 * Display type for the album.
	 *
	 * @var string
	 */
	public $display_type = 'photocrati-nextgen_basic_compact_album';

	/**
	 * Settings for the display type.
	 *
	 * @var array
	 */
	public $display_type_settings = [];

	/**
	 * Gets the primary key column name.
	 *
	 * @return string
	 */
	public function get_primary_key_column() {
		return 'id';
	}

	/**
	 * Gets the data mapper instance.
	 *
	 * @return Mapper
	 */
	public function get_mapper() {
		return Mapper::get_instance();
	}

	/**
	 * Gets all galleries in this album.
	 *
	 * @param bool $models Unused.
	 * @return array
	 * @TODO Remove $models attribute when Pro has reached the first stage of POPE removal compatibility
	 */
	public function get_galleries( $models = false ) {
		$mapper      = GalleryMapper::get_instance();
		$gallery_key = $mapper->get_primary_key_column();
		return $mapper->find_all( [ "{$gallery_key} IN %s", $this->sortorder ] );
	}

	/**
	 * Validates album data.
	 *
	 * @return bool|array True if valid, array of errors otherwise.
	 */
	public function validation() {
		// Filter the text columns at the model layer, as Gallery::validation() and
		// Image::validation() already do for theirs. DriverBase::save() calls
		// validation() and then persists this same model, so every album write path —
		// REST, XML-RPC, Lightroom and the legacy admin screens — is covered here
		// rather than only in whichever controller happened to add a sanitizer.
		//
		// Both columns use the script-only mode, and that choice is load-bearing:
		// stripping outright would revert #495. Album names and descriptions are
		// allowed to carry a tag allowlist — `a[href,title,target]`, `b`, `i`, `u`,
		// `em`, `strong`, `p`, `br`, `ul`, `ol`, `li` — which the album controller
		// applies via wp_kses() on the way in (Legacy/admin/album.php:212 and :304,
		// added by 322514ff for #495 after #441's strip-everything fix drew customer
		// complaints) and which the public breadcrumb renders through wp_kses() on the
		// way out (templates/Albums/breadcrumbs.php:8). wp_strip_all_tags() here would
		// silently destroy what both ends deliberately preserve, on every album save.
		//
		// strip_html( $v, true ) keeps that allowlist and removes script and event
		// handlers, matching Gallery::validation() for its title and
		// Image::validation() for its alttext and description. It does not, on its own,
		// keep `target`: HTMLPurifier's default Attr.AllowedFrameTargets is empty, so a
		// link set to open in a new tab lost that on every save — including saves
		// triggered by an unrelated edit such as sortorder or previewpic. The targets
		// below are therefore passed in explicitly. HTMLPurifier attaches
		// rel="noreferrer noopener" to a target it keeps, so this does not reintroduce
		// reverse tabnabbing, and `_parent` / `_top` stay stripped.
		if ( isset( $this->name ) ) {
			$this->name = Sanitizer::strip_html( $this->name, true, self::ALLOWED_FRAME_TARGETS );
		}

		if ( isset( $this->albumdesc ) ) {
			$this->albumdesc = Sanitizer::strip_html( $this->albumdesc, true, self::ALLOWED_FRAME_TARGETS );
		}

		$errors = array_merge(
			[],
			$this->validates_presence_of( 'name' ),
			$this->validates_numericality_of( 'previewpic' )
		);

		return empty( $errors ) ? true : $errors;
	}
}
