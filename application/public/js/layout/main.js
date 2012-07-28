$(document).on('ready', function(e)
{
  // Set default Quark.ajax settings
  Quark.setAJAXSettings({
    fail: function(Response, status_text, jqXHR)
    {
      Main.alert('FAIL: ' + Response.message);
    }
  });
});

// Wrap main functions in Main object.
var Main = new (function()
{
  this.alert = function(message)
  {
    /**
     * TODO: Make super fancy alerts and implement "message type"
     */
    alert(message);
  }
})();
