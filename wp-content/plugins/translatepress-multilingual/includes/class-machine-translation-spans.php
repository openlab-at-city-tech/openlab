<?php

if ( !defined('ABSPATH' ) )
    exit();

/**
 * Class TRP_Machine_Translation_Spans
 *
 * Protects text from automatic translation by wrapping it in <span id="trp-{type}-N"> before the string is
 * sent to the engine and overwriting whatever the engine returned inside the span afterwards. The engine sees
 * the real words instead of a placeholder hole it tends to fill with invented words. Where a span can't go
 * (inside an attribute value), and for the escaped percent sign %%, a 1TPnT placeholder is sent instead and
 * put back afterwards (see wrap()). printf conversions (%s, %1$s) go verbatim, except to Google Translate.
 *
 * Handles the words from trp_exclude_words_from_automatic_translation (user exclude list, [trp_language]
 * content, third parties). Glossary terms join the same matching pass through trp_machine_translation_span_terms.
 */
class TRP_Machine_Translation_Spans {

    const EXCLUDE_PREFIX = 'trp-exclude-';

    // Any span id or token prefix this class or a trp_machine_translation_span_terms group can produce
    const ANY_PREFIX_REGEX = 'trp-[a-z0-9_]+-';

    const SPAN_TAG_REGEX = '/<span\b[^<>]*>|<\/span\s*>/i';

    // A tag or comment in the string to wrap. A quote right after = opens an attribute value, where > doesn't end
    // the tag; a value runs to the next quote unless a < comes first. Any other quote is an ordinary character.
    // Possessive: no backtracking
    const TAG_SCAN_REGEX = '/<!--.*?-->|<[a-zA-Z\/!?](?:[^>"\'=]++|=\s*+"[^"<]*+"|=\s*+\'[^\'<]*+\'|["\'=])*+>/s';

    // Placeholder used where a span can't go (inside an attribute value), for %% anywhere and for
    // trp_machine_translation_span_terms groups with 'form' => 'token'. Also read back as engines transliterate it:
    // Cyrillic 1ТП2Т / 1ТР2Т, Greek 1ΤΠ2Τ, full-width １ＴＰ２Ｔ
    const TOKEN_REGEX = '/[1１][TТΤＴ][PПРΠΡＰ]\s*([0-9０-９]+)\s*[TТΤＴ]/iu';

    // Latin form only, for sent strings and for engine output that isn't valid UTF-8
    const LATIN_TOKEN_REGEX = '/1TP\s*(\d+)\s*T/i';

    const PRINTF_PREFIX = 'trp-printf-';

    // The escaped percent sign %%, which engines turn into % or %%%, is always sent as a token. Conversions (%s, %d,
    // %1$s, $%s...) go verbatim: a hole in their place costs more than it saves. See printf_as_tokens()
    const PRINTF_REGEX = '/%%/';

    // %% and the conversions, for engines that rewrite them. No space flag, so 100% sure stays text
    const PRINTF_CONVERSION_REGEX = '/%%|%(?:\d+\$)?(?:[-+0]|\'.)*\d*(?:\.\d+)?[bcdeEfFgGosuxX]/';

    // Attributes whose value is text an engine translates. Excluded words and glossary terms in them are sent as
    // tokens; in any other attribute only %% is (restored verbatim). See text_attributes()
    const TEXT_ATTRIBUTES = 'title alt placeholder aria-label aria-description aria-placeholder aria-valuetext aria-roledescription label';

    // Attribute an engine may move the id to: id, class, data-id...
    const ID_ATTRIBUTE_REGEX = '(?<![\w-])(?:[\w-]+-)?(?:id|class)';

    // Longest alternation per pattern when words_pattern() has to split, PCRE compiled at least 24 KB in every test
    const PATTERN_MAX_BYTES = 16000;

    // Engines nest a span in another at most a level or two, deeper is malformed output that costs a scan per level
    const MAX_NESTING_DEPTH = 10;

    protected $settings;
    protected static $logged = array();

    // Protected texts sent as 1TPnT in this request, n => array( 'prefix', 'text', 'attribute' ). The same text gets
    // the same number in every string, so two strings that differ only inside a token can't be confused, and a
    // string changed by a later before_send callback is still restored. See token_number()
    protected static $registry = array();
    protected static $registry_index = array();

    public function __construct( $settings = array() ) {
        $this->settings = $settings;
    }

