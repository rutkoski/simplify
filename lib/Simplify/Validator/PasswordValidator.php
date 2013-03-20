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

class Simplify_Validation_PasswordValidator extends Simplify_Validation_AbstractValidator
{

  /**
   *
   * @var boolean
   */
  public $exists;

  /**
   *
   * @var string
   */
  public $confirm;

  /**
   *
   * @var string
   */
  public $empty;

  /**
   *
   * @var string
   */
  public $emptyPasswordMessage;

  /**
   * Constructor.
   *
   * @return void
   */
  public function __construct($passwordDontMatchMessage, $emptyPasswordMessage = null, $exists = null, $confirm = null, $empty = null)
  {
    parent::__construct($passwordDontMatchMessage);

    $this->emptyPasswordMessage = $emptyPasswordMessage;
    $this->exists = $exists;
    $this->confirm = $confirm;
    $this->empty = $empty;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#validate($value)
   */
  public function validate($value)
  {
    if (! $this->exists) {
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
