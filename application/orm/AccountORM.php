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
