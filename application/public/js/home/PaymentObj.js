/**
 * Payment Object
 */
function PaymentObj(Data)
{
  // Row data, this is the fields from table.
  // See "HomeController::ajaxLoadPayments()" to know the fields list.
  this.data = Data;
  
  // Object events
  var Events = {
    onSave: function (RowData)
    {
      console.log('Payment with id ' + RowData.id + ' saved!');
    },
    onFail: function (fail_reason)
    {
      throw 'Fail to save payment with id ' + RowData.id;
    }
  };
  
  var ajax_loading = false;
  
  /**
   * Make an ajax request to save the payment data into database
   */
  this.save = function ()
  {
    if (!ajax_loading) {
      Quark.ajax('home/ajax-save-payment', {
        data: RowData,
        beforeSend: function(jqXHR, Settings)
        {
          ajax_loading = true;
        },
        complete: function(jqXHR, text_status)
        {
          ajax_loading = false;
        },
        success: function(Response, status_text, jqXHR)
        {
          // Refresh row fields with updated data
          RowData = Response.result;
          Events.onSave(RowData);
        },
        fail: function(Response, status_text, jqXHR)
        {
          Events.onFail(Response.message);
        }
      });
    }
  };
};
