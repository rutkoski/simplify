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
 */

/**
 * A DataHolder is a Simplify_Dictionary that keeps track of changes to values.
 *
 * @author Rodrigo Rutkoski Rodrigues <rutkoski@gmail.com>
 */
interface Simplify_Data_HolderInterface extends Simplify_DictionaryInterface
{

  /**
   * Make all changes to data permanent.
   *
   * @return DataHolderInterface
   */
  public function commit($names = null);

  /**
   * Get an array with name/values pairs that have been modified since the last call commit.
   *
   * @return array
   */
  public function getModified();

  /**
   *
   * @return boolean
   */
  public function isDirty();

}
