<?php
class Movement extends SpendControlORM
{
  /**
   * Nombre de la tabla relacionada con este ORM
   * @var string
   */
  public static $table = 'movement';

  /**
   * Nombre de la conección que utiliza este ORM
   * @var string
   */
  public static $connection = 'default';
  
  /**
   * Valida los datos antes de guardar, este metodo es invocado
   * automaticamente por save().
   *
   * @return boolean
   */
  protected function validate()
  {
    /**
     * TODO:
     * Programar algoritmo de validación
     */
    return true;
  }

  /**
   * Devuelve una instancia de QuarkORMQueryBuilder configurada para realizar
   * consultas sobre la tabla y conección del ORM actual.
   *
   * @return QuarkORMQueryBuilder
   */
  public static function query()
  {
    return new QuarkORMQueryBuilder(__CLASS__);
  }
}
