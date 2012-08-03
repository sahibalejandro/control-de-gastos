var PaymentsHandler = new (function ()
{
  var payments = [];
  
  this.add = function (Payment)
  {
    payments[Payment.id] = Payment;
  };
  
  this.get = function (payment_id)
  {
    return payments[payment_id];
  };
  
  this.delete = function (payment_id)
  {
    payments[payment_id].delete();
  };
  
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
    
    // Insert payment in DOM
    $('#payments_bar').append($Wrapper);
    
    // Refresh payment element data
    this.refreshDOM(payment_id);
  };
  
  this.refreshDOM = function (payment_id)
  {
    var $Payment = $('#payment_' + payment_id);
    $Payment.find('.payment_concept').text(payments[payment_id].data.concept);
    $Payment.find('.payment_amount').text(payments[payment_id].data.amount_formated);
  };
  
  this.init = function ()
  {
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
  
})();
