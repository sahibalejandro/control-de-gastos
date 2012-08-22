/**
 * Payment Object
 */
function PaymentObj(Data)
{
  // Row data, this is the fields from table.
  // See "HomeController::ajaxLoadPayments()" to know the fields list.
  this.data = Data;
  
  /**
   * Is true while ajax loading
   */
  var ajax_loading = false;
  
  /**
   * To access from inside ajax events.
   */
  var _this = this;
  
  /**
   * Make an ajax request to delete the payment from database
   */
  this.delete = function(success_callback, fail_callback)
  {
    if (!ajax_loading) {
      Quark.ajax('home/ajax-delete-payment', {
        data: {'payment_id': this.data.id},
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
          success_callback();
        },
        fail: function(Response, status_text, jqXHR)
        {
          fail_callback(Response);
        }
      });
    }
  }
  
  /**
   * Make an ajax request to save the payment data into database
   */
  this.save = function (success_callback, fail_callback)
  {
    if (!ajax_loading) {
      Quark.ajax('home/ajax-save-payment', {
        data: {
          'id': this.data.id,
          'amount': this.data.amount,
          'concept': this.data.concept
        },
        beforeSend: function (jqXHR, Settings)
        {
          ajax_loading = true;
        },
        complete: function (jqXHR, text_status)
        {
          ajax_loading = false;
        },
        success: function (Response, status_text, jqXHR)
        {
          // Refresh row fields with updated data
          _this.data = Response.result;
          success_callback(_this.data);
        },
        fail: function (Response, status_text, jqXHR)
        {
          fail_callback(Response);
        }
      });
    }
  };
};
