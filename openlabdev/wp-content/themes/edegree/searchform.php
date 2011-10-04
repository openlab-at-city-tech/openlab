<form name="search_form" class="search_form" action="<?php bloginfo('url'); ?>/" method="get">
    <input id="s" class="text_input" type="text" onblur="restoreDefault(this)" onfocus="clearDefault(this)" name="s" value="<?php _e('Search')?>"/>
    <input type="submit" align="middle" id="search-submit" value="<?php _e('Go')?>" />
    <input id="searchsubmit" type="hidden" value="Search"/>
</form>