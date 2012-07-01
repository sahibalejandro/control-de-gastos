<?php
$this->renderView('layout/header.php');
?>
<form id="frm_login" class="well" action="javascript:;">
  <h2>Iniciar sesión</h2>
  <label>EMail:</label>
  <input type="text" name="email" id="email" />
  <label>Contraseña:</label>
  <input type="password" name="pass" id="pass" />
  <label class="checkbox">
    <input type="checkbox" name="remember" value="S"> Recordarme durante 15 días.
  </label>
  <button id="btn_login" type="submit" class="btn">Iniciar sesión</button>
</form>
<?php
$this->appendJsFiles('home/login.js')
  ->renderView('layout/footer.php');
?>
