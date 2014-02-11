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
 * Database exception
 *
 */
class Simplify_Db_Exception extends Exception
{

  /**
   * Factory a specific exception for known sql state values
   *
   * @param unknown_type $SQLSTATE
   * @param unknown_type $info
   * @param unknown_type $code
   * @return Exception
   */
  public static function factoryException($SQLSTATE, $info, $code)
  {
    switch ($SQLSTATE) {
      case '42S02' : // Base table or view not found
        return new Simplify_Db_Exception_TableNotFoundException($info, $code);
      case '42S22' : // Column not found
        return new Simplify_Db_Exception_ColumnNotFoundException($info, $code);

      case '00000' : // Success
      case '01000' : // General warning
      case '01001' : // Cursor operation conflict
      case '01002' : // Disconnect error
      case '01004' : // Data truncated
      case '01006' : // Privilege not revoked
      case '01S00' : // Invalid connection string attribute
      case '01S01' : // Error in row
      case '01S02' : // Option value changed
      case '07001' : // Wrong number of parameters
      case '07002' : // Mismatching parameters
      case '07003' : // Cursor specification cannot be executed
      case '07004' : // Missing parameters
      case '07005' : // Invalid cursor state
      case '07006' : // Restricted data type attribute violation
      case '07008' : // Invalid descriptor count
      case '08000' : // Connection exception
      case '08001' : // Unable to connect to the data source, e.g. invalid license key
      case '08002' : // Connection already in use
      case '08003' : // Connection not open
      case '08004' : // Data source rejected establishment of connection
      case '08007' : // Connection failure during transaction
      case '08900' : // Server lookup failed
      case '08S01' : // Communication link failure
      case '21000' : // Cardinality violation
      case '21S01' : // Insert value list does not match column list
      case '21S02' : // Degree of derived table does not match column list
      case '22000' : // Data exception
      case '22001' : // String data, right truncation
      case '22003' : // Numeric value out of range
      case '22007' : // Invalid datetime format
      case '22012' : // Division by zero
      case '22018' : // Error in assignment
      case '22026' : // String data, length mismatch
      case '23000' : // Integrity constraint violation
      case '25000' : // Invalid transaction state
      case '25S02' : // Transaction is still active
      case '25S03' : // Transaction has been rolled back
      case '26000' : // Invalid SQL statement identifier
      case '28000' : // Invalid authorization specification
      case '34000' : // Invalid cursor name
      case '3C000' : // Duplicate cursor name
      case '40000' : // Commit transaction resulted in rollback transaction
      case '40001' : // Serialization failure, e.g. timeout or deadlock
      case '42000' : // Syntax error or access rule violation
      case '42S01' : // Base table or view already exists
      case '42S11' : // Index already exists
      case '42S12' : // Index not found
      case '42S21' : // Column already exists
      case '42S23' : // No default for column
      case '44000' : // WITH CHECK OPTION violation
      case 'HY000' : // General error
      case 'HY001' : // Storage allocation failure
      case 'HY002' : // Invalid column number
      case 'HY003' : // Invalid application buffer type
      case 'HY004' : // Invalid SQL Data type
      case 'HY008' : // Operation cancelled
      case 'HY009' : // Invalid use of null pointer
      case 'HY010' : // Function sequence error
      case 'HY011' : // Operation invalid at this time
      case 'HY012' : // Invalid transaction operation code
      case 'HY015' : // No cursor name avilable
      case 'HY018' : // Server declined cancel request
      case 'HY090' : // Invalid string or buffer length
      case 'HY091' : // Descriptor type out of range
      case 'HY092' : // Attribute or Option type out of range
      case 'HY093' : // Invalid parameter number
      case 'HY095' : // Function type out of range
      case 'HY096' : // Information type out of range
      case 'HY097' : // Column type out of range
      case 'HY098' : // Scope type out of range
      case 'HY099' : // Nullable type out of range
      case 'HY100' : // Uniqueness option type out of range
      case 'HY101' : // Accuracy option type out of range
      case 'HY103' : // Direction option out of range
      case 'HY104' : // Invalid precision or scale value
      case 'HY105' : // Invalid parameter type
      case 'HY106' : // Fetch type out of range
      case 'HY107' : // Row value out of range
      case 'HY108' : // Concurrency option out of range
      case 'HY109' : // Invalid cursor position
      case 'HY110' : // Invalid driver completion
      case 'HY111' : // Invalid bookmark value
      case 'HYC00' : // Driver not capable
      case 'HYT00' : // Timeout expired
      case 'HZ010' : // RDA error: Access control violation
      case 'HZ020' : // RDA error: Bad repetition count
      case 'HZ080' : // RDA error: Resource not available
      case 'HZ090' : // RDA error: Resource already open
      case 'HZ100' : // RDA error: Resource unknown
      case 'HZ380' : // RDA error: SQL usage violation
      case 'IM001' : // Driver does not support this function
      case 'IM002' : // Data source name not found and no default driver specified
      case 'IM003' : // Specified driver could not be loaded
      case 'IM004' : // Driver's AllocEnv failed
      case 'IM005' : // Driver's AllocConnect failed
      case 'IM006' : // Driver's SetConnectOption failed
      case 'IM007' : // No data source or driver specified, dialog prohibited
      case 'IM008' : // Dialog failed
      case 'IM009' : // Unable to load translation DLL
      case 'IM010' : // Data source name too long
      case 'IM011' : // Driver name too long
      case 'IM012' : // DRIVER keyword syntax error
      case 'IM013' : // Trace file error
        return new self($info, $code);
    }
  }

}
