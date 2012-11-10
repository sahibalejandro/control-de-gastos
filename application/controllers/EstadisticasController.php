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
  public function index($account_id = 0)
  {
    // If $account_id is > 0 then verify if the account belongs to the actual user
    if ($account_id > 0) {
      if (AccountORM::query()->count()->where(array(
            'id' => $account_id,
            'users_id' => $this->UserData->id
          ))->puff() == 0
      ) {
        // Account not belongs to the actual user, fuck him!
        $account_id = 0;
      }
    }
    
    $this->addViewVars(array(
      'account_id' => $account_id,
      'accounts'   => AccountORM::query()
        ->find()
        ->where(array('users_id' => $this->UserData->id))
        ->order('name')
        ->puff()
    ));
    $this->renderView();
  }
  
  /**
   * Load data for populate the data-tables in javascript, for the Google API,
   * if $_POST['account_id'] is > 0 then the data-tables belongs just for one
   * account, else retreieve data-tables for all user's accounts.
   * If $_POST['account_id'] is "avaiable" then retrieve the data-tables for avaiable
   * amounts.
   */
  public function ajaxLoadChartDataTable()
  {
    // Get min date of all user's movements.
    $MinDate = MovementORM::getMovementsMinDate(
      $this->UserData->id,
      $_POST['account_id']
    );
    
    if ($_POST['account_id'] > 0) {
      $user_accounts_ids = array($_POST['account_id']);
    } else {
      /* Extract the IDs of user's accounts, and add the account ID 0, to graph
       * the totals */
      $user_accounts_ids = array(0);
      
      $user_accounts = AccountORM::query()
        ->select('id')
        ->where(array('users_id' => $this->UserData->id))
        ->puff();
        
      foreach ($user_accounts as $UserAccount) {
        $user_accounts_ids[] = $UserAccount->id;
      }
    }
    
    /** Get key dates of movements in the user's accounts */
    $key_dates = MovementORM::getKeyDates($this->UserData->id, $user_accounts_ids);
    
    $datatable_rows = array();
    $datatable_columns = array(
      array('string', 'Fecha')
    );
    
    foreach ($user_accounts_ids as $account_id) {
      if ($account_id == 0) {
        $datatable_columns[] = array('number', 'Total');
      } else {
        $AccountORM = AccountORM::query()->findByPk($account_id);
        $datatable_columns[] = array('number', $AccountORM->name);
      }
    }
    
    foreach ($key_dates as $DateTime) {
      $row = array();
      $row[] = $DateTime->format('d-M-Y');
      foreach ($user_accounts_ids as $account_id) {
        $row[] = AccountORM::getTotalAmountAtDate(
          $account_id,
          $this->UserData->id,
          $DateTime,
          $MinDate
        );
      }
      $datatable_rows[] = $row;
    }
    
    $this->setAjaxResponse(array(
      'columns' => $datatable_columns,
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
