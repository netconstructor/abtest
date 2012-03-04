<?php
$tab = 'debug';
?>
<?php include 'tabs.php' ?>
<div class="wrap">
  <p>
    Normally, when a user first sees a variation to an experiment, this variation is locked for this user so that – in this session – she always sees the same variation.<br />
    If you want to test your experiments without this variation lock taking place for you (and only you), you can enable debug mode. Also, when entering debug mode, all tracking will be disabled for your session.
  </p>
  <?php
  if ($_SESSION['abtest_debug']) {
    ?>
    <p>
      <strong>Debug mode is on.</strong>
    </p>
    <p>
      <input type="button" value="Exit debug mode" class="button-secondary" onclick="document.location = '?page=abtest&amp;action=set_debug_mode&amp;debug=0';" />
    </p>
    <?php
  } else {
    ?>
    <p>
      <input type="button" value="Enter debug mode" class="button-secondary" onclick="document.location = '?page=abtest&amp;action=set_debug_mode&amp;debug=1';" />
    </p>
    <?php
  }
  ?>
</div>