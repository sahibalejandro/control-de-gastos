<!DOCTYPE HTML>
<html lang="es-MX">
<head>
  <meta charset="UTF-8">
  <title><?php
    // Print page title if necessary
    if(!empty($this->page_title)):
      echo $this->QuarkStr->esc($this->page_title), ' - ';
    endif;
    ?>Control de gastos
  </title>
  <?php
  $this
    ->prependCssFiles('layout/bootstrap.css', 'layout/main.css')
    ->includeCssFiles();
  ?>
</head>
<body>
  <!-- BEGIN: Main navbar = = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a href="<?php echo $this->QuarkURL->getBaseURL(); ?>" id="brand"
          class="brand" title="Control de gastos BETA">Control de gastos <div id="beta_icon">Beta</div></a>
        <?php if( $this->QuarkSess->getAccessLevel() == 0 ):
          $this->appendJsFiles('layout/login.js');
        ?>
        <!-- BEGIN: Login form = = = = = = = = = = = = = = = = = = = = = = = = = -->
        <ul class="nav pull-right">
          <li class="dropdown">
            <a href="#" id="btn_login_dropdown" class="dropdown-toggle"
              data-toggle="dropdown">
              Iniciar sesión
              <span class="caret"></span>
            </a>
            <ul id="login_dropdown" class="dropdown-menu">
              <li>
                <form action="javascript:;" id="frm_login">
                  <label for="login_email">Correo electrónico:</label>
                  <input type="email" name="login_email" id="login_email"
                    required="required" value="">
                  <label for="login_passwd">Contraseña:</label>
                  <input type="password" name="login_passwd" id="login_passwd"
                    required="required" value="">
                  <label for="remember">
                    <input type="checkbox" name="login_cookie" name="login_cookie" value="1"> No cerrar sesión
                  </label>
                  <button type="submit" class="btn btn-primary" id="btn_login">
                    <i class="icon-lock icon-white"></i>
                    Iniciar sesión
                  </button>
                  <a href="<?php
                    echo $this->QuarkURL->getURL('recover-password');
                  ?>" id="link_recover_password">¿Olvidaste tu contraseña?</a>
                </form>
              </li>
            </ul>
          </li>
        </ul>
        <!-- END: Login form = = = = = = = = = = = = = = = = = = = = = = = = = = -->
        <?php else: ?>
        <!-- BEGIN: User nav bar = = = = = = = = = = = = = = = = = = = = = = = = -->
        <ul class="nav pull-right">
          <li class="dropdown"><a href="#" class="dropdown-toggle"
            data-toggle="dropdown"><i class="icon-user icon-white"></i>
            <?php echo $this->QuarkStr->esc($this->UserData->name); ?>
            <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#"><i class="icon-pencil"></i> Editar perfil</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo $this->QuarkURL->getURL('salir'); ?>"><i class="icon-off"></i> Cerrar sesión</a></li>
            </ul>
          </li>
        </ul>
        <!-- END: User nav bar = = = = = = = = = = = = = = = = = = = = = = = = = -->
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- END: Main navbar = = = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
  
  <!-- BEGIN: Main container = = = = = = = = = = = = = = = = = = = = = = = = = = -->
  <div class="container">
