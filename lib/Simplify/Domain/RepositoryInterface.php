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
 * 
 * Common interface for domain repositories
 *
 */
interface Simplify_Domain_RepositoryInterface
{

  /**
   * Find one record by it's primary key (optional) and parameters (optional).
   * If no $id given, return first record in result set.
   *
   * @param mixed $id value for primary key
   * @param array $params query parameters
   * @return mixed
   * @throws RecordNotFoundException if record not found
   */
  public function find($id = null, $params = null);

  /**
   * Find multiple records.
   *
   * @param array $params query parameters
   * @return mixed[]
   */
  public function findAll($params = null);

  /**
   * Find record count.
   *
   * @param array $params query parameters
   * @return int
   */
  public function findCount($params = null);

  /**
   * Save one record.
   *
   * @param mixed $data row data
   * @return void
   */
  public function save(&$data);

  /**
   * Delete one record by it's primary key or parameters.
   *
   * @param mixed $id value for primary key
   * @param array $params query parameters
   * @return int delete count
   */
  public function delete($id = null, $params = null);

  /**
   * Delete multiple records.
   *
   * @param array $params query parameters
   * @return int delete count
   */
  public function deleteAll($params = null);

}
