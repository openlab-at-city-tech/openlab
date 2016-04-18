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
	'calendar_o_calendars'      => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_o_title'          => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_o_mode'           => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'MONTH'),
	'calendar_o_weekstart'      => array('N'=>'1','Y'=>'0','Z'=>'0','value' => '1'),
	'calendar_o_language'       => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_o_timezone'       => array('N'=>'1','Y'=>'0','Z'=>'0','value' => 'none'),
	'calendar_s_enable'         => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '1'),
	'calendar_s_calendars'      => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_s_title'          => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_s_width'          => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'calendar_s_height'         => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'calendar_s_show_title'     => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_s_show_navs'      => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_s_show_date'      => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_s_show_print'     => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_s_show_tabs'      => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_s_show_calendars' => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_s_show_timezone'  => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_enable'         => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '1'),
	'calendar_w_calendars'      => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_w_title'          => array('N'=>'0','Y'=>'0','Z'=>'0','value' => ''),
	'calendar_w_width'          => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'calendar_w_height'         => array('N'=>'1','Y'=>'0','Z'=>'1','value' => 'auto'),
	'calendar_w_show_title'     => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_show_navs'      => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_show_date'      => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_show_print'     => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_show_tabs'      => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_show_calendars' => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
	'calendar_w_show_timezone'  => array('N'=>'1','Y'=>'1','Z'=>'0','value' => '0'),
);