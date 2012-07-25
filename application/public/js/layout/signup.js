$(document).on('ready', function(e)
{
  // Send signup form through AJAX
  $('#frm_signup').on('submit', function(e)
  {
    Quark.ajax('home/ajax-signup', {
      data: $(this).serialize(),
      beforeSend: function(jqXHR, Settings)
      {
        $('#btn_signup').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        $('#btn_signup').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        Main.alert(Response.message);
        // Reset form.
        $('#frm_signup').trigger('reset');
      }
    });
  });
});
