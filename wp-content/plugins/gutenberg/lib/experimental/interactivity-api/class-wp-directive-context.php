<?php
/**
 * Context data implementation.
 *
 * @package Gutenberg
 * @subpackage Interactivity API
 */

if ( class_exists( 'WP_Directive_Context' ) ) {
	return;
}

/**
 * This is a data structure to hold the current context.
 *
 * Whenever encountering a `data-wp-context` directive, we need to update
 * the context with the data found in that directive. Conversely,
 * when "leaving" that context (by encountering a closing tag), we
 * need to reset the context to its previous state. This means that
 * we actually need sort of a stack to keep track of all nested contexts.
 *
 * Example:
 *
 * <div data-wp-context='{ "foo": 123 }'>
 *     <!-- foo should be 123 here. -->
 *     <div data-wp-context='{ "foo": 456 }'>
 *         <!-- foo should be 456 here. -->
 *     </div>
 *     <!-- foo should be reset to 123 here. -->
 * </div>
 */
class WP_Directive_Context {
	/**
	 * The stack used to store contexts internally.
	 *
	 * @var array An array of contexts.
	 */
	protected $stack = array( array() );

	/**
	 * Constructor.
	 *
	 * Accepts a context as an argument to initialize this with.
	 *
	 * @param array $context A context.
	 */
	public function __construct( $context = array() ) {
		$this->set_context( $context );
	}

	/**
	 * Return the current context.
	 *
	 * @return array The current context.
	 */
	public function get_context() {
		return end( $this->stack );
	}

	/**
	 * Set the current context.
	 *
	 * @param array $context The context to be set.
	 *
	 * @return void
	 */
	public function set_context( $context ) {
		array_push(
			$this->stack,
			array_replace_recursive( $this->get_context(), $context )
		);
	}

	/**
	 * Reset the context to its previous state.
	 *
	 * @return void
	 */
	public function rewind_context() {
		array_pop( $this->stack );
	}
}
