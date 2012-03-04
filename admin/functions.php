<?php
function redirect_to($url) {
  ?>
  <script type="text/javascript">
    document.location = '<?php echo $url ?>';
  </script>
  <?php
  die();
}
?>