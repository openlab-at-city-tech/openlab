<?php
require_once( dirname(__FILE__) . '../../../../wp-config.php');
require_once( dirname(__FILE__) . '/functions.php');
header("Content-type: text/css");
global $options;
foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { ${$value['id']} = $value['std']; } else { ${$value['id']} = get_settings( $value['id'] ); } }
echo $ahstheme_customcss;
?>
