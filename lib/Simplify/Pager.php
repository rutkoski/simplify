<?php

/**
 * SimplifyPHP Framework
 *
 * This file is part of SimplifyPHP Framework.
 *
 * SimplifyPHP Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * SimplifyPHP Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 */

namespace Simplify;

/**
 * 
 * Calculates paging information.
 *
 */
class Pager
{

  /**
   * Number of items.
   * @var integer
   */
  protected $count;

  /**
   * Current item offset. Offset numbers start in 0.
   * @var integer
   */
  protected $offset;

  /**
   * Item offset on last page.
   * @var integer
   */
  protected $lastOffset;

  /**
   * Current page. Page numbers start in 1.
   * @var integer
   */
  protected $page;

  /**
   * Number of pages.
   * @var integer
   */
  protected $pages;

  /**
   * Number of items on each page.
   * @var integer
   */
  protected $pageSize;

  /**
   * Constructor.
   *
   * @param $count integer Number of items.
   * @param $pageSize integer Number of items on each page.
   * @param $pageOrOffset integer Current page or offset.
   * @param $isOffset boolean Where previous parameter is current offset (true)
   * or page (false).
   * @throws Exception If $pageOrOffset is invalid.
   * @return void
   */
  public function __construct($count, $pageSize, $pageOrOffset, $isOffset = true)
  {
    $count = (int) $count;
    $pageSize = (int) $pageSize;
    $pageOrOffset = (int) $pageOrOffset;
    
    if ($pageSize <= 0)
      $pageSize = PHP_INT_MAX;
    
    $this->count = is_array($count) ? count($count) : $count;
    $this->pageSize = $pageSize;
    $this->pages = ceil($count / $pageSize);
    
    if ($isOffset) {
      if ($pageOrOffset < 0) {
        throw new \Exception('Current offset must be equal to or greater than 0.');
      }
      elseif ($pageOrOffset > 0 && $pageOrOffset > $count) {
        throw new \Exception('Current offset must be lesser than the number of items.');
      }
      
      $this->offset = $pageOrOffset;
      $this->page = $this->getPageFromOffset($pageOrOffset);
    }
    else {
      if ($pageOrOffset < 1) {
        throw new \Exception('Current page must be equal to or greater than 1.');
      }
      elseif ($pageOrOffset > $this->pages) {
        throw new \Exception('Current page must be lesser than or equal to the number of pages.');
      }
      
      $this->page = $pageOrOffset;
      $this->offset = $this->getOffsetFromPage($pageOrOffset);
    }
    
    $this->lastOffset = $count == 0 ? 0 : (($this->pages - 1) * $this->pageSize);
  }

  /**
   *
   * @return int Number of itens in each page
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }

  /**
   * Get page number from offset.
   *
   * @param $offset integer Item offset.
   * @return integer Page number.
   */
  public function getPageFromOffset($offset)
  {
    return ceil(($offset + 1) / $this->pageSize);
  }

  /**
   * Get offset from page number.
   *
   * @param $page integer Page number.
   * @return integer Item offset.
   */
  public function getOffsetFromPage($page)
  {
    return ($page - 1) * $this->pageSize;
  }

  /**
   * Get a list of page numbers close to current page.
   *
   * @param integer $precision Total pages in list.
   * @return array The list of page numbers.
   */
  public function getPageList($precision = 10)
  {
    $p = $precision;
    $f = $this->getFirstPage();
    $c = $this->getCurrentPage();
    $l = $this->getLastPage();
    
    $pages = array();
    for($i = max($f, $c - $p); $i < $c; $i++)
      $pages[] = $i;
    for($i = $c; $i <= min($l, $c + $p); $i++)
      $pages[] = $i;
    return $pages;
  }

  /**
   * Check if we´re on the first page.
   *
   * @return boolean True if it´s the first page, false otherwise.
   */
  public function isFirstPage()
  {
    return $this->page == $this->getFirstPage();
  }

  /**
   * Check if we´re on the last page.
   *
   * @return boolean True if it´s the last page, false otherwise.
   */
  public function isLastPage()
  {
    return $this->page == $this->pages;
  }

  /**
   * Get first page number.
   *
   * @return integer
   */
  public function getFirstPage()
  {
    return 1;
  }

