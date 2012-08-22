/**
 * Object to handle movements methods
 */
var MovementsHandler = new (function()
{
  // Change this value if .account_body height is modified in home/index.css
  // This value is used in .movements_list's 'mousewheel' event
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
      MovementsHandler.showEditMovement($(this).data().accountId, null);
    });
    
    // Edit a movement
    $('.btn_movement_edit').live('click', function(e)
    {
      e.preventDefault();
      MovementsHandler.showEditMovement($(this).data().accountId
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
        // Update the DOM with new/updated movement
        $Movement = $('#movement_' + $('#movement_id').val());
        if($Movement.length == 0){
          // Insert new movement in the account list
          $('#account_' + $('#movement_account_id').val()).find('.movements_list').prepend(Response.result.movement_html);
          
        } else {
          // Replace actual movement with the new one.
          $('#movement_' + $('#movement_id').val()).replaceWith(Response.result.movement_html);
        }
        
        // Update user's total amounts
        AccountsHandler.updateTotalAmounts(Response.result.total_amounts);
        
        // Hide movement modal
        $('#modal_edit_movement').modal('hide');
      }
    });
  };
  
  /**
   * Shows the modal dialog of form to edit an account's movement.
   */
  this.showEditMovement = function(account_id, movement_id)
  {
    // Movement date default to today.
    var MovementDate = new Date();
    var $MovementTypeRadios = $('input:radio[name=movement_type]');
    
    // Add the account's name to the title of the modal dialog
    $('#movement_account_name').text(
      $('#account_' + account_id + '_name').text()
    );
    
    if(movement_id == null){
      // for add a new movement reset the form
      $('#frm_movement').trigger('reset');
      // hidden inputs are not reset by reset method?
      $('#movement_id').val(0);
      // Radio buttons don't reset if "checked" property was changed by javascript?
      $MovementTypeRadios[0].checked = true;
    } else {
      // get the "data-json" from the movement to fill the form
      var Movement = $('#movement_' + movement_id).data().json;
      
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
    $('#modal_edit_movement').modal({
      'backdrop': 'static',
      'keyboard': false
    });
    $('#movement_concept').focus();
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
  };
})();
