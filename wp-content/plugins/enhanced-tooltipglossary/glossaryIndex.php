<?php

class CMTT_Glossary_Index {

	public static $shortcodeDisplayed = false;
	protected static $filePath = '';
	protected static $cssPath = '';
	protected static $jsPath = '';
	protected static $preContent = '';

	/**
	 * Adds the hooks
	 */
	public static function init() {
		self::$filePath = plugin_dir_url( __FILE__ );
		self::$cssPath  = self::$filePath . 'assets/css/';
		self::$jsPath   = self::$filePath . 'assets/js/';

		/*
		 * ACTIONS
		 */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'addScripts' ) );
		add_action( 'cmtt_glossary_shortcode_after', array( __CLASS__, 'addScriptParams' ) );
		add_action( 'cmtt_glossary_index_query_before', array( __CLASS__, 'outputEmbeddedScripts' ), 10, 2 );

		/*
		 * FILTERS
		 */

		/*
		 * Glossary Index Tooltip Content
		 */
		add_filter( 'cmtt_glossary_index_tooltip_content', array( __CLASS__, 'getTheTooltipContentBase' ), 10, 2 );
		add_filter( 'cmtt_glossary_index_tooltip_content', array( 'CMTT_Free', 'addCodeBeforeAfter' ), 15, 2 );
		add_filter( 'cmtt_glossary_index_tooltip_content', array(
			'CMTT_Free',
			'cmtt_glossary_parse_strip_shortcodes'
		), 20, 2 );
		add_filter( 'cmtt_glossary_index_tooltip_content', array(
			'CMTT_Free',
			'cmtt_glossary_filterTooltipContent'
		), 30, 2 );

		add_filter( 'cmtt_glossary_index_remove_links_to_terms', array( __CLASS__, 'removeLinksToTerms' ), 10, 2 );
		add_filter( 'cmtt_glossary_index_disable_tooltips', array( __CLASS__, 'disableTooltips' ), 10, 2 );
		add_filter( 'cmtt_glossary_index_disable_tooltips', array( __CLASS__, 'disableTooltipsOnIndex' ), 100 );

		add_filter( 'cmtt_glossary_index_pagination', array( __CLASS__, 'outputPagination' ), 10, 3 );

		add_filter( 'cmtt_glossary_index_listnav_content', array( __CLASS__, 'modifyListnav' ), 10, 3 );
		add_filter( 'cmtt_glossary_index_before_listnav_content', array( __CLASS__, 'modifyBeforeListnav' ), 10, 3 );
		add_filter( 'cmtt_index_term_tooltip_permalink', array( __CLASS__, 'modifyTermPermalink' ), 10, 3 );

		add_filter( 'cmtt_glossary_index_after_content', array( __CLASS__, 'wrapInMainContainer' ), 1, 3 );
		if ( class_exists( 'CMTT_Pro' ) && \CM\CMTT_Settings::get( 'cmtt_glossaryShowShareBox' ) == 1 ) {
			add_filter( 'cmtt_glossary_index_after_content', array( 'CMTT_Pro', 'cmtt_glossaryAddShareBox' ), 5, 3 );
		}
		add_filter( 'cmtt_glossary_index_after_content', array( __CLASS__, 'wrapInStyleContainer' ), 10, 3 );
		add_filter( 'cmtt_glossary_index_after_content', array( __CLASS__, 'addReferalSnippet' ), 50, 3 );

		add_filter( 'cmtt_glossary_index_shortcode_default_atts', array(
			__CLASS__,
			'setupDefaultGlossaryIndexAtts'
		), 5 );

		add_filter( 'cmtt_glossary_index_atts', array( __CLASS__, 'addGlossaryIndexGetAtts' ), 10 );

		add_filter( 'cmtt_tooltip_script_data', array( __CLASS__, 'tooltipsDisabledForPage' ), 50000 );
		add_filter( 'cmtt_glossary_container_additional_class', array( __CLASS__, 'addShowCountsClass' ) );

		add_filter( 'cmtt_pre_item_description_content', array( __CLASS__, 'maybeWrapBeforeDefinition' ), 10, 4 );
		add_filter( 'cmtt_postItemTitleContent', array( __CLASS__, 'maybeWrapAfterDefinition' ), 10, 6 );
		add_filter( 'cmtt_preItemTitleContent', array( __CLASS__, 'maybeWrapAfterThumbnail' ), 10, 6 );

		add_filter( 'cmtt_glossaryItemTitle', array( __CLASS__, 'addStructuredData' ), 10000, 3 );

		/*
		 * SHORTCODES
		 */
		/*
		 * @since 4.1.5 Add the option to change the shortcode name
		 */
		$glossaryShortcode = strtolower( \CM\CMTT_Settings::get( 'cmtt_glossaryShortcode', 'glossary' ) );
		add_shortcode( $glossaryShortcode, array( __CLASS__, 'glossaryShortcode' ) );
	}

	public static function addStructuredData( $title, $glossaryItem ) {
		$structuredData = \CM\CMTT_Settings::get( 'cmtt_add_structured_data_term_page', 1 );
		if ( $structuredData ) {
			$title = '<span itemprop="name">' . $title . '</span>';
		}

		return $title;
	}

	public static function outputEmbeddedScripts( $args, $shortcodeAtts ) {
		$embeddedMode = \CM\CMTT_Settings::get( 'cmtt_enableEmbeddedMode', false );
		if ( $embeddedMode ) {
			self::addScripts();
			self::addScriptParams( $shortcodeAtts );
		}
	}

	/**
	 * Adds the scripts which has to be included on the main glossary index page only
	 */
	public static function addScripts() {
		$embeddedMode = \CM\CMTT_Settings::get( 'cmtt_enableEmbeddedMode', false );
		$inFooter     = \CM\CMTT_Settings::get( 'cmtt_script_in_footer', true );
		/*
		 * If hashing is enabled scripts have to be loaded in the footer
		 */
		$hashTooltipContent = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipHashContent', '0' );
		/*
		 * If the embeddedMode is enabled we ignore the inFooter setting
		 */
		if ( $hashTooltipContent || ( $inFooter && ! $embeddedMode ) ) {
			add_action( 'wp_footer', array( __CLASS__, 'outputScripts' ), 9 );
		} else {
			self::outputScripts();
		}
		add_action( 'wp_footer', array( __CLASS__, 'outputTooltipWrapper' ), PHP_INT_MAX );
	}

	public static function outputScripts() {
		global $post;
		static $runOnce = false;
		if ( $runOnce === true ) {
			return;
		}

		global $post, $replacedTerms;
		$postId = empty( $post->ID ) ? '' : $post->ID;

		$embeddedMode    = \CM\CMTT_Settings::get( 'cmtt_enableEmbeddedMode', false );
		$inFooter        = \CM\CMTT_Settings::get( 'cmtt_script_in_footer', true );
		$isGlossaryTerm  = $post && ( ! empty( $post->post_type ) && in_array( $post->post_type, array( 'glossary' ) ) ); // TRUE if is glossary term page, FALSE otherwise
		$isGlossaryIndex = $post && has_shortcode( $post->post_content, 'glossary' );
		$isIframe        = $post && strpos( $post->post_content, 'cm-embedded-content' ) !== false;
		$miniSuffix      = ( current_user_can( 'manage_options' ) || \CM\CMTT_Settings::get( 'cmtt_disableMinifiedTooltip', false ) ) ? '' : '.min';
		$forceLoad       = \CM\CMTT_Settings::get( 'cmtt_forceLoadScripts', false );
		/*
		 * If the scripts are loaded in footer and there's no tooltips found, and we're not on Glossary Term Page, we can ignore loading scripts
		 */
		if ( ! $forceLoad && ( ( $inFooter && ! $embeddedMode ) && ( empty( $replacedTerms ) && ! self::$shortcodeDisplayed ) && ! $isGlossaryTerm && ! $isGlossaryIndex && ! $isIframe ) ) {
			return;
		}

		$tooltipData = array();

		$displayAnimation = \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' );
		$hideAnimation    = \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' );

		$tooltipArgs                             = array(
			'placement'               => \CM\CMTT_Settings::get( 'cmtt_tooltipPlacement', 'horizontal' ),
			'clickable'               => (bool) apply_filters( 'cmtt_is_tooltip_clickable', false ),
			'close_on_moveout'        => (bool) \CM\CMTT_Settings::get( 'cmtt_glossaryCloseOnMoveout', 1 ),
			'only_on_button'          => (bool) \CM\CMTT_Settings::get( 'cmtt_glossaryCloseOnlyOnButton', false ),
			'touch_anywhere'          => (bool) \CM\CMTT_Settings::get( 'cmtt_glossaryCloseOnTouchAnywhere', false ),
			'delay'                   => (int) 1000 * (float) \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayDelay', 0 ),
			'timer'                   => (int) 1000 * (float) \CM\CMTT_Settings::get( 'cmtt_tooltipHideDelay', 0 ),
			'minw'                    => (int) \CM\CMTT_Settings::get( 'cmtt_tooltipWidthMin', 200 ),
			'maxw'                    => (int) \CM\CMTT_Settings::get( 'cmtt_tooltipWidthMax', 400 ),
			'top'                     => (int) \CM\CMTT_Settings::get( 'cmtt_tooltipPositionTop', 5 ),
			'left'                    => (int) \CM\CMTT_Settings::get( 'cmtt_tooltipPositionLeft', 50 ),
			'endalpha'                => (int) \CM\CMTT_Settings::get( 'cmtt_tooltipOpacity', 100 ),
			'zIndex'                  => (int) \CM\CMTT_Settings::get( 'cmtt_tooltipZIndex', 1500 ),
			'borderStyle'             => \CM\CMTT_Settings::get( 'cmtt_tooltipBorderStyle' ),
			'borderWidth'             => \CM\CMTT_Settings::get( 'cmtt_tooltipBorderWidth' ) . 'px',
			'borderColor'             => \CM\CMTT_Settings::get( 'cmtt_tooltipBorderColor' ),
			'background'              => \CM\CMTT_Settings::get( 'cmtt_tooltipBackground' ),
			'foreground'              => \CM\CMTT_Settings::get( 'cmtt_tooltipForeground' ),
			'fontSize'                => \CM\CMTT_Settings::get( 'cmtt_tooltipFontSize' ) . 'px',
			'padding'                 => \CM\CMTT_Settings::get( 'cmtt_tooltipPadding' ),
			'borderRadius'            => \CM\CMTT_Settings::get( 'cmtt_tooltipBorderRadius' ) . 'px',
			'tooltipDisplayanimation' => $displayAnimation,
			'tooltipHideanimation'    => $hideAnimation,
			'toolip_dom_move'         => (bool) \CM\CMTT_Settings::get( 'cmtt_tooltipMoveTooltipInDOM' ),
			'link_whole_tt'           => (bool) \CM\CMTT_Settings::get( 'cmtt_tooltipLinkWholeTooltip' ),
		);
		$tooltipData['cmtooltip']                = apply_filters( 'cmtt_tooltip_script_args', $tooltipArgs );
		$tooltipData['ajaxurl']                  = admin_url( 'admin-ajax.php' );
		$tooltipData['post_id']                  = $postId;
		$tooltipData['mobile_disable_tooltips']  = \CM\CMTT_Settings::get( 'cmtt_glossaryMobileDisableTooltips', '0' );
		$tooltipData['desktop_disable_tooltips'] = \CM\CMTT_Settings::get( 'cmtt_glossaryDesktopDisableTooltips', '0' );
		$tooltipData['tooltip_on_click']         = \CM\CMTT_Settings::get( 'cmtt_glossaryShowTooltipOnClick', '0' );
		$tooltipData['exclude_ajax']             = 'cmttst_event_save';

		$tooltipFrontendJsDeps = array( 'jquery', 'cm-modernizr-js' );
		if ( \CM\CMTT_Settings::get( 'cmtt_audioPlayerEnabled', '0' ) ) {
			/*
			 * If needed we require mediaelement
			 */
			$tooltipFrontendJsDeps[] = 'mediaelement';
		}

		$scriptsConfig = array(
			'scripts' => array(
				'cm-modernizr-js'     => array(
					'path'      => self::$jsPath . 'modernizr.min.js',
					'in_footer' => $inFooter,
				),
				'tooltip-frontend-js' => array(
					'path'      => self::$jsPath . 'tooltip' . $miniSuffix . '.js',
					'deps'      => $tooltipFrontendJsDeps,
					'in_footer' => $inFooter,
					'localize'  => array(
						'var_name' => 'cmtt_data',
						'data'     => apply_filters( 'cmtt_tooltip_script_data', $tooltipData ),
					),
				),
			),
			'styles'  => array(
				'cmtooltip' => array(
					'path'   => self::$cssPath . 'tooltip' . $miniSuffix . '.css',
					'inline' => array(
						'data' => self::getDynamicCSS(),
					),
				),
				'dashicons' => array(
					'path' => false,
				),
			),
		);

		$animationsEnabled = $displayAnimation != 'no_animation' && $hideAnimation != 'no_animation';
		if ( $animationsEnabled && ! CMTT_AMP::is_amp_endpoint() ) {
			$scriptsConfig['styles']['animate-css'] = array(
				'path' => self::$cssPath . 'animate.css',
			);
		}

		$fontName = \CM\CMTT_Settings::get( 'cmtt_tooltipFontStyle', 'default (disables Google Fonts)' );
		if ( is_string( $fontName ) && $fontName !== 'default (disables Google Fonts)' && $fontName !== 'default' ) {
			$fontNameFixed                                  = strpos( $fontName, 'Condensed' ) !== false ? $fontName . ':300' : $fontName; // fix for the Open Sans Condensed
			$scriptsConfig['styles']['tooltip-google-font'] = array( 'path' => '//fonts.googleapis.com/css?family=' . $fontNameFixed );
		}

		self::_scriptStyleLoader( $scriptsConfig, $embeddedMode );
		$runOnce = true;
	}

	/**
	 * Add the dynamic CSS to reflect the styles set by the options
	 *
	 * @return string
	 */
	public static function getDynamicCSS() {
		ob_start();
		echo apply_filters( 'cmtt_dynamic_css_before', '' );
		?>

		.tiles ul.glossaryList li {
		min-width: <?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySmallTileWidth', '85px' ); ?> !important;
		width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySmallTileWidth', '85px' ); ?> !important;
		}
		.tiles ul.glossaryList span { min-width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySmallTileWidth', '85px' ); ?>; width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossarySmallTileWidth', '85px' ); ?>;  }
		.cm-glossary.tiles.big ul.glossaryList a { min-width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryBigTileWidth', '179px' ); ?>; width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryBigTileWidth', '179px' ); ?> }
		.cm-glossary.tiles.big ul.glossaryList span { min-width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryBigTileWidth', '179px' ); ?>; width:<?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryBigTileWidth', '179px' ); ?>; }

		<?php
		$linkColor        = \CM\CMTT_Settings::get( 'cmtt_tooltipLinkColor' );
		$linkColorOnHover = \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverColor' );

		$borderBottom          = sprintf( '%s %dpx %s;', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineStyle' ), \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineWidth' ), \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineColor' ) );
		$borderBottomHover     = sprintf( '%s %dpx %s;', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineStyle' ), \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineWidth' ), \CM\CMTT_Settings::get( 'cmtt_tooltipLinkHoverUnderlineColor' ) );
		$borderBottomTemporary = sprintf( '%s %dpx %s;', \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineStyle' ), \CM\CMTT_Settings::get( 'cmtt_tooltipLinkUnderlineWidth' ), \CM\CMTT_Settings::get( 'cmtt_glossaryDoubleClickUnderlineColor' ) );
		?>
		span.glossaryLink, a.glossaryLink {
		border-bottom: <?php echo $borderBottom; ?>
		<?php if ( ! empty( $linkColor ) ) : ?>
			color: <?php echo $linkColor; ?> !important;
		<?php endif; ?>
		}
		span.glossaryLink.temporary, a.glossaryLink.temporary {
		border-bottom: <?php echo $borderBottomTemporary; ?>
		}
		span.glossaryLink:hover, a.glossaryLink:hover {
		border-bottom: <?php echo $borderBottomHover; ?>
		<?php if ( ! empty( $linkColorOnHover ) ) : ?>
			color:<?php echo $linkColorOnHover; ?> !important;
		<?php endif; ?>
		}

		<?php
		$titleFontSize = \CM\CMTT_Settings::get( 'cmtt_indexTermFontSize' );
		if ( ! empty( $titleFontSize ) ) :
			?>
			.glossaryList .glossary-link-title,
			.glossaryList .cmtt-related-term-title {
			font-size: <?php echo $titleFontSize; ?>px !important;
			}
		<?php endif; ?>
		.glossaryList .glossary-link-title {
		font-weight: <?php echo \CM\CMTT_Settings::get( 'cmtt_indexTermFontWeight', 'normal' ); ?> !important;
		}

		<?php
		$imgSize = \CM\CMTT_Settings::get( 'cmtt_img_term_def_imgsize', '' );
		if ( ! empty( $imgSize ) ) :
			?>
			.cm-glossary.img-term-definition .glossary-container ul#glossaryList > li {
			grid-template-columns: minmax(100px, <?php echo $imgSize; ?>) 1fr;
			}
		<?php endif; ?>

		<?php
		$closeIconColor = \CM\CMTT_Settings::get( 'cmtt_tooltipCloseColor', '#222' );
		if ( ! empty( $closeIconColor ) ) :
			?>
			#tt #tt-btn-close{ color: <?php echo $closeIconColor; ?> !important}
		<?php endif; ?>

		.cm-glossary.grid ul.glossaryList li[class^='ln']  { width: <?php echo \CM\CMTT_Settings::get( 'cmtt_glossaryGridColumnWidth', '200px' ); ?> !important}

		<?php
		$closeIconSize = \CM\CMTT_Settings::get( 'cmtt_tooltipCloseSize', '20' );
		if ( ! empty( $closeIconSize ) ) :
			?>
			#tt #tt-btn-close{
			direction: rtl;
			font-size: <?php echo $closeIconSize; ?>px !important
			}
		<?php endif; ?>

		<?php
		$hintIcon = \CM\CMTT_Settings::get( 'cmtt_glossary_search_hint', '' );

		if ( ! empty( $hintIcon ) ) :
			$src = wp_get_attachment_image_src( (int) $hintIcon, 'thumbnail', false );
			?>
			div.cmtt_help {
			background-image: url(<?php echo $src[0]; ?>);
			background-size: contain;
			}
			div.cmtt_help:hover {
			background-image: url(<?php echo $src[0]; ?>);
			}
		<?php endif; ?>

		<?php
		$tooltipTextColorOverride = \CM\CMTT_Settings::get( 'cmtt_tooltipForegroundOverride' );
		$tooltipTextColor         = \CM\CMTT_Settings::get( 'cmtt_tooltipForeground' );
		if ( ! empty( $tooltipTextColor ) ) :
			?>
			#tt #ttcont glossaryItemBody * {color: <?php echo $tooltipTextColor; ?>}
		<?php
		endif;
		if ( ! empty( $tooltipTextColorOverride ) ) :
			?>
			#tt #ttcont *{color: <?php echo $tooltipTextColor; ?> !important}
		<?php endif; ?>


		<?php
		$showEmptyLetters = ! \CM\CMTT_Settings::get( 'cmtt_index_showEmpty' );
		if ( ! empty( $showEmptyLetters ) ) :
			?>
			#glossaryList-nav .ln-letters a.ln-disabled {display: none}
		<?php endif; ?>

		<?php
		$internalLinkColor = \CM\CMTT_Settings::get( 'cmtt_tooltipInternalLinkColor' );
		if ( ! empty( $internalLinkColor ) ) :
			?>
			#tt #ttcont a{color: <?php echo $internalLinkColor; ?> !important}
		<?php endif; ?>

		<?php
		$internalEditLinkColor = \CM\CMTT_Settings::get( 'cmtt_tooltipInternalEditLinkColor' );
		if ( ! empty( $internalEditLinkColor ) ) :
			?>
			#tt #ttcont .glossaryItemEditlink a{color: <?php echo $internalEditLinkColor; ?> !important}
		<?php endif; ?>

		<?php
		$internalMobileLinkColor = \CM\CMTT_Settings::get( 'cmtt_tooltipInternalMobileLinkColor' );
		if ( ! empty( $internalMobileLinkColor ) ) :
			?>
			#tt #ttcont .mobile-link a{color: <?php echo $internalMobileLinkColor; ?> !important}
		<?php endif; ?>

		<?php if ( \CM\CMTT_Settings::get( 'cmtt_tooltipShadow', 1 ) ) : ?>
			#ttcont {
			box-shadow: 0px 0px 20px #<?php echo str_replace( '#', '', \CM\CMTT_Settings::get( 'cmtt_tooltipShadowColor', '666666' ) ); ?>;
			-moz-box-shadow: 0px 0px 20px #<?php echo str_replace( '#', '', \CM\CMTT_Settings::get( 'cmtt_tooltipShadowColor', '666666' ) ); ?>;
			-webkit-box-shadow: 0px 0px 20px #<?php echo str_replace( '#', '', \CM\CMTT_Settings::get( 'cmtt_tooltipShadowColor', '666666' ) ); ?>;
			}
		<?php
		endif;
		$tooltipDisplayDelay = \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayDelay', '0.5' );
		$tooltipHideDelay    = \CM\CMTT_Settings::get( 'cmtt_tooltipHideDelay', '0.5' );

		if ( $tooltipDisplayDelay > 0 ) :
			?>
			.fadeIn,.zoomIn,.flipInY,.in{
			animation-duration:<?php echo $tooltipDisplayDelay; ?>s !important;
			}
		<?php
		endif;
		if ( $tooltipHideDelay > 0 ) :
			?>
			.fadeOut,.zoomOut,.flipOutY,.out{
			animation-duration:<?php echo $tooltipHideDelay; ?>s !important;
			}
		<?php
		endif;

		$slide_height = \CM\CMTT_Settings::get( 'cmtt_carousel_slide_height', '250px' );
		if ( ! empty( $slide_height ) ) :
			?>
			.cm-glossary.term-carousel .slick-slide,
			.cm-glossary.tiles-with-definition ul > li { height: <?php echo $slide_height; ?> !important}
		<?php
		endif;

		$tile_width = \CM\CMTT_Settings::get( 'cmtt_term_tiles_width', '220px' );
		if ( ! empty( $tile_width ) ) :
			?>
			.cm-glossary.tiles-with-definition ul {
			grid-template-columns: repeat(auto-fill, <?php echo $tile_width; ?>) !important;
			}
		<?php
		endif;

		if ( \CM\CMTT_Settings::get( 'cmtt_letter_width', 0 ) ) :
			?>
			#glossaryList-nav .ln-letters {
			width: 100%;
			display: flex;
			flex-wrap: wrap;
			}
			#glossaryList-nav .ln-letters a {
			text-align: center;
			flex-grow: 1;
			}
		<?php
		endif;

		$searchBtnColor = \CM\CMTT_Settings::get( 'cmtt_searchBtnBgColor', '' );
		if ( ! empty( $searchBtnColor ) ) :
			?>
			.cm-glossary #glossary-search {
			background-color: <?php echo $searchBtnColor; ?> !important;
			}
		<?php
		endif;

		$searchBtnColorOnHover = \CM\CMTT_Settings::get( 'cmtt_searchBtnBgColorOnHover', '' );
		if ( ! empty( $searchBtnColorOnHover ) ) :
			?>
			.cm-glossary #glossary-search:hover {
			background-color: <?php echo $searchBtnColorOnHover; ?> !important;
			}
		<?php
		endif;

		$searchBtnTextColor = \CM\CMTT_Settings::get( 'cmtt_searchBtnTextColor', '' );
		if ( ! empty( $searchBtnTextColor ) ) :
			?>
			.cm-glossary #glossary-search {
			color: <?php echo $searchBtnTextColor; ?> !important;
			}
		<?php
		endif;

		$searchBtnTextColorOnHover = \CM\CMTT_Settings::get( 'cmtt_searchBtnTextColorOnHover', '' );
		if ( ! empty( $searchBtnTextColorOnHover ) ) :
			?>
			.cm-glossary #glossary-search:hover {
			color: <?php echo $searchBtnTextColorOnHover; ?> !important;
			}
		<?php
		endif;

		$cubeBtnColorOnHover = \CM\CMTT_Settings::get( 'cmtt_cubeBtnColorSelected', '' );
		if ( ! empty( $cubeBtnColorOnHover ) ) :
			?>
			.cm-glossary.cube .listNav .ln-letters a {
			color: <?php echo $cubeBtnColorOnHover; ?>;
			border: 1px solid <?php echo $cubeBtnColorOnHover; ?>;
			}
		<?php
		endif;

		$cubeBtnColor         = \CM\CMTT_Settings::get( 'cmtt_cubeBtnColor', '' );
		if ( ! empty( $cubeBtnColor ) ) :
			?>
			.cm-glossary.cube .listNav .ln-letters a.ln-disabled {
			color: <?php echo $cubeBtnColor; ?>;
			border: 1px solid <?php echo $cubeBtnColor; ?>;
			}
		<?php
		endif;

		if ( \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle', '' ) == 'flipboxes-with-definition' ) :
			$number_of_flipboxes = \CM\CMTT_Settings::get( 'cmtt_number_of_flipboxes', 6 );
			$background_color = \CM\CMTT_Settings::get( 'cmtt_flipbox_background_color', '#cecece' );
			$flipbox_height   = \CM\CMTT_Settings::get( 'cmtt_flipbox_item_height', '160px' );
			?>
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList {
			grid-template-columns: repeat(<?php echo $number_of_flipboxes; ?>, 1fr);
			}
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList > li > div.term-block > .glossaryLinkMain,
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList > li > div.term-block > .glossaryLink,
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList .glossary_itemdesc .glossary-read-more-link {
			background-color: <?php echo $background_color; ?>;
			}
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList > li,
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList > li > div.term-block > .glossaryLinkMain,
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList > li > div.term-block > .glossaryLink,
			.cm-glossary.flipboxes-with-definition #glossaryList.glossaryList > li > div.term-block > .glossary_itemdesc {
			height: <?php echo $flipbox_height; ?>;
			}
		<?php
		endif;

		$selected_languages = \CM\CMTT_Settings::get( 'cmtt_language_table_list', array() );
		if ( \CM\CMTT_Settings::get( 'cmtt_glossaryDisplayStyle', '' ) == 'language-table' && is_array( $selected_languages ) && count( $selected_languages ) > 0 ) :
			$number = count( $selected_languages );
			?>
			.cm-glossary.language-table #glossaryList li {
			display: grid;
			grid-template-columns: repeat(<?php echo $number; ?>, 1fr);
			}
		<?php
		endif;

		// Styling for search form
		$searchInputBorderWidth     = \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderWidth', '' );
		$searchInputBorderStyle     = \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderStyle', '' );
		$searchInputBorderColor     = \CM\CMTT_Settings::get( 'cmtt_glossaryInputBorderColor', '' );
		$searchInputBackgroundColor = \CM\CMTT_Settings::get( 'cmtt_glossaryInputBackgroundColor', '' );
		$searchInputTextColor       = \CM\CMTT_Settings::get( 'cmtt_glossaryInputTextColor', '' );
		$searchInputFontSize        = \CM\CMTT_Settings::get( 'cmtt_glossaryInputFontSize', '' );
		$searchFormBorderRadius     = \CM\CMTT_Settings::get( 'cmtt_glossaryFormBorderRadius', '' );
		$searchFormCombine          = \CM\CMTT_Settings::get( 'cmtt_glossaryCombineForm', '' );
		$searchFormWidth            = \CM\CMTT_Settings::get( 'cmtt_glossarySearchFormWidth', '' );
		?>

		.glossary-search-wrapper {
		display: inline-block;
		<?php if ( ! empty( $searchFormWidth ) ) : ?>
			display: inline-flex;
			align-items: center;
			width: 100%;
			max-width: <?php echo $searchFormWidth; ?>px;
		<?php endif; ?>
		}


		<?php if ( ! empty( $searchFormCombine ) ) : ?>
			.glossary-search-wrapper {
			display: inline-flex;
			align-items: stretch;
			}

			input.glossary-search-term {
			border-right: none !important;
			margin-right: 0 !important;
			<?php if ( ! empty( $searchFormBorderRadius ) ) : ?>
				border-radius: <?php echo esc_attr( $searchFormBorderRadius ); ?>px 0 0 <?php echo esc_attr( $searchFormBorderRadius ); ?>px !important;
			<?php endif; ?>

			}

			button.glossary-search.button {
			border-left: none !important;
			<?php if ( ! empty( $searchFormBorderRadius ) ) : ?>
				border-radius: 0 <?php echo esc_attr( $searchFormBorderRadius ); ?>px <?php echo esc_attr( $searchFormBorderRadius ); ?>px 0 !important;
			<?php endif; ?>
			<?php if ( ! empty( $searchFormWidth ) ) : ?>
				flex-shrink: 0;
			<?php endif; ?>
			}
		<?php endif; ?>

		input.glossary-search-term {
		<?php if ( ! empty( $searchFormWidth ) ) : ?>
			width: 100%;
			margin-right: 5px;
		<?php endif; ?>
		outline: none;
		<?php if ( ! empty( $searchInputBorderWidth ) ) : ?>
			border-width: <?php echo esc_attr( $searchInputBorderWidth ); ?>px;
		<?php endif; ?>
		<?php if ( ! empty( $searchInputBorderStyle ) ) : ?>
			border-style: <?php echo esc_attr( $searchInputBorderStyle ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $searchInputBorderColor ) ) : ?>
			border-color: <?php echo esc_attr( $searchInputBorderColor ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $searchInputBackgroundColor ) ) : ?>
			background-color: <?php echo esc_attr( $searchInputBackgroundColor ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $searchInputTextColor ) ) : ?>
			color: <?php echo esc_attr( $searchInputTextColor ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $searchInputFontSize ) ) : ?>
			font-size: <?php echo esc_attr( $searchInputFontSize ); ?>px;
		<?php endif; ?>
		<?php if ( ! empty( $searchFormBorderRadius ) ) : ?>
			border-radius: <?php echo esc_attr( $searchFormBorderRadius ); ?>px;
		<?php endif; ?>
		}


		<?php if ( ! empty( $searchFormWidth ) ) : ?>
			button.glossary-search.button {
			flex-shrink: 0;
			}
		<?php endif; ?>

		<?php
		$searchButtonBorderWidth = \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderWidth', '' );
		$searchButtonBorderStyle = \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderStyle', '' );
		$searchButtonBorderColor = \CM\CMTT_Settings::get( 'cmtt_glossaryButtonBorderColor', '' );
		$searchButtonFontSize    = \CM\CMTT_Settings::get( 'cmtt_glossaryButtonFontSize', '' );
		?>

		button.glossary-search.button {
		outline: none;
		<?php if ( ! empty( $searchButtonBorderWidth ) ) : ?>
			border-width: <?php echo esc_attr( $searchButtonBorderWidth ); ?>px;
		<?php endif; ?>
		<?php if ( ! empty( $searchButtonBorderStyle ) ) : ?>
			border-style: <?php echo esc_attr( $searchButtonBorderStyle ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $searchButtonBorderColor ) ) : ?>
			border-color: <?php echo esc_attr( $searchButtonBorderColor ); ?>;
		<?php endif; ?>
		<?php if ( ! empty( $searchButtonFontSize ) ) : ?>
			font-size: <?php echo esc_attr( $searchButtonFontSize ); ?>px;
		<?php endif; ?>
		<?php if ( ! empty( $searchFormBorderRadius ) ) : ?>
			border-radius: <?php echo esc_attr( $searchFormBorderRadius ); ?>px;
		<?php endif; ?>
		}

		<?php
		echo apply_filters( 'cmtt_dynamic_css_after', '' );
		$content = ob_get_clean();

		/*
		 * One can use this filter to change/remove the standard styling
		 */
		$dynamicCSScontent = apply_filters( 'cmtt_dynamic_css', $content );

		return trim( $dynamicCSScontent );
	}

	public static function _scriptStyleLoader( $config, $embeddedMode = false ) {
		$stylesAndScripts = '';
		if ( ! empty( $config ) ) {
			if ( ! empty( $config['scripts'] ) && ! CMTT_AMP::is_amp_endpoint() ) {
				foreach ( $config['scripts'] as $scriptKey => $scriptData ) {
					$scriptData = shortcode_atts(
						array(
							'path'      => '',
							'deps'      => array(),
							'ver'       => CMTT_VERSION,
							'in_footer' => false,
							'localize'  => null,
						),
						$scriptData
					);

					/*
					 * In embedded situations jQuery will most likely be on the site already, so no need to call it
					 */
					if ( $embeddedMode && is_array( $scriptData['deps'] ) && ! empty( $scriptData['deps'] ) ) {
						foreach ( $scriptData['deps'] as $key => $value ) {
							if ( 'jquery' === $value ) {
								unset( $scriptData['deps'][ $key ] );
							}
						}
					}
					wp_enqueue_script( $scriptKey, $scriptData['path'], $scriptData['deps'], $scriptData['ver'], $scriptData['in_footer'] );

					if ( ! empty( $scriptData['localize'] ) && is_array( $scriptData['localize'] ) ) {
						$scriptDataLocalize = shortcode_atts(
							array(
								'var_name' => '',
								'data'     => array(),
							),
							$scriptData['localize']
						);
						wp_localize_script( $scriptKey, $scriptDataLocalize['var_name'], $scriptDataLocalize['data'] );
					}
				}
			}

			if ( ! empty( $config['styles'] ) ) {
				foreach ( $config['styles'] as $styleKey => $styleData ) {
					wp_enqueue_style( $styleKey, $styleData['path'], array(), CMTT_VERSION );
					/*
					 * It's WP 3.3+ function
					 */
					if ( function_exists( 'wp_add_inline_style' ) && ! empty( $styleData['inline'] ) && is_array( $styleData['inline'] ) ) {
						wp_add_inline_style( $styleKey, $styleData['inline']['data'] );
					}
				}
			}

			if ( $embeddedMode ) {
				ob_start();
				wp_print_scripts( array_keys( $config['scripts'] ) );
				wp_print_styles( array_keys( $config['styles'] ) );
				$stylesAndScripts = ob_get_clean();
			}
		}
		if ( ! empty( $stylesAndScripts ) ) {
			self::$preContent .= $stylesAndScripts;
			// add_filter( 'the_content', array( __CLASS__, '_preContent' ), PHP_INT_MAX );
			add_filter( 'cmtt_glossary_index_after_content', array( __CLASS__, '_preContent' ), PHP_INT_MAX );
		}

		return $stylesAndScripts;
	}

	public static function addScriptParams( $shortcodeAtts ) {
		global $post;
		static $runOnce;
		if ( $runOnce === true ) {
			return;
		}

		$listnavArgs      = array();
		$embeddedMode     = \CM\CMTT_Settings::get( 'cmtt_enableEmbeddedMode', false );
		$inFooter         = \CM\CMTT_Settings::get( 'cmtt_script_in_footer', true );
		$miniSuffix       = $miniSuffix = ( current_user_can( 'manage_options' ) || \CM\CMTT_Settings::get( 'cmtt_disableMinifiedTooltip', false ) ) ? '' : '.min';
		$default_language = \CM\CMTT_Settings::get( 'cmtt_enable_languages', 0 ) ? \CM\CMTT_Settings::get( 'cmtt_default_language', '' ) : '';
		if ( self::isServerSide() ) {
			$listnavArgs['limit'] = (int) \CM\CMTT_Settings::get( 'cmtt_limitNum', 0 );
		}
		if ( ! self::isServerSide() ) {
			$letters = (array) \CM\CMTT_Settings::get( 'cmtt_index_letters' );
			$letters = apply_filters( 'cmtt_index_letters', $letters, $shortcodeAtts, false );

			$listnavArgs            = array(
				'perPage'              => (int) \CM\CMTT_Settings::get( 'cmtt_perPage', 0 ),
				'limit'                => (int) \CM\CMTT_Settings::get( 'cmtt_limitNum', 0 ),
				'letters'              => $letters,
				'includeNums'          => (bool) \CM\CMTT_Settings::get( 'cmtt_index_includeNum' ),
				'includeAll'           => (bool) \CM\CMTT_Settings::get( 'cmtt_index_includeAll' ),
				'showRound'            => (bool) \CM\CMTT_Settings::get( 'cmtt_index_showRound' ),
				'initLetter'           => isset( $shortcodeAtts['letter'] ) ? $shortcodeAtts['letter'] : \CM\CMTT_Settings::get( 'cmtt_index_initLetter', '' ),
				'ignoreLetterOnSearch' => (bool) \CM\CMTT_Settings::get( 'cmtt_glossary_ignore_letter_on_search', 0 ),
				'initLetterOverride'   => ! empty( $shortcodeAtts['letter'] ),
				'allLabel'             => __( \CM\CMTT_Settings::get( 'cmtt_index_allLabel', 'ALL' ), 'cm-tooltip-glossary' ),
				'noResultsLabel'       => __( \CM\CMTT_Settings::get( 'cmtt_glossary_NoResultsLabel', 'Nothing found. Please change the filters.' ), 'cm-tooltip-glossary' ),
				'showCounts'           => (bool) \CM\CMTT_Settings::get( 'cmtt_index_showCounts', '1' ),
				'sessionSave'          => (bool) \CM\CMTT_Settings::get( 'cmtt_index_sessionSave', '1' ),
				'doingSearch'          => (bool) ! empty( $shortcodeAtts['search_term'] ),
				'letterBgWidth'        => (int) \CM\CMTT_Settings::get( 'cmtt_letter_width', 0 ),
				'language'             => isset( $shortcodeAtts['language'] ) ? $shortcodeAtts['language'] : $default_language,
				/*
				 * If sort by post_name (3.8.15)
				 */
				'sortByTitle'          => (bool) \CM\CMTT_Settings::get( 'cmtt_index_sortby_title' ),
			);
			$tooltipData['enabled'] = ! (bool) ( isset( $shortcodeAtts['disable_listnav'] ) ? $shortcodeAtts['disable_listnav'] : false );
			$tooltipData['list_id'] = apply_filters( 'cmtt_glossary_index_list_id', 'glossaryList' );
		}

		$tooltipData['fast_filter']              = (bool) apply_filters( 'cmtt_glossary_index_fast_filter', \CM\CMTT_Settings::get( 'cmtt_indexFastFilter', '0' ) );
		$tooltipData['letterBgWidth']            = \CM\CMTT_Settings::get( 'cmtt_letter_width', 0 );
		$tooltipData['listnav']                  = apply_filters( 'cmtt_listnav_js_args', $listnavArgs );
		$tooltipData['glossary_page_link']       = get_permalink( self::getGlossaryIndexPageId() );
		$tooltipData['ajaxurl']                  = admin_url( 'admin-ajax.php' );
		$tooltipData['scrollTop']                = \CM\CMTT_Settings::get( 'cmtt_glossaryScrollTop', 0 );
		$tooltipData['enableAjaxComplete']       = \CM\CMTT_Settings::get( 'cmtt_glossaryEnableAjaxComplete', 1 );
		$tooltipData['cmtt_listnav_script_data'] = 'glossary_search|cmttst_event_save';
		$tooltipData['noAjax']                   = apply_filters( 'cmtt_glossaryNoAjax', true );

		/*
		 * post_id is either the ID of the page where post has been found or the default Glossary Index Page from settings
		 */
		$tooltipData['post_id'] = ! empty( $post->ID ) ? $post->ID : self::getGlossaryIndexPageId();

		$scriptsConfig = array(
			'scripts' => array(
				'tooltip-listnav-js' => array(
					'path'      => self::$jsPath . 'cm-glossary-listnav' . $miniSuffix . '.js',
					'deps'      => array( 'jquery' ),
					'in_footer' => $inFooter,
					'localize'  => array(
						'var_name' => 'cmtt_listnav_data',
						'data'     => apply_filters( 'cmtt_listnav_script_data', $tooltipData ),
					),
				),
			),
			'styles'  => array(
				'jquery-listnav-style' => array(
					'path' => self::$cssPath . 'jquery.listnav.css',
				),
			),
		);

		/*
		 * Only load if "Use Fast-live-Filter?" option is enabled
		 */
		if ( $tooltipData['fast_filter'] ) {
			$scriptsConfig['scripts']['cm-fastlivefilter-js']         = array(
				'path'      => self::$jsPath . 'jquery.fastLiveFilter.js',
				'deps'      => array( 'jquery' ),
				'in_footer' => $inFooter,
			);
			$scriptsConfig['scripts']['tooltip-listnav-js']['deps'][] = 'cm-fastlivefilter-js';
		}

		/*
		 * Only load if "Term Carousel" view is enabled
		 */
		if ( isset( $shortcodeAtts['glossary_index_style'] ) && $shortcodeAtts['glossary_index_style'] == 'term-carousel' ) {
			$scriptsConfig['scripts']['slick-js']                     = array(
				'path'      => self::$jsPath . 'slick.min.js',
				'deps'      => array( 'jquery' ),
				'in_footer' => $inFooter,
			);
			$scriptsConfig['styles']['slick-css']                     = array(
				'path' => self::$cssPath . 'slick.css',
			);
			$scriptsConfig['scripts']['tooltip-listnav-js']['deps'][] = 'slick-js';
		}

		self::_scriptStyleLoader( $scriptsConfig, $embeddedMode );
		$runOnce = true;
	}

	/**
	 * Returns true if the server-side pagination is enabled (and perPage is enabled)
	 *
	 * @return boolean
	 */
	public static function isServerSide() {
		// If AMP version is enabled, then force to use server-side pagination
		$default = \CM\CMTT_Settings::get( 'cmtt_perPage' ) >= 0 && ( \CM\CMTT_Settings::get( 'cmtt_glossaryServerSidePagination' ) == 1 || CMTT_AMP::is_amp_endpoint() );

		return (bool) apply_filters( 'cmtt_is_serverside_pagination', $default );
	}

	/**
	 * Function should return the ID of the Glossary Index Page
	 *
	 * @return integer
	 * @since 2.7.4
	 */
	public static function getGlossaryIndexPageId() {
		$glossaryPageID = apply_filters( 'cmtt_get_glossary_index_page_id', \CM\CMTT_Settings::get( 'cmtt_glossaryID' ) );
		/*
		 * WPML integration
		 */
		if ( function_exists( 'icl_object_id' ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
			$glossaryPageID = icl_object_id( $glossaryPageID, 'page', ICL_LANGUAGE_CODE );
		}

		return $glossaryPageID;
	}

	/**
	 * Filter the shortcode atts with the $_GET
	 *
	 * @param type $baseAtts
	 *
	 * @return type
	 */
	public static function addGlossaryIndexGetAtts( $baseAtts ) {
		$getAtts = (array) filter_input_array( INPUT_GET );
		$atts    = array_merge( $baseAtts, $getAtts );

		return $atts;
	}

	/**
	 * Returns true if the server-side pagination is enabled
	 *
	 * @return array
	 */
	public static function setupDefaultGlossaryIndexAtts( $baseAtts ) {
		$defaultAtts['pagination_position'] = \CM\CMTT_Settings::get( 'cmtt_glossaryPaginationPosition', 'bottom' );
		$atts                               = array_merge( $baseAtts, $defaultAtts );

		return $atts;
	}

	/**
	 * Function serves the shortcode: [glossary]
	 */
	public static function glossaryShortcode( $atts = array() ) {
		global $post;

		if ( ! is_array( $atts ) ) {
			$atts = array();
		}

		if ( $post !== null ) {
			$glossaryPageLink = get_page_link( $post );
		} elseif ( ! empty( $atts['post_id'] ) ) {
			$glossaryPageLink = get_permalink( $atts['post_id'] );
		} else {
			$glossaryPageLink = get_permalink( self::getGlossaryIndexPageId() );
		}

		$default_atts   = apply_filters(
			'cmtt_glossary_index_shortcode_default_atts',
			array(
				'glossary_page_link'   => $glossaryPageLink,
				'exact_search'         => \CM\CMTT_Settings::get( 'cmtt_index_searchExact' ),
				'only_on_search'       => \CM\CMTT_Settings::get( 'cmtt_showOnlyOnSearch' ),
				'show_search'          => \CM\CMTT_Settings::get( 'cmtt_glossary_showSearch', 1 ),
				'only_relevant_cats'   => \CM\CMTT_Settings::get( 'cmtt_glossary_onlyRelevantCats', 0 ),
				'only_relevant_tags'   => \CM\CMTT_Settings::get( 'cmtt_glossary_onlyRelevantTags', 0 ),
				'glossary_index_style' => apply_filters( 'cmtt_glossary_index_style', \CM\CMTT_Settings::get( 'cmtt_glossaryListTiles' ) == '1' ? 'small-tiles' : 'classic' ),
				'itemspage'            => filter_input( INPUT_GET, 'itemspage' ),
				'perpage'              => filter_input( INPUT_GET, 'perpage' ),
			)
		);
		$shortcode_atts = apply_filters( 'cmtt_glossary_index_atts', array_merge( $default_atts, $atts ) );

		/*
		 * Filtering to protect against the XSS attacks since 3.5.10
		 */
		foreach ( $shortcode_atts as $key => $value ) {
			if ( is_string( $value ) ) {
				$shortcode_atts[ $key ] = htmlspecialchars( filter_var( $value, FILTER_DEFAULT ) );
			}
		}

		do_action( 'cmtt_glossary_shortcode_before', $shortcode_atts );

		$output = self::outputGlossaryIndexPage( $shortcode_atts );

		do_action( 'cmtt_glossary_shortcode_after', $shortcode_atts, $atts );

		self::$shortcodeDisplayed = true;

		return $output;
	}

	/**
	 * Displays the main glossary index
	 *
	 * @param type $shortcodeAtts
	 *
	 * @return string $content
	 */
	public static function outputGlossaryIndexPage( $shortcodeAtts ) {
		global $post;

		do_action( 'qm/start', 'cmtt_index_getting_terms' );

		$content = '';

		$glossaryIndexContentArr = array();

		if ( $post === null && ! empty( $shortcodeAtts['post_id'] ) ) {
			$post = get_post( $shortcodeAtts['post_id'] );
		}

		/*
		 *  Checks whether to show tooltips on main glossary page or not
		 */
		$tooltipsDisabled = apply_filters( 'cmtt_glossary_index_disable_tooltips', false, $post );

		/*
		 * Set the display style of Glossary Index Page
		 */
		$glossaryIndexStyle = $shortcodeAtts['glossary_index_style'];

		if ( isset( $shortcodeAtts['glossary-search-term'] ) ) {
			$shortcodeAtts['search_term'] = $shortcodeAtts['glossary-search-term'];
		}

		/*
		 * Get the pagination position
		 */
		$paginationPosition = $shortcodeAtts['pagination_position'];

		$sortByTitle = \CM\CMTT_Settings::get( 'cmtt_index_sortby_title', 'title' );

		$serverSidePagination = self::isServerSide();

		$args = array(
			'post_type'              => 'glossary',
			'post_status'            => 'publish',
			'orderby'                => $sortByTitle,
			'order'                  => 'ASC',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'suppress_filters'       => false,
			'exact'                  => $shortcodeAtts['exact_search'],
			'nopaging'               => true
		);

		if ( ! empty( $shortcodeAtts['author_id'] ) ) {
			$args['author'] = $shortcodeAtts['author_id'];
		}

		/*
		 * Added in 3.3.7 - we need a way to make sure no posts are displayed if the search_term is empty,
		 * as we only want to display the Index if the user's searching
		 *
		 * In 3.5.0 I had to change from -1 to solve the 'fix' in WordPress
		 */
		if ( $shortcodeAtts['only_on_search'] && empty( $shortcodeAtts['search_term'] ) && empty( $shortcodeAtts['letter'] ) ) {
			$args['p'] = PHP_INT_MAX;
		}

		$args = apply_filters( 'cmtt_glossary_index_query_args', $args, $shortcodeAtts );
		do_action( 'cmtt_glossary_index_query_before', $args, $shortcodeAtts );

		$glossary_index = CMTT_Free::getGlossaryItems( $args, 'index', $shortcodeAtts );
		$glossary_query = CMTT_Free::$lastQueryDetails['query'];

		do_action( 'cmtt_glossary_index_query_after', $glossary_query, $args );

		if ( ! empty( $shortcodeAtts['search_term'] ) ) {
			$glossary_index = array_filter( $glossary_index, function ( $glossaryItem ) use ( $shortcodeAtts ) {
				$search_in = CMTT_Glossary_Index::get_fields_to_search_in();
				$found     = false;

				if ( in_array( '0', $search_in ) ) {
					$found = $found || preg_match( '/' . $shortcodeAtts['search_term'] . '/i', $glossaryItem->post_title );
				}

				if ( ! $found && in_array( '1', $search_in ) ) {
					$found = $found || preg_match( '/' . $shortcodeAtts['search_term'] . '/i', $glossaryItem->post_content );
				}

				return $found;
			} );
		}

		if ( $serverSidePagination && $limit_terms = (int) \CM\CMTT_Settings::get( 'cmtt_limitNum', 0 ) ) {

			$firstLettersArr = [];

			$glossary_index = array_filter( $glossary_index, function ( $glossaryItem ) use ( $limit_terms, &$firstLettersArr ) {

				$firstLetter = CMTT_Free::getFirstLetter( $glossaryItem, true );

				if ( isset( $firstLettersArr[ $firstLetter ] ) ) {
					$firstLettersArr[ $firstLetter ] ++;
				} else {
					$firstLettersArr[ $firstLetter ] = 1;
				}

				if ( $firstLettersArr[ $firstLetter ] > $limit_terms ) {
					return false;
				}

				return true;
			} );
		}

		$glossary_list_id = apply_filters( 'cmtt_glossary_index_list_id', 'glossaryList' );
		$listnavContent   = self::maybeOutputListnav( $shortcodeAtts, $glossary_index, $glossary_query, $content, $glossary_list_id );

		$glossary_index = apply_filters( 'cmtt_glossary_index_term_list', $glossary_index, $glossary_query, $shortcodeAtts );

		if ( ! empty( $shortcodeAtts['letter'] ) && 'all' !== $shortcodeAtts['letter'] ) {
			$glossary_index = array_filter( $glossary_index, function ( $glossaryItem ) use ( $shortcodeAtts, $sortByTitle ) {
				return $shortcodeAtts['letter'] === CMTT_Free::getFirstLetter( $glossaryItem, $sortByTitle );
			} );
		}

		$numberOfPages = 1;
		if ( $serverSidePagination ) {
			$postsPerPage = (int) isset( $shortcodeAtts['perpage'] ) ? $shortcodeAtts['perpage'] : \CM\CMTT_Settings::get( 'cmtt_perPage', 50 );
			$currentPage  = (int) isset( $shortcodeAtts['itemspage'] ) ? $shortcodeAtts['itemspage'] : 1;
			if ( ! $postsPerPage ) {
				$postsPerPage = PHP_INT_MAX;
			}
			$numberOfPages = (int) ceil( count( $glossary_index ) / $postsPerPage );
		}

		/*
		 * New feature in 3.8.20 - redirect to glossary term if there's just one result
		 * https://secure.helpscout.net/conversation/1014679497/100603?folderId=768551
		 * @since 4.2.0 - moved higher for better performance
		 */
		$direct_to_term = \CM\CMTT_Settings::get( 'cmtt_glossary_directToTermPage', false );
		if ( $direct_to_term && ! empty( $shortcodeAtts['search_term'] ) && 1 == count( $glossary_index ) ) {
			$glossary_item = reset( $glossary_index );
			$redirect_url  = CMTT_Free::get_term_link( $glossary_item->ID );
			$content       = '<div><input type="hidden" id="cmtt_redirector" data-url="' . $redirect_url . '" /></div>';

			return $content;
		}

		$content .= $listnavContent;

		do_action( 'qm/stop', 'cmtt_index_getting_terms' );

		if ( ! empty( $glossary_index ) ) {

			$enabled_caching = \CM\CMTT_Settings::get( 'cmtt_glossaryEnableCaching', false );
			$sorted          = false;

			do_action( 'qm/start', 'cmtt_index_before_loop' );

			do_action( 'qm/start', 'cmtt_index_sorting' );

			if ( $enabled_caching ) {
				$hash                  = CMTT_Free::_get_hash( CMTT_Free::$lastQueryDetails['ids'], 'sorted' );
				$glossary_index_sorted = get_transient( $hash );
				$sorted                = false !== $glossary_index_sorted && is_array( $glossary_index_sorted );
			}

			if ( ! $sorted ) {
				/*
				 * Allow filtering before sorting
				 */
				$glossary_index = apply_filters( 'cmtt_glossary_index_items_before_sorting', $glossary_index, $glossary_query, $shortcodeAtts );

				usort( $glossary_index, array( __CLASS__, 'compareGlossaryTerms' ) );

				/*
				 * Allow filtering after sorting
				 */
				$glossary_index = apply_filters( 'cmtt_glossary_index_items_after_sorting', $glossary_index, $glossary_query, $shortcodeAtts );

				if ( $enabled_caching && is_array( $glossary_index ) ) {
					set_transient( $hash, $glossary_index, 3600 );
				}
			} else {
				$glossary_index = $glossary_index_sorted;
			}

			do_action( 'qm/stop', 'cmtt_index_sorting' );

			if ( $serverSidePagination ) {
				$glossary_index = array_slice( $glossary_index, ( $currentPage - 1 ) * $postsPerPage, $postsPerPage );
			}

			$letters = (array) \CM\CMTT_Settings::get( 'cmtt_index_letters' );
			$letters = apply_filters( 'cmtt_index_letters', $letters, $shortcodeAtts );

			$content .= self::maybeOutputPagination( $paginationPosition, 'top', $numberOfPages, $shortcodeAtts );

			if ( CMTT_AMP::is_amp_endpoint() ) {
				$content .= '<amp-state id="cmindsState">
                <script type="application/json">
                    {
                        "visibleLetter": "ln-all",
                        "visibleTooltip": ""
                    }
                </script></amp-state>';
			}
			$structuredData      = \CM\CMTT_Settings::get( 'cmtt_add_structured_data_term_page', 1 ) ? ' itemscope itemtype="https://schema.org/DefinedTermSet"' : '';
			$ulAdditionalClass   = $glossaryIndexStyle == 'term-carousel' ? ' term-carousel-wrapper' : '';
			$glossaryListContent = '<ul class="glossaryList' . $ulAdditionalClass . '" role="tablist" id="' . $glossary_list_id . '" ' . $structuredData . '>';
			$content             .= apply_filters( 'cmtt_before_glossary_list', $glossaryListContent );
			$content             = apply_filters( 'cmtt_glossary_index_before_terms_list', $content, $glossaryIndexStyle, $shortcodeAtts );

			do_action( 'qm/stop', 'cmtt_index_before_loop' );

			/*
			 * Output the terms of the glossary
			 */
			foreach ( $glossary_index as $glossaryItem ) {

				$content .= self::maybeOutputIndexLetter( $glossaryItem, $glossaryIndexStyle, $shortcodeAtts, $letters );

				$content .= self::maybeOutputSingleTerm( $glossaryItem, $glossaryIndexStyle, $shortcodeAtts,
					$letters, $tooltipsDisabled, $post );
			}

			$content .= '</ul>';
			$content = apply_filters( 'cmtt_glossary_index_after_terms_list', $content, $shortcodeAtts );

			$content .= self::maybeOutputPagination( $paginationPosition, 'bottom', $numberOfPages, $shortcodeAtts );

		} else {
			$noResultsText = __( \CM\CMTT_Settings::get( 'cmtt_glossary_NoResultsLabel', 'Nothing found. Please change the filters.' ), 'cm-tooltip-glossary' );
			$content       .= '<span class="error">' . $noResultsText . '</span>';
		}

		$content = apply_filters( 'cmtt_glossary_index_after_content', $content, $glossary_query, $shortcodeAtts );

		do_action( 'cmtt_after_glossary_index' );

		return $content;
	}

	public static function get_fields_to_search_in() {
		global $wp_query;
		$cb_search = $wp_query->get( 'cb_search' );
		/*
			 ML
		 * having the ability to search the index page only by Title or only by description
		 * or by both title and description.
		 * the Admin Settings is controlling this ability
		 */
		if ( trim( $cb_search ) != '' ) {
			if ( $cb_search == 'title' ) {
				$fields_to_search = '0';
			} else {
				$fields_to_search = '1';
			}
			$fields_to_search = array( $fields_to_search );
		} else {
			$fields_to_search = \CM\CMTT_Settings::get( 'cmtt_glossarySearchFromOptions' );
		}

		if ( ! is_array( $fields_to_search ) ) {
			if ( $fields_to_search == '2' ) {
				$fields_to_search = array( '0', '1' );
			} else {
				$fields_to_search = array( $fields_to_search );
			}
		}

		return (array) $fields_to_search;
	}

	/**
	 * @param array $shortcodeAtts
	 * @param array $glossary_index
	 * @param mixed $glossary_query
	 * @param string $content
	 * @param mixed $glossary_list_id
	 *
	 * @return string
	 */
	public static function maybeOutputListnav( $shortcodeAtts, $glossary_index, $glossary_query, $content, $glossary_list_id ): string {
		$letterSize = \CM\CMTT_Settings::get( 'cmtt_indexLettersSize' );

		$content .= apply_filters( 'cmtt_glossary_index_before_listnav_content', '', $shortcodeAtts, $glossary_query );

		$listnavContent = '<div id="' . $glossary_list_id . '-nav" class="listNav ' . $letterSize . '" role="tablist">';
		$listnavContent .= apply_filters( 'cmtt_glossary_index_listnav_content_inside', '', $shortcodeAtts, $glossary_query, $glossary_index );
		$listnavContent .= '</div>';

		$content .= apply_filters( 'cmtt_glossary_index_listnav_content', $listnavContent, $shortcodeAtts, $glossary_query );

		return $content;
	}

	protected static function maybeOutputPagination( $paginationPosition, $container, $numberOfPages, $shortcodeAtts ) {
		$content = '';

		if ( self::isServerSide() && in_array( $paginationPosition, array(
				$container,
				'both'
			) ) ) {
			$content .= apply_filters( 'cmtt_glossary_index_pagination', '', $numberOfPages, $shortcodeAtts );
		}

		return $content;
	}

	protected static function maybeOutputIndexLetter( $glossaryItem, $glossaryIndexStyle, $shortcodeAtts, $letters ) {
		static $isFirstIndexLetter = true;
		$content = '';

		$styles_without_desc = array(
			'classic-table',
			'modern-table',
			'expand-style',
			'expand2-style',
			'grid-style',
			'cube-style'
		);

		if ( ! in_array( $glossaryIndexStyle, $styles_without_desc ) ) {
			return $content;
		}

		$newIndexLetter = self::detectStartNewIndexLetter( $glossaryItem );
		if ( $newIndexLetter !== false ) {

			$liAdditionalAttr  = '';
			$liAdditionalClass = '';

			if ( CMTT_AMP::is_amp_endpoint() || isset( $shortcodeAtts['__amp_source_origin'] ) ) {
				if ( ! is_numeric( $newIndexLetter ) ) {
					$first_letter_count = array_search( $newIndexLetter, $letters );
					$first_letter_count = $first_letter_count ? $first_letter_count : '-';
				} else {
					$first_letter_count = 'num';
				}
				$liAdditionalClass .= ' ln-' . $first_letter_count;
				$liAdditionalAttr  .= ' [hidden]="cmindsState.visibleLetter!=\'ln-all\' && cmindsState.visibleLetter!=\'' . $liAdditionalClass . '\'"';
			}

			$indexLetterTag = CM\CMTT_Settings::get( 'cmtt_index_letter_tag', 'h3' );

			if ( ! $isFirstIndexLetter ) {
				$content .= '<li class="the-letter-separator' . $liAdditionalClass . '"' . $liAdditionalAttr . '></li>';
			}
			$content            .= '<li class="the-index-letter' . $liAdditionalClass . '"' . $liAdditionalAttr . '><' . $indexLetterTag . ' role="tab">' . $newIndexLetter . '</' . $indexLetterTag . '></li>';
			$isFirstIndexLetter = false;
		}

		return $content;
	}

	/**
	 * Detects the new letter in Glossary Index Page
	 *
	 * @staticvar boolean $lastIndexLetter
	 *
	 * @param type $glossaryItem
	 * @param type $title
	 *
	 * @return boolean
	 */
	public static function detectStartNewIndexLetter( $glossaryItem = null, $title = null ) {
		static $lastIndexLetter = false;

		if ( ( $glossaryItem && is_object( $glossaryItem ) && isset( $glossaryItem->post_title ) ) || ( $title && is_string( $title ) ) ) {
			/*
			 * In case the former parameter only is sent
			 */
			if ( empty( $title ) && ! empty( $glossaryItem ) ) {
				$title = $glossaryItem->post_title;
			}

			$newIndexLetter = CMTT_Free::getFirstLetter( $glossaryItem, true, true );

			if ( ! (bool) \CM\CMTT_Settings::get( 'cmtt_index_nonLatinLetters' ) ) {
				$newIndexLetter = remove_accents( $newIndexLetter );
			}

			if ( mb_strtolower( $newIndexLetter ) !== $lastIndexLetter ) {
				$lastIndexLetter = mb_strtolower( $newIndexLetter );

				return $lastIndexLetter;
			}
		}

		return false;
	}

	/**
	 * @param $glossaryItem
	 * @param $glossaryIndexStyle
	 * @param $shortcodeAtts
	 * @param $letters
	 * @param $tooltipsDisabled
	 * @param $post
	 *
	 * @return $content
	 */
	protected static function maybeOutputSingleTerm(
		$glossaryItem, $glossaryIndexStyle, $shortcodeAtts,
		$letters, $tooltipsDisabled, $post
	) {
		$itemContentWrapper = '';
		$itemContent        = '';

		/*
		 *  Checks whether to show links to glossary pages or not
		 */
		$removeLinksToTerms = apply_filters( 'cmtt_glossary_index_remove_links_to_terms', false, $post );

		if ( $removeLinksToTerms || CMTT_AMP::is_amp_endpoint() ) {
			$href         = '';
			$tag          = 'span';
			$windowTarget = '';
			$rel          = '';
		} else {
			$permalink    = CMTT_Free::get_term_link( $glossaryItem->ID );
			$tag          = 'a';
			$href         = 'href="' . apply_filters( 'cmtt_index_term_tooltip_permalink', $permalink, $glossaryItem, $shortcodeAtts ) . '"';
			$windowTarget = ( \CM\CMTT_Settings::get( 'cmtt_glossaryInNewPage' ) == 1 ) ? ' target="_blank" ' : '';
			/*
			 * @since 4.0.13 Add rel="nofollow" to glossary links.
			 */
			$rel = ( \CM\CMTT_Settings::get( 'cmtt_addNofollowToTermLink' ) == 1 ) ? ' rel="nofollow" ' : '';
		}

		$liAdditionalClass = '';
		$thumbnail         = '';
		$titleAttrPrefix   = __( \CM\CMTT_Settings::get( 'cmtt_titleAttributeLabelPrefix', 'Glossary:' ), 'cm-tooltip-glossary' );
		$titleAttr         = ( \CM\CMTT_Settings::get( 'cmtt_showTitleAttribute' ) == 1 ) ? ' title="' . $titleAttrPrefix . ' ' . esc_attr( $glossaryItem->post_title ) . '" ' : '';

		if ( \CM\CMTT_Settings::get( 'cmtt_showFeaturedImageThumbnail', false ) ) {

			if ( in_array( $glossaryIndexStyle, array(
				'classic-excerpt',
				'classic-definition'
			) ) ) {
				$linkToOriginal = \CM\CMTT_Settings::get( 'cmtt_linkThumbnailToOriginal', false );
				$size           = apply_filters( 'cmtt_thumbnail_size', array( 50, 50 ) );
				$attr           = apply_filters(
					'cmtt_thumbnail_attr',
					array(
						'style' => 'margin:1px 5px',
						'rel'   => 'lightbox',
					)
				);
				$thumbnail      = get_the_post_thumbnail( $glossaryItem->ID, $size, $attr );
				if ( ! empty( $thumbnail ) ) {
					$liAdditionalClass = 'cmtt-has-thumbnail cmtt-classic';
					if ( ! empty( $linkToOriginal ) ) {
						$imageUrl           = get_the_post_thumbnail_url( $glossaryItem->ID, 'original' );
						$thumbnailLinkClass = apply_filters( 'cmtt_thumbnail_link_class', 'cmtt-thumbnail-link', $glossaryItem );
						$thumbnail          = sprintf( '<a href="%s" rel="lightbox" class="%s">%s</a>', $imageUrl, $thumbnailLinkClass, $thumbnail );
					}
				}
			}

			// Begin image tiles thumbnail PLUS
			if ( in_array( $glossaryIndexStyle, array( 'image-tiles-view' ) ) ) {
				if ( class_exists( 'CMTT_Glossary_Plus' ) ) {
					$result            = CMTT_Glossary_Plus::_image_tiles_view( $glossaryItem->ID );
					$thumbnail         = ( isset( $result['liAdditionalClass'] ) ) ? $result['thumbnail'] : '';
					$liAdditionalClass = ( isset( $result['liAdditionalClass'] ) ) ? $result['liAdditionalClass'] : '';
				}
			}
			// End image tiles thumbnail PLUS
		}

		if ( in_array( $glossaryIndexStyle, array( 'img-term-definition' ) ) ) {
			if ( class_exists( 'CMTT_Glossary_Plus' ) ) {
				$result            = CMTT_Glossary_Plus::_image_term_definition_view( $glossaryItem->ID );
				$thumbnail         = ( isset( $result['liAdditionalClass'] ) ) ? $result['thumbnail'] : '';
				$liAdditionalClass = ( isset( $result['liAdditionalClass'] ) ) ? $result['liAdditionalClass'] : '';
			}
		}

		$liAdditionalClass = apply_filters( 'cmtt_liAdditionalClass', $liAdditionalClass, $glossaryItem, $letters );
		$liAdditionalAttr  = '';

		$rand_id = rand( 0, 100 ); // Using rand number to separate the same terms in AMP version
		if ( CMTT_AMP::is_amp_endpoint() || isset( $shortcodeAtts['__amp_source_origin'] ) ) {
			$first_letter_class = 'ln-1';
			$liAdditionalAttr   .= ' [hidden]="cmindsState.visibleLetter!=\'ln-all\' && cmindsState.visibleLetter!=\'' . $first_letter_class . '\'"';
			$titleAttr          .= ' on="tap:AMP.setState({ cmindsState: {visibleTooltip: \'tooltip-' . $glossaryItem->ID . $rand_id . '\'} })"';
		}

		$structuredData   = \CM\CMTT_Settings::get( 'cmtt_add_structured_data_term_page', 1 ) ? ' itemscope itemtype="https://schema.org/DefinedTerm" ' : '';
		$liAdditionalAttr .= $structuredData;

		$itemContentWrapper .= apply_filters('cmtt_liContentWrapper','<li class="' . $liAdditionalClass . '" ' . $liAdditionalAttr . '>');
		$itemContent        .= $thumbnail;
		$itemContent        = apply_filters( 'cmtt_preItemTitleContent', $itemContent, $glossaryItem, $glossaryIndexStyle );

		/*
		 * Start the internal tag: span or a
		 */
		$additionalClass = apply_filters( 'cmtt_term_tooltip_additional_class', '', $glossaryItem );
		$excludeTT       = CMTT_Free::_get_meta( '_cmtt_exclude_tooltip', $glossaryItem->ID );

		/*
		 * Style links based on option
		 */
		$glossary_list_class = apply_filters( 'cmtt_glossary_index_list_class', ( \CM\CMTT_Settings::get( 'cmtt_glossaryDiffLinkClass' ) == 1 ) ? 'glossaryLinkMain' : 'glossaryLink glossary-link-title' );

		/*
		 * If sort by post_name (3.8.15)
		 */
		$dataPostName = '';
		$lang         = \CM\CMTT_Settings::get( 'cmtt_index_locale' );
		if ( empty( $sortByTitle ) && in_array( substr( $lang, 0, 2 ), array( 'ja', 'ar', 'ru', 'zh' ) ) ) {
			$dataPostName = ' data-postname="' . $glossaryItem->post_name . '" ';
		}
		$styleAttr = ' style="' . apply_filters( 'cmtt_term_style_attribute', '', $glossaryItem ) . '" ';

		$customAttrBase = '';
		$structuredData = \CM\CMTT_Settings::get( 'cmtt_add_structured_data_term_page', 1 ) ? ' itemprop="url" ' : '';
		$customAttrBase .= $structuredData;

		$customAttr  = apply_filters( 'cmtt_term_custom_attribute', $customAttrBase, $glossaryItem );
		$itemContent .= '<' . $tag . ' ' . $href . ' ' . $windowTarget . $rel
		                . ' role="term" class="' . $glossary_list_class . ' ' . $additionalClass . '" ' . $titleAttr . ' '
		                . $dataPostName . $styleAttr . ' ' . $customAttr;

		$disableTooltipForTerm = \CM\CMTT_Settings::get( 'cmtt_glossaryListTermDisableTooltip', 0 );
		/*
		 * Add tooltip if needed (general setting enabled and page not excluded from plugin)
		 */
		do_action( 'qm/start', 'cmtt_index_single_term_tooltip' );
		if ( ! $tooltipsDisabled && ! $excludeTT && ! $disableTooltipForTerm ) {
			$tooltipContent = CMTT_Free::getTooltipContent( $glossaryItem );
			$itemContent    .= ' aria-describedby="tt" data-cmtooltip="' . $tooltipContent . '"';
		}
		do_action( 'qm/stop', 'cmtt_index_single_term_tooltip' );

		$itemContent .= '>';

		/*
		 * Add filter to change the content of what's before the glossary item title on the list
		 */
		$itemContent = apply_filters( 'cmtt_glossaryPreItemTitleContent_add', $itemContent, $glossaryItem );

		/*
		 * Insert post term title
		 */
		$replacedTitle = apply_filters( 'cmtt_glossaryItemTitle', $glossaryItem->post_title, $glossaryItem, 1 );
		$itemContent   .= $replacedTitle;

		$itemContent .= '</' . $tag . '>';
		$itemContent = apply_filters( 'cmtt_pre_item_description_content', $itemContent, $glossaryItem, $glossaryIndexStyle, $rand_id );

		/*
		* Check if we need to add description/excerpt on tooltip index
		*/
		$glossaryItemDesc = apply_filters( 'cmtt_glossary_index_item_desc', '', $glossaryItem, $glossaryIndexStyle, $shortcodeAtts );

		$itemContent .= $glossaryItemDesc;
		$itemContent = apply_filters( 'cmtt_postItemTitleContent', $itemContent, $glossaryItem, $glossaryIndexStyle, $tag, $windowTarget, $shortcodeAtts );

		$content = $itemContentWrapper . $itemContent . '</li>';

		return $content;
	}

	/**
	 * Create the actual glossary
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	public static function lookForShortcode( $content ) {
		$currentPost    = get_post();
		$glossaryPageID = self::getGlossaryIndexPageId();

		$seo = doing_action( 'wpseo_head' );
		if ( $seo ) {
			return $content;
		}

		$shortcode_name = \CM\CMTT_Settings::get( 'cmtt_glossaryShortcode', 'glossary' );
		if ( is_numeric( $glossaryPageID ) && is_page( $glossaryPageID ) && $glossaryPageID > 0 && $currentPost && $currentPost->ID == $glossaryPageID ) {
			if ( ! has_shortcode( $currentPost->post_content, $shortcode_name ) ) {
				$content = $currentPost->post_content . '[' . $shortcode_name . ']';
				wp_update_post(
					array(
						'ID'           => $glossaryPageID,
						'post_content' => $content,
					)
				);
			}
		}

		return $content;
	}

	/**
	 * Function tries to generate the new Glossary Index Page
	 */
	public static function tryGenerateGlossaryIndexPage() {
		$glossaryIndexId = self::getGlossaryIndexPageId();
		$shortcode_name  = \CM\CMTT_Settings::get( 'cmtt_glossaryShortcode', 'glossary' );
		if ( - 1 == $glossaryIndexId ) {
			$id = wp_insert_post(
				array(
					'post_author'  => get_current_user_id(),
					'post_status'  => 'publish',
					'post_title'   => 'Glossary',
					'post_type'    => 'page',
					'post_content' => '[' . $shortcode_name . ']',
				)
			);

			if ( is_numeric( $id ) ) {
				update_option( 'cmtt_glossaryID', $id );
			}
		}
	}

	/**
	 * Get the base of the Tooltip Content on Glossary Index Page
	 *
	 * @param string $content
	 * @param object $glossary_item
	 *
	 * @return string
	 */
	public static function getTheTooltipContentBase( $content, $glossary_item ) {
		if ( \CM\CMTT_Settings::get( 'cmtt_glossaryExcerptHover' ) && $glossary_item->post_excerpt ) {
			$content = $glossary_item->post_excerpt;
		} else {
			$content = $glossary_item->post_content;
			if ( class_exists( 'Themify_Builder' ) ) {
				$themify_json = CMTT_Free::_get_meta( '_themify_builder_settings_json', $glossary_item->ID );
				if ( ! empty( $themify_json ) ) {
					global $ThemifyBuilder;
					$builder_data = $ThemifyBuilder->get_builder_output( $glossary_item->ID, $glossary_item->post_content );
					$content      .= $builder_data;
				}
			}
		}

		if ( has_shortcode( $content, 'cmtgend' ) ) {
			$content = preg_match( '/\[cmtgend\](.*?)\[\/cmtgend\]/s', $content, $match );
			$content = $match[1];
		}

		return $content;
	}

	/**
	 * Check whether to remove links to term pages from Glossary Index or not
	 *
	 * @param boolean $disable
	 * @param object $post
	 *
	 * @return boolean
	 */
	public static function removeLinksToTerms( $disable, $post ) {
		$removeLinksToTerms = \CM\CMTT_Settings::get( 'cmtt_glossaryListTermLink' ) == 1;
		$linksDisabled      = false;
		if ( ! empty( $post ) ) {
			$linksDisabled = ( 1 == CMTT_Free::_get_meta( '_glossary_disable_links_for_page', $post->ID ) );
		}
		$disable = $linksDisabled || $removeLinksToTerms;

		return $disable;
	}

	/**
	 * Check whether to disable the tooltips on Glossary Index page
	 *
	 * @param bool $disable
	 *
	 * @return bool
	 */
	public static function disableTooltipsOnIndex( $disable ) {
		/*
		 * When this option is enabled we don't want titles to display tooltips
		 */
		$disableNewValue = (bool) \CM\CMTT_Settings::get( 'cmtt_glossaryOnlyTitleLinksToTerm', 0 );

		return $disable || $disableNewValue;
	}

	/**
	 * Wrap Glossary Index in styling container
	 *
	 * @param $content
	 * @param $glossary_query
	 * @param $shortcodeAtts
	 *
	 * @return mixed|string
	 */
	public static function wrapInStyleContainer( $content, $glossary_query, $shortcodeAtts ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			$glossaryIndexStyle = $shortcodeAtts['glossary_index_style'];
			if ( $glossaryIndexStyle != 'classic' ) {
				$styles = apply_filters(
					'cmtt_glossary_index_style_classes',
					array(
						'small-tiles' => 'tiles',
					)
				);
				if ( isset( $styles[ $glossaryIndexStyle ] ) ) {
					$class   = $styles[ $glossaryIndexStyle ];
					$content = '<div class="cm-glossary ' . $class . '">' . $content . '<div class="clear clearfix cmtt-clearfix"></div></div>';
				}
			} else {
				$content = '<div class="cm-glossary">' . $content . '</div>';
			}
		}

		return $content;
	}

	/**
	 * Wrap Glossary Index in main container
	 *
	 * @param type $content
	 * @param type $glossaryIndexStyle
	 *
	 * @return type
	 */
	public static function addShowCountsClass( $additionalClass ) {
		$showCounts = \CM\CMTT_Settings::get( 'cmtt_index_showCounts', '1' );
		if ( ! $showCounts ) {
			$additionalClass .= 'no-counts';
		}

		return $additionalClass;
	}

	/**
	 * Wrap Glossary Index in main container
	 *
	 * @param type $content
	 * @param type $glossaryIndexStyle
	 *
	 * @return type
	 */
	public static function wrapInMainContainer( $content, $glossary_query, $shortcodeAtts ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			$additionalClass = apply_filters( 'cmtt_glossary_container_additional_class', '' );
			$content         = '<div class="glossary-container ' . $additionalClass . '">' . $content . '</div>';
		}

		return $content;
	}

	/**
	 * Check whether to disable the tooltips on Glossary Index page
	 *
	 * @param type $disable
	 * @param type $post
	 *
	 * @return type
	 */
	public static function addReferalSnippet( $content, $glossary_query, $shortcodeAtts ) {
		if ( \CM\CMTT_Settings::get( 'cmtt_glossaryReferral' ) == 1 && \CM\CMTT_Settings::get( 'cmtt_glossaryAffiliateCode' ) ) {
			$content .= CMTT_Free::cmtt_getReferralSnippet();
		}

		return $content;
	}

	/**
	 * Removes the ListNav when there's server side pagination
	 *
	 * @param type $content
	 *
	 * @return string
	 */
	public static function removeListnav( $content ) {
		if ( self::isServerSide() ) {
			$content = '';
		}

		return $content;
	}

	/**
	 * Removes the ListNav when there's server side pagination
	 *
	 * @param type $content
	 *
	 * @return string
	 */
	public static function modifyListnav( $content, $shortcodeAtts, $glossaryQuery ) {
		if ( 'sidebar-termpage' === $shortcodeAtts['glossary_index_style'] ) {
			$content = '';
		}

		return $content;
	}

	/**
	 * Removes the ListNav when there's server side pagination
	 *
	 * @param type $content
	 *
	 * @return string
	 */
	public static function modifyBeforeListnav( $content, $shortcodeAtts, $glossaryQuery ) {
		/*
		 * Pass the shortcodeAtts to the subsequent queries (search click, letter click, category click etc.)
		 */
		$attributesArr = array( 'glossary_index_style', 'related', 'author_id' );
		foreach ( $attributesArr as $value ) {
			if ( isset( $shortcodeAtts[ $value ] ) ) {
				$content .= '<input type="hidden" class="cmtt-attribute-field" name="' . esc_attr( $value ) . '" value="' . esc_attr( $shortcodeAtts[ $value ] ) . '">';
			}
		}

		return $content;
	}

	/**
	 * Removes the ListNav when there's server side pagination
	 *
	 * @param type $content
	 *
	 * @return string
	 */
	public static function modifyTermPermalink( $permalink, $glossaryItem, $shortcodeAtts ) {
		$indexUrl = isset( $shortcodeAtts['glossary_page_link'] ) ? $shortcodeAtts['glossary_page_link'] : '';
		if ( 'sidebar-termpage' === $shortcodeAtts['glossary_index_style'] ) {
			$name      = get_post_field( 'post_name', $glossaryItem->ID );
			$permalink = add_query_arg( array( 'term' => $name ), $indexUrl );
		}

		return $permalink;
	}

	public static function maybeWrapBeforeDefinition( $indexItemContent, $glossaryItem, $id, $glossaryIndexStyle ) {
		if ( in_array( $glossaryIndexStyle, array(
			'term-definition',
		) ) ) {
			$indexItemContent = '<div class="term-block">' . $indexItemContent . '</div>';
		}

		return $indexItemContent;
	}

	public static function maybeWrapAfterDefinition( $indexItemContent, $glossaryItem, $glossaryIndexStyle ) {
		if ( in_array( $glossaryIndexStyle, array(
			'flipboxes-with-definition'
		) ) ) {
			$indexItemContent = '<div class="term-block">' . $indexItemContent . '</div>';
		}

		if ( in_array( $glossaryIndexStyle, array(
			'img-term-definition',
		) ) ) {
			$indexItemContent = $indexItemContent . '</div>';
		}

		return $indexItemContent;
	}

	public static function maybeWrapAfterThumbnail( $indexItemContent, $glossaryItem, $glossaryIndexStyle ) {
		if ( in_array( $glossaryIndexStyle, array(
			'img-term-definition',
		) ) ) {
			$indexItemContent = $indexItemContent . '<div class="term-block">';
		}

		return $indexItemContent;
	}

	/**
	 * Outputs the pagination
	 *
	 * @param string $content
	 * @param mixed $glossary_query
	 * @param int $currentPage
	 *
	 * @return string
	 */
	public static function outputPagination( $content, $lastPage, $shortcodeAtts ) {
		$currentPage      = $shortcodeAtts['itemspage'] ?? 1;
		$glossaryPageLink = $shortcodeAtts['glossary_page_link'];

		$pageSelected = ( 1 == $currentPage ) ? ' selected' : '';

		$showPages = 11;
		if ( $lastPage < 2 ) {
			return $content;
		}

		$prevPage = ( $currentPage - 1 < 1 ) ? 1 : $currentPage - 1;
		$nextPage = ( $currentPage + 1 > $lastPage ) ? $lastPage : $currentPage + 1;

		$prevHalf = ( $currentPage - ceil( $showPages / 2 ) ) <= 0 ? 0 : ( $currentPage - ceil( $showPages / 2 ) );
		$prevDiff = ( ceil( $showPages / 2 ) - $currentPage >= 0 ) ? ceil( $showPages / 2 ) - $currentPage : 0;
		$nextHalf = ( $currentPage + ceil( $showPages / 2 ) ) > $lastPage ? $lastPage : ( $currentPage + ceil( $showPages / 2 ) );

		$prevSectionPage = ( $currentPage - ceil( $showPages / 2 ) ) < 1 ? 1 : $currentPage - ceil( $showPages / 2 );
		$nextSectionPage = ( $currentPage + ceil( $showPages / 2 ) ) > $lastPage ? $lastPage : $currentPage + ceil( $showPages / 2 );

		$pagesStart = ( $prevHalf > 0 ) ? $prevHalf : 1;
		$pagesEnd   = min( $nextHalf + $prevDiff, $nextSectionPage );

		$showFirst = $prevHalf > 1;
		$showLast  = $nextHalf < $lastPage;

		$roundPagination = (bool) \CM\CMTT_Settings::get( 'cmtt_glossaryPaginationRound', 0 );
		if ( isset( $_POST['gtags'] ) ) {
			$currentTag = $_POST['gtags'];
		}
		ob_start();
		?>
		<ul class="pageNumbers <?php echo esc_attr( ( $roundPagination ? 'round' : '' ) ); ?>">

			<?php
			if ( 1 != $currentPage ) :
				$args = array( 'itemspage' => $prevPage );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>"
				   title="<? _e( 'Previous page' ); ?>">
					<li class="prev" data-page-number="<?php echo $prevPage; ?>">
						&lt;&lt;
					</li>
				</a>
			<?php endif; ?>

			<?php
			if ( $showFirst ) :
				$args = array( 'itemspage' => 1 );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>">
					<li class="prev" data-page-number="<?php echo $prevPage; ?>">
						&lt;&lt;
					</li>
				</a>
			<?php endif; ?>

			<?php
			if ( $prevSectionPage > 1 ) :
				$args = array( 'itemspage' => $prevSectionPage );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>">
					<li class="prev-section" data-page-number="<?php echo $prevSectionPage; ?>">
						(...)
					</li>
				</a>
			<?php endif; ?>

			<?php
			for ( $i = $pagesStart; $i <= $pagesEnd; $i ++ ) :
				$args = array( 'itemspage' => $i );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<?php $pageSelected = ( $i == $currentPage ) ? ' selected' : ''; ?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>">
					<li class="numeric<?php echo $pageSelected; ?>" data-page-number="<?php echo $i; ?>">
						<?php echo $i; ?>
					</li>
				</a>
			<?php endfor; ?>

			<?php
			if ( $nextHalf !== $lastPage ) :
				$args = array( 'itemspage' => $nextSectionPage );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>">
					<li class="next-section" data-page-number="<?php echo $nextSectionPage; ?>">
						(...)
					</li>
				</a>
			<?php endif; ?>

			<?php
			$pageSelected = ( $lastPage == $currentPage ) ? ' selected' : '';
			if ( $showLast ) :
				$args = array( 'itemspage' => $lastPage );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>">
					<li class="numeric <?php echo $pageSelected; ?>" data-page-number="<?php echo $lastPage; ?>">
						<?php echo $lastPage; ?>
					</li>
				</a>
			<?php endif; ?>

			<?php
			if ( $lastPage != $currentPage ) :
				$args = array( 'itemspage' => ( $nextPage ) );
				if ( ! empty( $currentTag ) ) {
					$args = array_merge( $args, array( 'gtags' => $currentTag ) );
				}
				if ( CMTT_AMP::is_amp_endpoint() ) {
					$args['amp'] = 1;
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( $args, $glossaryPageLink ) ); ?>">
					<li class="next" data-page-number="<?php echo $nextPage; ?>" title="<? _e( 'Next page' ); ?>">
						&gt;&gt;
					</li>
				</a>
			<?php endif; ?>

		</ul>
		<?php
		$content .= ob_get_contents();
		ob_end_clean();

		return (string) $content;
	}

	/**
	 * Check if tooltips are disabled for given page
	 *
	 * @param type $tooltipData
	 *
	 * @return type
	 * @global type $post
	 */
	public static function tooltipsDisabledForPage( $tooltipData ) {
		global $post;
		$postId = empty( $post->ID ) ? '' : $post->ID;

		if ( ! empty( $postId ) && ! is_front_page() ) {
			/*
			 *  Checks whether to show tooltips on this page or not
			 */
			if ( self::disableTooltips( false, $post ) ) {
				unset( $tooltipData['cmtooltip'] );
			}
		}

		return $tooltipData;
	}

	/**
	 * Check whether to disable the tooltips on Glossary Index page
	 *
	 * @param bool $disable
	 * @param mixed $post
	 *
	 * @return bool
	 */
	public static function disableTooltips( $disable, $post ) {
		if ( ! empty( $post ) ) {
			$tooltipsDisabledGlobal = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltip' ) != 1;

			$disableTooltip = (int) CMTT_Free::_get_meta( '_glossary_disable_tooltip_for_page', $post->ID );
			switch ( $disableTooltip ) {
				case 0:
					$tooltipsDisabled = $tooltipsDisabledGlobal;
					break;
				case 1:
					$tooltipsDisabled = 1;
					break;
				case 2:
					$tooltipsDisabled = 0;
					break;
				default:
					$tooltipsDisabled = $tooltipsDisabledGlobal;
			}
			$disable = $tooltipsDisabled;
		}

		// Check if we need to show the tooltip for current visitor
		$tooltips_for_all = \CM\CMTT_Settings::get( 'cmtt_glossaryTooltipForAll', 1 );
		$allowed_roles    = \CM\CMTT_Settings::get( 'cmtt_glossaryRolesShowTooltip', array() );
		if ( ! $tooltips_for_all ) {
			if ( ! empty( $allowed_roles ) ) {
				if ( is_user_logged_in() && is_array( $allowed_roles ) ) {
					$user = wp_get_current_user();

					// Don't show the tooltip if current visitor role isn't allowed
					if ( empty( array_intersect( $allowed_roles, $user->roles ) ) ) {
						$disable = 1;
					}
				}

				// Don't show the tooltip for not logged in users if it not allowed
				if ( ! is_user_logged_in() && ! in_array( 'Guest', (array) $allowed_roles ) ) {
					$disable = 1;
				}
			}
		}

		return $disable;
	}

	public static function _preContent( $content ) {
		if ( ! defined( 'DOING_AJAX' ) ) {
			if ( ! empty( self::$preContent ) && is_string( self::$preContent ) ) {
				$content = self::$preContent . $content;
			}
		}

		return $content;
	}

	public static function outputTooltipWrapper() {
		$addflipclass = 'cmtt';
		if ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'center_flip' && \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) != 'center_flip' ) {
			$addflipclass .= ' has-in no-out';
		}
		if ( \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'center_flip' && \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) != 'center_flip' ) {
			$addflipclass .= ' no-in';
		}
		if ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'center_flip' && \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'center_flip' ) {
			$addflipclass .= ' has-in';
		}
		if ( \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'horizontal_flip' || \CM\CMTT_Settings::get( 'cmtt_tooltiphideanimation', 'no_animation' ) == 'horizontal_flip' || \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation' ) == 'grow' || \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation' ) == 'shrink' || \CM\CMTT_Settings::get( 'cmtt_tooltipDisplayanimation', 'no_animation' ) == 'fade_in' || \CM\CMTT_Settings::get( 'cmtt_tooltipHideanimation', 'no_animation' ) == 'fade_out' ) {
			$addflipclass .= ' animated';
		}
		/*
		 * Required for accessibility (for screen readers to see)
		 */
		echo '<div id="tt" role="tooltip" class="' . apply_filters( 'cmtt_tt_class', $addflipclass ) . '"></div>';
	}

	/**
	 * Sort array with specialchars alphabetically and maintain index
	 * association.
	 *
	 * Example:
	 *
	 * $array = array('Barcelona', 'Madrid', 'Albacete', 'Álava', 'Bilbao');
	 *
	 * asort($array);
	 * var_dump($array);
	 *     => array('Albacete', 'Barcelona', 'Bilbao', 'Madrid', 'Álava')
	 *
	 * $array = util::array_mb_sort($array);
	 * var_dump($array);
	 *     => array('Álava', 'Albacete', 'Barcelona', 'Bilbao', 'Madrid')
	 *
	 * @param array $array Array of elements to sort.
	 *
	 * @return  array           Sorted array
	 *
	 * @access  public
	 *
	 * @static
	 */
	public static function array_mb_sort_alphabetically( array $array, $reverse = false ) {
		if ( $reverse ) {
			usort( $array, array( __CLASS__, 'mb_string_compare' ) );
		} else {
			uasort( $array, array( __CLASS__, 'mb_string_compare' ) );
		}

		return $array;
	}


	/**
	 * Comparaison de chaines unicode. This method can come in handy when we
	 * want to use as a callback function on uasort & usort PHP functions to
	 * sort arrays when you have special characters for example accents.
	 *
	 * @param string $s1 First string to compare with
	 *
	 * @param string $s2 Second string to compare with
	 *
	 * @return  boolean
	 *
	 * @access  public
	 * @since   1.0.000
	 * @static
	 */
	public static function compareGlossaryTerms( $obj1, $obj2 ) {
		static $collator = null;
		$sortByTitle = \CM\CMTT_Settings::get( 'cmtt_index_sortby_title', 'title' );

		if ( $sortByTitle ) {
			/*
			 * This version doesn't support the two items with different meanings
			 */
			$s1 = mb_strtolower( preg_replace( '/\W/u', '', $obj1->post_title ) );
			$s2 = mb_strtolower( preg_replace( '/\W/u', '', $obj2->post_title ) );
		} else {
			$s1 = urldecode( $obj1->post_name );
			$s2 = urldecode( $obj2->post_name );
		}

		if ( true === extension_loaded( 'intl' ) ) {

			if ( null === $collator ) {
				$customLocale = \CM\CMTT_Settings::get( 'cmtt_index_locale', '' );
				$locale       = ! empty( $customLocale ) ? $customLocale : get_locale();
				$collator     = collator_create( $locale );
				/*
				 * Add support for natural sorting order
				 */
				$collator->setAttribute( Collator::NUMERIC_COLLATION, Collator::ON );
			}

			if ( true === is_object( $collator ) ) {
				return $collator->compare( $s1, $s2 );
			}
		}

		return self::mb_string_compare( $s1, $s2 );
	}

	/**
	 * Comparaison de chaines unicode. This method can come in handy when we
	 * want to use as a callback function on uasort & usort PHP functions to
	 * sort arrays when you have special characters for example accents.
	 *
	 * @param string $s1 First string to compare with
	 *
	 * @param string $s2 Second string to compare with
	 *
	 * @return  boolean
	 *
	 * @access  public
	 * @since   1.0.000
	 * @static
	 */
	public static function mb_string_compare( $s1, $s2 ) {
		return strcmp(
			iconv( 'UTF-8', 'ISO-8859-1//TRANSLIT', self::decode_characters( $s1 ) ),
			iconv( 'UTF-8', 'ISO-8859-1//TRANSLIT', self::decode_characters( $s2 ) )
		);
	}

	/**
	 * Decode a string
	 *
	 * @param string $string Encoded string
	 *
	 * @return  string
	 *
	 * @access  public
	 *
	 * @static
	 */
	public static function decode_characters( $string ) {
		$string = htmlspecialchars_decode( htmlentities( $string, ENT_COMPAT, 'utf-8', false ) );
		$string = preg_replace( '~^(&([a-zA-Z0-9]);)~', htmlentities( '${1}' ), $string );

		return ( $string );
	}

}
