<?php
/**
 * Controller to handle statistics and chart pages
 * @author Sahib J. Leo
 */
class EstadisticasController extends BaseController
{
  /**
   * Shows chart GUI
   */
  public function index()
  {
    $this->addViewVars(array(
      'accounts' => AccountORM::query()->find()->order('name')->puff()
    ));
    $this->renderView();
  }
  
  public function ajaxLoadChartDataTable()
  {
    // Get min date of all user's movements.
    $MinDate = MovementORM::getMovementsMinDate($this->UserData->id);
    
    /** Get key dates of movements in the user's accounts */
    $key_dates = MovementORM::getKeyDates($this->UserData->id);
    
    $user_accounts_ids = array(1, 2);
    
    $datatable_rows = array();
    $dataable_columns = array(
      array('string', 'Fecha')
    );
    
    foreach ($user_accounts_ids as $account_id) {
      $AccountORM = AccountORM::query()->findByPk($account_id);
      $dataable_columns[] = array('number', $AccountORM->name);
    }
    
    foreach ($key_dates as $DateTime) {
      $row = array();
      $row[] = $DateTime->format('d-M-Y');
      foreach ($user_accounts_ids as $account_id) {
        $row[] = AccountORM::getTotalAmountAtDate(
          $account_id,
          $DateTime,
          $MinDate
        );
      }
      $datatable_rows[] = $row;
    }
    
    $this->setAjaxResponse(array(
      'columns' => $dataable_columns,
      'rows' => $datatable_rows
    ));
  }
  
  /**
   * Loads data table for charts, ussualy this receibe a min date, max date and
   * account id via $_POST to load data from database with these filters.
   */
  public function ajaxLoadChartDataTable_TEST()
  {
    //$interval = 'day';
    $interval = 'week';
    //$interval = 'month';
    //$interval = 'year';
    
    // Get timestamp from min and max date
    $MinDate = new DateTime();
    $MaxDate = new DateTime();
    $MinDate->setTimestamp(strtotime('2012-09-02'));
    $MaxDate->setTimestamp(strtotime('2012-09-28'));
    
    // Fix the timestamps to fit with the interval
    switch ($interval) {
      case 'day':
        $MinDate->modify('-1 day');
        $MaxDate->modify('+1 day');
        break;
      case 'week':
        // Fit min week
        $week_date = $MinDate->format('N');
        if ($week_date > 1) {
          $MinDate->modify('-'. (($week_date+1) - 1). ' day');
        }
        // Fit max week
        $week_date = $MaxDate->format('N');
        if ($week_date < 7) {
          $MaxDate->modify('+'. (8 - $week_date). ' day');
        }
        break;
      case 'month':
        $MinDate->setDate($MinDate->format('Y'), $MinDate->format('m'), 0);
        $MaxDate->setTimestamp(mktime(0,0,0,$MaxDate->format('m') + 2, 0, $MaxDate->format('Y')));
        break;
    }
    
    echo $MinDate->format('d-m-Y'), ' - ', $MaxDate->format('d-m-y'),
      PHP_EOL, PHP_EOL;
    
    header('Content-Type:text/plain');
    while ($MinDate->getTimestamp() <= $MaxDate->getTimestamp()) {
      echo $MinDate->format('Y-m-d'), PHP_EOL;
      $MinDate->modify('+1 '. $interval);
    }
  }
}
