<?php

/**
 * Save category meta
 *
 * Callback function to save category meta data
 *
 * @since  1.0
 */

add_action( 'edited_category', 'johannes_save_category_meta_fields', 10, 2 );
add_action( 'create_category', 'johannes_save_category_meta_fields', 10, 2 );

if ( !function_exists( 'johannes_save_category_meta_fields' ) ) :
	function johannes_save_category_meta_fields( $term_id ) {

		if ( isset( $_POST['johannes'] ) ) {

			$meta = array();

			if ( isset( $_POST['johannes']['image'] ) ) {
				$meta['image'] = $_POST['johannes']['image'];
			}

			if ( isset( $_POST['johannes']['settings'] ) ) {
				$meta['settings'] = $_POST['johannes']['settings'];

				if ( $_POST['johannes']['settings'] == 'custom' ) {

					if ( isset( $_POST['johannes']['layout'] ) ) {
						$meta['layout'] = $_POST['johannes']['layout'];
					}

					if ( isset( $_POST['johannes']['loop'] ) ) {
						$meta['loop'] = $_POST['johannes']['loop'];
					}

					if ( isset( $_POST['johannes']['pagination'] ) ) {
						$meta['pagination'] = $_POST['johannes']['pagination'];
					}

					if ( isset( $_POST['johannes']['ppp_num'] ) ) {
						$meta['ppp_num'] = absint( $_POST['johannes']['ppp_num'] );
					}

					if ( isset( $_POST['johannes']['sidebar'] ) ) {
						$meta['sidebar'] = $_POST['johannes']['sidebar'];
					}
				}

			}


			if ( !empty( $meta ) ) {
				update_term_meta( $term_id, '_johannes_meta', $meta );
			} else {
				delete_term_meta( $term_id, '_johannes_meta' );
			}

		}

	}
endif;


/**
 * Add category meta
 *
 * Callback function to load category meta fields on "new category" screen
 *
 * @since  1.0
 */

add_action( 'category_add_form_fields', 'johannes_category_add_meta_fields', 10, 2 );

