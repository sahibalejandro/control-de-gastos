    </div>
    <!-- #main_body -->
  <?php
  /*
   * JavaScript power!
   */
  $this->prependJsFiles('lib/jquery.min.js','lib/bootstrap.js', 'main-script.js')
    ->includeJsFiles(false);
  ?>
  </body>
</html>
