<?php

	/**
	 * Helper class to make paginating our records easy
	 * @author Luke <mugapedia@gmail.com>
	 * @date June 8, 2014
	 */
	class Pagination
	{
		/**
		 * The current page we are in
		 * @var int
		 */
		public $curr_page;
		
		/**
		 * The no. of records per page
		 * @var int
		 */
		public $per_page;
		
		/**
		 * The total no. of records
		 * @var int
		 */
		public $total_count;
		
		/**
		 * Magic construct
		 * @param int $page The current page
		 * @param int $per_page The no. of records per page
		 * @param int $total_count The total no. of records
		 */
		function __construct($page=1, $per_page=20, $total_count=0)
		{
			$this->curr_page   = (int)$page;
			$this->per_page    = (int)$per_page;
			$this->total_count = (int)$total_count;
		}	
		
		/**
		 * Method to get the no. of records to jump be4 we start counting
		 * @retun int
		 */
		public function offset()
		{
			// Assuming 20 items per page
			// page 1 has an offset of 0 (1-1) * 20
			// page 2 has an offset of 20 (2-1) * 20
			// in other words page 2 starts with item 21
			return ($this->curr_page - 1) * $this->per_page;
		}
		
		/**
		 * Method to return the total no. of pages
		 * @return int
		 */
		public function totalPages()
		{
			return ceil($this->total_count / $this->per_page);
		}
		
		/**
		 * Method to return the previous page
		 * @return int
		 */
		public function previousPage()
		{
			return ($this->curr_page - 1);
		}
		
		/**
		 * Method to return the next page
		 * @retun int
		 */
		public function nextPage()
		{
			return ($this->curr_page + 1);
		}
		
		/**
		 * Method to check whether a previous page exists
		 * @retun boolean
		 */
		public function hasPreviousPage()
		{
			return $this->previousPage() >= 1 ? true : false;
		}
		
		/**
		 * Method to check whether a next page exists
		 * @return boolean
		 */
		public function hasNextPage()
		{
			return $this->nextPage() <= $this->totalPages() ? true : false;
		}
	}

?>