<?php
	namespace TFN;

	class DPLA_SearchQuery
	{
		/**
		 * The DPLA object this class will use to access the API.
		 *
		 * @var DPLA
		 */
		protected $_dpla = null;

		/**
		 * The search options for this query.
		 *
		 * @var array
		 */
		protected $_options = array();

		/**
		 * Create a new SearchQuery object.
		 *
		 * @param DPLA  $dpla    The DPLA object to use.
		 * @param array $options An initial set of search options.
		 */
		public function __construct($dpla, $options = array())
		{
			if (!($dpla instanceof DPLA)) {
				throw new Exception('Please provide an object of type \TFN\DPLA');
			}
			$this->_dpla = $dpla;
			$this->_options = $options;
		}

		/**
		 * Execute this query and return a SearchResults object.
		 *
		 * @return DPLA_SearchResults
		 */
		public function execute()
		{
			if (!class_exists('\TFN\DPLA_SearchResults')) {
				require dirname(__FILE__).'/searchresults.php';
			}
			return new DPLA_SearchResults(
				$this->_dpla,
				$this->_options,
				$this->_dpla->callAPI('items', $this->_options));
		}

		/**
		 * Set the generic search term for the query.
		 *
		 * @param string $var The generic search term to add.
		 * @return DPLA_SearchQuery
		 */
		public function forText($var)
		{
			$this->_options['q'] = $var;
			return $this;
		}

		/**
		 * Set a value against a variable in the sourceResource data.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withSourceResourceField($field, $val)
		{
			$this->_options['sourceResource.'.$field] = $val;
			return $this;
		}

		/**
		 * Set a value to search for in the sourceResource.contributor field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withContributor($val)
		{
			return $this->withSourceResourceField('contributor', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.creator field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withCreator($val)
		{
			return $this->withSourceResourceField('creator', $val);
		}

		/**
		 * Set a date range for the query.
		 *
		 * @param mixed $from An integer timestamp or a string parsable by strtotime().
		 * @param mixed $to   An integer timestamp or a string parsable by strtotime().
		 * @return DPLA_SearchQuery
		 */
		public function withDateRange($from, $to)
		{
			if (intval($from) != $from) {
				$from = strtotime($from);
			}
			if (intval($to) != $to) {
				$to = strtotime($to);
			}
			return
				$this->withMinDate(date('Y', $from), date('m', $from), date('d', $from))
				     ->withMaxDate(date('Y', $to),   date('m', $to),   date('d', $to));
		}

		/**
		 * Set a minimum date for the query.
		 *
		 * @param mixed $year  An integer year or a string parsable by strtotime().
		 * @param int   $month An optional integer month, ignored if $year is a date string.
		 * @param int   $day   An optional integer day, ignored if $year is a date string.
		 * @return DPLA_SearchQuery
		 */
		public function withDateFrom($year, $month = false, $day = false)
		{
			return $this->withSourceResourceField(
				'date.after',
				$year.
					(!$month ? '' : '-'.str_pad($month, 2, '0', STR_PAD_LEFT).
						(!$day ? '' : '-'.str_pad($day, 2, '0', STR_PAD_LEFT))));
		}

		/**
		 * Set a maximum date for the query.
		 *
		 * @param mixed $year  An integer year or a string parsable by strtotime().
		 * @param int   $month An optional integer month, ignored if $year is a date string.
		 * @param int   $day   An optional integer day, ignored if $year is a date string.
		 * @return DPLA_SearchQuery
		 */
		public function withDateTo($year, $month = false, $day = false)
		{
			return $this->withSourceResourceField(
				'date.before',
				$year.
					(!$month ? '' : '-'.str_pad($month, 2, '0', STR_PAD_LEFT).
						(!$day ? '' : '-'.str_pad($day, 2, '0', STR_PAD_LEFT))));
		}

		/**
		 * Set a value to search for in the sourceResource.description field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withDescription($val)
		{
			return $this->withSourceResourceField('description', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.extent field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withExtent($val)
		{
			return $this->withSourceResourceField('extent', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.creator format.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withFormat($val)
		{
			return $this->withSourceResourceField('format', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.publisher field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withPublisher($val)
		{
			return $this->withSourceResourceField('publisher', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.rights field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withRights($val)
		{
			return $this->withSourceResourceField('rights', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.spatial field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withSpatial($val)
		{
			return $this->withSourceResourceField('spatial', $val);
		}

		/**
		 * A central point (latitude and longiture) and a distance that defines
		 * the area within which to search.
		 *
		 * @param string $latitude  The latitude you wish to use (0 to 90).
		 * @param string $longitude The longitude you wish to use (-180 to 180).
		 * @param string $distance  The distance from the point, suffixed with
		 *                          mi(miles) or km(milometers)
		 * @return DPLA_SearchQuery
		 */
		public function withSpatialArea($latitude, $longitude, $distance)
		{
			return
				$this->withSourceResourceField('spatial.coordinates', $latitude.','.$longitude)
				     ->withSourceResourceField('spatial.distance', $distance);
		}

		/**
		 * Set a value to search for in the sourceResource.spatial.city field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withSpatialCity($val)
		{
			return $this->withSourceResourceField('spatial.city', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.spatial.state field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withSpatialState($val)
		{
			return $this->withSourceResourceField('spatial.state', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.spatial.county field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withSpatialCounty($val)
		{
			return $this->withSourceResourceField('spatial.county', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.subject field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withSubject($val)
		{
			return $this->withSourceResourceField('subject', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.title field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withTitle($val)
		{
			return $this->withSourceResourceField('title', $val);
		}

		/**
		 * Set a value to search for in the sourceResource.type field.
		 *
		 * @param string $var The value.
		 * @return DPLA_SearchQuery
		 */
		public function withType($val)
		{
			return $this->withSourceResourceField('type', $val);
		}

		/**
		 * Specify the page and number per page for the query.
		 *
		 * @param int $page     The page number to get.
		 * @param int $per_page The number of results per page (max 99).
		 * @return DPLA_SearchQuery
		 */
		public function withPaging($page, $per_page)
		{
			$this->_options['page'] = $page;
			$this->_options['page_size'] = min(99, $per_page);
			return $this;
		}
	}
