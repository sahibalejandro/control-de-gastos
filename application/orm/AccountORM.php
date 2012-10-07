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
   * @return float
   */
  public static function getTotalAmountAtDate(
    $account_id,
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
    
    $sql = 'SELECT
      (SELECT IFNULL(SUM(`amount`), 0)
        FROM `movements`
        WHERE (`accounts_id` = :account_id AND `type` = 1)
          AND (DATE(`date`) BETWEEN :min_date AND :max_date))
      -
      (SELECT IFNULL(SUM(`amount`), 0)
        FROM `movements`
        WHERE (`accounts_id` = :account_id AND `type` = 0)
          AND (DATE(`date`) BETWEEN :min_date AND :max_date)) AS `total_amount`';

    return (float)QuarkORMEngine::query(
        $sql,
        array(
          ':account_id' => $account_id,
          ':min_date' => $MinDate->format('Y-m-d'),
          ':max_date' => $Date->format('Y-m-d'),
        ),
        self::$connection
      )->fetchColumn(0);
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
