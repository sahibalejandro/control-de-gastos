<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php
    /*
     * Agregar titulo de pagina si es defindio.
     */
    if( isset($page_title) ) echo $this->QuarkStr->esc(), ' - ';
  ?>Control de Gastos</title>
  <base href="<?php echo $this->QuarkURL->getBaseURL(); ?>" />
  <?php
  /*
   * CSS Styles.
   */
  $this->prependCssFiles(
    'bootstrap.css',
    'main-style.css',
    'bootstrap-responsive.css')->includeCssFiles(false);
  ?>
  <!--[if lt IE 9]>
  <script type="text/javascript" src="application/public/js/html5shiv.js"></script>
  <![endif]-->
</head>
<body>
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a href="" class="brand">Control de gastos</a>
        <?php if($this->QuarkSess->getAccessLevel() > 0): ?>
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <div class="nav-collapse">
          <ul class="nav">
            <li <?php
              if(Quark::getCalledControllerName() == 'home'):
                echo 'class="active"';
              endif;
            ?>><a href="<?php
              echo $this->QuarkURL->getURL('home') ?>">Mis Cuentas</a></li>
            <li <?php
              if(Quark::getCalledControllerName() == 'profile'):
                echo 'class="active"';
              endif;
            ?>><a href="<?php
              echo $this->QuarkURL->getURL('profile') ?>">Perfil</a></li>
          </ul>
          <ul class="nav pull-right">
            <li><a href="<?php
              echo $this->QuarkURL->getURL('home/logout') ?>">Cerrar sesión</a></li>
          </ul>
        </div><!-- .nav-collapse -->
        <?php endif; // if(access level > 0) ?>
      </div><!-- .container -->
    </div><!-- .navbar-inner -->
  </div><!-- .navbar -->

  <!-- messages wrapper -->
  <div id="main_msgs_wrapper"></div>

  <!-- Loading AJAX -->
  <span id="main_ajax_loading" class="label label-warning">Cargando...</span>

  <!-- Le main body -->
  <div id="main_body" class="container">
    <!-- #main_header -->
