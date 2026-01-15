<?php

use PublishPress\WordPressReviews\ReviewsController;

if (! class_exists('AdvancedGutenberg_Reviews')) {
    class AdvancedGutenberg_Reviews
    {
        /**
         * @var  ReviewsController
         */
        private $reviewController;

        public function __construct()
        {
            $this->reviewController = new ReviewsController(
                'advanced-gutenberg',
                'PublishPress Blocks',
                esc_url(ADVANCED_GUTENBERG_PLUGIN_DIR_URL . 'assets/images/logo-notice.png')
            );
        }

        public function init()
        {
            add_filter('publishpress_wp_reviews_display_banner_publishpress', [ $this, 'shouldDisplayBanner' ]);

            $this->reviewController->init();
        }

        public function shouldDisplayBanner($shouldDisplay)
        {
            if (! is_admin() || ! current_user_can('edit_posts')) {
                return false;
            }

            return true;
        }
    }

    $review = new AdvancedGutenberg_Reviews();
    $review->init();
}
