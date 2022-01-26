

<?php
if (($sc_args->getMap_event() == 'TaggedPostsSC')||($sc_args->getMap_event() == 'SetGeotag') || 
   ($sc_args->getMap_event() == 'AddMarker') || ($sc_args->getMap_event() == 'FileSC')) {
  $output .= 'var osm_ext_control = new ol.control.ZoomToExtent({
      extent: [-11243808.051695308, 1.202710291, 9561377.290892059,
6852382.107835932]
    });'. PHP_EOL;

if ($sc_args->getMap_event() == 'TaggedPostsSC'){
  $output .= 'MetaboxEventhandler.TaggedPostsSC('.$MapName.');';
  $output .= $MapName.'.addControl(osm_ext_control);' . PHP_EOL;
}
else if ($sc_args->getMap_event() == 'SetGeotag'){
  $output .= 'MetaboxEventhandler.SetGeotag('.$MapName.','.$post->ID.');';
  $output .= $MapName.'.addControl(osm_ext_control);' . PHP_EOL;
}
else if ($sc_args->getMap_event() == 'AddMarker'){
  $output .= 'MetaboxEventhandler.AddMarker('.$MapName.','.$post->ID.');';
  $output .= $MapName.'.addControl(osm_ext_control);' . PHP_EOL;
}
else if ($sc_args->getMap_event() == 'FileSC'){
  $output .= $MapName.'.addControl(osm_ext_control);' . PHP_EOL;
}
}
?>
