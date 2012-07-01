$(document).on('ready', function(e)
{
  /*
   * Mostrar mensaje "cargando" en todas las solicitudes AJAX
   */
  $('#main_ajax_loading').ajaxStart(function(e)
  {
    $(this).fadeIn('fast');
  }).ajaxStop(function(e)
  {
    $(this).fadeOut('fast');
  });

  /**
   * Quark.ajax default Settings
   */
  Quark.setAJAXSettings({
    fail: function(data)
    {
      Main.showMessage(data.message, 'error');
    },
    scriptError: function(err)
    {
      Main.showMessage('Ocurrió un error al procesar la solicitud, intenta de nuevo.'
        + (QUARK_DEBUG ? "\nERROR:\n" + err : ''), 'error');
    },
    accessDenied: function(url)
    {
      Main.showMessage('Acceso denegado a "' + url + '"', 'error');
    },
    notFound: function(url)
    {
      Main.showMessage('No encontrado "' + url + '"', 'error');
    },
    error: function(jqXHR, text_status, error_thrown)
    {
      Main.showMessage("Error al realizar la solicitud, intenta de nuevo.\nSTATUS: "
        + text_status + "\nMESSAGE: " + error_thrown, 'error');
    }
  });
});

var Main = new (function()
{
  var _alert_titles = {
    error: 'Error!',
    success: 'Perfecto!',
    warning: 'Alerta!',
    info: 'Información:'
  };

  this.showMessage = function(message, type)
  {
    var count_down_interval;
    var alert_time = 10;

    function start_count_down()
    {
      count_down_interval = setInterval(function()
      {
        if(alert_time > 0){
          alert_time--;
          $CountDown.text(alert_time);
        } else {
          clearInterval(count_down_interval);
          $Alert.slideUp('fast', function()
          {
            $(this).remove();
          });
        }
      }, 1000);
    }
    
    var $CountDown = $('<div>').addClass('count_down').text(alert_time);
    var $Alert = $('<div>').addClass('alert' + (type ? ' alert-' + type : ''))
      .html('<strong>' + (type ? _alert_titles[type] : 'Alerta!') + '</strong> '
      + message.replace(/\n/g, '<br />'))
      .prepend( $('<a>').attr({
        href: '#', 'data-dismiss': 'alert'
        }).addClass('close').html('&times;').on('click', function(e)
        {
          clearInterval(count_down_interval);
        }) )
      .on('mouseenter', function()
      {
        clearInterval(count_down_interval);
      }).on('mouseleave', function()
      {
        start_count_down();
      })
      .prepend($CountDown)
      .hide();

    $('#main_msgs_wrapper').append($Alert);
    $Alert.slideDown('fast', function()
    {
      start_count_down();
    });
  }
})();

