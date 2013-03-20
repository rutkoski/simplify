<?php

class Simplify_View_Helper_Pager extends Simplify_View_Helper
{
/*
  public function show(Pager $pager, $params = null)
  {
    if ($pager->getTotalPages() > 1) {
      $ul = e('<ul>')->addClass('pager');

      $route = s::request()->route();

      $get = array('limit' => $pager->getPageSize());

      if ($pager->isFirstOffset()) {
        //$ul->append(e('<li>')->addClass('first')->addClass('disabled')->html($this->icon->show('first')));
        //$ul->append(e('<li>')->addClass('previous')->addClass('disabled')->append($this->icon->show('previous')));
        $i = $this->icon->show('first');
        $a = e('<a>')->attr('href', '#')->append($i);
        $li = e('<li>')->addClass('first')->append($a);
        $ul->append($li);

        $i = $this->icon->show('previous');
        $a = e('<a>')->attr('href', '#')->append($i);
        $li = e('<li>')->addClass('previous')->append($a);
        $ul->append($li);
      }
      else {
        $get['offset'] = $pager->getFirstOffset();
        $i = $this->icon->show('first');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i);
        $li = e('<li>')->addClass('first')->append($a);
        $ul->append($li);

        $get['offset'] = $pager->getPreviousOffset();
        $i = $this->icon->show('previous');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i);
        $li = e('<li>')->addClass('previous')->append($a);
        $ul->append($li);
      }

      foreach ($pager->getPageList() as $page) {
        if ($pager->getCurrentPage() == $page) {
          //$ul->append(e('<li>')->addClass('page')->addClass('current')->html($page));
          $a = e('<a>')->attr('href', '#')->append($page);
          $li = e('<li>')->addClass('page')->append($a);
          $ul->append($li);
        }
        else {
          $get['offset'] = $pager->getOffsetFromPage($page);
          $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($page);
          $li = e('<li>')->addClass('page')->append($a);
          $ul->append($li);
        }
      }

      if ($pager->isLastOffset()) {
        //$ul->append(e('<li>')->addClass('next')->addClass('disabled')->html($this->icon->show('next')));
        //$ul->append(e('<li>')->addClass('last')->addClass('disabled')->append($this->icon->show('last')));
        $i = $this->icon->show('next');
        $a = e('<a>')->attr('href', '#')->append($i);
        $li = e('<li>')->addClass('next')->append($a);
        $ul->append($li);

        $i = $this->icon->show('last');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i);
        $li = e('<li>')->addClass('last')->append($a);
        $ul->append($li);
      }
      else {
        $get['offset'] = $pager->getNextOffset();
        $i = $this->icon->show('next');
        $a = e('<a>')->attr('href', '#')->append($i);
        $li = e('<li>')->addClass('next')->append($a);
        $ul->append($li);

        $get['offset'] = $pager->getLastOffset();
        $i = $this->icon->show('last');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i);
        $li = e('<li>')->addClass('last')->append($a);
        $ul->append($li);
      }

      return e('<div>')->addClass('')->append($ul);
    }

    return;
  }
 */

  public function show(Simplify_Pager $pager, $params = null)
  {
    if ($pager->getTotalPages() > 1) {
      $ul = e('<div>')->addClass('btn-group');

      $route = s::request()->route();

      $get = array('limit' => $pager->getPageSize());

      if ($pager->isFirstOffset()) {
        //$ul->append(e('<li>')->addClass('first')->addClass('disabled')->html($this->icon->show('first')));
        //$ul->append(e('<li>')->addClass('previous')->addClass('disabled')->append($this->icon->show('previous')));
        $i = $this->icon->show('first');
        $a = e('<a>')->attr('href', '#')->append($i)->addClass('btn')->addClass('disabled');
        //$li = e('<li>')->addClass('first')->append($a);
        $ul->append($a);

        $i = $this->icon->show('previous');
        $a = e('<a>')->attr('href', '#')->append($i)->addClass('btn')->addClass('disabled');
        //$li = e('<li>')->addClass('previous')->append($a);
        $ul->append($a);
      }
      else {
        $get['offset'] = $pager->getFirstOffset();
        $i = $this->icon->show('first');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i)->addClass('btn');
        //$li = e('<li>')->addClass('first')->append($a);
        $ul->append($a);

        $get['offset'] = $pager->getPreviousOffset();
        $i = $this->icon->show('previous');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i)->addClass('btn');
        //$li = e('<li>')->addClass('previous')->append($a);
        $ul->append($a);
      }

      foreach ($pager->getPageList() as $page) {
        if ($pager->getCurrentPage() == $page) {
          //$ul->append(e('<li>')->addClass('page')->addClass('current')->html($page));
          $a = e('<a>')->attr('href', '#')->append($page)->addClass('btn')->addClass('disabled');
          //$li = e('<li>')->addClass('page')->append($a);
          $ul->append($a);
        }
        else {
          $get['offset'] = $pager->getOffsetFromPage($page);
          $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($page)->addClass('btn');
          //$li = e('<li>')->addClass('page')->append($a);
          $ul->append($a);
        }
      }

      if ($pager->isLastOffset()) {
        //$ul->append(e('<li>')->addClass('next')->addClass('disabled')->html($this->icon->show('next')));
        //$ul->append(e('<li>')->addClass('last')->addClass('disabled')->append($this->icon->show('last')));
        $i = $this->icon->show('next');
        $a = e('<a>')->attr('href', '#')->append($i)->addClass('btn')->addClass('disabled');
        //$li = e('<li>')->addClass('next')->append($a);
        $ul->append($a);

        $i = $this->icon->show('last');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i)->addClass('btn')->addClass('disabled');
        //$li = e('<li>')->addClass('last')->append($a);
        $ul->append($a);
      }
      else {
        $get['offset'] = $pager->getNextOffset();
        $i = $this->icon->show('next');
        $a = e('<a>')->attr('href', '#')->append($i)->addClass('btn');
        //$li = e('<li>')->addClass('next')->append($a);
        $ul->append($a);

        $get['offset'] = $pager->getLastOffset();
        $i = $this->icon->show('last');
        $a = e('<a>')->attr('href', Simplify_URL::make($route, $get))->append($i)->addClass('btn');
        //$li = e('<li>')->addClass('last')->append($a);
        $ul->append($a);
      }

      return $ul;
    }

    return;
  }

}
