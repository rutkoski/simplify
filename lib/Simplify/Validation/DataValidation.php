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
 * Validates data
 *
 */
class Simplify_Validation_DataValidation
{

  /**
   * Validation errors
   *
   * @var string[]
   */
  protected $errors;

  /**
   * Validation rules
   *
   * @var Simplify_ValidationInterface
   */
  protected $rules = array();

  /**
   * Constructor
   *
   * @param mixed[] $rules
   */
  public function __construct(array $rules = null)
  {
    if (is_array($rules)) {
      $this->parse($rules);
    }
  }

  /**
   *
   * @return Simplify_Validation_DataValidation
   */
  public static function parseFrom($rules)
  {
    if (!($rules instanceof Simplify_Validation_DataValidation)) {
      $rules = new self($rules);
    }

    return $rules;
  }

  /**
   * Get validation errors
   *
   * @return string[]
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Set a validation rule for a given key in a data set
   *
   * @param string $name
   * @param Simplify_ValidationInterface $rule validation rule
   */
  public function setRule($name, Simplify_ValidationInterface $rule)
  {
    if (!isset($this->rules[$name])) {
      $this->rules[$name] = array();
    }

    $this->rules[$name][] = $rule;
  }

  /**
   * Validates all or specific key in data set
   *
   * @param mixed[] $data data set
   * @param string $name key in data set
   * @throws Simplify_ValidationException
   */
  public function validate(&$data, $name = null)
  {
    $errors = array();

    if (empty($name)) {
      foreach ($this->rules as $name => $rules) {
        try {
          $this->validate($data, $name);
        }
        catch (Simplify_ValidationException $e) {
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
          catch (Simplify_ValidationException $e) {
            $errors[$name] = $e->getErrors();
          }
        }
      }
    }

    $this->errors = $errors;

    if (!empty($errors))
      throw new Simplify_ValidationException($errors);
  }

  /**
   *
   * @param mixed[] $rules
   */
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
      elseif (!($rule instanceof Simplify_ValidationInterface)) {
        $this->setRule($name, $this->factory($rule[0], $rule[1], sy_get_param($rule, 2)));
      }
      else {
        $this->setRule($name, $rule);
      }
    }
  }

  /**
   * Factory validation rule
   *
   * @param string $rule rule class
   * @param string $message
   * @param mixed[string] $params
   * @throws Exception
   * @return Simplify_ValidationInterface
   */
  protected function factory($rule, $message, array $params = null)
  {
    $Rule = new $rule($message);

    foreach ((array) $params as $param => $value) {
      $Rule->$param = $value;
    }

    return $Rule;
  }

}
