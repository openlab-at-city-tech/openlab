<?php

class CBOX_Group_Type {
	public $slug;

	public function get_slug() {
		return $this->slug;
	}

	public function get_label() {
		switch ( $this->slug ) {
			case 'course' :
				return 'Courses';

			case 'club' :
				return 'Clubs';

			case 'project' :
				return 'Projects';

			case 'portfolio' :
				return 'Portfolios';
		}
	}

	public function get_site_template_id() {
		$site_template_ids = get_option( 'openlab_group_type_default_site_template_ids' );

		if ( isset( $site_template_ids[ $this->get_slug() ] ) ) {
			return (int) $site_template_ids[ $this->get_slug() ];
		}

		// Fall back on the first available.
		$templates = $this->get_site_templates( true );
		if ( $templates ) {
			return $templates[0]['id'];
		}

		return 0;
	}

	public function get_site_templates( $raw = false ) {
		$site_template_categories = $this->get_site_template_categories();
		if ( $site_template_categories ) {
			$category_ids = wp_list_pluck( $site_template_categories, 'term_id' );
		} else {
			$category_ids = [ 0 ];
		}

		$site_template_posts = get_posts(
			[
				'post_type'      => 'cboxol_site_template',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'name',
				'tax_query'      => [
					[
						'taxonomy' => 'cboxol_template_category',
						'terms'    => $category_ids,
						'field'    => 'term_id',
					],
				],
			]
		);

		$this_object = $this;

		$site_templates = array_map(
			function( $template ) use ( $this_object ) {
				return $this_object->get_site_template_info( $template->ID );
			},
			$site_template_posts
		);

		if ( $raw ) {
			return $site_templates;
		}

		/*
		 * Special case: If the current template is not in an associated category
		 * (ie it was unlinked somehow) it should be included in the list.
		 */
		$list_has_linked_template = false;
		$linked_site_template_id  = $this->get_site_template_id();
		foreach ( $site_templates as $site_template ) {
			if ( $linked_site_template_id === $site_template['id'] ) {
				$list_has_linked_template = true;
				break;
			}
		}

		if ( ! $list_has_linked_template ) {
			$site_templates[] = $this->get_site_template_info( $linked_site_template_id );
		}

		$site_templates = array_filter( $site_templates );

		return $site_templates;
	}

	public function get_site_template_categories() {
		return get_terms(
			[
				'taxonomy'   => 'cboxol_template_category',
				'number'     => 0,
				'hide_empty' => false,
				'meta_query' => [
					[
						'key'   => 'cboxol_group_type',
						'value' => $this->get_slug(),
					],
				],
			]
		);
	}

	public function get_site_template_info( $template_id ) {
		$site_id = cboxol_get_template_site_id( $template_id );

		$template = get_post( $template_id );

		if ( ! $site_id || ! $template ) {
			return null;
		}

		return [
			'id'       => $template_id,
			'siteId'   => $site_id,
			'name'     => $template->post_title,
			'url'      => get_home_url( $site_id ),
			'adminUrl' => get_admin_url( $site_id ),
		];
	}
}
