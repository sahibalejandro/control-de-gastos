    </div>
    <!-- #main_body -->
  <?php
  /*
   * JavaScript power!
   */
  $this->prependJsFiles('lib/jquery.min.js','lib/bootstrap.js', 'main-script.js')
    ->includeJsFiles(false);
  ?>
  <!-- GitHub ribbon yay! -->
  <a href="https://github.com/sahibalejandro/control-de-gastos/"><img style="position: absolute; top: 0; left: 0; border: 0; z-index: 1031;" src="https://s3.amazonaws.com/github/ribbons/forkme_left_orange_ff7600.png" alt="Fork me on GitHub"></a>
  </body>
</html>
