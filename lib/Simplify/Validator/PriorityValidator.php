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
 * Organize validation rules by priority. Priority 0 is highest, positive numbers are lower priority.
 * 
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Data_Validation
 */
class Simplify_Validation_PriorityValidator extends Simplify_Validation_AbstractValidator
{

  /**
   * Validation rules.
   * 
   * @var array
   */
  private $rules;

  /**
   * Holds the last rule that failed.
   * If validation was successfull, this value is null.
   * 
   * @var IValidator
   */
  private $lastRule;

  /**
   * 
   * @return unknown_type
   */
  public function __construct()
  {
    $this->rules = array();
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#getError()
   */
  public function getError()
  {
    return $this->getLastRule() ? $this->getLastRule()->getError() : null;
  }

  /**
   * Get the last rule that failed.
   * 
   * @return IValidator
   */
  public function getLastRule()
  {
    return $this->lastRule;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/data/validation/IValidator#validate($value)
   */
  public function validate($value)
  {
    $this->lastRule = null;
    
    foreach ($this->rules as $rules) {
      foreach ($rules as $rule) {
        try {
          $rule->validate($value);
        }
        
        catch (Simplify_Validation_ValidationException $e) {
          $this->lastRule = $rule;
          $this->fail();
        }
      }
    }
  }

  /**
   * Add a rule to the chain.
   * 
   * @param IValidator $rule Validation rule.
   * @param integer $priority Priority level for rule.
   * @return CompositeValidator
   */
  public function addRule(IValidator $rule, $priority = 0)
  {
    if (! isset($this->rules[$priority])) {
      $this->rules[$priority] = array();
    }
    
    $this->rules[$priority][] = $rule;
    
    return $this;
  }

}

?>