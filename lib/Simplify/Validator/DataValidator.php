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
 *
 * @author "Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>"
 *
 */
class Simplify_Validation_DataValidator
{

  /**
   * Validation errors
   *
   * @var array
   */
  protected $errors;

  /**
   * Validation rules
   *
   * @var array
   */
  protected $rules = array();

  /**
   * Constructor.
   *
   * @param array $rules
   */
  public function __construct(array $rules = null)
  {
    if (is_array($rules)) {
      $this->parse($rules);
    }
  }

  /**
   *
   * @return DataValidator
   */
  public static function parseFrom($rules)
  {
    if (! ($rules instanceof Simplify_Validation_DataValidator)) {
      $rules = new self($rules);
    }

    return $rules;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/api/IDataValidator#getErrors()
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/api/IDataValidator#setRule($name, $validator)
   */
  public function setRule($name, Simplify_Validation_ValidatorInterface $validator)
  {
    if (! isset($this->rules[$name])) {
      $this->rules[$name] = array();
    }

    $this->rules[$name][] = $validator;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/api/IDataValidator#validate($data, $name)
   */
  public function validate(&$data, $name = null)
  {
    $errors = array();

    if (empty($name)) {
      foreach ($this->rules as $name => $rules) {
        try {
          $this->validate($data, $name);
        }
        catch (Simplify_Validation_ValidationException $e) {
          $errors += $e->getErrors();
        }
      }
    }
    else {
      if (isset($this->rules[$name])) {
        foreach ($this->rules[$name] as $rule) {
          try {
            $rule->validate(sy_get_param($data, $name));
          }
          catch (Simplify_Validation_ValidationException $e) {
            $errors[$name] = $rule->getError();
          }
        }
      }
    }

    $this->errors = $errors;

    if (! empty($errors)) {
      throw new ValidationException($errors);
    }

    return $this;
  }

  protected function parse($rules)
  {
    foreach ($rules as $name => $rule) {
      if (empty($rule)) {
        continue;
      }
      if (is_array($rule[0])) {
        foreach ($rule as $_rule) {
          $this->setRule($name, $this->factory($_rule[0], $_rule[1], sy_get_param($_rule, 2)));
        }
      }
      elseif (! ($rule instanceof Simplify_Validation_ValidatorInterface)) {
        $this->setRule($name, $this->factory($rule[0], $rule[1], sy_get_param($rule, 2)));
      }
      else {
        $this->setRule($name, $rule);
      }
    }
  }

  protected function factory($rule, $message, array $params = null)
  {
    $class = Inflector::camelize($rule);

    if (strpos($class, 'Validator') === false) {
      $class .= 'Validator';
    }

    if (! class_exists($class)) {
      throw new Exception("Could not factory validator <b>$rule[0]</b>: class not found.");
    }

    $Rule = new $class($message);

    foreach ((array) $params as $param => $value) {
      $Rule->$param = $value;
    }

    return $Rule;
  }

}
