<?php
$tab = 'experiments';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Save the experiment
  $name = stripslashes($_POST['name']);
  
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."abtest_experiments SET name=%s WHERE id=%d", $name, $id));
  
  redirect_to('?page=abtest&action=show_experiment&id=' . $id);
} else {
  $name = $var->name;
  $content = $var->content;
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Edit experiment</h3>
  <form method="post">
    <p>
      <label for="name">Experiment name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Update experiment" id="submitbutton" />
      or <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $id ?>">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>