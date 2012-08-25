<?php
$this
  ->appendCssFiles('layout/login.css')
  ->renderView('layout/header.php');

if ($twitter_err != false):
?>
Falló la comunicación con twitter, intenta más tarde. (<?php echo $this->QuarkStr->esc($twitter_err); ?>)
<?php
else:
?>
Conectate usando tu cuenta de Twitter, no no necesitas registrarte y es más seguro.
<a href="<?php echo $auth_url ?>" id="btn_signin">Inicia sesión con Twitter</a>
<?php
endif;

$this->renderView('layout/footer.php');
?>
