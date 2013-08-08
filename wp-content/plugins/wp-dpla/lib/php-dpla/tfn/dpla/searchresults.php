<?php
	namespace TFN;

	class DPLA_SearchResults
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
		 * The raw search results array.
		 *
		 * @var array
		 */
		protected $_results = null;

		/**
		 * Create a new SearchResults object.
		 *
		 * @param DPLA  $dpla    The DPLA object to use.
		 * @param array $options The array of search options used to generate these results.
		 * @param array $results The raw search results as returned by the API.
		 */
		public function __construct($dpla, $options, $results)
		{
			$this->_dpla = $dpla;
			$this->_options = $options;
			$this->_results = $results;
		}

		/**
		 * Get the total number of matching documents for the search query.
		 *
		 * @return int
		 */
		public function getTotalCount()
		{
			return $this->_results['count'];
		}

		/**
		 * Get the page number that these search results represent.
		 *
		 * @return int
		 */
		public function getPageNumber()
		{
			return ($this->_results['start'] / $this->getPerPage()) + 1;
		}

		/**
		 * Get the number of results per page.
		 *
		 * @return int
		 */
		public function getPerPage()
		{
			return $this->_results['limit'];
		}

		/**
		 * Get a SearchQuery object that will run the same query that produced
		 * these results.
		 *
		 * @return DPLA_SearchQuery
		 */
		public function getSearchQuery()
		{
			return $this->_dpla->createSearchQuery($this->_options);
		}

		/**
		 * Get a SearchQuery object that represents the previous page for this search.
		 *
		 * @return DPLA_SearchQuery
		 * @throws Exception If this is the first page.
		 */
		public function getPrevPageSearchQuery()
		{
			if ($this->_results['start'] <= 0) {
				throw new Exception('Cannot get page 0!');
			}
			return $this->_dpla->createSearchQuery($this->_options)->withPaging($this->getPageNumber() - 1, $this->_results['limit']);
		}

		/**
		 * Get a SearchResults object for the previous page of this search.
		 *
		 * @return DPLA_SearchResults
		 * @throws Exception If this is the first page.
		 */
		public function getPrevPage()
		{
			return $this->getPrevPageSearchQuery()->execute();
		}

		/**
		 * Get a SearchQuery object that represents the next page for this search.
		 *
		 * @return DPLA_SearchQuery
		 * @throws Exception If this is the last page.
		 */
		public function getNextPageSearchQuery()
		{
			if ($this->_results['start'] + $this->_results['limit'] > $this->getTotalCount()) {
				throw new Exception('This is the last page!');
			}
			return $this->_dpla->createSearchQuery($this->_options)->withPaging($this->getPageNumber() + 1, $this->_results['limit']);
		}

		/**
		 * Get a SearchResults object for the next page of this search.
		 *
		 * @return DPLA_SearchResults
		 * @throws Exception If this is the last page.
		 */
		public function getNextPage()
		{
			return $this->getNextPageSearchQuery()->execute();
		}

		/**
		 * Get an array of documents on this page of search results.
		 *
		 * @return array
		 */
		public function getDocuments()
		{
			return $this->_results['docs'];
		}
	}
