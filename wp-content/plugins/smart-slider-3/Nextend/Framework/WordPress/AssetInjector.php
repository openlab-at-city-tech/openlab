<?php


namespace Nextend\Framework\WordPress;


use Nextend\Framework\Asset\AssetManager;
use Nextend\Framework\Pattern\SingletonTrait;
use Nextend\WordPress\OutputBuffer;

class AssetInjector {

    use SingletonTrait;

    private static $cssComment = '<!--n2css-->';
    private static $jsComment = '<!--n2js-->';

    protected $js = '';
    protected $css = '';

    /**
     * @var OutputBuffer
     */
    protected $outputBuffer;

    private $headTokens = array();

    private $useAlternativeAction = false;

    protected function init() {
        if (is_admin()) {
            $this->useAlternativeAction = true;
        }


        if (defined('WP_CLI') && WP_CLI) {
            //Do not start output buffering while WP_CLI active

        } else {

            $this->outputBuffer = OutputBuffer::getInstance();
            if (defined('SMART_SLIDER_OB_START') && SMART_SLIDER_OB_START >= 0) {
                $this->outputBuffer->setExtraObStart(SMART_SLIDER_OB_START);
            }

            $this->addInjectCSSJSComment();

            add_filter('wordpress_prepare_output', array(
                $this,
                'prepareOutput'
            ));
        }
    }

    public function prepareOutput($buffer) {
        static $once = false;
        if (!$once) {
            $once = true;
            $this->finalizeCssJs();

            if (!empty($this->css)) {
                $n2cssPos = strpos($buffer, self::$cssComment);
                if ($n2cssPos !== false) {
                    $buffer    = substr_replace($buffer, $this->css, $n2cssPos, strlen(self::$cssComment));
                    $this->css = '';
                } else {
                    $parts = preg_split('/<\/head[\s]*>/i', $buffer, 2);
                    // There might be no head and it would result a notice.
                    if (count($parts) == 2) {
                        list($head, $body) = $parts;
                        /**
                         * We must tokenize the HTML comments in the head to prepare for condition CSS/scripts
                         * Eg.: <!--[if lt IE 9]><link rel='stylesheet' href='ie8.css?ver=1.0' type='text/css' media='all'> <![endif]-->
                         */
                        $head = preg_replace_callback('/<!--.*?-->/s', array(
                            $this,
                            'tokenizeHead'
                        ), $head);

                        $head = preg_replace_callback('/<noscript>.*?<\/noscript>/s', array(
                            $this,
                            'tokenizeHead'
                        ), $head);

                        $lastStylesheetPosition = strrpos($head, "<link rel='stylesheet'");
                        if ($lastStylesheetPosition === false) {
                            $lastStylesheetPosition = strrpos($head, "<link rel=\"stylesheet\"");
                            if ($lastStylesheetPosition === false) {
                                $lastStylesheetPosition = strrpos($head, "<link");
                            }
                        }
                        if ($lastStylesheetPosition !== false) {

                            /**
                             * Find the end of the tag <link tag
                             */
                            $lastStylesheetPositionEnd = strpos($head, ">", $lastStylesheetPosition);
                            if ($lastStylesheetPositionEnd !== false) {

                                /**
                                 * Insert CSS after the ending >
                                 */
                                $head      = substr_replace($head, $this->css, $lastStylesheetPositionEnd + 1, 0);
                                $this->css = '';

                                /**
                                 * Restore HTML comments
                                 */
                                $head = preg_replace_callback('/<!--TOKEN([0-9]+)-->/', array(
                                    $this,
                                    'restoreHeadTokens'
                                ), $head);

                                $buffer = $head . '</head>' . $body;
                            }

                        }
                    }
                }
            }

            if (!empty($this->js)) {
                $n2jsPos = strpos($buffer, self::$jsComment);
                if ($n2jsPos !== false) {
                    $buffer   = substr_replace($buffer, $this->js, $n2jsPos, strlen(self::$jsComment));
                    $this->js = '';
                } else {
                    $parts = preg_split('/<\/head[\s]*>/i', $buffer, 2);
                    // There might be no head and it would result a notice.
                    if (count($parts) == 2) {
                        list($head, $body) = $parts;
                        /**
                         * We must tokenize the HTML comments in the head to prepare for condition CSS/scripts
                         * Eg.: <!--[if lt IE 9]><link rel='stylesheet' href='ie8.css?ver=1.0' type='text/css' media='all'> <![endif]-->
                         */
                        $head = preg_replace_callback('/<!--.*?-->/s', array(
                            $this,
                            'tokenizeHead'
                        ), $head);

                        $head = preg_replace_callback('/<noscript>.*?<\/noscript>/s', array(
                            $this,
                            'tokenizeHead'
                        ), $head);

                        $lastStylesheetPosition = strrpos($head, "<link rel='stylesheet'");
                        if ($lastStylesheetPosition === false) {
                            $lastStylesheetPosition = strrpos($head, "<link rel=\"stylesheet\"");
                            if ($lastStylesheetPosition === false) {
                                $lastStylesheetPosition = strrpos($head, "<link");
                            }
                        }
                        if ($lastStylesheetPosition !== false) {

                            /**
                             * Find the end of the tag <link tag
                             */
                            $lastStylesheetPositionEnd = strpos($head, ">", $lastStylesheetPosition);
                            if ($lastStylesheetPositionEnd !== false) {

                                /**
                                 * Insert JS after the ending >
                                 */
                                $head     = substr_replace($head, $this->js, $lastStylesheetPositionEnd + 1, 0);
                                $this->js = '';

                                /**
                                 * Restore HTML comments
                                 */
                                $head = preg_replace_callback('/<!--TOKEN([0-9]+)-->/', array(
                                    $this,
                                    'restoreHeadTokens'
                                ), $head);

                                $buffer = $head . '</head>' . $body;
                            }

                        }
                    }
                }
            }

            if ($this->css != '' || $this->js != '') {
                $parts = preg_split('/<\/head[\s]*>/', $buffer, 2);

                return implode($this->css . $this->js . '</head>', $parts);
            }
        }

        return $buffer;
    }

