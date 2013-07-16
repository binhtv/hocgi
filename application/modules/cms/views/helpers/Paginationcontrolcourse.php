<?php

/**
 * Helper for making easy show a flash
 *
 * @package    Copy from SGN
 * @subpackage Helper
 * @copyright
 * @license
 */
class cms_View_Helper_Paginationcontrolcourse
{

	public $view;
	
	public function paginationcontrolcourse($currentPageNumber, $totalItem, $numRowPerPage, $partial = null, $name=null, $tuition_from = null, $tuition_to = null, $city=null, $tab=null, $keySearch=null)
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
			$pages->tab = $tab;
			$pages->keySearch = $keySearch;
		}
	
		return $this->view->partial($partial, $pages);
	}

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

}