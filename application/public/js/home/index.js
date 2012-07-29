$(document).on('ready', function(e)
{
  // Configure jQuery UI Datepicker default settings.
  $.datepicker.setDefaults({
    'altFormat': 'yy-mm-dd',
    'dateFormat': "D dd 'de' M, yy"
  });
  
  // Initialize add accounts handler
  AccountsHandler.init();
  
  // Initialize movements handler
  MovementsHandler.init();
});

/**
 * Object to handle "new account" methods
 */
var AccountsHandler = new (function()
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
      AccountsHandler.showModalAddAccount();
    });
    
    // Bind click event to select new account color
    $('.btn_color_selector').on('click', function(e)
    {
      AccountsHandler.setColorFrom(this);
    });
    
    // Bind submit event to new account form
    $('#frm_add_account').on('submit', function(e)
    {
      e.preventDefault();
      AccountsHandler.add();
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
        // Insert account HTML into DOM and show message
        $('#accounts_list').append(Response.result.account_html);
        // Update total amounts
        AccountsHandler.updateTotalAmounts(Response.result.total_amounts);
        // Hide modal dialog
        $('#modal_add_account').modal('hide');
        // Show message
        Main.alert(Response.message);
      }
    });
  };
  
  /**
   * TODO: Implement this method
   */
  this.updateTotalAmounts = function(TotalAmounts)
  {
    console.log(TotalAmounts);
  }
  
  /**
   * Shows add account modal dialog
   */
  this.showModalAddAccount = function()
  {
    // Show modal dialog
    $('#modal_add_account').modal({
      'backdrop': 'static',
      'keyboard': false
    });
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

/**
 * Object to handle movements methods
 */
var MovementsHandler = new (function()
{
  // Change this value if .account_body height is modified in home/index.css
  var _account_body_h = 400;
  
  /**
   * Initialize events and stuff.
   */
  this.init = function()
  {
    // Show movement actions buttons
    $('.movement').live('mouseenter', function(e)
    {
      $(this).find('.movement_actions').show();
    })
    // Hide movement actions buttons and dropdown menu if is visible
    .live('mouseleave', function(e)
    {
      if($(this).find('.dropdown-menu').is(':visible')){
        $(this).find('.dropdown-toggle').trigger('click');
      }
      $(this).find('.movement_actions').hide();
    });
    
    // Load mover movements
    $('.btn_more_movements').live('click', function(e)
    {
      e.preventDefault();
      MovementsHandler.loadMore(this);
    });
    
    // Toggle movement type
    $('.btn_movement_change_type').live('click', function(e)
    {
      e.preventDefault();
      MovementsHandler.changeMovementType(this);
    });
    
    // Delete movement
    $('.btn_movement_delete').live('click', function(e)
    {
      e.preventDefault();
      MovementsHandler.deleteMovement(this);
    });
    
    // Datepicker
    $('#movement_date_gui').datepicker({
      'altField': '#movement_date'
    });
    
    // Add a new movement to an account.
    $('.btn_new_movement').live('click', function(e)
    {
      e.preventDefault();
      MovementsHandler.editMovement($(this).data().accountId, null);
    });
    
    // Edit a movement
    $('.btn_movement_edit').live('click', function(e)
    {
      e.preventDefault();
      MovementsHandler.editMovement($(this).data().accountId
        , $(this).data().movementId);
    });
    
    // Submit movement
    $('#frm_movement').on('submit', function(e)
    {
      e.preventDefault();
      MovementsHandler.saveMovement();
    });
        
    // Assign "mousewheel" event to scroll movement lists
    $('.movements_list').live('mousewheel', function(e, delta, delta_x, delta_y)
    {
      var $MovementList   = $(this);
      var movement_list_h = $MovementList.height();
      
      // Only scrolls if necessary
      if(movement_list_h > _account_body_h){
        var jump  = delta > 0 ? +20 : -20;
        var y = $MovementList.position().top + jump;
        
        // Scrolling up (mouswheel down)
        if(delta > 0){
          if( y > 0){
            y = 0;
          } else {
            e.preventDefault();
          }
        // Scrolling down (mouswheel up)
        } else {
          var min_y = -(movement_list_h - _account_body_h);
          if(y < min_y ){
            y = min_y;
          } else {
            e.preventDefault();
          }
        }
        $MovementList.css('top', y + 'px');
      } 
    });
  };
  
  /**
   * Sends movement data to save into database
   */
  this.saveMovement = function()
  {
    Quark.ajax('home/ajax-save-movement', {
      data: $('#frm_movement').serialize(),
      beforeSend: function(jqXHR, Settings)
      {
        $('#btn_save_movement').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        $('#btn_save_movement').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        
      }
    });
  };
  
  /**
   * Shows the modal dialog of form to edit an account's movement.
   */
  this.editMovement = function(account_id, movement_id)
  {
    // Movement date default to today.
    var MovementDate = new Date();
    
    // Add the account's name to the title of the modal dialog
    $('#movement_account_name').text(
      $('#account_' + account_id + '_name').text()
    );
    
    if(movement_id == null){
      // for add a new movement just clean the form
      $('#frm_movement').trigger('reset');
    } else {
      // get the "data-json" from the movement to fill the form
      var Movement = $('#movement_' + movement_id).data().json;
      var $MovementTypeRadios = $('input:radio[name=movement_type]');
      
      // The date comes in "yyyy-mm-dd hh:mm:ss" format, we need only
      // the "yyyy-mm-dd" part, so we split it.
      MovementDate = $.datepicker.parseDate('yy-mm-dd', Movement.date.split(' ')[0]);
      $('#movement_id').val(Movement.id);
      $('#movement_amount').val(Movement.amount);
      $('#movement_concept').val(Movement.concept);
      $MovementTypeRadios.removeAttr('checked');
      if(Movement.type == 1){
        $MovementTypeRadios[0].checked = true;
      } else {
        $MovementTypeRadios[1].checked = true;
      }
    }
        
    // The form always need the account's id.
    $('#movement_account_id').val(account_id);
    
    // Set date int the datepicker
    $('#movement_date_gui').datepicker('setDate', MovementDate);
    
    // Show modal and focus amount field
    $('#modal_edit_movement').modal('show');
    $('#movement_amount').focus();
  };
  
  /**
   * Request the delete of a movement.
   */
  this.deleteMovement = function(CallerBtn)
  {
    var $Btn = $(CallerBtn);
    // Send request only if the button is not locked
    if( !$Btn.data('locked') ){
      var movement_id = $Btn.data('movement-id');
      var $Movement = $('#movement_' + movement_id);
      
      Quark.ajax('home/ajax-delete-movement', {
        data: {
          'movement_id': movement_id
        },
        beforeSend: function(jqXHR, Settings)
        {
          // Lock button
          $Btn.data('locked', true);
          // Mark movement to delete
          $Movement.addClass('to_delete');
        },
        complete: function(jqXHR, text_status)
        {
          // Unlock button
          $Btn.data('locked', false);
          // Unmark movement only if is not animating, if is animating that means
          // will be removed
          if(!$Movement.is(':animated')){
            $Movement.removeClass('to_delete');
          }
        },
        success: function(Response, status_text, jqXHR)
        {
          // Remove movement from DOM
          $Movement.slideUp('fast', function(e)
          {
            $(this).remove();
          });
          // Update total amounts
          AccountsHandler.updateTotalAmounts(Response.result.total_amounts);
        }
      });
    }
  };
  
  /**
   * Request movement change type
   */
  this.changeMovementType = function(CallerBtn)
  {
    var $Btn = $(CallerBtn);
    // Send request only if the button is not locked
    if( !$Btn.data('locked') ){
      var movement_id = $Btn.data('movement-id');
      Quark.ajax('home/ajax-change-movement-type', {
        data: {
          'movement_id': movement_id
        },
        beforeSend: function(jqXHR, Settings)
        {
          // Lock button
          $Btn.data('locked', true);
        },
        complete: function(jqXHR, text_status)
        {
          // Unlock button
          $Btn.data('locked', false);
        },
        success: function(Response, status_text, jqXHR)
        {
          // Update movement type in DOM
          $('#movement_' + movement_id).removeClass('in out').addClass(
            Response.result.type == 1 ? 'in' : 'out'
          ).data().json.type = Response.result.type;
          
          // Update total amounts
          AccountsHandler.updateTotalAmounts(Response.result.total_amounts);
        }
      });
    }
  };
  
  /**
   * Sends request to load more movements
   */
  this.loadMore = function(CallerBtn)
  {
    var $Btn = $(CallerBtn);
    // Load only if is not loading
    if( !$Btn.data('locked') && !$Btn.data('all_loaded') ){
      // Backup button text to restore on complete request.
      var btn_text = $Btn.text();
      
      Quark.ajax('home/ajax-load-more-movements', {
        data: {
          'account_id': $Btn.data('account-id'),
          'max_timestamp': $Btn.data('max-timestamp')
        },
        beforeSend: function(jqXHR, Settings)
        {
          // Lock loading task and change the button text
          $Btn.data('locked', true).text('Cargando...');
        },
        complete: function(jqXHR, text_status)
        {
          // Unlock loading task and restore the original button text only
          // if not all movements was loaded.
          if(!$Btn.data('all_loaded')){
            $Btn.data('locked', false).text(btn_text);
          }
        },
        success: function(Response, status_text, jqXHR)
        {
          // Only add movements if the exists
          if(Response.result.loaded_ids.length == 0){
            $Btn.data('all_loaded', true).text('No hay más movimientos');
          } else {
            // Update max timestamp in the button
            $Btn.data('max-timestamp', Response.result.max_timestamp);
            // Search for movements already in DOM to not duplicate it.
            for(i in Response.result.loaded_ids){
              $Movement = $('#movement_' + Response.result.loaded_ids[i]);
              if($Movement.length > 0){
                $Movement.remove();
              }
            }
            // Insert loaded movements before "load more" button
            $Btn.before(Response.result.movements_html);
          }
        }
      });
    }
  }
  
})();
