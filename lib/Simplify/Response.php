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

/**
 * Default response manager.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
class Simplify_Response
{

  const AUTO = null;

  const HEADERS = false;

  /**
   *
   * @var array
   */
  protected static $headers = array();

  /**
   * Queue an http header to the sent to the browser.
   *
   * @param string $string the header
   * @param int $http_response_code http response code
   * @return Response
   */
  public function header($header, $http_response_code = null, $replace = false)
  {
    if ($replace) {
      self::$headers = array();
    }

    $header = array($header, $http_response_code);

    self::$headers[md5(serialize($header))] = $header;

    return $this;
  }

  /**
   * Send headers and output $content.
   *
   * @param mixed $content the content
   * @return string
   */
  public function output($content)
  {
    // automagically call __toString if it exists in $content
    $content = '' . $content;

    $this->outputHeaders();

    echo $content;

    return $content;
  }

  /**
   * Send headers to the browser.
   *
   * @return Response
   */
  public function outputHeaders()
  {
    if (! headers_sent() && ! empty(self::$headers)) {
      foreach (array_unique(self::$headers) as $header) {
        header($header[0], false, $header[1]);
      }
    }

    return $this;
  }

  /**
   * Url redirection.
   *
   * @param string $url the url
   * @param string $http_response_code http response code
   * @return void
   */
  public function redirect($url = null, $http_response_code = null)
  {
    if (!empty($url)) {
      $url = Simplify_URL::parse($url);
      $this->header("Location: $url", false, $http_response_code);
    }
    $this->outputHeaders();
    exit();
  }

  /**
   * Url redirection with 404 status.
   *
   * @param string $url the url
   * @return void
   */
  public function redirect404($url = null)
  {
    $this->header('HTTP/1.1 404 Not Found');
    $this->redirect($url);
  }

}
