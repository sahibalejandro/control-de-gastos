<!DOCTYPE HTML>
<html lang="es-MX">
<head>
  <meta charset="UTF-8">
  <title><?php
    // Print page title if necessary
    if(!empty($this->page_title)):
      echo $this->QuarkStr->esc($this->page_title), ' - ';
    endif;
    ?>gassto
  </title>
  <?php
  $this
    ->prependCssFiles('layout/bootstrap.min.css', 'layout/main.css')
    ->includeCssFiles();
  ?>
</head>
<body>
  <!-- BEGIN: Main navbar = = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
  <div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
      <div class="container">
        <a href="<?php echo $this->QuarkURL->getBaseURL(); ?>" id="brand"
          class="brand" title="gassto BETA">gassto <div id="beta_icon">Beta</div></a>
        <?php if( $this->QuarkSess->getAccessLevel() > 0 ): ?>
        <!-- BEGIN: User nav bar = = = = = = = = = = = = = = = = = = = = = = = = -->
        <ul class="nav pull-right">
          <li class="dropdown"><a href="#" class="dropdown-toggle"
            data-toggle="dropdown"><i class="icon-user"></i>
            @<?php echo $this->QuarkStr->esc($this->UserData->screen_name); ?>
            <span class="caret"></span></a>
            <ul class="dropdown-menu">
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
