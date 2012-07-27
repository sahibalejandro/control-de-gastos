User's accounts management
==========================

Show accounts data:
------------------

Load accounts data and render HTML code to show to user.  

Add new account:
----------------
User selects *new account* option in menu bar.  
Show from dialog to fill this fields:

-   Account name
-   Account color, with a default selected color
-   Account initial amount, default to 0
-   Button to cancel
-   Button to save

If user click on *cancel* the dialog is closed.  
If user click on *save* disable buttons and send form data through AJAX to *home/ajax-add-account*

home/ajax-add-account:
----------------------
This method get the new account data from $_POST and insert into database, then
returns the HTML render of new account to client to display in DOM.

    start
      Predefined POST variables: name, color, initial_amount
      name = trim(name)
      settype(initial_amount, float)
      
      If name is empty:
        AJAXRespond "error message"
      Else:
        try:
          Account = new AccountORM
          Account->name    = name
          Account->color   = color
          Account->user_id = Signed user ID
          Account->save()
          If initial_amount is > 0:
            Movement = new MovementORM
            Movement->account_id = Account->id
            Movement->user_id    = Signed user ID
            Movement->type       = 1
            Movement->amount     = initial_amount
            Movement->concept    = "Initial amount"
            Movement->save()
          render = renderView(<account_view_path>, Account)
          AJAXRespond render
        catch ORM exception:
          AJAXRespond "error message"
    end

### on response from home/ajax-add-account ###
If the response is an error message then show it and focus account name field in
the new account form dialog.
    
If the response is the HTML render then hide the new account form dialog and insert
the HTML render in DOM.
