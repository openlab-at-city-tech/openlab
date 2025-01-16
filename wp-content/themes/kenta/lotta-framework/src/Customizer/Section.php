<?php

namespace LottaFramework\Customizer;

abstract class Section {

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var int|mixed
	 */
	protected $priority;

	/**
	 * @var string|null
	 */
	protected $panel;

	/**
	 * @param $id
	 * @param $title
	 * @param int $priority
	 * @param null $panel
	 */
	public function __construct( $id, $title, int $priority = 10, $panel = null ) {
		$this->id       = $id;
		$this->title    = $title;
		$this->priority = $priority;
		$this->panel    = $panel;
	}

	/**
	 * Get section id
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get section args
	 *
	 * @return array
	 */
	public function getSectionArgs() {
		return [
			'title'    => $this->title,
			'priority' => $this->priority,
			'panel'    => $this->panel
		];
	}

	/**
	 * get section controls
	 *
	 * @return array
	 */
	abstract public function getControls();
}