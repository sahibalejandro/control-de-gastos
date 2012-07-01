$(document).on('ready', function(e)
{
  // Seamos amigables y evitemos un click al usuario
  $('#email').trigger('focus');

  /*
   * Enviar datos del formulario de login usando AJAX
   */
  $('#frm_login').on('submit', function(e)
  {
    e.preventDefault();
    Quark.ajax('home/ajax-login',{
      data: $(this).serialize(),
      beforeSend: function()
      {
        $('#btn_login').attr('disabled','disabled').text('Iniciando...');
      },

      complete: function(jqXHR, text_status, QuarkJSONResponse)
      {
        $('#btn_login').removeAttr('disabled').text('Iniciar sesión');
      },

      success: function(data, text_status, jqXHR)
      {
        if(data.result == true){
          window.location.href = Quark.getURL('home');
        }
      },
      fail: function(data, text_status, jqXHR)
      {
        Main.showMessage(data.message, 'error');
        $('#pass').val('');
      }
    });
  });
  /* END: login */
});
