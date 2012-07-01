$(document).on('ready', function(e)
{
  DynamicInputs.initialize(); 
});

var DynamicInputs = new (function()
{
  // Lista de meses para el metodo toDate()
  var _months = [null, 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago'
    , 'Sep', 'Oct', 'Nov', 'Dic'];

  this.initialize = function()
  {
    // Eventos live
    $('.dynamic_input').live('focus', function(e)
    {
      this.value = $(this).data('inputData').original;
      $(this).select();
    });

    $('.dynamic_input').live('blur', function(e)
    {
      DynamicInputs.requestUpdate(this, true);
    });

    $('.dynamic_input').live('keypress', function(e)
    {
      if(e.keyCode == 13){
        DynamicInputs.requestUpdate(this, false);
      }
    });
  };

  this.requestUpdate = function(InputHTMLElement, auto_format)
  {
    var $Input = $(InputHTMLElement);
    var new_value = $Input.val();

    /*
     * Validar el formato, si no es valido volvemos al valor original y no
     * hacemos update
     */
    var valid = true;
    if($Input.hasClass('currency'))
    {
      valid = new_value.match(/^\d{1,7}(\.\d+)?$/);
    } else if($Input.hasClass('date')){
      valid = new_value.match(/^\d{4}(-\d{1,2}){2}$/);
    }

    if(!valid){
      InputHTMLElement.value = new_value = $Input.data('inputData').original;
    }

    if( new_value == $Input.data('inputData').original ){
      // No hay cambios, solo formato.
      if(auto_format){
        DynamicInputs.formatInput(InputHTMLElement)
      }
    } else {
      /*
       * Solicitar actualización del dato
       */
      
      // Pow! pow! pow! Sangre!
      Quark.ajax('home/ajax-update-field', {
        data: {
          id: $Input.data('inputData').id,
          orm: $Input.data('inputData').orm,
          field: $Input.data('inputData').field,
          value: new_value
        },
        beforeSend: function()
        {
          $Input.attr('disabled', 'disabled');
        },
        complete: function(jqXHR, text_status, QuarkJSONResponse)
        {
          $Input.removeAttr('disabled');

          // Si no se pudo actualizar el dato volvemos al original
          if(QuarkJSONResponse.error || QuarkJSONResponse.data.error){
            $Input.val( $Input.data('inputData').original );
          } else {
            $Input.data('inputData').original = new_value;
            // Al actualizar los curremcy/amount hay que actualizar
            // los montos totales
            Accounts.loadTotalAmounts();
          }

          // Formatear el campo si es necesario
          if(auto_format){
            DynamicInputs.formatInput(InputHTMLElement);
          }
        }
      });
    }
  };

  this.formatInput = function(InputHTMLElement)
  {
    $Input = $(InputHTMLElement);
    if($Input.hasClass('currency')){
      InputHTMLElement.value = DynamicInputs.toCurrency(InputHTMLElement.value);
    } else if($Input.hasClass('date')){
      InputHTMLElement.value = DynamicInputs.toDate(InputHTMLElement.value);
    }
  };

  this.formatInputsInScope = function(scope)
  {
    $('.dynamic_input', scope).each(function()
    {
      DynamicInputs.formatInput(this);
    });
  };

  /* Le formatters
  ==================================================*/
  this.toCurrency = function(number)
  {
    // Expresion 0+000
    var Rgx = /(\d+)(\d{3})/;
    // Seprar enteros y decimales
    var n = number.split('.');
    
    // reemplazar 0+000 por 0,000 hasta que no exista 0+000
    while(Rgx.test(n[0])){
      n[0] = n[0].replace(Rgx, '$1,$2');
    }

    // Unir todo en cadena tipo $0,000,000.00
    return '$ ' + n[0] + '.' + (n[1] == undefined ? '00' : n[1]);
  }

  this.toDate = function(date)
  {
    var date = date.split('-');
    var day = parseInt(date[2], 10);
    var month = parseInt(date[1], 10);
    var year = date[0];

    return (day < 10 ? '0'+day : day) + ' - ' + _months[month] + ' - ' + year;
  }

})();
