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
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 * @copyright Copyright 2008 Rodrigo Rutkoski Rodrigues
 */

/**
 *
 * Password validation
 *
 */
class Simplify_Validation_Password extends Simplify_Validation_AbstractValidation
{

  /**
   * True if the record exists
   *
   * @var boolean
   */
  public $exists;

  /**
   * Password confirmation
   *
   * @var string
   */
  public $confirm;

  /**
   * Empty password
   *
   * @var string
   */
  public $empty;

  /**
   * Constructor
   *
   * @param string $passwordDontMatchMessage
   * @param string $emptyPasswordMessage
   * @param boolean $exists
   * @param string $confirm
   * @param string $empty
   */
  public function __construct($passwordDontMatchMessage, $emptyPasswordMessage = null, $exists = null, $confirm = null, $empty = null)
  {
    parent::__construct(array('confirm' => $passwordDontMatchMessage, 'empty' => $emptyPasswordMessage));

    $this->exists = $exists;
    $this->confirm = $confirm;
    $this->empty = $empty;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_ValidationInterface::validate()
   */
  public function validate($value)
  {
    if (!$this->exists) {
      if ($value == $this->empty) {
        $this->fail($this->message['empty']);
      }
      elseif ($value != $this->confirm) {
        $this->fail($this->message['confirm']);
      }
    }
    elseif (($value != $this->empty || $this->confirm != $this->empty) && $value != $this->confirm) {
      $this->fail($this->message['confirm']);
    }
  }

}
