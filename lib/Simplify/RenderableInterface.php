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
 * Interface for renderable objects.
 *
 * @author Rodrigo Rutkoski Rodrigues, <rutkoski@gmail.com>
 * @package Simplify_Kernel_Core_Api
 */
interface Simplify_RenderableInterface extends Simplify_DictionaryInterface
{

  /**
   * Get layout name or file.
   *
   * @return string
   */
  public function getLayout();

  /**
   * Get template name or file.
   *
   * @return string
   */
  public function getTemplate();

  /**
   * Set layout name or file.
   *
   * @param string $layout The layout.
   * @return void
   */
  public function setLayout($layout);

  /**
   * Set template name or file.
   *
   * @param string $template The template.
   * @return void
   */
  public function setTemplate($template);

}