    /**
     * Wrap the matches of several groups in <span id="{prefix}N">, N counting from 1 per prefix in this string.
     * A match inside a quoted attribute value, where a tag can't go, and every match of a group with
     * 'form' => 'token' becomes a 1TPnT placeholder instead, numbered per request by token_number() so the same
     * text always gets the same number; $tokens receives n => array( 'prefix', 'text', 'attribute' ) for those.
     * In an attribute that isn't text (href, class, style...) only %% is protected, other matches there are
     * skipped, like a match that starts or ends elsewhere inside a tag. Overlapping matches
     * are resolved longest first, then by group priority (lower wins). A string that is not valid UTF-8 is
     * matched with its invalid bytes replaced (see utf8_subject()), a match covering one of them is skipped.
     * Text that already reads 1TPnT is left as it is and may be restored as the token with that number.
     *
     * @param string $string
     * @param array  $groups List of array( 'prefix' => span id prefix, 'pattern' => PCRE pattern or list of patterns, 'priority' => int, 'form' => 'span' (default) or 'token' ).
     * @param array  $tokens Out: the 1TPnT placeholders put in the result.
     * @return string
     */
    public static function wrap( $string, $groups, &$tokens = array() ) {
        $tokens = array();
        if ( !is_string( $string ) || $string === '' || empty( $groups ) ) {
            return $string;
        }

        // One byte per string position: "1" strictly inside a tag, so a match can't start or end there, "2" inside the
        // quoted value of a text attribute, where a match becomes a token, "3" inside any other quoted value, where only
        // %% does. A '<' not followed by a tag name, '/', '!' or '?' is text (x < 5)
        $in_tag = '';
        if ( preg_match_all( self::TAG_SCAN_REGEX, $string, $tag_matches, PREG_OFFSET_CAPTURE ) ) {
            $text_attributes = null;
            foreach ( $tag_matches[0] as $tag ) {
                $in_tag .= str_repeat( '0', $tag[1] + 1 - strlen( $in_tag ) ) . str_repeat( '1', strlen( $tag[0] ) - 1 );
                if ( $tag[0][1] !== '!' && preg_match_all( '/([^\s=\/<>"\']++)\s*=\s*(?:"([^"<]*+)"|\'([^\'<]*+)\')/', $tag[0], $values, PREG_OFFSET_CAPTURE ) ) {
                    if ( $text_attributes === null ) {
                        $text_attributes = self::text_attributes();
                    }
                    foreach ( $values[0] as $index => $unused ) {
                        $value = isset( $values[2][ $index ] ) && $values[2][ $index ][1] >= 0 ? $values[2][ $index ] : ( isset( $values[3][ $index ] ) ? $values[3][ $index ] : null );
                        if ( $value === null || $value[1] < 0 ) {
                            continue;
                        }
                        $name = strtolower( $values[1][ $index ][0] );
                        $mark = isset( $text_attributes[ $name ] ) || ( $name === 'value' && preg_match( '/^<input\b[^>]*\btype\s*=\s*["\']?(?:submit|button|reset)\b/i', $tag[0] ) ) ? '2' : '3';
                        for ( $i = $tag[1] + $value[1], $stop = $i + strlen( $value[0] ); $i <= $stop; $i++ ) {
                            $in_tag[ $i ] = $mark;
                        }
                    }
                }
            }
        }
        $in_tag .= str_repeat( '0', strlen( $string ) + 1 - strlen( $in_tag ) );

        $subject = self::utf8_subject( $string );
        if ( $subject !== $string ) {
            self::log_once( 'invalid-string', 'string to translate is not valid UTF-8, matched the excluded words and glossary terms ignoring the invalid bytes (logged once per request): ' . substr( $subject, 0, 200 ) );
        }

        $candidates = array();
        foreach ( $groups as $group ) {
            if ( empty( $group['pattern'] ) || empty( $group['prefix'] ) ) {
                continue;
            }
            $form = isset( $group['form'] ) && $group['form'] === 'token' ? 'token' : 'span';
            foreach ( (array) $group['pattern'] as $pattern ) {
                $count = preg_match_all( $pattern, $subject, $matches, PREG_OFFSET_CAPTURE );
                if ( $count === false ) {
                    self::log_once( 'pattern-error-' . preg_last_error(), 'could not match the ' . $group['prefix'] . 'N text to protect from automatic translation, PCRE error ' . preg_last_error() . ' (logged once per request). Pattern: ' . substr( $pattern, 0, 200 ) );
                }
                if ( !$count ) {
                    continue;
                }
                foreach ( $matches[0] as $match ) {
                    $start = $match[1];
                    $end   = $start + strlen( $match[0] );
                    if ( $match[0] === '' || ( $subject !== $string && substr( $string, $start, $end - $start ) !== $match[0] ) ) {
                        continue;
                    }
                    $candidate_form = $form;
                    $in_attribute   = false;
                    if ( $in_tag[ $start ] !== '0' || $in_tag[ $end ] !== '0' ) {
                        // inside a tag only a quoted attribute value can hold it, as a token
                        $mark = $in_tag[ $start ];
                        if ( ( $mark !== '2' && $mark !== '3' ) || strspn( $in_tag, $mark, $start, $end - $start + 1 ) !== $end - $start + 1 ) {
                            continue;
                        }
                        if ( $mark === '3' && $group['prefix'] !== self::PRINTF_PREFIX ) {
                            continue;
                        }
                        $candidate_form = 'token';
                        $in_attribute   = true;
                    }
                    $candidates[] = array( 'start' => $start, 'end' => $end, 'prefix' => $group['prefix'], 'priority' => isset( $group['priority'] ) ? (int) $group['priority'] : 10, 'form' => $candidate_form, 'attribute' => $in_attribute );
                }
            }
        }

        // Longest first, then priority, then position; the index keeps ties in match order
        $lengths    = array();
        $priorities = array();
        $starts     = array();
        foreach ( $candidates as $candidate ) {
            $lengths[]    = $candidate['end'] - $candidate['start'];
            $priorities[] = $candidate['priority'];
            $starts[]     = $candidate['start'];
        }
        $index = array_keys( $candidates );
        array_multisort( $lengths, SORT_DESC, SORT_NUMERIC, $priorities, SORT_ASC, SORT_NUMERIC, $starts, SORT_ASC, SORT_NUMERIC, $index, SORT_ASC, SORT_NUMERIC, $candidates );
        // Bytes already covered by a selected match, "1" per byte
        $taken    = str_repeat( '0', strlen( $string ) );
        $selected = array();
        foreach ( $candidates as $candidate ) {
            $length = $candidate['end'] - $candidate['start'];
            if ( strspn( $taken, '0', $candidate['start'], $length ) !== $length ) {
                continue;
            }
            for ( $i = $candidate['start']; $i < $candidate['end']; $i++ ) {
                $taken[ $i ] = '1';
            }
            $selected[ $candidate['start'] ] = $candidate;
        }
        if ( empty( $selected ) ) {
            return $string;
        }
        ksort( $selected );

        $result = '';
        $cursor = 0;
        $ids    = array();
        foreach ( $selected as $range ) {
            $text = substr( $string, $range['start'], $range['end'] - $range['start'] );
            if ( $range['form'] === 'token' ) {
                $number            = self::token_number( $range['prefix'], $text, $range['attribute'] );
                $tokens[ $number ] = self::$registry[ $number ];
                $markup            = '1TP' . $number . 'T';
            } else {
                $ids[ $range['prefix'] ] = isset( $ids[ $range['prefix'] ] ) ? $ids[ $range['prefix'] ] + 1 : 1;
                $markup                  = '<span id="' . $range['prefix'] . $ids[ $range['prefix'] ] . '">' . $text . '</span>';
            }
            $result .= substr( $string, $cursor, $range['start'] - $cursor ) . $markup;
            $cursor  = $range['end'];
        }

        return $result . substr( $string, $cursor );
    }

    /**
     * Number of the 1TPnT token for a protected text, the same for the same text anywhere in this request and
     * new for a text not seen yet. Text inside an attribute value is numbered apart from the same text elsewhere,
     * because its restore is escaped (see restore_tokens()).
     *
     * @param string $prefix
     * @param string $text
     * @param bool   $attribute
     * @return int
     */
    public static function token_number( $prefix, $text, $attribute = false ) {
        $key = $prefix . "\0" . ( $attribute ? 'a' : 't' ) . "\0" . $text;
        if ( !isset( self::$registry_index[ $key ] ) ) {
            $number                        = count( self::$registry ) + 1;
            self::$registry[ $number ]     = array( 'prefix' => $prefix, 'text' => $text, 'attribute' => (bool) $attribute );
            self::$registry_index[ $key ]  = $number;
        }
        return self::$registry_index[ $key ];
    }

    /**
     * Forget every token number given out so far. Only for a long-running process between two independent
     * translate() calls (never during one) and for tests; the registry otherwise lives for the request.
     */
    public static function reset_tokens() {
        self::$registry       = array();
        self::$registry_index = array();
    }

    /**
     * Attributes whose value is text, as a lookup array.
     *
     * @return array name => true
     */
    public static function text_attributes() {
        /**
         * Attributes whose value is text an engine translates (lowercase names). Excluded words and glossary terms in
         * them are sent as 1TPnT tokens; in every other attribute only printf conversions are. The value attribute of
         * submit, button and reset inputs is always text.
         *
         * @param array $attributes
         */
        $names = apply_filters( 'trp_machine_translation_text_attributes', explode( ' ', self::TEXT_ATTRIBUTES ) );
        return array_fill_keys( array_map( 'strtolower', array_filter( (array) $names, 'is_string' ) ), true );
    }

    /**
     * Copy of $string where every byte that isn't part of a valid UTF-8 sequence is replaced with "?", so /u
     * patterns can run on it and its byte offsets still point into $string.
     *
     * @param string $string
     * @return string
     */
    protected static function utf8_subject( $string ) {
        if ( $string === '' || preg_match( '//u', $string ) ) {
            return $string;
        }
        $valid    = '[\x00-\x7F]|[\xC2-\xDF][\x80-\xBF]|\xE0[\xA0-\xBF][\x80-\xBF]|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|\xED[\x80-\x9F][\x80-\xBF]|\xF0[\x90-\xBF][\x80-\xBF]{2}|[\xF1-\xF3][\x80-\xBF]{3}|\xF4[\x80-\x8F][\x80-\xBF]{2}';
        $scrubbed = preg_replace( '/\G(?:' . $valid . ')*+\K[\x80-\xFF]/', '?', $string );
        return $scrubbed === null ? preg_replace( '/[\x80-\xFF]/', '?', $string ) : $scrubbed;
    }

