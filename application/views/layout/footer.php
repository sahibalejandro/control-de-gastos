  <?php
  $this->prependJsFiles('layout/bootstrap.min.js', 'layout/main.js')->includeJsFiles();
  ?>
  </div>
  <!-- END: Main container = = = = = = = = = = = = = = = = = = = = = = = = = = -->
  <!-- BEGIN: Footer = = = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
  <div id="footer">
    Control de gastos&copy; Sahib Alejandro Jaramillo Leo 2012
    &bull; <a href="<?php echo $this->QuarkURL->getURL('terminos-y-condiciones'); ?>">Términos y condiciones</a>
    &bull; <a href="<?php echo $this->QuarkURL->getURL('ayuda'); ?>">Ayuda</a>
    &bull; <a href="<?php echo $this->QuarkURL->getURL('contacto'); ?>">Contacto</a>
  </div>
  <!-- END: Footer = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = -->
</body>
</html>
