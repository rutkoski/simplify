<?php

class Simplify_Db_Pdo_QueryResult extends Simplify_Db_QueryResult
{

  /**
   *
   * @var PDOStatement
   */
  protected $stmt;

  /**
   *
   * @var string
   */
  protected $query;

  /**
   *
   * @var mixed
   */
  protected $data;

  /**
   *
   * @var int
   */
  protected $limit;

  /**
   *
   * @var int
   */
  protected $offset;

  /**
   *
   * @param PDOStatement $stmt
   * @param string $query
   * @param array $data
   * @return void
   */
  public function __construct($stmt, $query, $data = null, $limit = null, $offset = null)
  {
    $this->stmt = $stmt;
    $this->query = $query;
    $this->data = $data;
    $this->limit = $limit;
    $this->offset = $offset;

    /**
     * @TODO tratar numero de parametros em relação aos parametros declarados no statement
     */
    if (! is_null($data)) {}

    $this->stmt->execute((array) $data);

    Simplify_Db_Pdo_Database::validate($this->stmt, $query, $data);

    Simplify_Db_Database::log(array($query, $data, $limit, $offset));
  }

  public function query()
  {
    return $this->query;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#free()
   */
  public function free()
  {
    $this->stmt->closeCursor();
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#fetchOne()
   */
  public function fetchOne()
  {
    $return = $this->stmt->fetchColumn();
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#fetchRow($n)
   */
  public function fetchRow($n = null)
  {
    $return = is_null($n)
      ? $this->stmt->fetch(PDO::FETCH_ASSOC)
      : $this->stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT, $n);
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#fetchCol($n)
   */
  public function fetchCol($n = null)
  {
    $return = is_null($n)
      ? $this->stmt->fetchAll(PDO::FETCH_COLUMN)
      : $this->stmt->fetchAll(PDO::FETCH_COLUMN, intval($n));
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#fetchAll()
   */
  public function fetchAll()
  {
    $return = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#numRows()
   */
  public function numRows()
  {
    if (is_int($this->stmt)) {
      $return = $this->stmt;
    }
    else {
      $return = $this->stmt->rowCount();
    }

    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/api/IQueryResult#numCols()
   */
  public function numCols()
  {
    $return = $this->stmt->columnCount();
    return $return;
  }

  /**
   * (non-PHPdoc)
   * @see simplify/kernel/dao/QueryResult#columnNames()
   */
  public function columnNames()
  {
    throw new Exception('TODO');

    //$return = $this->stmt->getColumnNames();
    return $return;
  }

}