  /**
   * Get previous page number.
   *
   * @return integer
   */
  public function getPreviousPage()
  {
    return $this->page > 1 ? $this->page - 1 : 1;
  }

  /**
   * Get current page number.
   *
   * @return integer
   */
  public function getCurrentPage()
  {
    return $this->page;
  }

  /**
   * Get next page number.
   *
   * @return integer
   */
  public function getNextPage()
  {
    if ($this->count == 0) {
      return $this->getFirstPage();
    }
    
    return $this->isLastPage() ? $this->page : $this->page + 1;
  }

  /**
   * Get last page number.
   *
   * @return integer
   */
  public function getLastPage()
  {
    return $this->pages;
  }

  /**
   * Get the number of pages.
   *
   * @return integer
   */
  public function getTotalPages()
  {
    return $this->pages;
  }

  /**
   * Get the number of items.
   *
   * @return integer
   */
  public function getCount()
  {
    return $this->count;
  }

  /**
   * Check if we´re on the first offset.
   *
   * @return boolean True if it´s the first offset, false otherwise.
   */
  public function isFirstOffset()
  {
    return $this->offset == $this->getFirstOffset();
  }

  /**
   * Check if we´re on the last offset.
   *
   * @return boolean True if it´s the last offset, false otherwise.
   */
  public function isLastOffset()
  {
    return $this->offset == $this->getLastOffset();
  }

  /**
   * Get first item offset.
   *
   * @return integer
   */
  public function getFirstOffset()
  {
    return 0;
  }

  /**
   * Get item offset for previous page.
   *
   * @return integer
   */
  public function getPreviousOffset()
  {
    return $this->offset > $this->pageSize ? $this->offset - $this->pageSize : 0;
  }

  /**
   * Get item offset for current page.
   *
   * @return integer
   */
  public function getCurrentOffset()
  {
    return $this->offset;
  }

  /**
   * Get item offset for next page.
   *
   * @return integer
   */
  public function getNextOffset()
  {
    if ($this->count == 0) {
      return 0;
    }
    
    $nextOffset = $this->offset + $this->pageSize;
    
    if ($nextOffset <= $this->getLastOffset()) {
      return $nextOffset;
    }
    else {
      return $this->getLastOffset();
    }
  }

  /**
   * Get first item offset.
   *
   * @return integer
   */
  public function getLastOffset()
  {
    return $this->lastOffset;
  }

  /**
   * Get first visible item offset
   *
   * @return int
   */
  public function getFromOffset()
  {
    return $this->count == 0 ? 0 : $this->getCurrentOffset() + 1;
  }

  /**
   * Get last visible offset
   *
   * @return int
   */
  public function getToOffset()
  {
    return $this->isLastOffset() ? $this->getCount() : $this->getNextOffset();
  }

  /**
   * Get all information.
   *
   * @return array
   */
  public function getAll()
  {
    return array('count' => $this->count, 'isFirstPage' => $this->isFirstPage(), 'isLastPage' => $this->isLastPage(), 'firstPage' => $this->getFirstPage(), 'previousPage' => $this->getPreviousPage(), 'currentPage' => $this->getCurrentPage(), 'nextPage' => $this->getNextPage(), 
        'lastPage' => $this->getLastPage(), 'totalPages' => $this->getTotalPages(), 'isFirstOffset' => $this->isFirstOffset(), 'isLastOffset' => $this->isLastOffset(), 'firstOffset' => $this->getFirstOffset(), 'previousOffset' => $this->getPreviousOffset(), 
        'currentOffset' => $this->getCurrentOffset(), 'nextOffset' => $this->getNextOffset(), 'lastOffset' => $this->getLastOffset(), 'fromOffset' => $this->getFromOffset(), 'toOffset' => $this->getToOffset());
  }

  /**
   * Get page number from offset.
   * @param $offset integer Item offset.
   * @return integer Page number.
   */
  public static function pageFromOffset($offset, $pageSize)
  {
    return ceil(($offset + 1) / $pageSize);
  }

  /**
   * Get offset from page number.
   * @param $page integer Page number.
   * @return integer Item offset.
   */
  public static function offsetFromPage($page, $pageSize)
  {
    return ($page - 1) * $pageSize;
  }

}
