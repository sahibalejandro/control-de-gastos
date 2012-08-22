/**
 * Object to handle "new account" methods
 */
var AccountsHandler = new (function()
{
  /**
   * Account ID to use in methods where is not needed to store the value
   * into hidden inputs.
   */
  var _ACCOUNT_ID = 0;
  
  /**
   * Initialize add account engine
   */
  this.init = function()
  {
    // Bind click event to show edit account modal dialog on "add" mode.
    $('#btn_add_account').on('click', function(e)
    {
      e.preventDefault();
      // Pass 0 (zero) to method to "add new account" mode
      AccountsHandler.showModalEditAccount(0);
    });
    
    // Bind click event to show edit account modal dialog
    $('.btn_edit_account').live('click', function(e)
    {
      e.preventDefault();
      AccountsHandler.showModalEditAccount($(this).data().accountId);
    });
    
    // Bind click event to select new account color
    $('.btn_color_selector').on('click', function(e)
    {
      e.preventDefault();
      AccountsHandler.setColorFrom(this);
    });
    
    // Bind submit event to new account form
    $('#frm_edit_account').on('submit', function(e)
    {
      e.preventDefault();
      AccountsHandler.saveAccount();
    });
    
    // Button to show a confirm dialog to delete an account
    $('.btn_delete_account').live('click', function(e)
    {
      e.preventDefault(),
      AccountsHandler.confirmDeleteAccount( $(this).data().accountId );
    });
    
    // Button to confirm delete account
    $('#btn_delete_account').on('click', function(e)
    {
      e.preventDefault();
      AccountsHandler.deleteAccount();
    });
  };
  
  /**
   * Load accounts from server and send the list to callback function
   */
  this.loadAccountsList = function (callback)
  {
    Quark.ajax('home/ajax-load-accounts-list', {
      success: function(Response, status_text, jqXHR)
      {
        callback(Response.result);
      }
    });
  }
  
  /**
   * Sends a request to delete tha account specified by the id _ACCOUNT_ID
   */
  this.deleteAccount = function()
  {
    Quark.ajax('home/ajax-delete-account', {
      data: {
        'account_id': _ACCOUNT_ID
      },
      beforeSend: function(jqXHR, Settings)
      {
        // Disable "delete button"
        $('#btn_delete_account').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        // Enable "delete button"
        $('#btn_delete_account').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        // Hide modal
        $('#modal_delete_account').modal('hide');
        // Remove account and show message
        $('#account_' + _ACCOUNT_ID).remove();
        // Update total amounts
        AccountsHandler.updateTotalAmounts(Response.result.total_amounts);
        // Reload accounts list in pay payment modal
        PaymentsHandler.loadAccountsList();
        Main.alert(Response.message);
      }
    });
  }
  
  /**
   * Show the dialog to confirm the deletion of an account
   */
  this.confirmDeleteAccount = function(account_id)
  {
    // Set the account id to delete on the next call to deleteAccount() method.
    _ACCOUNT_ID = account_id;
    
    // Set account name in title
    $('#delete_account_name').text( $('#account_' + account_id + '_name').text() );
    
    // Show modal
    $('#modal_delete_account').modal({
      'backdrop': 'static',
      'keyboard': false
    });
    
  };
  
  /**
   * Sends AJAX request to try to save account using #frm_edit_account from data
   */
  this.saveAccount = function()
  {
    Quark.ajax('home/ajax-save-account', {
      data: $('#frm_edit_account').serialize(),
      beforeSend: function(jqXHR, Settings)
      {
        $('#btn_save_account').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        $('#btn_save_account').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        // Insert account HTML into DOM if is new account.
        if (Response.result.account_html != null) {
          $('#accounts_list').append(Response.result.account_html);
          
          // Update total amounts
          AccountsHandler.updateTotalAmounts(Response.result.total_amounts);
        } else {
          // Update json account data and DOM
          Account = $.parseJSON(Response.result.account_json);
          var $Account = $('#account_' + Account.id);
          $Account.data().json = Account;
          
          // Update account's color
          $Account.find('.account_header').css(
            'background-color',
            '#' + Account.color
          );
          
          // Update account's name
          $('#account_' + Account.id + '_name').text(Account.name);
        }
        
        // Hide modal dialog
        $('#modal_edit_account').modal('hide');
        
        // Reload accounts list in pay payment modal
        PaymentsHandler.loadAccountsList();
        
        // Show message
        Main.alert(Response.message);
      }
    });
  };
  
  /**
   * Update the DOM to show total amounts
   */
  this.updateTotalAmounts = function(TotalAmounts)
  {
    $('#total_amount_total').text(TotalAmounts.total_formated);
    $('#total_amount_payments').text(TotalAmounts.payments_formated);
    $('#total_amount_available').text(TotalAmounts.available_formated);
  }
  
  /**
   * Shows add account modal dialog
   */
  this.showModalEditAccount = function(account_id)
  {
    // Show modal dialog
    $('#modal_edit_account').modal({
      'backdrop': 'static',
      'keyboard': false
    });
    
    // Configure form to show account's data if necessary
    if (account_id == 0) {
      $('#frm_edit_account').trigger('reset');
      $('#edit_account_name').text(' nueva');
      // Show initial amount layout, maybe was hidden.
      $('#edit_account_init_amount').show();
      // Select default color
      $('.btn_color_selector:first', '#account_color_selectors').trigger('click');
    } else {
      var Account = $('#account_' + account_id).data().json;
      $('#edit_account_name').text(': ' + Account.name);
      $('#account_name').val(Account.name);
      // Initial amount is not needed when editing an account
      $('#edit_account_init_amount').hide();
      // Select account's color
      $(
        '.btn_color_selector[data-color=' + Account.color + ']',
        '#account_color_selectors'
      ).trigger('click');
    }
    
    // Account's ID is always set
    $('#account_id').val(account_id);
    
    // Autofocus account name field
    $('#account_name').focus();
  };
  
  /**
   * Set new account color from the CSS background-color property from given Element
   */
  this.setColorFrom = function(Element)
  {
    // First unselect actual selected color
    $('.btn_color_selector.active').removeClass('active');
    // Now activate Element
    $(Element).addClass('active');
    // Set color value to hidden input
    $('#account_color').val( $(Element).data('color') );
  };
})();
