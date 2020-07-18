<?php
$loaders = $this->get_setting('loaders');
?>
<div id='OutoftheBox'>
  <div class='OutoftheBox list-container noaccess'>
    <img src="<?php echo $loaders['protected']; ?>" data-src-retina="<?php echo $loaders['protected']; ?>">
    <p><?php echo $this->get_setting('userfolder_noaccess'); ?>.</p>
  </div>
</div>