if ( !function_exists( 'johannes_category_add_meta_fields' ) ) :
	function johannes_category_add_meta_fields() {
		$meta = johannes_get_category_meta();
		$loops = johannes_get_post_layouts();
		$layouts = johannes_get_archive_layouts();
		$paginations = johannes_get_pagination_layouts();
		$sidebar_layouts = johannes_get_sidebar_layouts();
		$sidebars = johannes_get_sidebars_list();
?>
        <div class="form-field johannes-opt-display">
            <label><?php esc_html_e( 'Display settings', 'johannes' ); ?></label>
            <label>
                <input type="radio" class="johannes-settings-type" name="johannes[settings]" value="inherit" <?php checked( $meta['settings'], 'inherit' ); ?>>
				<?php esc_html_e( 'Inherit from Category theme options', 'johannes' ); ?>
            </label>
            <label>
                <input type="radio" name="johannes[settings]" value="custom" <?php checked( $meta['settings'], 'custom' ); ?>>
				<?php esc_html_e( 'Customize', 'johannes' ); ?>
            </label>

        </div>

        <?php $class = $meta['settings'] == 'custom' ? '' : 'johannes-hidden'; ?>

        <div class="form-field johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">

			<label><?php esc_html_e( 'Template layout', 'johannes' ); ?></label>

		    <p>
		    	<ul class="johannes-img-select-wrap">
				  	<?php foreach ( $layouts as $id => $layout ): ?>
				  		<li>
				  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['layout'], 'selected' ) ); ?>">
				  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
				  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[layout]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['layout'] );?>/>
				  		</li>
				  	<?php endforeach; ?>
			    </ul>
		    	<small class="howto"><?php esc_html_e( 'Choose a layout', 'johannes' ); ?></small>
		    </p>

	    </div>

        <div class="form-field johannes-opt-layouts johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">

			<label><?php esc_html_e( 'Posts layout', 'johannes' ); ?></label>

		    <p>
		    	<ul class="johannes-img-select-wrap">
				  	<?php foreach ( $loops as $id => $layout ): ?>
				  		<li>
				  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['loop'], 'selected' ) ); ?>" data-sidebar="<?php echo absint( johannes_loop_has_sidebar($id) ); ?>">
				  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
				  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[loop]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['loop'] );?>/>
				  		</li>
				  	<?php endforeach; ?>
			    </ul>
		    	<small class="howto"><?php esc_html_e( 'Choose a layout', 'johannes' ); ?></small>
		    </p>

	    </div>

	    <div class="form-field johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<label><?php esc_html_e( 'Post per page', 'johannes' ); ?></label>
		    <p>
		  		<input type="number" class="johannes-count-me small-text" name="johannes[ppp_num]" value="<?php echo absint( $meta['ppp_num'] ); ?>"/>
		    </p>
        </div>

        <?php $sidebar_class = johannes_loop_has_sidebar( $meta['loop'] ) ? '' : 'johannes-opt-disabled'; ?>
        <div class="form-field johannes-opt-sidebar <?php echo esc_attr( $sidebar_class ); ?> johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<label><?php esc_html_e( 'Sidebar', 'johannes' ); ?></label>
			    <p>
			    	<ul class="johannes-img-select-wrap">
					  	<?php foreach ( $sidebar_layouts as $id => $layout ): ?>
					  		<li>
					  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['sidebar']['position'], 'selected' ) ); ?>">
					  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
					  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[sidebar][position]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['sidebar']['position'] );?>/>
					  		</li>
					  	<?php endforeach; ?>
			    	</ul>
			    	<small class="howto"><?php esc_html_e( 'Choose sidebar position', 'johannes' ); ?></small>
			    	<br/>
			    </p>

			    <p>
				    <select name="johannes[sidebar][classic]" class="johannes-count-me">
					  	<?php foreach ( $sidebars as $id => $sidebar ): ?>
					  		<option class="johannes-count-me" value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $meta['sidebar']['classic'] );?>><?php echo esc_html( $sidebar ); ?></option>
					  	<?php endforeach; ?>
				  	</select>
			    	<small class="howto"><?php esc_html_e( 'Choose a regular sidebar to display', 'johannes' ); ?></small>
			    	<br/>
		    	</p>

		    	<p>
			    	<select name="johannes[sidebar][sticky]" class="johannes-count-me">
					  	<?php foreach ( $sidebars as $id => $sidebar ): ?>
					  		<option class="johannes-count-me" value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $meta['sidebar']['sticky'] );?>><?php echo esc_html( $sidebar ); ?></option>
					  	<?php endforeach; ?>
				  	</select>
			    	<small class="howto"><?php esc_html_e( 'Choose a sticky sidebar to display', 'johannes' ); ?></small>
			    	<br/>
		    	</p>
        </div>

       
        <div class="form-field johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<label><?php esc_html_e( 'Pagination', 'johannes' ); ?></label>
		    <p>
		    	<ul class="johannes-img-select-wrap">
				  	<?php foreach ( $paginations as $id => $layout ): ?>
				  		<li>
				  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['pagination'], 'selected' ) ); ?>">
				  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
				  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[pagination]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['pagination'] );?>/>
				  		</li>
				  	<?php endforeach; ?>
			    </ul>
		    	<small class="howto"><?php esc_html_e( 'Choose a layout', 'johannes' ); ?></small>
		    </p>
        </div>

        <div class="form-field">
            <label><?php esc_html_e( 'Image', 'johannes' ); ?></label>
			<?php $display = $meta['image'] ? 'initial' : 'none'; ?>
            <p>
                <img id="johannes-image-preview" src="<?php echo esc_url( $meta['image'] ); ?>" style="width: 300px;  border: 2px solid #ebebeb; display:<?php echo esc_attr( $display ); ?>;">
            </p>

            <p>
                <input type="hidden" name="johannes[image]" id="johannes-image-url" value="<?php echo esc_attr( $meta['image'] ); ?>"/>
                <input type="button" id="johannes-image-upload" class="button-secondary" value="<?php esc_html_e( 'Upload', 'johannes' ); ?>"/>
                <input type="button" id="johannes-image-clear" class="button-secondary" value="<?php esc_html_e( 'Clear', 'johannes' ); ?>" style="display:<?php echo esc_attr( $display ); ?>"/>
            </p>

            <p class="description"><?php esc_html_e( 'Upload an image for this category', 'johannes' ); ?></p>
        </div>

		<?php
	}
