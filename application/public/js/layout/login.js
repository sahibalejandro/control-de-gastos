$(document).on('ready', function(e)
{
  // Focus email input after show the login form.
  $('#btn_login_dropdown').on('click', function(e)
  {
    // Wait before focus the input because the form is hidden.
    setTimeout(function()
    {
      $('#login_email').focus();
    }, 150);
  });
  
  // Submit form data through AJAX
  $('#frm_login').on('submit', function(e)
  {
    Quark.ajax('home/ajax-login', {
      data: $(this).serialize(),
      beforeSend: function(jqXHR, Settings)
      {
        $('#btn_login').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        $('#btn_login').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        // Reload page on login
        // window.location.reload(true);
      },
      fail: function(Response, status_text, jqXHR)
      {
        // Call global fail function
        Quark.getAJAXSettings().fail(Response, status_text, jqXHR);
        
        // Clear password and focus email field.
        $('#login_passwd').val('');
        $('#login_email').select();
      }
    });
  });
});
