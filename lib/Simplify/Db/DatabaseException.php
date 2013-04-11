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

/*
 SQLSTATE  Short Description
00000 Success
01000 General warning
01001 Cursor operation conflict
01002 Disconnect error
01004 Data truncated
01006 Privilege not revoked
01S00 Invalid connection string attribute
01S01 Error in row
01S02 Option value changed
07001 Wrong number of parameters
07002 Mismatching parameters
07003 Cursor specification cannot be executed
07004 Missing parameters
07005 Invalid cursor state
07006 Restricted data type attribute violation
07008 Invalid descriptor count
08000 Connection exception
08001 Unable to connect to the data source, e.g. invalid license key
08002 Connection already in use
08003 Connection not open
08004 Data source rejected establishment of connection
08007 Connection failure during transaction
08900 Server lookup failed
08S01 Communication link failure
21000 Cardinality violation
21S01 Insert value list does not match column list
21S02 Degree of derived table does not match column list
22000 Data exception
22001 String data, right truncation
22003 Numeric value out of range
22007 Invalid datetime format
22012 Division by zero
22018 Error in assignment
22026 String data, length mismatch
23000 Integrity constraint violation
25000 Invalid transaction state
25S02 Transaction is still active
25S03 Transaction has been rolled back
26000 Invalid SQL statement identifier
28000 Invalid authorization specification
34000 Invalid cursor name
3C000 Duplicate cursor name
40000 Commit transaction resulted in rollback transaction
40001 Serialization failure, e.g. timeout or deadlock
42000 Syntax error or access rule violation
42S01 Base table or view already exists
42S02 Base table or view not found
42S11 Index already exists
42S12 Index not found
42S21 Column already exists
42S22 Column not found
42S23 No default for column
44000 WITH CHECK OPTION violation
HY000 General error
HY001 Storage allocation failure
HY002 Invalid column number
HY003 Invalid application buffer type
HY004 Invalid SQL Data type
HY008 Operation cancelled
HY009 Invalid use of null pointer
HY010 Function sequence error
HY011 Operation invalid at this time
HY012 Invalid transaction operation code
HY015 No cursor name avilable
HY018 Server declined cancel request
HY090 Invalid string or buffer length
HY091 Descriptor type out of range
HY092 Attribute or Option type out of range
HY093 Invalid parameter number
HY095 Function type out of range
HY096 Information type out of range
HY097 Column type out of range
HY098 Scope type out of range
HY099 Nullable type out of range
HY100 Uniqueness option type out of range
HY101 Accuracy option type out of range
HY103 Direction option out of range
HY104 Invalid precision or scale value
HY105 Invalid parameter type
HY106 Fetch type out of range
HY107 Row value out of range
HY108 Concurrency option out of range
HY109 Invalid cursor position
HY110 Invalid driver completion
HY111 Invalid bookmark value
HYC00 Driver not capable
HYT00 Timeout expired
HZ010 RDA error: Access control violation
HZ020 RDA error: Bad repetition count
HZ080 RDA error: Resource not available
HZ090 RDA error: Resource already open
HZ100 RDA error: Resource unknown
HZ380 RDA error: SQL usage violation
IM001 Driver does not support this function
IM002 Data source name not found and no default driver specified
IM003 Specified driver could not be loaded
IM004 Driver's AllocEnv failed
IM005 Driver's AllocConnect failed
IM006 Driver's SetConnectOption failed
IM007 No data source or driver specified, dialog prohibited
IM008 Dialog failed
IM009 Unable to load translation DLL
IM010 Data source name too long
IM011 Driver name too long
IM012 DRIVER keyword syntax error
IM013 Trace file error
*/

/**
 *
 * Database exception
 *
 */
class Simplify_Db_DatabaseException extends Exception
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
      case '42S02' :
        return new Simplify_Db_TableNotFoundException($info, $code);

      default :
        return new self($info, $code);
    }
  }

}
