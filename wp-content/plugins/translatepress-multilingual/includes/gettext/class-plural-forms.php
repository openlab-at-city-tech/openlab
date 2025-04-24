<?php


if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Plural_Form
 *
 * Helpful gettext plural form functions
 */
class TRP_Plural_Forms {

    protected $_gettext_select_plural_form;
    protected $settings;
    protected $_nplurals;
    protected $gettext_plural_forms_headers;
    protected $cached_language = null;

    /**
     * TRP_Plural_Form constructor.
     *
     * @param array $settings Settings option.
     */
    public function __construct( $settings ) {
        $this->settings = $settings;
        $this->gettext_plural_forms_headers = $this->get_plural_forms_headers();
    }

	/**
	 * Returns plural form needed according to the actual number of items
	 *
	 * Dependent on language.
	 *
	 * @param $number
	 * @param $language
	 * @return int
	 */
	public function get_plural_form( $count, $language ){
		if ( $count === null ){
			return 0;
		}
		$header = $this->gettext_plural_forms_headers[$language];
		return $this->gettext_select_plural_form( $count, $header, $language );
	}

	public function get_number_of_plural_forms( $language ){
		list( $nplurals, $expression )     = $this->nplurals_and_expression_from_header( $this->gettext_plural_forms_headers[$language] );
		return $nplurals;
	}

    public function get_plural_forms_headers(){
        if ( !isset( $this->gettext_plural_forms_headers ) ){
            $this->gettext_plural_forms_headers = $this->set_plural_forms_headers( $this->settings['translation-languages'] );
        }
        return $this->gettext_plural_forms_headers;
    }

    /**
     * Gets plural form headers from trp_db_stored_data option.
     *
     * Auto-completes missing headers from default textdomain mo files
     *
     * @param $languages
     * @return array
     */
    public function set_plural_forms_headers( $languages ){
        global $l10n;

        $trp_db_stored_data = get_option( 'trp_db_stored_data', array() );
        if ( !isset($trp_db_stored_data['gettext_plural_forms_header']) ){
            $trp_db_stored_data['gettext_plural_forms_header'] = array();
        }

        $changes = false;
        $current_locale = get_locale();
        foreach( $languages as $language_code ){
            if ( !isset( $trp_db_stored_data['gettext_plural_forms_header'][$language_code] ) ){
                load_default_textdomain($language_code);

                if ( isset($l10n['default']->headers['Plural-Forms'] ) ) {
                    $header = $l10n['default']->headers['Plural-Forms'];
                }else{
                    $header = 'nplurals=2; plural=n != 1;';
                }
                $trp_db_stored_data['gettext_plural_forms_header'][$language_code] = $header;
                $changes = true;
            }
        }
        if ( $changes ) {
            update_option( 'trp_db_stored_data', $trp_db_stored_data );

            // restore previous textdomain
            load_default_textdomain($current_locale);
        }

        return $trp_db_stored_data['gettext_plural_forms_header'];
    }


    private function gettext_select_plural_form( $count, $header, $language ) {
        if ( ! isset( $this->_gettext_select_plural_form ) || $this->cached_language != $language ) {
            list( $nplurals, $expression )     = $this->nplurals_and_expression_from_header( $header );
            $this->_nplurals                   = $nplurals;
            $this->_gettext_select_plural_form = $this->make_plural_form_function( $nplurals, $expression );
            $this->cached_language = $language;
        }
        return call_user_func( $this->_gettext_select_plural_form, $count );
    }

    private function nplurals_and_expression_from_header( $header ) {
        if ( preg_match( '/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches ) ) {
            $nplurals   = (int) $matches[1];
            $expression = trim( $matches[2] );
            return array( $nplurals, $expression );
        } else {
            return array( 2, 'n != 1' );
        }
    }

    /**
     * Makes a function, which will return the right translation index, according to the
     * plural forms header
     *
     * @param int    $nplurals
     * @param string $expression
     */
    private function make_plural_form_function( $nplurals, $expression ) {
        try {
            $handler = new Plural_Forms( rtrim( $expression, ';' ) );
            return array( $handler, 'get' );
        } catch ( Exception $e ) {
            // Fall back to default plural-form function.
            return $this->make_plural_form_function( 2, 'n != 1' );
        }
    }

	/**
	 * Copied from wp-includes/pomo/translated and adapted to allow input for plural form ($index) instead of $count
	 *
	 * @param $singular
	 * @param $plural
	 * @param int $index Changed from $count to index
	 * @param $context
	 *
	 * @return mixed
	 */
	public function translate_plural( $singular, $plural, $index, $context, $translations ) {
		$entry              = new Translation_Entry(
			array(
				'singular' => $singular,
				'plural'   => $plural,
				'context'  => $context,
			)
		);
		$translated         = $translations->translate_entry( $entry );
		$total_plural_forms = $translations->get_plural_forms_count();
		if ( $translated && 0 <= $index && $index < $total_plural_forms &&
		     is_array( $translated->translations ) &&
		     isset( $translated->translations[ $index ] ) ) {
			return $translated->translations[ $index ];
		} else {
			return 0 == $index ? $singular : $plural;
		}
	}
}
