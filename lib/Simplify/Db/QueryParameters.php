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

namespace Simplify\Db;

/**
 *
 * Query parameters that can be passed to Simplify\Db\QueryObject::setParams()
 *
 */
class QueryParameters
{

  const SQL = 'sql';

  const FROM = 'from';

  const SELECT = 'select';

  const JOIN = 'join';

  const LEFT_JOIN = 'leftJoin';

  const INNER_JOIN = 'innerJoin';

  const WHERE = 'where';

  const GROUP_BY = 'groupBy';

  const HAVING = 'having';

  const ORDER_BY = 'orderBy';

  const LIMIT = 'limit';

  const OFFSET = 'offset';

  const DATA = 'data';

}
