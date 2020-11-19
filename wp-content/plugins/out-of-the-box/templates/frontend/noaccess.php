<?php
$loaders = $this->get_setting('loaders');
?>
<div id='OutoftheBox'>
  <div class='OutoftheBox list-container noaccess'>
    <div style="width:50%; margin: 0 auto; text-align:center;">
      <img src="<?php echo $loaders['protected']; ?>" data-src-retina="<?php echo $loaders['protected']; ?>" style="width: 256px;">
      <p><?php echo $this->get_setting('userfolder_noaccess'); ?>.</p>
    </div>
  </div>
</div>