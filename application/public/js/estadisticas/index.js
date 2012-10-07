/**
 * Script for statistics section
 * @author Sahib J. Leo
 */
$(document).on('ready', function (e)
{
  DateSelector.init();
  // Disable component, it will be enabled when chart API is loaded.
  DateSelector.enable(false);
  
  ChartHandler.init();
});

/**
 * Date selector component
 * @author Sahib J. Leo
 */
var DateSelector = new (function ()
{
  /** Account selector */
  var $AccountSelector;
  
  /** Button to load data and draw chart */
  var $BtnChart;
  
  /**
   * Initialize component
   */
  this.init = function ()
  {  
    /** Get elements references */
    $BtnChart        = $('#btn_chart');
    $AccountSelector = $('#account_id');
    
    /** Button chart event */
    $BtnChart.on('click', function (e)
    {
      e.preventDefault();
      ChartHandler.loadDataAndDrawChart({
        'account_id': $AccountSelector.val()
      });
    });
  };
  
  /**
   * Enable/disable date selector component
   */
  this.enable = function (enable)
  {
    if (enable) {
      $BtnChart.removeAttr('disabled');
      $AccountSelector.removeAttr('disabled');
    } else {
      $BtnChart.attr('disabled', 'disabled');
      $AccountSelector.attr('disabled', 'disabled');
    }
  };
});

/**
 * Object for load data and drawin charts
 * @author Sahib J. Leo
 */
var ChartHandler = new (function ()
{
  /** Flag to know when data table is loading from server */
  var loading_data_table = false;
  
  /**
   * Called when visualization API is loaded
   */
  function on_load_callback()
  {
    DateSelector.enable(true);
  };
  
  /**
   * Initialize the Google's Visualization engine
   */
  this.init = function ()
  {
    // Load the Visualization API
    google.load('visualization', '1.0', {
      'packages':['corechart'],
      'callback': on_load_callback
    });
  };
  
  /**
   * Loads data from server using the settings, then draw the chart.
   * Settings:
   *   account_id
   */
  this.loadDataAndDrawChart = function (Settings)
  {
    Quark.ajax('estadisticas/ajax-load-chart-data-table', {
      data: {
        'account_id': Settings.account_id
      },
      beforeSend: function(jqXHR, Settings)
      {
        if (loading_data_table) {
          return false;
        } else {
          loading_data_table = true;
        }
      },
      complete: function(jqXHR, text_status)
      {
        loading_data_table = false;
      },
      success: function(Response, status_text, jqXHR)
      {
        var ChartData = new google.visualization.DataTable();
        
        /** Columns */
        for (i in Response.result.columns) {
          ChartData.addColumn(
            Response.result.columns[i][0],
            Response.result.columns[i][1]
          );
        }
        
        /** Rows */
        ChartData.addRows(Response.result.rows);
        
        /** Chart options */
        var ChartOptions = {
          'title': 'Cuentas',
          'width': 938,
          'height': 478
        };
        
        /** Create chart and draw it */
        var Chart = new google.visualization.LineChart(document.getElementById('chart'));
        Chart.draw(ChartData, ChartOptions);
      },
      fail: function(Response, status_text, jqXHR)
      {
        Main.alert(Response.message);
      }
    });
    // end of: Quark.ajax(...);
  };
  // end of: this.loadDataAndDrawChart()
})();
