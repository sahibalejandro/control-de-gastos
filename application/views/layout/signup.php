<?php
$this->renderView('layout/header.php');
?>
<form action="javascript:;" id="frm_signup">
  <h2>Registrate para usar la aplicación</h2>
  <label for="signup_name">Tu nombre:</label>
  <input type="text" name="signup_name" id="signup_name" required="required"
    class="input-xlarge" maxlength="50">
  <label for="signup_email">Correo electrónico:</label>
  <input type="email" name="signup_email" id="signup_email" required="required"
    class="input-xlarge" maxlength="50">
  <label for="signup_passwd">Contraseña:</label>
  <input type="password" name="signup_passwd" id="signup_passwd" required="required"
    class="input-xlarge" maxlength="20">
  <p class="help-block">La contraseña debe tener entre 8 y 20 caracteres.</p>
  <label for="accept_terms">
    <input type="checkbox" name="accept_terms" id="accept_terms" value="1">
    Acepto los <a href="<?php
      echo $this->QuarkURL->getURL('terminos');
    ?>">términos y condiciones.</a>
  </label>
  <div class="form-actions">
    <button type="submit" class="btn btn-success">Registrarme</button>
  </div>
</form>
<?php
$this->appendJsFiles('layout/signup.js')->renderView('layout/footer.php');
?>
