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
 * CNPJ valition
 *
 */
class Simplify_Validation_Cnpj extends Simplify_Validation_AbstractValidation
{

  /**
   *
   * @var boolean
   */
  public $required;

  /**
   * 
   * @param string $message
   * @param boolean $required
   */
  public function __construct($message, $required = true)
  {
    parent::__construct($message);
    
    $this->required = $required;
  }

  /**
   * (non-PHPdoc)
   * @see Simplify_ValidationInterface::validate()
   */
  public function validate($value)
  {
    if (empty($value)) {
      if ($this->required) {
        $this->fail();
      }
      else {
        return;
      }
    }
    
    if (!$this->validaCNPJ($value)) {
      $this->fail();
    }
  }

  /**
   * http://codigofonte.uol.com.br/codigo/php/validacao/validacao-de-cpf-com-php
   *
   * @param $cnpj
   * @return boolean
   */
  protected function validaCNPJ($cnpj)
  {
    // Verifiva se o número digitado contém todos os digitos
    if (strlen($cnpj) != 18)
      return false;
    
    $soma1 = ($cnpj[0] * 5) + ($cnpj[1] * 4) + ($cnpj[3] * 3) + ($cnpj[4] * 2) + ($cnpj[5] * 9) + ($cnpj[7] * 8) + ($cnpj[8] * 7) + ($cnpj[9] * 6) + ($cnpj[11] * 5) + ($cnpj[12] * 4) + ($cnpj[13] * 3) + ($cnpj[14] * 2);
    
    $resto = $soma1 % 11;
    
    $digito1 = $resto < 2 ? 0 : 11 - $resto;
    
    $soma2 = ($cnpj[0] * 6) + ($cnpj[1] * 5) + ($cnpj[3] * 4) + ($cnpj[4] * 3) + ($cnpj[5] * 2) + ($cnpj[7] * 9) + ($cnpj[8] * 8) + ($cnpj[9] * 7) + ($cnpj[11] * 6) + ($cnpj[12] * 5) + ($cnpj[13] * 4) + ($cnpj[14] * 3) + ($cnpj[16] * 2);
    
    $resto = $soma2 % 11;
    
    $digito2 = $resto < 2 ? 0 : 11 - $resto;
    
    return (($cnpj[16] == $digito1) && ($cnpj[17] == $digito2));
  }

}
