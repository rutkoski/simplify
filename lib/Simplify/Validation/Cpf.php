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
 * CPF validation
 *
 */
class Simplify_Validation_Cpf extends Simplify_Validation_AbstractValidation
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
    
    if (!$this->validaCPF($value)) {
      $this->fail();
    }
  }

  /**
   * http://codigofonte.uol.com.br/codigo/php/validacao/validacao-de-cpf-com-php
   *
   * @param $cpf
   * @return boolean
   */
  protected function validaCPF($cpf)
  { // Verifiva se o número digitado contém todos os digitos
    $cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
    
    // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
    if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
      return false;
    }
    else { // Calcula os números para verificar se o CPF é verdadeiro
      for($t = 9; $t < 11; $t++) {
        for($d = 0, $c = 0; $c < $t; $c++) {
          $d += $cpf{$c} * (($t + 1) - $c);
        }
        
        $d = ((10 * $d) % 11) % 10;
        
        if ($cpf{$c} != $d) {
          return false;
        }
      }
      
      return true;
    }
  }

}
