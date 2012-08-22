/**
 * Handle all payments functionality, besides uses PaymentObj.js
 */
var PaymentsHandler = new (function ()
{
  var payments = [];
  
  /**
   * Delete a payment object calling their own delete method and removing from DOM
   * on success.
   */
  this.delete = function (payment_id)
  {
    payments[payment_id].delete(
      // Success callback
      function ()
      {
        // Remove payment from DOM
        $('#payment_' + payment_id).remove();
      },
      // Fail callback
      function (QuarkAJAXResponse)
      {
        Main.alert(QuarkAJAXResponse.message);
      });
  };
  
  /**
   * Update an existing ".payment" element with data from the payment object
   * specified by the id "payment_id"
   */
  this.refreshDOM = function (payment_id)
  {
    var $Payment = $('#payment_' + payment_id);
    $Payment.find('.payment_concept').text(payments[payment_id].data.concept);
    $Payment.find('.payment_amount').text(payments[payment_id].data.amount_formated);
  };
  
  this.showModalPayment = function (payment_id)
  {
    if (payment_id == null) {
      // Reset form to add new payment
      $('#frm_payment').trigger('reset');
      // hidden inputs don't reset?
      $('#payment_id').val(0);
    } else {
      // Load payment data to fill form
      $('#payment_id').val(payment_id);
      $('#payment_concept').val(payments[payment_id].data.concept);
      $('#payment_amount').val(payments[payment_id].data.amount);
    }
    
    // Show modal and focus payment concept field
    $('#modal_payment').modal({
      'backdrop': 'static',
      'keyboard': false
    });
    $('#payment_concept').focus();
  };
  
  /**
   * Initialize payments engine
   */
  this.init = function ()
  {
    // Show dialog to add new payment
    $('#btn_add_payment').on('click', function (e)
    {
      e.preventDefault();
      PaymentsHandler.showModalPayment(null);
    });
    
    // On submit payment form
    $('#frm_payment').on('submit', function (e)
    {
      e.preventDefault();
      
      var Payment;
      var payment_id = $('#payment_id').val();
      var is_new = false;
      
      if (payment_id > 0) {
        // Get payment object to update
        Payment = payments[payment_id];
      } else {
        // Create new empty Payment object and set ID to 0 (zero) for
        // INSERT new payment on save
        Payment = new PaymentObj({'id': 0});
        is_new = true;
      }
      
      // Get data from the form and update the object
      Payment.data.concept = $('#payment_concept').val();
      Payment.data.amount  = $('#payment_amount').val();
      
      // Save payment
      Payment.save(
        // Success callback
        function (PaymentData)
        {
          // Hide modal
          PaymentsHandler.hideModalPayment();
          // Update DOM
          if (!is_new) {
            PaymentsHandler.refreshDOM(PaymentData.id);
          } else {
            // Insert new payment in payments list.
            payments[PaymentData.id] = new PaymentObj(PaymentData);
            PaymentsHandler.insertInDOM(PaymentData.id);
          }
        },
        
        // Fail callback
        function (QuarkAJAXResponse)
        {
          // Hide modal
          PaymentsHandler.hideModalPayment();
          // Show error message
          Main.alert(QuarkAJAXResponse.message);
        });
    });
    
    // Load all payments
    Quark.ajax('home/ajax-load-payments', {
      success: function(Response, status_text, jqXHR)
      {
        // Fill payments array
        for (i in Response.result) {
          
          // Insert payment in payments array
          var Payment = new PaymentObj(Response.result[i]);
          payments[Payment.data.id] = Payment;
          
          // Insert payment in DOM
          PaymentsHandler.insertInDOM(Payment.data.id);
        }
      }
    });
  }
  
  /**
   * Create and insert new HTML elements in DOM to display a payment specified
   * by the id
   */
  this.insertInDOM = function (payment_id)
  {
    /*
     * Create all DOM elements
     * ======================================================================== */
    var $Wrapper = $('<div>').addClass('payment')
      .attr('id', 'payment_' + payment_id);
      
    var $Header = $('<div>').addClass('payment_header clearfix');
    var $Concept = $('<div>').addClass('payment_concept');
    var $Amount = $('<div>').addClass('payment_amount');
    var $BtnGroup = $('<div>').addClass('btn-group');
    
    var $BtnDropdown = $('<a>').addClass('btn btn-mini dropdown-toggle')
      .attr('data-toggle', 'dropdown')
      .append($('<span>').addClass('caret'));
      
    var $DropdownMenu = $('<ul>').addClass('dropdown-menu');
    
    var $MenuItemPay = $('<li>');
    var $MenuItemPayBtn = $('<a>').attr('href', '#').append(
      $('<i>').addClass('icon-ok'),
      ' Pagar'
    );
    
    var $MenuItemEdit = $('<li>');
    var $MenuItemEditBtn = $('<a>').attr('href', '#').append(
      $('<i>').addClass('icon-pencil'),
      ' Editar...'
    );
    
    var $MenuItemDelete = $('<li>');
    var $MenuItemDeleteBtn = $('<a>').attr('href', '#').append(
      $('<i>').addClass('icon-trash'),
      ' Borrar pago'
    );
    
    /*
     * Events
     * ======================================================================== */
     
    // Show/hide dropdown button
    $Wrapper.on('mouseenter', function (e)
    {
      $BtnGroup.show();
    }).on('mouseleave', function (e)
    {
      $BtnGroup.hide();
    });
    
    // Pay button
    $MenuItemPayBtn.on('click', function (e)
    {
      e.preventDefault();
      console.log('Pay: ', payment_id);
    });
    
    // Edit button
    $MenuItemEditBtn.on('click', function (e)
    {
      e.preventDefault();
      PaymentsHandler.showModalPayment(payment_id);
    });
    
    // Delete button
    $MenuItemDeleteBtn.on('click', function (e)
    {
      e.preventDefault();
      PaymentsHandler.delete(payment_id);
    });
    
    /*
     * Merge all elements
     * ======================================================================== */
    $Wrapper.append(
      $Header.append(
        $Concept,
        $BtnGroup.append(
          $BtnDropdown,
          $DropdownMenu.append(
            $MenuItemPay.append($MenuItemPayBtn),
            $MenuItemEdit.append($MenuItemEditBtn),
            $MenuItemDelete.append($MenuItemDeleteBtn)
          )
        )
      ),
      $Amount
    );
    
    // Insert payment in DOM (with no data like amount or concept)
    $('#payments_bar').append($Wrapper);
    
    // Refresh payment element data
    this.refreshDOM(payment_id);
  };
  
  this.hideModalPayment = function ()
  {
    $('#modal_payment').modal('hide');
  };
})();
