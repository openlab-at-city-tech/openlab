<?php

	// Require admin functions
	require( dirname(__FILE__) . '/../admin/admin.functions.php' );

    class ZotpressSidebarWidget extends WP_Widget
    {
        function __construct()
        {
            $widget_ops = array('description' => __('Display your citations on your sidebar','zotpress'));
			parent::__construct(false, __('Zotpress Widget','zotpress'), $widget_ops);
        }

        function widget( $args, $instance )
        {
            extract( $args );

            // ARGUMENTS
            $widget_title = apply_filters('widget_title', $instance['widget_title'] );

            $api_user_id = $instance['api_user_id'];

            $author = isset( $instance['author'] ) ? $instance['author'] : false;
            $year = isset( $instance['year'] ) ? $instance['year'] : false;

            $data_type = isset( $instance['data_type'] ) ? $instance['data_type'] : "items";
            $collection_id = isset( $instance['collection_id'] ) ? $instance['collection_id'] : false;
            $item_key = isset( $instance['item_key'] ) ? $instance['item_key'] : false;
            $tag_name = isset( $instance['tag_name'] ) ? $instance['tag_name'] : false;

            $style = isset( $instance['style'] ) ? $instance['style'] : "apa";
            $limit = isset( $instance['limit'] ) ? $instance['limit'] : "false";

            $inclusive = isset( $instance['inclusive'] ) ? $instance['inclusive'] : false;
            $sort = isset( $instance['sort'] ) ? $instance['sort'] : false;
            $sortby = isset( $instance['sortby'] ) ? $instance['sortby'] : false;
			if ($sortby == "default")
			$sortby = false;

            $image = isset( $instance['image'] ) ? $instance['image'] : "no";
            $download = isset( $instance['download'] ) ? $instance['download'] : "no";
            $title = isset( $instance['zptitle'] ) ? $instance['zptitle'] : "no";
            $cite = isset( $instance['zpcite'] ) ? $instance['zpcite'] : "no";
            $notes = isset( $instance['zpnotes'] ) ? $instance['zpnotes'] : "no";


            // Required for theme
            echo $before_widget;

            if ($widget_title) echo $before_title . $widget_title . $after_title;


			echo "<div class=\"zp-ZotpressSidebarWidget\">\n\n";

			$zp_sidebar_shortcode = "[zotpress";

			if ($api_user_id)	{ $zp_sidebar_shortcode .= " userid='$api_user_id' "; }
			if ($author)		{ $zp_sidebar_shortcode .= " author='$author' "; }
			if ($year)		{ $zp_sidebar_shortcode .= " year='$year' "; }
			if ($data_type)	{ $zp_sidebar_shortcode .= " datatype='$data_type' "; }
			if ($collection_id)	{ $zp_sidebar_shortcode .= " collection='$collection_id' "; }
			if ($item_key)	{ $zp_sidebar_shortcode .= " item='$item_key' "; }
			if ($tag_name)	{ $zp_sidebar_shortcode .= " tag='$tag_name' "; }
			if ($style)		{ $zp_sidebar_shortcode .= " style='$style' "; }
			if ($limit)		{ $zp_sidebar_shortcode .= " limit='$limit' "; }
			if ($sort)		{ $zp_sidebar_shortcode .= " order='$sort' "; }
			if ($sortby)		{ $zp_sidebar_shortcode .= " sortby='$sortby' "; }
			if ($image)		{ $zp_sidebar_shortcode .= " showimage='$image' "; }
			if ($download)	{ $zp_sidebar_shortcode .= " download='$download' "; }
			if ($title)		{ $zp_sidebar_shortcode .= " title='$title' "; }
			if ($cite)		{ $zp_sidebar_shortcode .= " cite='$cite' "; }
			if ($notes)		{ $zp_sidebar_shortcode .= " note='$notes' "; }
			if ($inclusive)		{ $zp_sidebar_shortcode .= " inclusive='$inclusive' "; }

			$zp_sidebar_shortcode = trim($zp_sidebar_shortcode) . "]";

			echo do_shortcode($zp_sidebar_shortcode);

			echo "</div><!-- .zp-ZotpressSidebarWidget -->\n\n";


            // Required for theme
            echo $after_widget;
        }



        function update( $new_instance, $old_instance )
        {
            $instance = $old_instance;

            $instance['widget_title'] = strip_tags( $new_instance['widget_title'] );

            $instance['api_user_id'] = strip_tags( $new_instance['api_user_id'] );

            $instance['author'] = str_replace(" ", "+", strip_tags($new_instance['author']));
            $instance['year'] = str_replace(" ", "+", strip_tags($new_instance['year']));

            $instance['data_type'] = strip_tags( $new_instance['data_type'] );
            $instance['collection_id'] = strip_tags($new_instance['collection_id']);
            $instance['item_key'] = strip_tags($new_instance['item_key']);
            $instance['tag_name'] = str_replace(" ", "+", strip_tags($new_instance['tag_name']));

            $instance['style'] = strip_tags($new_instance['style']);
            $instance['inclusive'] = strip_tags($new_instance['inclusive']);
            $instance['sort'] = strip_tags($new_instance['sort']);
            $instance['sortby'] = strip_tags($new_instance['sortby']);

            $instance['limit'] = strip_tags($new_instance['limit']);

            $instance['image'] = strip_tags($new_instance['image']);
            $instance['download'] = strip_tags($new_instance['download']);
            $instance['zptitle'] = strip_tags($new_instance['zptitle']);
            $instance['zpcite'] = strip_tags($new_instance['zpcite']);
            $instance['zpnotes'] = strip_tags($new_instance['zpnotes']);

            return $instance;
        }



        function form( $instance )
        {
			// Set form defaults
			if ( ! isset($instance) || count($instance) == 0)
			{
				$instance['widget_title'] = "";

				$instance['author'] = "";
				$instance['year'] = "";

				$instance['collection_id'] = "";
				$instance['item_key'] = "";
				$instance['tag_name'] = "";

	            $instance['style'] = "";
	            $instance['limit'] = "";
			}

            ?>

                <style type="text/css">
                <!--
					#zp-Sidebar-Widget-Container select {
						background-color: #fff;
						display: block;
					}
                    div.zp-ZotpressSidebarWidget-Required span.req {
                        color: #CC0066;
                        font-weight: bold;
                        font-size: 1.4em;
						padding-left: 0.25em;
                        vertical-align: -35%;
                    }

                    div.zp-ZotpressSidebarWidget-Required {
                        background-color: #fcfcfc;
						border: 1px solid red;
                        margin: 0 0 10px 0;
                        padding: 10px;
                        border-radius: 5px;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                    }
                -->
                </style>

				<div id="zp-Sidebar-Widget-Container">

					<p>
						<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>"><?php _e('Widget Title','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" type="text" value="<?php echo $instance['widget_title']; ?>" class="widefat">
					</p>

					<div class="zp-ZotpressSidebarWidget-Required">

					<?php

					if ( zp_get_total_accounts() > 0 )
						if ( isset( $instance['api_user_id'] ) )
							echo zp_get_accounts( false, true, true, $this->get_field_id('api_user_id'), $this->get_field_name('api_user_id'), $instance['api_user_id'] );
						else
							echo zp_get_accounts( false, true, true, $this->get_field_id('api_user_id'), $this->get_field_name('api_user_id'), false );
					?>

					</div>

					<p>
						<label for="<?php echo $this->get_field_id( 'data_type' ); ?>"><?php _e('Data Type','zotpress'); ?>:</label>
						<select id="<?php echo $this->get_field_id( 'data_type' ); ?>" name="<?php echo $this->get_field_name( 'data_type' ); ?>" class="widefat">
							<option value="items" <?php if ( isset( $instance['data_type'] ) && 'items' == $instance['data_type'] ) echo 'selected="selected"'; ?>><?php _e('Items','zotpress'); ?></option>
							<option value="tags" <?php if ( isset( $instance['data_type'] ) && 'tags' == $instance['data_type'] ) echo 'selected="selected"'; ?>><?php _e('Tags','zotpress'); ?></option>
							<option value="collections" <?php if ( isset( $instance['data_type'] ) && 'collections' == $instance['data_type'] ) echo 'selected="selected"'; ?>><?php _e('Collections','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'author' ); ?>"><?php _e('Limit to Author','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" type="text" value="<?php echo $instance['author']; ?>" class="widefat" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'year' ); ?>"><?php _e('Limit by Year','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'year' ); ?>" name="<?php echo $this->get_field_name( 'year' ); ?>" type="text" value="<?php echo $instance['year']; ?>" class="widefat" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'collection_id' ); ?>"><?php _e('Limit to Collection (ID)','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'collection_id' ); ?>" name="<?php echo $this->get_field_name( 'collection_id' ); ?>" type="text" value="<?php echo $instance['collection_id']; ?>" class="widefat" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'item_key' ); ?>"><?php _e('Limit to Item (Key)','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'item_key' ); ?>" name="<?php echo $this->get_field_name( 'item_key' ); ?>" type="text" value="<?php echo $instance['item_key']; ?>" class="widefat" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'tag_name' ); ?>"><?php _e('Limit to Tag','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'tag_name' ); ?>" name="<?php echo $this->get_field_name( 'tag_name' ); ?>" type="text" value="<?php echo $instance['tag_name']; ?>" class="widefat" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e('Style','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" type="text" value="<?php echo $instance['style']; ?>" class="widefat" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'sortby' ); ?>"><?php _e('Sort By','zotpress'); ?>:</label>
						<select id="<?php echo $this->get_field_id( 'sortby' ); ?>" name="<?php echo $this->get_field_name( 'sortby' ); ?>" class="widefat">
							<option value="default"><?php _e('Default','zotpress'); ?></option>
							<option value="author" <?php if ( isset( $instance['sortby'] ) && 'author' == $instance['sortby'] ) echo 'selected="selected"'; ?>><?php _e('Author','zotpress'); ?></option>
							<option value="date" <?php if ( isset( $instance['sortby'] ) && 'date' == $instance['sortby'] ) echo 'selected="selected"'; ?>><?php _e('Date','zotpress'); ?></option>
							<option value="title" <?php if ( isset( $instance['sortby'] ) && 'title' == $instance['sortby'] ) echo 'selected="selected"'; ?>><?php _e('Title','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'sort' ); ?>"><?php _e('Sort Order','zotpress'); ?>:</label>
						<select id="<?php echo $this->get_field_id( 'sort' ); ?>" name="<?php echo $this->get_field_name( 'sort' ); ?>" class="widefat">
							<option value="desc" <?php if ( isset( $instance['sort'] ) && 'desc' == $instance['sort'] ) echo 'selected="selected"'; ?>><?php _e('Desc','zotpress'); ?></option>
							<option value="asc" <?php if ( isset( $instance['sort'] ) && 'asc' == $instance['sort'] ) echo 'selected="selected"'; ?>><?php _e('Asc','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e('Limit','zotpress'); ?>:</label>
						<input id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo $instance['limit']; ?>" class="widefat">
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'inclusive' ); ?>"><?php _e('Inclusive Filtering','zotpress'); ?>?:</label>
						<select id="<?php echo $this->get_field_id( 'inclusive' ); ?>" name="<?php echo $this->get_field_name( 'inclusive' ); ?>" class="widefat">
							<option value="yes" <?php if ( isset( $instance['inclusive'] ) && 'yes' == $instance['inclusive'] ) echo 'selected="selected"'; ?>><?php _e('Yes','zotpress'); ?></option>
							<option value="no" <?php if ( isset( $instance['inclusive'] ) && 'no' == $instance['inclusive'] ) echo 'selected="selected"'; ?>><?php _e('No','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e('Show Image','zotpress'); ?>?:</label>
						<select id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" class="widefat">
							<option value="no" <?php if ( isset( $instance['image'] ) && 'no' == $instance['image'] ) echo 'selected="selected"'; ?>><?php _e('No','zotpress'); ?></option>
							<option value="yes" <?php if ( isset( $instance['image'] ) && 'yes' == $instance['image'] ) echo 'selected="selected"'; ?>><?php _e('Yes','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'download' ); ?>"><?php _e('Downloadable','zotpress'); ?>?:</label>
						<select id="<?php echo $this->get_field_id( 'download' ); ?>" name="<?php echo $this->get_field_name( 'download' ); ?>" class="widefat">
							<option value="no" <?php if ( isset( $instance['download'] ) && 'no' == $instance['download'] ) echo 'selected="selected"'; ?>><?php _e('No','zotpress'); ?></option>
							<option value="yes" <?php if ( isset( $instance['download'] ) && 'yes' == $instance['download'] ) echo 'selected="selected"'; ?>><?php _e('Yes','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'zptitle' ); ?>"><?php _e('Show Title','zotpress'); ?>?:</label>
						<select id="<?php echo $this->get_field_id( 'zptitle' ); ?>" name="<?php echo $this->get_field_name( 'zptitle' ); ?>" class="widefat">
							<option value="no" <?php if ( isset( $instance['zptitle'] ) && 'no' == $instance['zptitle'] ) echo 'selected="selected"'; ?>><?php _e('No','zotpress'); ?></option>
							<option value="yes" <?php if ( isset( $instance['zptitle'] ) && 'yes' == $instance['zptitle'] ) echo 'selected="selected"'; ?>><?php _e('Yes','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'zpnotes' ); ?>"><?php _e('Show Notes','zotpress'); ?>?:</label>
						<select id="<?php echo $this->get_field_id( 'zpnotes' ); ?>" name="<?php echo $this->get_field_name( 'zpnotes' ); ?>" class="widefat">
							<option value="no" <?php if ( isset( $instance['zpnotes'] ) && 'no' == $instance['zpnotes'] ) echo 'selected="selected"'; ?>><?php _e('No','zotpress'); ?></option>
							<option value="yes" <?php if ( isset( $instance['zpnotes'] ) && 'yes' == $instance['zpnotes'] ) echo 'selected="selected"'; ?>><?php _e('Yes','zotpress'); ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'zpcite' ); ?>"><?php _e('Cite with RIS','zotpress'); ?>?:</label>
						<select id="<?php echo $this->get_field_id( 'zpcite' ); ?>" name="<?php echo $this->get_field_name( 'zpcite' ); ?>" class="widefat">
							<option value="no" <?php if ( isset( $instance['zpcite'] ) && 'no' == $instance['zpcite'] ) echo 'selected="selected"'; ?>><?php _e('No','zotpress'); ?></option>
							<option value="yes" <?php if ( isset( $instance['zpcite'] ) && 'yes' == $instance['zpcite'] ) echo 'selected="selected"'; ?>><?php _e('Yes','zotpress'); ?></option>
						</select>
					</p>

				</div> <!-- #zp-Sidebar-Widget-Container -->

            <?php
        }
    }

    function ZotpressSidebarWidgetInit() {
        register_widget( 'ZotpressSidebarWidget' );
    }

?>