    /**
     * error_log() a problem once per request, under WP_DEBUG.
     *
     * @param string $cause
     * @param string $message
     */
    protected static function log_once( $cause, $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG && !isset( self::$logged[ $cause ] ) ) {
            self::$logged[ $cause ] = true;
            error_log( 'TranslatePress: ' . $message );
        }
    }

    /**
     * The 1TPnT tokens a sent string holds that this request gave out.
     *
     * @param string $sent_string
     * @return array n => array( 'prefix', 'text', 'attribute' ).
     */
    public static function tokens_in( $sent_string ) {
        $tokens = array();
        if ( !is_string( $sent_string ) || empty( self::$registry ) || stripos( $sent_string, '1TP' ) === false || !preg_match_all( self::LATIN_TOKEN_REGEX, $sent_string, $matches ) ) {
            return $tokens;
        }
        foreach ( $matches[1] as $digits ) {
            $number = self::token_digits( $digits );
            if ( isset( self::$registry[ $number ] ) ) {
                $tokens[ $number ] = self::$registry[ $number ];
            }
        }
        return $tokens;
    }

    /**
     * @param string $digits Number of a TOKEN_REGEX match, ASCII or full-width digits.
     * @return int 0 when too long to be one this request gave out.
     */
    protected static function token_digits( $digits ) {
        $digits = str_replace( array( '０', '１', '２', '３', '４', '５', '６', '７', '８', '９' ), array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), $digits );
        return strlen( $digits ) < 10 ? (int) $digits : 0;
    }

    /**
     * @param string $sent_string
     * @param string $prefix
     * @return array n => text, for the tokens of one prefix.
     */
    public static function token_texts( $sent_string, $prefix ) {
        $texts = array();
        foreach ( self::tokens_in( $sent_string ) as $number => $token ) {
            if ( $token['prefix'] === $prefix ) {
                $texts[ $number ] = $token['text'];
            }
        }
        return $texts;
    }

    /**
     * Replace the 1TPnT tokens of one prefix (null: of every prefix) that the sent string holds with
     * $callback( n, text ). A replacement other than the sent text is HTML-escaped inside an attribute value, so a
     * glossary translation with a quote can't end the attribute. Numbers this request didn't give out, or that
     * aren't in the sent string, are left alone. A token the engine repeats is restored each time.
     *
     * @param string      $machine_string
     * @param string      $sent_string
     * @param string|null $prefix
     * @param callable    $callback
     * @return array array( result string, numbers of the tokens found ).
     */
    public static function restore_tokens( $machine_string, $sent_string, $prefix, $callback ) {
        $found  = array();
        $tokens = self::tokens_in( $sent_string );
        if ( empty( $tokens ) || !is_string( $machine_string ) || ( strpos( $machine_string, '1' ) === false && strpos( $machine_string, '１' ) === false ) ) {
            return array( $machine_string, $found );
        }
        $restore = function ( $match ) use ( $tokens, $prefix, $callback, &$found ) {
            $number = self::token_digits( $match[1] );
            if ( !isset( $tokens[ $number ] ) || ( $prefix !== null && $tokens[ $number ]['prefix'] !== $prefix ) ) {
                return $match[0];
            }
            $found[]  = $number;
            $restored = (string) call_user_func( $callback, $number, $tokens[ $number ]['text'] );
            if ( $tokens[ $number ]['attribute'] && $restored !== $tokens[ $number ]['text'] ) {
                $restored = htmlspecialchars( $restored, ENT_QUOTES, 'UTF-8', false );
            }
            return $restored;
        };
        $result = preg_replace_callback( self::TOKEN_REGEX, $restore, $machine_string );
        if ( $result === null ) {
            $result = preg_replace_callback( self::LATIN_TOKEN_REGEX, $restore, $machine_string );
        }
        return array( $result === null ? $machine_string : $result, $found );
    }

    /**
     * Find the spans whose id starts with $prefix_regex followed by a number. The closing tag is found by
     * counting nested spans, so protected text may contain spans of its own. Byte offsets.
     *
     * @param string $string
     * @param string $prefix_regex Id prefix, as a regex fragment.
     * @return array List of array( 'id', 'start', 'content_start', 'end', 'content' ). 'end' is null and 'content' '' when the span is never closed.
     */
    public static function find_spans( $string, $prefix_regex ) {
        $spans = array();
        if ( !preg_match_all( self::opening_tag_regex( $prefix_regex ), $string, $openings, PREG_OFFSET_CAPTURE ) ) {
            return $spans;
        }

        // Closing tag of each opening in one pass, at the first </span> back at its nesting level (a self-closing <span/> waits for one too)
        $is_opening = array();
        foreach ( $openings[0] as $opening ) {
            $is_opening[ $opening[1] ] = true;
        }
        $closing = array();
        $pending = array();
        $depth   = 0;
        preg_match_all( self::SPAN_TAG_REGEX, $string, $tags, PREG_OFFSET_CAPTURE );
        foreach ( $tags[0] as $tag ) {
            $change = self::tag_depth_change( $tag[0] );
            if ( $change >= 0 ) {
                $depth += $change;
                if ( isset( $is_opening[ $tag[1] ] ) ) {
                    $pending[] = array( $tag[1], $depth );
                }
                continue;
            }
            while ( !empty( $pending ) && $pending[ count( $pending ) - 1 ][1] >= $depth ) {
                $waiting                = array_pop( $pending );
                $closing[ $waiting[0] ] = $tag;
            }
            $depth = max( 0, $depth - 1 );
        }

        $cursor = 0;
        foreach ( $openings[0] as $index => $opening ) {
            $start = $opening[1];
            if ( $start < $cursor ) {
                continue;
            }
            $content_start = $start + strlen( $opening[0] );
            $end           = isset( $closing[ $start ] ) ? $closing[ $start ][1] + strlen( $closing[ $start ][0] ) : null;
            $spans[] = array(
                'id'            => strtolower( $openings[1][ $index ][0] ),
                'start'         => $start,
                'content_start' => $content_start,
                'end'           => $end,
                'content'       => $end === null ? '' : substr( $string, $content_start, $closing[ $start ][1] - $content_start ),
            );
            $cursor = $end === null ? $content_start : $end;
        }

        return $spans;
    }

    /**
     * Opening tag of a protecting span. The engine may change the case, drop the quotes around the id or move
     * it to the class attribute.
     *
     * @param string $prefix_regex
     * @return string
     */
    protected static function opening_tag_regex( $prefix_regex ) {
        return '/<span\b[^<>]*' . self::ID_ATTRIBUTE_REGEX . '\s*=\s*["\']?(' . $prefix_regex . '\d+)(?!\d)["\']?[^<>]*>/i';
    }

    /**
     * +1 for an opening span, -1 for a closing one, 0 for a self-closing <span/>.
     *
     * @param string $tag A match of SPAN_TAG_REGEX.
     * @return int
     */
    protected static function tag_depth_change( $tag ) {
        if ( $tag[1] === '/' ) {
            return -1;
        }
        return substr( $tag, -2 ) === '/>' ? 0 : 1;
    }

    /**
     * Turn HTML-escaped protecting spans (&lt;span id=&quot;trp-exclude-1&quot;&gt;) back into tags and close
     * opening tags that lost their bracket.
     *
     * @param string $string
     * @param string $prefix_regex
     * @return string
     */
    public static function decode_escaped_spans( $string, $prefix_regex ) {
        // Opening tag the engine left without its closing bracket: <span id="trp-exclude-1"Akme</span>
        $repaired = preg_replace( '/(?><span\b[^<>]*?' . self::ID_ATTRIBUTE_REGEX . '\s*=\s*["\']?' . $prefix_regex . '\d+(?!\d)["\']?)(?![^<>]*>)/i', '$0>', $string );
        $string   = $repaired === null ? $string : $repaired;

        $quote   = '(?:&quot;|&#0*34;|&#x0*22;|&#0*39;|&#x0*27;|"|\')?';
        $lt      = '(?:&lt;|&#0*60;|&#x0*3c;)';
        $gt      = '(?:&gt;|&#0*62;|&#x0*3e;)';
        $decoded = preg_replace( '/' . $lt . 'span\s+' . self::ID_ATTRIBUTE_REGEX . '=' . $quote . '(' . $prefix_regex . '\d+)' . $quote . '\s*' . $gt . '/i', '<span id="$1">', $string, -1, $count );
        if ( $decoded === null || $count === 0 ) {
            return $string;
        }
        return preg_replace( '/' . $lt . '\/span\s*' . $gt . '/i', '</span>', $decoded );
    }

    /**
     * Whether the text the engine returned inside a span can be overwritten with the protected text: same
     * number of words, give or take one. Languages without spaces compare characters without combining marks
     * (Thai vowels and tones), up to twice the length and at least 8 more.
     *
     * @param string $content              Engine text inside the span, trimmed.
     * @param string $source               Text that was sent inside the span.
     * @param string $target_language_code
     * @return bool
     */
    public static function is_overwritable( $content, $source, $target_language_code ) {
        if ( !self::uses_word_boundaries( $target_language_code ) ) {
            $length = self::count_characters( $source );
            return self::count_characters( $content ) <= max( 2 * $length, $length + 8 );
        }
        return abs( self::count_words( $content ) - self::count_words( $source ) ) <= 1;
    }

    /**
     * @param string $text
     * @return int Characters, combining marks not counted.
     */
    protected static function count_characters( $text ) {
        $base = preg_replace( '/\p{M}+/u', '', $text );
        return mb_strlen( $base === null ? $text : $base, 'UTF-8' );
    }

    /**
     * @param string $text
     * @return int
     */
    public static function count_words( $text ) {
        $text  = strip_tags( $text );
        $count = preg_match_all( '/[\p{L}\p{N}]+(?:[\'’\-][\p{L}\p{N}]+)*/u', $text );
        return $count === false ? count( preg_split( '/\s+/', trim( $text ), -1, PREG_SPLIT_NO_EMPTY ) ) : $count;
    }

    /**
     * What to put in place of a span: the replacement when the engine kept the span to the protected text or
     * left it empty, the engine text with the protected text swapped when it pulled neighbouring words in, the
     * engine text when it already holds the replacement, null when the span drifted too far to tell or holds part
     * of the text with the rest right outside it (the caller keeps the engine text).
     *
     * An empty span, or one holding only punctuation, stays as it is when it touches another span tag, or a letter
     * in a language with spaces: the engine marked a spot next to text it wrote itself.
     *
     * @param string $content              Engine text inside the span.
     * @param string $source               Text that was sent inside the span.
     * @param string $replacement          Text to restore.
     * @param string $target_language_code
     * @param array  $neighbours           See span_neighbours(); empty when unknown.
     * @return string|null
     */
    public static function restore_span( $content, $source, $replacement, $target_language_code, $neighbours = array() ) {
        $trimmed = trim( $content );
        if ( $trimmed === '' || preg_match( '/[\p{L}\p{N}]/u', $trimmed ) === 0 ) {
            $touching = !empty( $neighbours['tag'] ) || ( !empty( $neighbours['letter'] ) && self::uses_word_boundaries( $target_language_code ) );
            return $touching ? $content : $replacement;
        }
        if ( mb_strtolower( $trimmed, 'UTF-8' ) === mb_strtolower( $source, 'UTF-8' ) ) {
            return $replacement;
        }
        $position = mb_stripos( $trimmed, $source, 0, 'UTF-8' );
        if ( $position !== false ) {
            return mb_substr( $trimmed, 0, $position, 'UTF-8' ) . $replacement . mb_substr( $trimmed, $position + mb_strlen( $source, 'UTF-8' ), null, 'UTF-8' );
        }
        // A code the engine split where letters meet digits (iPhone15 into iPhone 15)
        $parts = preg_split( '/(?<=\p{L})(?=\p{N})|(?<=\p{N})(?=\p{L})/u', $source );
        if ( is_array( $parts ) && count( $parts ) > 1 ) {
            $swapped = preg_replace( '/' . implode( '\s+', array_map( function ( $part ) { return preg_quote( $part, '/' ); }, $parts ) ) . '/iu', str_replace( array( '\\', '$' ), array( '\\\\', '\\$' ), $replacement ), $trimmed, 1, $count );
            if ( $count === 1 && $swapped !== null ) {
                return $swapped;
            }
        }
        if ( $replacement !== '' && mb_stripos( $trimmed, $replacement, 0, 'UTF-8' ) !== false ) {
            return $content;
        }
        // Overwriting would drop the markup the engine moved inside the span
        if ( $trimmed !== strip_tags( $trimmed ) ) {
            return null;
        }
        if ( self::is_split( $trimmed, $source, $neighbours ) || self::is_split( $trimmed, $replacement, $neighbours ) ) {
            return null;
        }
        return self::is_overwritable( $trimmed, $source, $target_language_code ) ? $replacement : null;
    }

    /**
     * Whether the span holds part of $whole and a word of the rest sits right outside it (das <span>große</span>
     * Apfel for Großer Apfel). Overwriting would repeat that word.
     *
     * @param string $content    Engine text inside the span, trimmed.
     * @param string $whole
     * @param array  $neighbours See span_neighbours().
     * @return bool
     */
    protected static function is_split( $content, $whole, $neighbours ) {
        if ( !isset( $neighbours['before'], $neighbours['after'] ) || mb_strlen( $content, 'UTF-8' ) < 2 || mb_strlen( $content, 'UTF-8' ) >= mb_strlen( $whole, 'UTF-8' ) || mb_stripos( $whole, $content, 0, 'UTF-8' ) === false ) {
            return false;
        }
        $around = $neighbours['before'] . ' ' . $neighbours['after'];
        $words  = preg_split( '/[^\p{L}\p{N}\p{M}]+/u', $whole, -1, PREG_SPLIT_NO_EMPTY );
        foreach ( is_array( $words ) ? $words : array() as $word ) {
            if ( mb_stripos( $content, $word, 0, 'UTF-8' ) === false && mb_stripos( $word, $content, 0, 'UTF-8' ) === false && mb_stripos( $around, $word, 0, 'UTF-8' ) !== false ) {
                return true;
            }
        }
        return false;
    }

    /**
     * What surrounds a span in the engine output.
     *
     * @param string $string
     * @param int    $start  Offset of the opening tag.
     * @param int    $end    Offset after the closing tag.
     * @return array 'letter': a letter or digit touches the span, 'tag': a span tag does, 'before' / 'after': up to
     *               300 bytes of text on each side, tags stripped.
     */
    protected static function span_neighbours( $string, $start, $end ) {
        $before = substr( $string, max( 0, $start - 300 ), min( 300, $start ) );
        $after  = (string) substr( $string, $end, 300 );
        return array(
            'letter' => self::letter_at( $string, $start, true ) || self::letter_at( $string, $end, false ),
            'tag'    => preg_match( '/<\/?span\b[^<>]*>\z/i', $before ) === 1 || preg_match( '/^<\/?span\b/i', $after ) === 1,
            'before' => strip_tags( ltrim( $before, "\x80..\xBF" ) ),
            'after'  => strip_tags( $after ),
        );
    }

    /**
     * Map span id => text inside it.
     *
     * @param string $string
     * @param string $prefix
     * @return array
     */
    public static function get_span_contents( $string, $prefix ) {
        $contents = array();
        foreach ( self::find_spans( $string, preg_quote( $prefix, '/' ) ) as $span ) {
            if ( $span['end'] !== null ) {
                $contents[ $span['id'] ] = $span['content'];
            }
        }
        return $contents;
    }

    /**
     * Replace each closed span of this type with $callback( $id, $content, $neighbours ), $neighbours as
     * span_neighbours() returns it (empty for text around nested spans). A span left open by the engine
     * loses its opening tag and keeps its text. A span nested in a span of another type keeps its tags for
     * that type's callback. A span holding nothing but nested spans gets $callback( $id, '' ) before them,
     * unless one of them has the same id.
     *
     * Spans nested deeper than MAX_NESTING_DEPTH keep the engine text, remove_leftover_tags() drops their tags.
     *
     * @param string   $string
     * @param string   $prefix
     * @param callable $callback
     * @param int      $depth    Nesting level, for the recursion.
     * @return array array( result string, ids of the closed spans found ).
     */
    public static function replace_spans( $string, $prefix, $callback, $depth = 0 ) {
        $result = '';
        $cursor = 0;
        $found  = array();
        $string  = self::decode_escaped_spans( $string, self::ANY_PREFIX_REGEX );
        $foreign = array_values( array_filter( self::find_spans( $string, '(?!' . preg_quote( $prefix, '/' ) . ')' . self::ANY_PREFIX_REGEX ), function ( $foreign_span ) {
            return $foreign_span['end'] !== null;
        } ) );
        $next_foreign = 0;
        foreach ( self::find_spans( $string, preg_quote( $prefix, '/' ) ) as $span ) {
            $result .= self::tighten_span_edges( substr( $string, $cursor, $span['start'] - $cursor ), $cursor > 0 );
            if ( $span['end'] === null ) {
                $cursor = $span['content_start'];
                continue;
            }
            $found[] = $span['id'];
            $inner   = self::find_spans( $span['content'], self::ANY_PREFIX_REGEX );
            if ( empty( $inner ) ) {
                $replacement = self::replace_with_edges( $span['content'], call_user_func( $callback, $span['id'], $span['content'], self::span_neighbours( $string, $span['start'], $span['end'] ) ), $string, $span['start'], $span['end'] );
            } else {
                // The engine nested spans: restore inner spans of this type, and the outer one in the text around them
                $last   = end( $inner );
                $head   = substr( $span['content'], 0, $inner[0]['start'] );
                $tail   = $last['end'] === null ? '' : substr( $span['content'], $last['end'] );
                $middle = substr( $span['content'], strlen( $head ), strlen( $span['content'] ) - strlen( $head ) - strlen( $tail ) );
                if ( $depth < self::MAX_NESTING_DEPTH ) {
                    list( $middle, $inner_found ) = self::replace_spans( $middle, $prefix, $callback, $depth + 1 );
                    foreach ( $inner_found as $inner_id ) {
                        $found[] = $inner_id;
                    }
                } else {
                    self::log_once( 'nesting-depth', 'engine nested protecting spans more than ' . self::MAX_NESTING_DEPTH . ' deep, kept its text below that (logged once per request)' );
                    $inner_found = array_map( function ( $inner_span ) {
                        return $inner_span['id'];
                    }, $inner );
                }
                if ( trim( $head ) !== '' ) {
                    $head = self::replace_with_edges( $head, call_user_func( $callback, $span['id'], trim( $head ) ), $string, $span['start'], null );
                } elseif ( trim( $tail ) !== '' ) {
                    $tail = self::replace_with_edges( $tail, call_user_func( $callback, $span['id'], trim( $tail ) ), $string, null, $span['end'] );
                } elseif ( !in_array( $span['id'], $inner_found, true ) ) {
                    $outer = (string) call_user_func( $callback, $span['id'], '' );
                    if ( $outer !== '' ) {
                        $lead = $head !== '' && self::letter_at( $string, $span['start'], true ) ? $head : '';
                        $head = $lead . $outer . ( $head !== '' ? $head : self::word_separator( $outer, $middle ) );
                        $tail = $tail !== '' && self::letter_at( $string, $span['end'], false ) ? $tail : '';
                    }
                }
                $replacement = $head . $middle . $tail;
            }
            // Closed spans don't overlap, so only the first foreign span ending after this one can hold it
            while ( isset( $foreign[ $next_foreign ] ) && $foreign[ $next_foreign ]['end'] < $span['end'] ) {
                $next_foreign++;
            }
            $inside_foreign = isset( $foreign[ $next_foreign ] ) && $span['start'] >= $foreign[ $next_foreign ]['content_start'];
            if ( $inside_foreign ) {
                $replacement = substr( $string, $span['start'], $span['content_start'] - $span['start'] ) . $replacement . '</span>';
            }
            $cursor = $span['end'];
            // A span that ends up empty leaves no double space, nor a space at the start
            if ( !$inside_foreign && trim( $replacement ) === '' && ctype_space( substr( $string, $cursor, 1 ) ) && ( ( $result === '' && $depth === 0 ) || ctype_space( substr( $result, -1 ) ) ) ) {
                $replacement = '';
                $cursor++;
            }
            $result .= $replacement;
        }

        return array( $result . ( $cursor > 0 ? self::tighten_span_edges( substr( $string, $cursor ), true, false ) : substr( $string, $cursor ) ), $found );
    }

    /**
     * Put the replacement in place of the span text. The engine sometimes pulls the space between the span and
     * the next word inside the span, keep it when there is a letter or digit on the other side of the tag.
     *
     * @param string   $content     Text inside the span.
     * @param string   $replacement
     * @param string   $string      Whole string the span is in.
     * @param int|null $span_start  Offset of the opening tag, null when the content doesn't touch it.
     * @param int|null $span_end    Offset after the closing tag, null when the content doesn't touch it.
     * @return string
     */
    protected static function replace_with_edges( $content, $replacement, $string, $span_start, $span_end ) {
        if ( $replacement === $content || !preg_match( '/^(\s*+)(?:.*\S)?(\s*)\z/su', $content, $edges ) ) {
            return $replacement;
        }
        $before = $span_start === null || self::letter_at( $string, $span_start, true ) ? $edges[1] : '';
        $after  = $span_end === null || self::letter_at( $string, $span_end, false ) ? $edges[2] : '';
        return $before . $replacement . $after;
    }

    /**
     * Whether the character right before (or after) a byte offset is a letter or digit. Looks at that character
     * only: matching /u on the text before each span made restoring many spans quadratic.
     *
     * @param string $string
     * @param int    $offset Character boundary.
     * @param bool   $before
     * @return bool
     */
    protected static function letter_at( $string, $offset, $before ) {
        if ( $before ) {
            $char = ltrim( substr( $string, max( 0, $offset - 4 ), min( 4, $offset ) ), "\x80..\xBF" );
            return (bool) preg_match( '/[\p{L}\p{N}]\z/u', $char );
        }
        $char = substr( $string, $offset, 4 );
        $lead = $char === '' ? 0 : ord( $char[0] );
        return (bool) preg_match( '/^[\p{L}\p{N}]/u', substr( $char, 0, $lead < 0xC0 ? 1 : ( $lead < 0xE0 ? 2 : ( $lead < 0xF0 ? 3 : 4 ) ) ) );
    }

    /**
     * Space to put between two protected texts the engine nested with nothing between them, none for scripts
     * written without spaces.
     *
     * @param string $left
     * @param string $right May hold span tags.
     * @return string
     */
    protected static function word_separator( $left, $right ) {
        if ( !preg_match( '/^(?:<[^<>]*>)*(.)/su', $right, $first ) || preg_match( '/\s\z/u', $left ) || preg_match( '/\s/u', $first[1] ) ) {
            return '';
        }
        $no_space = '[\p{Han}\p{Hiragana}\p{Katakana}\p{Thai}\p{Lao}\p{Khmer}\p{Myanmar}\p{Tibetan}]';
        return preg_match( '/' . $no_space . '\z/u', $left ) || preg_match( '/^' . $no_space . '/u', $first[1] ) ? '' : ' ';
    }

    /**
     * Drop the space engines put between a span and an elision (d' Acme), a possessive (Acme 's) or closing
     * punctuation (Acme .), so restore_translation_quotes doesn't read the apostrophe as a quote.
     *
     * @param string $text       Text between two spans.
     * @param bool   $after_span Text follows a span.
     * @param bool   $before_span Text precedes a span.
     * @return string
     */
    protected static function tighten_span_edges( $text, $after_span, $before_span = true ) {
        $tightened = $text;
        if ( $after_span ) {
            $tightened = preg_replace( '/^\s+(?=[.,)]|[\'’]s(?!\p{L}))/u', '', $tightened );
        }
        if ( $before_span && $tightened !== null ) {
            $tightened = preg_replace( '/(?<![\p{L}\'’‘])((?:\p{L}{1,2}|\p{L}*qu)[\'’])\s+\z/iu', '$1', $tightened );
        }
        return $tightened === null ? $text : $tightened;
    }

    /**
     * Whether a language separates words with spaces. Scripts that don't (Chinese, Japanese, Thai...) need
     * substring matching because a word boundary never occurs there.
     *
     * @param string $language_code
     * @return bool
     */
    public static function uses_word_boundaries( $language_code ) {
        // Filter name predates this class, kept so existing code keeps glossary matching and span restoring in sync
        $no_boundary = apply_filters( 'trp_glossary_languages_without_word_boundaries', array( 'zh', 'ja', 'th', 'lo', 'km', 'my', 'bo' ) );
        $prefix      = strtolower( (string) strtok( (string) $language_code, '_' ) );
        return !in_array( $prefix, $no_boundary, true );
    }

    /**
     * Lookarounds that make a term match as a whole word. At an edge where the term has a letter (or combining
     * mark), the neighbour must not be a letter, combining mark or _, nor a digit after the term (a digit before
     * it is fine: 100zł, 5kg; NASA2026 is matched whole only by words_pattern() with $trailing_digits). Where it has a
     * digit or _, the neighbour must not be a letter, mark, digit or _ (3D not in 13D, 5 not in 5kg). Where it has
     * anything else (C#, $), the neighbour must not be a letter, mark, digit or _ when $symbol_edges, and isn't
     * checked otherwise ($100).
     *
     * @param bool $symbol_edges
     * @return string[] array( before the term, after the term ).
     */
    public static function word_edges( $symbol_edges = true ) {
        if ( $symbol_edges ) {
            return array(
                '(?:(?=[\p{L}\p{M}])(?<![\p{L}\p{M}_])|(?![\p{L}\p{M}])(?<![\p{L}\p{M}\p{N}_]))',
                '(?:(?<=[\p{L}\p{M}])(?![\p{L}\p{M}\p{N}_])|(?<![\p{L}\p{M}])(?![\p{L}\p{M}\p{N}_]))',
            );
        }
        return array(
            '(?:(?=[\p{L}\p{M}])(?<![\p{L}\p{M}_])|(?=[\p{N}_])(?<![\p{L}\p{M}\p{N}_])|(?![\p{L}\p{M}\p{N}_]))',
            '(?:(?<=[\p{L}\p{M}])(?![\p{L}\p{M}\p{N}_])|(?<=[\p{N}_])(?![\p{L}\p{M}\p{N}_])|(?<![\p{L}\p{M}\p{N}_]))',
        );
    }

    /**
     * Split a match that words_pattern() extended with $trailing_digits.
     *
     * @param string $text
     * @return array|null array( word part, digits ), null when $text isn't a word followed by digits.
     */
    public static function split_trailing_digits( $text ) {
        return preg_match( '/^(.*[\p{L}\p{M}])(\p{N}+)\z/su', (string) $text, $code ) ? array( $code[1], $code[2] ) : null;
    }

    /**
     * @param string $word         Text sent in a span or token.
     * @param array  $replacements word => replacement.
     * @return string The word's replacement; for a word followed by digits (iPhone15), the word part's.
     */
    protected static function replacement_for( $word, $replacements ) {
        if ( isset( $replacements[ $word ] ) ) {
            return $replacements[ $word ];
        }
        $code = self::split_trailing_digits( $word );
        return $code !== null && isset( $replacements[ $code[0] ] ) ? $replacements[ $code[0] ] . $code[1] : $word;
    }

    /**
     * Single regex matching any of the words, longest first, so a word is never matched inside a longer one.
     * Words that are not valid UTF-8 are skipped. When the regex is too large to compile, the words are split
     * over several, wrap() still prefers the longest match across them.
     *
     * @param array $words
     * @param bool  $whole_words      False for languages without spaces between words. See word_edges().
     * @param bool  $case_insensitive
     * @param bool  $symbol_edges     Whole words only: check the neighbour at an edge that isn't a letter, digit or _.
     * @param bool  $trailing_digits  Whole words only: digits right after a word ending in a letter join the match, so
     *                                a code like NASA2026 or iPhone15 is protected whole instead of not at all (see
     *                                split_trailing_digits()). A longer configured word (Gold100) still wins.
     * @return string|string[] PCRE pattern, a list of them when the words don't fit in one, '' when no word is usable.
     */
    public static function words_pattern( $words, $whole_words, $case_insensitive, $symbol_edges = true, $trailing_digits = false ) {
        $valid = array();
        foreach ( $words as $word ) {
            if ( preg_match( '//u', $word ) ) {
                $valid[] = $word;
            } else {
                self::log_once( 'invalid-term', 'skipped an excluded word or glossary term that is not valid UTF-8 (logged once per request): "' . self::utf8_subject( $word ) . '"' );
            }
        }
        if ( empty( $valid ) ) {
            return '';
        }
        usort( $valid, function ( $a, $b ) {
            return mb_strlen( $b, 'UTF-8' ) - mb_strlen( $a, 'UTF-8' );
        } );
        $quoted = array_map( function ( $word ) {
            return preg_quote( $word, '/' );
        }, $valid );
        $modifiers = $case_insensitive ? 'iu' : 'u';
        $edges     = $whole_words ? self::word_edges( $symbol_edges ) : array( '', '' );
        $suffix    = $whole_words && $trailing_digits ? '(?:(?<=[\p{L}\p{M}])\p{N}++)?' : '';
        $build     = function ( $alternation ) use ( $edges, $modifiers, $suffix ) {
            return '/' . $edges[0] . '(?:' . $alternation . ')' . $suffix . $edges[1] . '/' . $modifiers;
        };

        $pattern = $build( implode( '|', $quoted ) );
        if ( @preg_match( $pattern, '' ) !== false ) {
            return $pattern;
        }
        self::log_once( 'pattern-split', 'excluded words or glossary terms too many for one regex, split into several (logged once per request)' );
        $patterns = array();
        $chunk    = '';
        foreach ( $quoted as $word ) {
            if ( $chunk !== '' && strlen( $chunk ) + strlen( $word ) >= self::PATTERN_MAX_BYTES ) {
                $patterns[] = $build( $chunk );
                $chunk      = '';
            }
            $chunk .= ( $chunk === '' ? '' : '|' ) . $word;
        }
        $patterns[] = $build( $chunk );
        return $patterns;
    }

    /**
     * Protect the words excluded from automatic translation (case-sensitive whole words, substring in a source
     * language without word boundaries), the escaped percent sign %% and whatever other text
     * trp_machine_translation_span_terms adds, e.g. glossary terms. Text nodes get spans; inside a quoted attribute
     * value, and for %% everywhere, a 1TPnT placeholder goes instead (see wrap()). printf conversions are sent as
     * they are unless the user excluded them, or as 1TPnT for Google Translate (see printf_as_tokens()).
     *
     * Hooked to trp_machine_translation_string_before_send.
     *
     * @param string      $string
     * @param string      $target_language_code
     * @param string|null $source_language_code
     * @param array       $exclude_words
     * @return string
     */
    public function wrap_protected_text( $string, $target_language_code = '', $source_language_code = null, $exclude_words = array() ) {
        if ( !is_string( $string ) || $string === '' ) {
            return $string;
        }

        $groups               = array();
        $words                = array();
        $printf               = array();
        $source_language_code = $source_language_code ? $source_language_code : ( isset( $this->settings['default-language'] ) ? $this->settings['default-language'] : '' );
        $whole_words          = self::uses_word_boundaries( $source_language_code );
        foreach ( self::normalize_exclude_words( $exclude_words ) as $word ) {
            if ( strpos( $string, $word ) === false ) {
                continue;
            }
            // An excluded printf conversion ends at its letter, so %s still matches in %smin
            if ( $whole_words && preg_match( '/^%(?:\d+\$)?[a-zA-Z]$/', $word ) ) {
                $printf[] = $word;
            } else {
                $words[] = $word;
            }
        }
        // Symbol edges unchecked so entries like $ or % keep matching in $100 and 100%
        $patterns = array_values( array_filter( array_merge( (array) self::words_pattern( $words, $whole_words, false, false, true ), (array) self::words_pattern( $printf, false, false ) ) ) );
        if ( !empty( $patterns ) ) {
            $groups[] = array(
                'prefix'   => self::EXCLUDE_PREFIX,
                'pattern'  => count( $patterns ) === 1 ? $patterns[0] : $patterns,
                'priority' => 20,
            );
        }
        // %% goes as 1TPnT everywhere: engines turn an escaped percent sign into % or %%%
        $printf_as_tokens = strpos( $string, '%' ) !== false && $this->printf_as_tokens( $target_language_code, $source_language_code );
        if ( $printf_as_tokens || strpos( $string, '%%' ) !== false ) {
            $groups[] = array(
                'prefix'   => self::PRINTF_PREFIX,
                'pattern'  => $printf_as_tokens ? self::PRINTF_CONVERSION_REGEX : self::PRINTF_REGEX,
                'priority' => 5,
                'form'     => 'token',
            );
        }

        /**
         * Text to protect with spans, see wrap(). Each group's prefix must match ANY_PREFIX_REGEX (trp-{name}-) and
         * needs its own trp_machine_translation_string_after_receive callback below priority 30, where
         * remove_leftover_tags() strips the spans nobody unwrapped and puts the sent text back for tokens nobody
         * restored (TRP_Machine_Translation_Spans::restore_tokens()), so a token group doesn't need a callback when
         * the text goes back as sent. A match inside a text attribute's value is a 1TPnT token whatever the group
         * says; 'form' => 'token' makes every match one.
         *
         * @param array       $groups               List of array( 'prefix' => 'trp-{name}-', 'pattern' => PCRE pattern or list of patterns, 'priority' => int, 'form' => 'span' (default) or 'token' ).
         * @param string      $string               String about to be sent.
         * @param string      $target_language_code
         * @param string|null $source_language_code
         */
        $groups = apply_filters( 'trp_machine_translation_span_terms', $groups, $string, $target_language_code, $source_language_code );

        return self::wrap( $string, $groups );
    }

    /**
     * Whether printf conversions go to the engine as 1TPnT tokens. Google Translate rewrites them (%1$s into %1$,
     * $s read as dollars, %1$.2f into 0.2f), the other engines keep them.
     *
     * @param string      $target_language_code
     * @param string|null $source_language_code
     * @return bool
     */
    protected function printf_as_tokens( $target_language_code, $source_language_code ) {
        $engine = isset( $this->settings['trp_machine_translation_settings']['translation-engine'] ) ? $this->settings['trp_machine_translation_settings']['translation-engine'] : '';
        /**
         * Send printf conversions (%s, %1$s, %.2f) as 1TPnT tokens instead of verbatim. A token leaves a hole an LLM
         * may fill with invented words, verbatim conversions get rewritten by Google Translate.
         *
         * @param bool        $as_tokens            True for Google Translate.
         * @param string      $engine               Active translation engine.
         * @param string      $target_language_code
         * @param string|null $source_language_code
         */
        return (bool) apply_filters( 'trp_machine_translation_printf_as_tokens', $engine === 'google_translate_v2', $engine, $target_language_code, $source_language_code );
    }

    /**
     * @param mixed $exclude_words
     * @return array The non-empty scalar words as strings, without duplicates.
     */
    protected static function normalize_exclude_words( $exclude_words ) {
        return array_values( array_unique( array_map( 'strval', array_filter( (array) $exclude_words, function ( $word ) {
            return is_scalar( $word ) && (string) $word !== '';
        } ) ) ) );
    }

    /**
     * Put the excluded words back (verbatim, or what trp_replace_placeholders_with returns for them) in place of
     * whatever the engine returned inside their spans, unless the span drifted too far from them (see restore_span()),
     * and in place of their 1TPnT tokens. printf tokens get their conversion back.
     *
     * Hooked to trp_machine_translation_string_after_receive.
     *
     * @param string      $machine_string
     * @param string      $sent_string
     * @param string      $original_string
     * @param string      $target_language_code
     * @param string|null $source_language_code
     * @param array|null  $exclude_words        The request's exclude words. Empty or not passed: the words in $sent_string's spans and tokens.
     * @return string
     */
    public function unwrap_excluded_words( $machine_string, $sent_string = '', $original_string = '', $target_language_code = '', $source_language_code = null, $exclude_words = null ) {
        if ( !is_string( $machine_string ) || !is_string( $sent_string ) ) {
            return $machine_string;
        }
        $sent_words     = strpos( $sent_string, self::EXCLUDE_PREFIX ) === false ? array() : self::get_span_contents( $sent_string, self::EXCLUDE_PREFIX );
        $sent_tokens    = self::token_texts( $sent_string, self::EXCLUDE_PREFIX );
        $printf_tokens  = self::token_texts( $sent_string, self::PRINTF_PREFIX );
        $missing_tokens = array();
        $words          = self::normalize_exclude_words( $exclude_words );

        // an escaped percent sign the user also excluded (%% in the list) takes its replacement like any excluded word
        if ( empty( $sent_words ) && empty( $sent_tokens ) && !array_intersect( $printf_tokens, $words ) ) {
            if ( !empty( $printf_tokens ) ) {
                list( $machine_string, $found ) = self::restore_tokens( $machine_string, $sent_string, self::PRINTF_PREFIX, function ( $number, $text ) {
                    return $text;
                } );
                $this->log_missing_tokens( array_diff_key( $printf_tokens, array_flip( $found ) ), 'printf', $target_language_code, $sent_string, $machine_string );
            }
            return $machine_string;
        }

        $words = !empty( $words ) ? $words : array_values( array_unique( array_merge( array_values( $sent_words ), array_values( $sent_tokens ) ) ) );
        /**
         * Replace excluded words with other text in the translation. Applied once per translated string, only when
         * the sent string has trp-exclude spans or tokens, or a printf token for a conversion in the exclude list (on
         * master it ran for every string). Return one replacement per word, in the same order; any other count is
         * ignored. A replacement is used for each span or token of its word, except a span that drifted, and for the
         * word part of a span with digits after the word (iPhone15); inside an attribute value it is HTML-escaped.
         *
         * @param array       $replacements         Defaults to $exclude_words.
         * @param array       $exclude_words        The request's exclude list, not only this string's words, without empty
         *                                          values or duplicates and re-indexed. No more %s %d % $ # or printf
         *                                          defaults, no glossary terms, not sorted by length. When the list
         *                                          is empty (direct call, or spans added by other code): the unique
         *                                          words in the sent spans and tokens.
         * @param array       $placeholders         Always array(), kept for the argument position.
         * @param string      $machine_string       Engine output with the trp-exclude spans and the exclude and printf 1TPnT
         *                                          tokens still in, glossary spans and tokens already unwrapped.
         * @param string      $original_string      Original string as passed to translate().
         * @param string      $target_language_code
         * @param string|null $source_language_code
         */
        $replacements = apply_filters( 'trp_replace_placeholders_with', $words, $words, array(), $machine_string, $original_string, $target_language_code, $source_language_code );
        if ( !is_array( $replacements ) || count( $replacements ) !== count( $words ) ) {
            $replacements = $words;
        }
        $replacements = array_combine( $words, array_values( $replacements ) );

        $result = $machine_string;
        if ( !empty( $printf_tokens ) ) {
            list( $result, $found ) = self::restore_tokens( $result, $sent_string, self::PRINTF_PREFIX, function ( $number, $text ) use ( $replacements ) {
                return self::replacement_for( $text, $replacements );
            } );
            $missing_tokens += array_diff_key( $printf_tokens, array_flip( $found ) );
        }
        if ( !empty( $sent_tokens ) ) {
            list( $result, $found ) = self::restore_tokens( $result, $sent_string, self::EXCLUDE_PREFIX, function ( $number, $text ) use ( $replacements ) {
                return self::replacement_for( $text, $replacements );
            } );
            $missing_tokens += array_diff_key( $sent_tokens, array_flip( $found ) );
        }

        $drifted = array();
        $found   = array();
        if ( !empty( $sent_words ) ) {
            $sent_text    = strip_tags( $sent_string );
            $machine_text = strip_tags( $result );
            $whole_words  = self::uses_word_boundaries( $target_language_code );
            $present      = array();
            $overwritten  = array();
            list( $result, $found ) = self::replace_spans( $result, self::EXCLUDE_PREFIX, function ( $id, $content, $neighbours = array() ) use ( $sent_words, $replacements, $target_language_code, $sent_text, $machine_text, $whole_words, &$present, &$drifted, &$overwritten ) {
                if ( !isset( $sent_words[ $id ] ) ) {
                    return $content;
                }
                $word = $sent_words[ $id ];
                if ( mb_stripos( $content, $word, 0, 'UTF-8' ) === false ) {
                    if ( !isset( $present[ $word ] ) ) {
                        $present[ $word ] = self::count_occurrences( $machine_text, $word, $whole_words ) >= self::count_occurrences( $sent_text, $word, $whole_words );
                    }
                    // The engine moved the span off the word, or used its id twice, and wrote the word itself
                    if ( $present[ $word ] ) {
                        $drifted[ $id ] = $word;
                        return $content;
                    }
                    // a missing word goes back even into an empty span next to a word
                    unset( $neighbours['tag'], $neighbours['letter'] );
                }
                $replacement = self::replacement_for( $word, $replacements );
                $restored    = self::restore_span( $content, $word, $replacement, $target_language_code, $neighbours );
                if ( $restored === null ) {
                    $drifted[ $id ] = $word;
                    return $content;
                }
                if ( $restored === $replacement && mb_stripos( $content, $word, 0, 'UTF-8' ) === false ) {
                    $overwritten[ $word ] = true;
                }
                return $restored;
            } );
            foreach ( array_keys( $overwritten ) as $word ) {
                $result = self::remove_stray_parts( $result, $word, $sent_text );
            }
        }

        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $missing = array_diff_key( $sent_words, array_flip( $found ) );
            if ( !empty( $missing ) ) {
                error_log( 'TranslatePress: excluded word span missing or left unclosed in the ' . $target_language_code . ' translation for "' . implode( '", "', $missing ) . '". Sent: ' . $sent_string . ' Received: ' . $machine_string );
            }
            if ( !empty( $drifted ) ) {
                error_log( 'TranslatePress: excluded word span drifted in the ' . $target_language_code . ' translation for "' . implode( '", "', $drifted ) . '", kept the engine text. Sent: ' . $sent_string . ' Received: ' . $machine_string );
            }
            $this->log_missing_tokens( $missing_tokens, 'excluded word or printf', $target_language_code, $sent_string, $machine_string );
        }

        return $result;
    }

    /**
     * @param string $text
     * @param string $needle
     * @param bool   $whole_words Not next to a letter (Pro doesn't count in Professionnel).
     * @return int Case-insensitive occurrences.
     */
    protected static function count_occurrences( $text, $needle, $whole_words ) {
        if ( $needle === '' ) {
            return 0;
        }
        if ( $whole_words ) {
            $count = preg_match_all( '/(?<![\p{L}\p{M}])' . preg_quote( $needle, '/' ) . '(?![\p{L}\p{M}])/iu', $text );
            if ( $count !== false ) {
                return $count;
            }
        }
        return mb_substr_count( mb_strtolower( $text, 'UTF-8' ), mb_strtolower( $needle, 'UTF-8' ), 'UTF-8' );
    }

    /**
     * After a span was overwritten with an excluded phrase, drop the words of that phrase the engine had moved out
     * of the span (<span>Професионална</span> продавница Acme for Acme Pro), as many as the text now has more than
     * the sent one. Only whole words in the text, outside tags and outside the phrase itself, last ones first.
     *
     * @param string $string
     * @param string $phrase
     * @param string $sent_text Sent string, tags stripped.
     * @return string
     */
    protected static function remove_stray_parts( $string, $phrase, $sent_text ) {
        $parts = preg_split( '/[^\p{L}\p{N}\p{M}]+/u', $phrase, -1, PREG_SPLIT_NO_EMPTY );
        if ( !is_array( $parts ) || count( $parts ) < 2 ) {
            return $string;
        }
        foreach ( array_unique( $parts ) as $part ) {
            $pattern = '/(?<![\p{L}\p{N}\p{M}])' . preg_quote( $part, '/' ) . '(?![\p{L}\p{N}\p{M}])/u';
            $excess  = preg_match_all( $pattern, strip_tags( $string ) ) - preg_match_all( $pattern, $sent_text );
            if ( mb_strlen( $part, 'UTF-8' ) < 2 || $excess <= 0 || !preg_match_all( $pattern, $string, $matches, PREG_OFFSET_CAPTURE ) ) {
                continue;
            }
            $covered = array();
            if ( preg_match_all( self::TAG_SCAN_REGEX, $string, $tags, PREG_OFFSET_CAPTURE ) ) {
                foreach ( $tags[0] as $tag ) {
                    $covered[] = array( $tag[1], $tag[1] + strlen( $tag[0] ) );
                }
            }
            for ( $offset = stripos( $string, $phrase ); $offset !== false; $offset = stripos( $string, $phrase, $offset + 1 ) ) {
                $covered[] = array( $offset, $offset + strlen( $phrase ) );
            }
            $stray = array();
            foreach ( $matches[0] as $match ) {
                $inside = false;
                foreach ( $covered as $range ) {
                    $inside = $inside || ( $match[1] >= $range[0] && $match[1] < $range[1] );
                }
                if ( !$inside ) {
                    $stray[] = $match;
                }
            }
            foreach ( array_slice( array_reverse( $stray ), 0, $excess ) as $match ) {
                $start  = $match[1];
                $end    = $start + strlen( $match[0] );
                $spaces = strspn( strrev( substr( $string, 0, $start ) ), " \t" );
                if ( $spaces > 0 ) {
                    $start -= $spaces;
                } else {
                    $end += strspn( $string, " \t", $end );
                }
                $string = substr( $string, 0, $start ) . substr( $string, $end );
            }
        }
        return $string;
    }

    /**
     * error_log() the 1TPnT tokens the engine dropped, under WP_DEBUG.
     *
     * @param array  $missing n => text
     * @param string $what
     * @param string $target_language_code
     * @param string $sent_string
     * @param string $machine_string
     */
    protected function log_missing_tokens( $missing, $what, $target_language_code, $sent_string, $machine_string ) {
        if ( !empty( $missing ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'TranslatePress: ' . $what . ' placeholder missing in the ' . $target_language_code . ' translation for "' . implode( '", "', $missing ) . '". Sent: ' . $sent_string . ' Received: ' . $machine_string );
        }
    }

    /**
     * Drop protecting span tags the unwrap callbacks could not pair (unknown prefix, never closed) and closing
     * tags the engine added without an opening one. 1TPnT tokens nobody restored get their sent text back.
     *
     * Hooked to trp_machine_translation_string_after_receive, after the unwrap callbacks.
     *
     * @param string $machine_string
     * @param string $sent_string
     * @return string
     */
    public function remove_leftover_tags( $machine_string, $sent_string = '' ) {
        if ( !is_string( $machine_string ) || !is_string( $sent_string ) ) {
            return $machine_string;
        }
        if ( self::tokens_in( $sent_string ) ) {
            list( $machine_string ) = self::restore_tokens( $machine_string, $sent_string, null, function ( $number, $text ) {
                return $text;
            } );
        }
        if ( !preg_match( self::opening_tag_regex( self::ANY_PREFIX_REGEX ), $sent_string ) ) {
            return $machine_string;
        }
        $result = self::decode_escaped_spans( $machine_string, self::ANY_PREFIX_REGEX );
        $result = preg_replace( self::opening_tag_regex( self::ANY_PREFIX_REGEX ), '', $result );
        if ( $result === null ) {
            return $machine_string;
        }

        $extra    = array_slice( self::orphan_closing_tags( $result ), count( self::orphan_closing_tags( $sent_string ) ) );
        $stripped = '';
        $cursor   = 0;
        foreach ( $extra as $tag ) {
            $stripped .= substr( $result, $cursor, $tag[1] - $cursor );
            $cursor    = $tag[1] + strlen( $tag[0] );
        }

        return $stripped . substr( $result, $cursor );
    }

    /**
     * Closing </span> tags without an opening span before them.
     *
     * @param string $string
     * @return array List of array( tag, byte offset ).
     */
    protected static function orphan_closing_tags( $string ) {
        $orphans = array();
        $depth   = 0;
        if ( preg_match_all( self::SPAN_TAG_REGEX, $string, $tags, PREG_OFFSET_CAPTURE ) ) {
            foreach ( $tags[0] as $tag ) {
                $change = self::tag_depth_change( $tag[0] );
                if ( $change >= 0 ) {
                    $depth += $change;
                } elseif ( $depth > 0 ) {
                    $depth--;
                } else {
                    $orphans[] = $tag;
                }
            }
        }
        return $orphans;
    }
}
