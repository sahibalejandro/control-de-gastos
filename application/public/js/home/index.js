$(document).on('ready', function(e)
{
  // Initialize AddAccount handler
  AddAccount.init();
});

/**
 * Object to handle "new account" methods
 */
var AddAccount = new (function()
{
  /**
   * Initialize add account engine
   */
  this.init = function()
  {
    // Bind click event to show add account modal dialog
    $('#btn_add_account').on('click', function(e)
    {
      e.preventDefault();
      AddAccount.showModal();
    });
    
    // Bind click event to select new account color
    $('.btn_color_selector').on('click', function(e)
    {
      AddAccount.setColorFrom(this);
    });
    
    // Bind submit event to new account form
    $('#btn_submit_add_account').on('click', function(e)
    {
      AddAccount.add();
    });
  };
  
  /**
   * Sends AJAX request to try to add a new account using #frm_add_account from data.
   */
  this.add = function()
  {
    Quark.ajax('home/ajax-add-account', {
      data: $('#frm_add_account').serialize(),
      beforeSend: function(jqXHR, Settings)
      {
        $('#btn_submit_add_account').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        $('#btn_submit_add_account').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        
      }
    });
  }
  
  /**
   * Shows add account modal dialog
   */
  this.showModal = function()
  {
    // Show modal dialog
    $('#modal_add_account').modal();
    // Reset form
    $('#frm_add_account').trigger('reset');
    // Select first color
    $('.btn_color_selector:first', '#account_color_selectors').trigger('click');
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
