<?php $search_text = __('Type and press enter to search.', 'themetrust'); ?>
<form method="get" id="searchform"   action="<?php bloginfo('url'); ?>/">
<input type="text" value="<?php echo $search_text; ?>"   name="s" id="s"
onblur="if (this.value == '')   {this.value = '<?php echo $search_text; ?>';}"
onfocus="if (this.value == '<?php echo $search_text; ?>'){this.value = '';}" />
<input type="hidden" id="searchsubmit" />
</form>