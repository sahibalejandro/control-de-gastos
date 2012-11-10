<?php
class MovementORM extends QuarkORM
{
  /**
   * Table name related to this object
   * @var string
   */
  public static $table = 'movements';

  /**
   * Connection name for this object
   * @var string
   */
  public static $connection = 'default';
  
  /**
   * To validate your data before save to database
   *
   * @return boolean
   */
  protected function validate()
  {
    /**
     * TODO:
     * Validate object properties and return true on success or false on failure
     */
    return true;
  }

  /**
   * Return a QuarkORMQueryBuilder instance prepared to run queries on the table
   * related to this object.
   *
   * @return QuarkORMQueryBuilder
   */
  public static function query()
  {
    return new QuarkORMQueryBuilder(__CLASS__);
  }
  
  /**
   * Returns an array of DateTime objects, with the dates where there movements
   * by the user specified by $user_id, dates are unique (group by date).
   * 
   * @param int $user_id User's ID
   * @param array $accounts_id Array with accounts IDs to filter by.
   * @return array(DateTime)
   */
  public static function getKeyDates($user_id, $accounts_id = null)
  {
    $sql = 'SELECT DATE(`date`) FROM `movements`
      WHERE `users_id` = :user_id';
      
    // Add accounts filter if defined
    if (is_array($accounts_id)) {
      $sql .= ' AND `accounts_id` IN ('. implode(',', $accounts_id). ')';
    }
      
    $sql .= ' GROUP BY 1 ORDER BY 1;';
    
    $PDOSt = QuarkORMEngine::query(
      $sql,
      array(':user_id' => $user_id),
      self::$connection
    );

    /** Return array of DateTime objects */
    function create_DateTime($date)
    {
      return new DateTime($date);
    };
    
    return $PDOSt->fetchAll(PDO::FETCH_FUNC, 'create_DateTime');
  }
  
  /**
   * Returns up to 10 movements with a date less than (older than) to the
   * specified bt $max_timestap, from the account specified by
   * $user_id and $account_id
   */
  public static function getSince($user_id, $account_id, $max_timestamp = null)
  {
    $QueryBuilder = self::query()->find();
    
    $QueryBuilder->where('`users_id` = :user_id AND `accounts_id` = :account_id'
      , array(':user_id' => $user_id, ':account_id' => $account_id));
    
    if($max_timestamp != null){
      $QueryBuilder->andWhere('UNIX_TIMESTAMP(`date`) < :max_timestamp'
        , array(':max_timestamp' => $max_timestamp));
    }
    
    return $QueryBuilder->limit(10)->order('date', 'DESC')->puff();
  }
  
  /**
   * Get min date of movements that belongs to the user specified by $user_id
   * @param int $user_id The user's id
   * @param int $account_id Filter by one account if specified
   * @return DateTime
   */
  public static function getMovementsMinDate($user_id, $account_id = 0)
  {
    $Query = self::query()->min('date')->where(array('users_id' => $user_id));
    
    if ($account_id > 0) {
      $Query->andWhere(array('accounts_id' => $account_id));
    }
    
    return new DateTime($Query->puff()->date);
  }
}