endif;


/**
 * Edit category meta
 *
 * Callback function to load category meta fields on edit screen
 *
 * @since  1.0
 */

add_action( 'category_edit_form_fields', 'johannes_category_edit_meta_fields', 10, 2 );

if ( !function_exists( 'johannes_category_edit_meta_fields' ) ) :
	function johannes_category_edit_meta_fields( $term ) {
		$meta = johannes_get_category_meta( $term->term_id );
		$loops = johannes_get_post_layouts();
		$layouts = johannes_get_archive_layouts();
		$paginations = johannes_get_pagination_layouts();
		$sidebar_layouts = johannes_get_sidebar_layouts();
		$sidebars = johannes_get_sidebars_list();
?>
        <tr class="form-field johannes-opt-display">
            <th scope="row" valign="top">
                <?php esc_html_e( 'Display settings', 'johannes' ); ?>
            </th>
            <td>
                <label>
                    <input type="radio" name="johannes[settings]" value="inherit" <?php checked( $meta['settings'], 'inherit' ); ?>>
					<?php esc_html_e( 'Inherit from Category theme options', 'johannes' ); ?>
                </label>
                <br/>
                <label>
                    <input type="radio" name="johannes[settings]" value="custom" <?php checked( $meta['settings'], 'custom' ); ?>>
					<?php esc_html_e( 'Customize', 'johannes' ); ?>
                </label>
            </td>
        </tr>
        
        <?php $class = $meta['settings'] == 'custom' ? '' : 'johannes-hidden'; ?>

        <tr class="form-field johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<th scope="row" valign="top">
                <?php esc_html_e( 'Template layout', 'johannes' ); ?>
            </th>
            <td>
			    <p>
			    	<ul class="johannes-img-select-wrap">
					  	<?php foreach ( $layouts as $id => $layout ): ?>
					  		<li>
					  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['layout'], 'selected' ) ); ?>">
					  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
					  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[layout]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['layout'] );?>/>
					  		</li>
					  	<?php endforeach; ?>
				    </ul>
			    	<small class="howto"><?php esc_html_e( 'Choose a layout', 'johannes' ); ?></small>
			    </p>
		    </td>
        </tr>

        <tr class="form-field johannes-opt-layouts johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<th scope="row" valign="top">
                <?php esc_html_e( 'Posts layout', 'johannes' ); ?>
            </th>
            <td>
			    <p>
			    	<ul class="johannes-img-select-wrap">
					  	<?php foreach ( $loops as $id => $layout ): ?>
					  		<li>
					  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['loop'], 'selected' ) ); ?>" data-sidebar="<?php echo absint( johannes_loop_has_sidebar($id) ); ?>">
					  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
					  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[loop]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['loop'] );?>/>
					  		</li>
					  	<?php endforeach; ?>
				    </ul>
			    	<small class="howto"><?php esc_html_e( 'Choose a layout', 'johannes' ); ?></small>
			    </p>
		    </td>
        </tr>

        <tr class="form-field johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<th scope="row" valign="top">
                <?php esc_html_e( 'Post per page', 'johannes' ); ?>
            </th>
            <td>
			    <p>
			  		<input type="number" class="johannes-count-me small-text" name="johannes[ppp_num]" value="<?php echo absint( $meta['ppp_num'] ); ?>"/>
			    </p>
		    </td>
        </tr>

        <?php $sidebar_class = johannes_loop_has_sidebar( $meta['loop'] ) ? '' : 'johannes-opt-disabled'; ?>

        <tr class="form-field johannes-opt-sidebar <?php echo esc_attr( $sidebar_class ); ?> johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<th scope="row" valign="top">
                <?php esc_html_e( 'Sidebar', 'johannes' ); ?>
            </th>
            <td>
			    <p>
			    	<ul class="johannes-img-select-wrap">
					  	<?php foreach ( $sidebar_layouts as $id => $layout ): ?>
					  		<li>
					  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['sidebar']['position'], 'selected' ) ); ?>">
					  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
					  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[sidebar][position]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['sidebar']['position'] );?>/>
					  		</li>
					  	<?php endforeach; ?>
			    	</ul>
			    	<small class="howto"><?php esc_html_e( 'Choose sidebar position', 'johannes' ); ?></small>
			    	<br/>
			    </p>

			    <p>
				    <select name="johannes[sidebar][classic]" class="johannes-count-me">
					  	<?php foreach ( $sidebars as $id => $sidebar ): ?>
					  		<option class="johannes-count-me" value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $meta['sidebar']['classic'] );?>><?php echo esc_html( $sidebar ); ?></option>
					  	<?php endforeach; ?>
				  	</select>
			    	<small class="howto"><?php esc_html_e( 'Choose a regular sidebar to display', 'johannes' ); ?></small>
			    	<br/>
		    	</p>

		    	<p>
			    	<select name="johannes[sidebar][sticky]" class="johannes-count-me">
					  	<?php foreach ( $sidebars as $id => $sidebar ): ?>
					  		<option class="johannes-count-me" value="<?php echo esc_attr( $id ); ?>" <?php selected( $id, $meta['sidebar']['sticky'] );?>><?php echo esc_html( $sidebar ); ?></option>
					  	<?php endforeach; ?>
				  	</select>
			    	<small class="howto"><?php esc_html_e( 'Choose a sticky sidebar to display', 'johannes' ); ?></small>
			    	<br/>
		    	</p>
		    </td>
        </tr>

        

        <tr class="form-field johannes-opt-display-custom <?php echo esc_attr( $class ); ?>">
        	<th scope="row" valign="top">
                <?php esc_html_e( 'Pagination', 'johannes' ); ?>
            </th>
            <td>
			    <p>
			    	<ul class="johannes-img-select-wrap">
					  	<?php foreach ( $paginations as $id => $layout ): ?>
					  		<li>
					  			<img src="<?php echo esc_url( $layout['src'] ); ?>" title="<?php echo esc_attr( $layout['alt'] ); ?>" class="johannes-img-select <?php echo esc_attr( johannes_selected( $id, $meta['pagination'], 'selected' ) ); ?>">
					  			<br/><span><?php echo esc_attr( $layout['alt'] ); ?></span>
					  			<input type="radio" class="johannes-hidden johannes-count-me" name="johannes[pagination]" value="<?php echo esc_attr( $id ); ?>" <?php checked( $id, $meta['pagination'] );?>/>
					  		</li>
					  	<?php endforeach; ?>
				    </ul>
			    	<small class="howto"><?php esc_html_e( 'Choose a layout', 'johannes' ); ?></small>
			    </p>
		    </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
                <?php esc_html_e( 'Image', 'johannes' ); ?>
            </th>
            <td>
				<?php $display = $meta['image'] ? 'initial' : 'none'; ?>
                <p>
                    <img id="johannes-image-preview" src="<?php echo esc_url( $meta['image'] ); ?>" style="width: 300px;  border: 2px solid #ebebeb; display:<?php echo esc_attr( $display ); ?>;">
                </p>

                <p>
                    <input type="hidden" name="johannes[image]" id="johannes-image-url" value="<?php echo esc_url( $meta['image'] ); ?>"/>
                    <input type="button" id="johannes-image-upload" class="button-secondary" value="<?php esc_html_e( 'Upload', 'johannes' ); ?>"/>
                    <input type="button" id="johannes-image-clear" class="button-secondary" value="<?php esc_html_e( 'Clear', 'johannes' ); ?>" style="display:<?php echo esc_attr( $display ); ?>"/>
                </p>

                <p class="description"><?php esc_html_e( 'Upload an image for this category', 'johannes' ); ?></p>
            </td>
        </tr>
		<?php
	}
endif;

?>
