<?php

namespace PDFEmbedder\Admin\Pages;

abstract class Page {

	public const SLUG = '';

	public abstract function get_title(): string;

	public abstract function content();

	/**
	 * When the object is treated like a string, return the slug.
	 *
	 * @since 4.9.0
	 */
	public function __toString(): string {

		return static::SLUG;
	}
}
