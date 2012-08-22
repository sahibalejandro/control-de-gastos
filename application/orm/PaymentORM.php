<?php
class PaymentORM extends QuarkORM
{
  /**
   * Table name related to this object
   * @var string
   */
  public static $table = 'payments';

  /**
   * Connection name for this object
   * @var string
   */
  public static $connection = 'default';
  
  /**
   * Returns the payment data in an array for ajax requests.
   * @return array(id, amount, concept, amount_formated)
   */
  public function getArrayForAJAX()
  {
    return array(
      'id'              => $this->id,
      'amount'          => $this->amount,
      'concept'         => $this->concept,
      'amount_formated' => '$'.number_format($this->amount, 2)
    );
  }
  
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
}
