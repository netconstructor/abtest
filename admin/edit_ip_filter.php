<?php
$tab = 'settings';

$id = (int)$_GET['id'];
$filter = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_ip_filters WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Save the ip_filter
  $ip = stripslashes($_POST['ip']);
  $description = stripslashes($_POST['description']);
  
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."abtest_ip_filters SET ip=%s, description=%s WHERE id=%d", $ip, $description, $id));
  
  redirect_to('?page=abtest&action=list_ip_filters');
} else {
  $ip = $filter->ip;
  $description = $filter->description;
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Edit IP filter</h3>
  <form method="post">
    <p>
      <label for="name">IP address: <small>(e.g. 127.0.0.1)</small></label><br />
      <input type="text" id="ip" name="ip" value="<?php echo htmlspecialchars($ip) ?>" style="width: 150px;" />
      <a href="#" onclick="jQuery('#ip').val('<?php echo $_SERVER['REMOTE_ADDR'] ?>');">Insert my current IP</a>
    </p>
    <p>
      <label for="content">Description:</label> <small>(e.g. Home IP)</small><br />
      <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($description) ?>" style="width: 300px;" />
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Update IP filter" id="submitbutton" />
      or <a href="?page=abtest&amp;action=settings">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#ip').focus();
</script>