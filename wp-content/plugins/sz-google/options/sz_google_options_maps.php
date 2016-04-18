<?php

/**
 * Array definition for plugin options linked to the corresponding
 * module with which will develop the function getOptions()
 *
 * @package SZGoogle
 * @subpackage Options
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Definition array() with all the options connected to the
// module which must be called by an include (setoptions)

return array(
	'maps_language' => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '99'),
	'maps_key'      => array('N'=>'0','Y'=>'0','Z'=>'0','value' => '' ),
	'maps_sensor'   => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'maps_signin'   => array('N'=>'0','Y'=>'1','Z'=>'0','value' => '0'),
	'maps_s_enable' => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '1'),
	'maps_s_lat'    => array('N'=>'1','Y'=>'0','Z'=>'1','value' => '41.9100711'),
	'maps_s_lng'    => array('N'=>'1','Y'=>'0','Z'=>'1','value' => '12.5359979'),
	'maps_s_width'  => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'maps_s_height' => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'maps_s_zoom'   => array('N'=>'1','Y'=>'0','Z'=>'1','value' => '8'),
	'maps_s_view'   => array('N'=>'0','Y'=>'0','Z'=>'0','value' => 'ROADMAP'),
	'maps_s_layer'  => array('N'=>'0','Y'=>'0','Z'=>'0','value' => 'NOTHING'),
	'maps_s_wheel'  => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '1'),
	'maps_s_marker' => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'maps_s_lazy'   => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'maps_w_enable' => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '1'),
	'maps_w_lat'    => array('N'=>'1','Y'=>'0','Z'=>'1','value' => '41.9100711'),
	'maps_w_lng'    => array('N'=>'1','Y'=>'0','Z'=>'1','value' => '12.5359979'),
	'maps_w_width'  => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'maps_w_height' => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'maps_w_zoom'   => array('N'=>'1','Y'=>'0','Z'=>'1','value' => '8'),
	'maps_w_view'   => array('N'=>'0','Y'=>'0','Z'=>'0','value' => 'ROADMAP'),
	'maps_w_layer'  => array('N'=>'0','Y'=>'0','Z'=>'0','value' => 'NOTHING'),
	'maps_w_wheel'  => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '1'),
	'maps_w_marker' => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'maps_w_lazy'   => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
);
