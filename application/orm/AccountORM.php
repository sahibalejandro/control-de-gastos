<?php
class AccountORM extends QuarkORM
{
  /**
   * Table name related to this object
   * @var string
   */
  public static $table = 'accounts';

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

  /*
   * Le static methods
    = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
  
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
   * Return the total amount of an account at the specified date interval, starting
   * at $MinDate up to $Date, if $account_id then return the available total amount
   * in the same dates.
   * 
   * @return float
   */
  public static function getTotalAmountAtDate(
    $account_id,
    $user_id,
    DateTime $Date,
    DateTime $MinDate = null
  ) {
    
    if ($MinDate == null) {
      /**
       * TODO:
       * Get min date of account's movements.
       */
      $MinDate = new DateTime('2012-01-01');
    }
    
    $sql_args = array(
      ':user_id'  => $user_id,
      ':min_date' => $MinDate->format('Y-m-d'),
      ':max_date' => $Date->format('Y-m-d'),
    );
    
    if ($account_id > 0) {
      $sql = 'SELECT
        (SELECT IFNULL(SUM(`amount`), 0)
          FROM `movements`
          WHERE (`users_id` = :user_id AND `accounts_id` = :account_id AND `type` = 1)
            AND (DATE(`date`) BETWEEN :min_date AND :max_date))
        -
        (SELECT IFNULL(SUM(`amount`), 0)
          FROM `movements`
          WHERE (`users_id` = :user_id AND `accounts_id` = :account_id AND `type` = 0)
            AND (DATE(`date`) BETWEEN :min_date AND :max_date)) AS `total_amount`';
      
      $sql_args[':account_id'] = $account_id;
    } else {
      $sql = 'SELECT
        (SELECT IFNULL(SUM(`amount`), 0)
          FROM `movements`
          WHERE (`users_id` = :user_id AND `type` = 1)
            AND (DATE(`date`) BETWEEN :min_date AND :max_date))
        -
        (SELECT IFNULL(SUM(`amount`), 0)
          FROM `movements`
          WHERE (`users_id` = :user_id AND `type` = 0)
            AND (DATE(`date`) BETWEEN :min_date AND :max_date)) AS `total_amount`';
    }
    
    return (float)QuarkORMEngine::query(
        $sql,
        $sql_args,
        self::$connection
      )->fetchColumn(0);
    /* end of: if ($account_id > 0) */
  }
   
  /**
   * Returns accounts for user specified for the given ID
   * @return array(AccountORM)
   */
  public static function getUserAccounts($user_id)
  {
    return self::query()->find()
      ->where(array('users_id' => $user_id))
      ->order('id', 'ASC')
      ->puff();
  }
}