    public function tokenizeHead($matches) {

        $index = count($this->headTokens);

        $this->headTokens[$index] = $matches[0];

        return '<!--TOKEN' . $index . '-->';
    }

    public function restoreHeadTokens($matches) {

        return $this->headTokens[$matches[1]];
    }

    private function finalizeCssJs() {
        static $finalized = false;
        if (!$finalized) {
            $finalized = true;

            if (class_exists('\\Nextend\\Framework\\Asset\\AssetManager', false)) {
                $this->css = AssetManager::getCSS();
                $this->js  = AssetManager::getJs();
            }
        }

        return true;
    }

    public function addInjectCSSJSComment() {
        if (!$this->useAlternativeAction) {
            add_action('wp_print_scripts', array(
                $this,
                'injectCSSJSComment'
            ));
        } else {
            /**
             * @see SSDEV-3909
             * The Site editor fires the wp_print_scripts action inside a the wp.editSite.initializeEditor script.
             * The Widgets editor fires the wp_print_scripts action inside a the wp.editWidgets.initialize script.
             * The Customizer fires the wp_print_scripts action inside a the wp.customizeWidgets.initialize script.
             */
            add_action('admin_head', array(
                $this,
                'injectCSSJSComment'
            ));

            add_action('customize_controls_print_scripts', array(
                $this,
                'injectCSSJSComment'
            ));
        }
    }

    public function removeInjectCSSJSComment() {
        if (!$this->useAlternativeAction) {
            remove_action('wp_print_scripts', array(
                $this,
                'injectCSSJSComment'
            ));
        } else {
            /**
             * @see SSDEV-3909
             * The Site editor fires the wp_print_scripts action inside a the wp.editSite.initializeEditor script.
             * The Widgets editor fires the wp_print_scripts action inside a the wp.editWidgets.initialize script.
             * The Customizer fires the wp_print_scripts action inside a the wp.customizeWidgets.initialize script.
             */
            remove_action('admin_head', array(
                $this,
                'injectCSSJSComment'
            ));

            remove_action('customize_controls_print_scripts', array(
                $this,
                'injectCSSJSComment'
            ));
        }
    }

    public function injectCSSJSComment() {
        static $once;
        if (!$once) {
            echo wp_kses(self::$cssComment, array());
            echo wp_kses(self::$jsComment, array());
            $once = true;
        }
    }
}