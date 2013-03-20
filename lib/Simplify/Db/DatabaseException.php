<?php

class Simplify_Db_DatabaseException extends Exception
{

  public static function factoryException($SQLSTATE, $info, $code)
  {
    switch ($SQLSTATE) {
      case '42S02':
        return new Simplify_Db_TableNotFoundException($info, $code);

      default:
        return new self($info, $code);
    }
  }

}
