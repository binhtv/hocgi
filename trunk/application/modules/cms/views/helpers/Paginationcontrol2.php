<?php

/**
 * Helper for making easy show a flash
 *
 * @package    Copy from SGN
 * @subpackage Helper
 * @copyright
 * @license
 */
class cms_View_Helper_Paginationcontrol2
{

	public $view;

	/**
	 * Generates an url given the name of a route.
	 *
	 * @access public
	 *
	 * @param  array $urlOptions Options passed to the assemble method of the Route object.
	 * @param  mixed $name The name of a Route to use. If null it will use the current Route
	 * @param  bool $reset Whether or not to reset the route defaults with those provided
	 * @return string Url for the link href attribute.
	 */
	public function paginationcontrol2($currentPageNumber, $totalItem, $numRowPerPage, $partial = null, $category=null, $type=null)
	{
		$pages->totalItemCount = $totalItem;
	 	$pages->first            = 1;
        $pages->current          = $currentPageNumber;
        $pageCount = (integer) ceil($totalItem / $numRowPerPage);
        $pages->last             = $pageCount;
	    $pages->itemCountPerPage = $numRowPerPage;
        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = $currentPageNumber - 1;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = $currentPageNumber + 1;
        }
        // Previous and next
        if ($currentPageNumber - 1 > 0) {
            $pages->previous = $currentPageNumber - 1;
        }

        if ($currentPageNumber + 1 <= $pageCount) {
            $pages->next = $currentPageNumber + 1;
        }
	 	if ($pageCount >1) {
		        $pageRange = 6;
		        $pageNumber = $currentPageNumber;
		        if ($pageRange > $pageCount) {
		            $pageRange = $pageCount;
		        }
				$pageCount = $pageCount + 1;
		        $delta = ceil($pageRange / 2);
		
		        if ($pageNumber - $delta > $pageCount - $pageRange) {
		            $lowerBound = $pageCount - $pageRange + 1;
		            $upperBound = $pageCount;
		        } else {
		            if ($pageNumber - $delta < 0) {
		                $delta = $pageNumber;
		            }
		
		            $offset     = $pageNumber - $delta;
		            $lowerBound = $offset + 1;
		            $upperBound = $offset + $pageRange;
		        	if ($lowerBound < 1) {
            			$lowerBound = 1;
        			} else if ( $lowerBound > $pageCount) {
        				$lowerBound = $pageCount;
        			}
		        	if ($upperBound < 1) {
            			$upperBound = 1;
        			} else if ( $upperBound > $pageCount) {
        				$upperBound = $pageCount;
        			}
		        }
				for($i = $lowerBound; $i< $upperBound; $i++) {
					$pages->pagesInRange[$i] = $i;
				}
				$pages->firstPageInRange = min($pages->pagesInRange);
       		    $pages->lastPageInRange  = max($pages->pagesInRange);
       		    $pages->limit = $numRowPerPage;
       		    $pages->category = $category;
       		    $pages->type = $type;
	 	}
	
		return $this->view->partial($partial, $pages);
	}
	
	
	public function paginationcontrolcourse($currentPageNumber, $totalItem, $numRowPerPage, $partial = null, $name=null, $tuition_from = null, $tuition_to = null, $city=null)
	{
		$pages->totalItemCount = $totalItem;
		$pages->first            = 1;
		$pages->current          = $currentPageNumber;
		$pageCount = (integer) ceil($totalItem / $numRowPerPage);
		$pages->last             = $pageCount;
		$pages->itemCountPerPage = $numRowPerPage;
		// Previous and next
		if ($currentPageNumber - 1 > 0) {
			$pages->previous = $currentPageNumber - 1;
		}
	
		if ($currentPageNumber + 1 <= $pageCount) {
			$pages->next = $currentPageNumber + 1;
		}
		// Previous and next
		if ($currentPageNumber - 1 > 0) {
			$pages->previous = $currentPageNumber - 1;
		}
	
		if ($currentPageNumber + 1 <= $pageCount) {
			$pages->next = $currentPageNumber + 1;
		}
		if ($pageCount >1) {
			$pageRange = 6;
			$pageNumber = $currentPageNumber;
			if ($pageRange > $pageCount) {
				$pageRange = $pageCount;
			}
			$pageCount = $pageCount + 1;
			$delta = ceil($pageRange / 2);
	
			if ($pageNumber - $delta > $pageCount - $pageRange) {
				$lowerBound = $pageCount - $pageRange + 1;
				$upperBound = $pageCount;
			} else {
				if ($pageNumber - $delta < 0) {
					$delta = $pageNumber;
				}
	
				$offset     = $pageNumber - $delta;
				$lowerBound = $offset + 1;
				$upperBound = $offset + $pageRange;
				if ($lowerBound < 1) {
					$lowerBound = 1;
				} else if ( $lowerBound > $pageCount) {
					$lowerBound = $pageCount;
				}
				if ($upperBound < 1) {
					$upperBound = 1;
				} else if ( $upperBound > $pageCount) {
					$upperBound = $pageCount;
				}
			}
			for($i = $lowerBound; $i< $upperBound; $i++) {
				$pages->pagesInRange[$i] = $i;
			}
			$pages->firstPageInRange = min($pages->pagesInRange);
			$pages->lastPageInRange  = max($pages->pagesInRange);
			$pages->limit = $numRowPerPage;
			$pages->name = $name;
			$pages->from = $tuition_from;
			$pages->to = $tuition_to;
			$pages->city = $city;
		}
	
		return $this->view->partial($partial, $pages);
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

}