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
}
