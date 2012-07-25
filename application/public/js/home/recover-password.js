$(document).on('ready', function(e)
{
  // Submit form through AJAX
  $('#frm_recover_password').on('submit', function(e)
  {
    Quark.ajax('home/ajax-recover-password', {
      data: $(this).serialize(),
      beforeSend: function(jqXHR, Settings)
      {
        $('#btn_send').attr('disabled', 'disabled');
      },
      complete: function(jqXHR, text_status)
      {
        $('#btn_send').removeAttr('disabled');
      },
      success: function(Response, status_text, jqXHR)
      {
        Main.alert(Response.message);
      }
    });
  });
});
