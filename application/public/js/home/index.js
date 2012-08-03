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
  
  // Initialize payments handler
  PaymentsHandler.init();
});
