<?php
defined('ABSPATH') || die;

if (!class_exists('WC_REST_Products_Controller')) {
    return;
}

/**
 * Controller for getting Woo Products info
 */
class AdvgbProductsController extends WC_REST_Products_Controller
{
    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'agwc/v1';

    /**
     * Make extra product orderby features supported by WooCommerce available to the WC API.
     * This includes 'price', 'popularity', and 'rating'.
     *
     * @param WP_REST_Request $request Request data.
     *
     * @return array
     */
    protected function prepare_objects_query($request) // phpcs:ignore -- PSR1.Methods.CamelCapsMethodName.NotCamelCaps - WooCommerce function
    {
        $args = parent::prepare_objects_query($request);

        $orderby = $request->get_param('orderby');
        $order   = $request->get_param('order');

        $ordering_args   = WC()->query->get_catalog_ordering_args($orderby, $order);
        $args['orderby'] = $ordering_args['orderby'];
        $args['order']   = $ordering_args['order'];
        if ($ordering_args['meta_key']) {
            $args['meta_key'] = $ordering_args['meta_key'];
        }

        return $args;
    }

    /**
     * Add new options for 'orderby' to the collection params.
     *
     * @return array
     */
    public function get_collection_params() // phpcs:ignore -- PSR1.Methods.CamelCapsMethodName.NotCamelCaps - WooCommerce function
    {
        $params                    = parent::get_collection_params();
        $params['orderby']['enum'] = array_merge($params['orderby']['enum'], array('price', 'popularity', 'rating'));

        return $params;
    }
}
