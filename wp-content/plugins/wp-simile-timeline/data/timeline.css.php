<?php
// load WordPress environment
include_once('../../../../wp-load.php');
define('WP_USE_THEMES', false);
// explicit HTTP header 200
header("HTTP/1.1 200 OK");
header("Status: 200 All rosy");

$ether_backgroundColors = get_option('stl_timeline_band_colors');
$stl_band_options = get_option('stl_timeline_band_options');

$stl_timeline_band = new WPSimileTimelineBand();
$wpst_bands = $stl_timeline_band->find_all('id,bg_color,interval_color,ether_highlight_color, highlight_label_color');

#print_r($wpst_bands);

// Create CSS file header
header("Cache-Control: public");
header("Pragma: cache");

$offset = 60*60*24*10; // 10 days
header("Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT");
header("Content-Type: text/css; charset: UTF-8");
?>

/* Dynamic theme used by the plugin (attributes set in options panel)
=====================================================================*/

.dynamic-theme { color: #eee; }
<?php foreach($wpst_bands as $index=>$band): ?>
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-ether-bg { background-color: <?php echo $band->bg_color; ?>; }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-ether-highlight { background-color: <?php echo $band->ether_highlight_color; ?>; }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-date-label-em { color: <?php echo $band->interval_color; ?>;  }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-date-label { color: <?php echo $band->interval_color; ?>; border-color: <?php echo $band->interval_color; ?>;  }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-event-label { color: <?php echo $band->interval_color; ?>; }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-ether-lines { border-color: <?php echo $band->interval_color; ?>; border-style: solid; }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-highlight-label-start{ color: <?php echo $band->highlight_label_color; ?>; }
.dynamic-theme .timeline-band-<?php echo $index; ?> .timeline-highlight-label-end{ color: <?php echo $band->highlight_label_color; ?>; }
<?php endforeach; ?>