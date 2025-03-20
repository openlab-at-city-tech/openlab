<?php
/**
 * @package wp-content-aware-engine
 * @author Joachim Jensen <joachim@dev.institute>
 * @license GPLv3
 * @copyright 2023 by Joachim Jensen
 */

defined('ABSPATH') || exit;

if (!class_exists('WPCAObjectManager')) {
    /**
     * @deprecated
     */
    class WPCAObjectManager extends WPCACollection
    {
    }
}
