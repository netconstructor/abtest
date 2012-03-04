<?php
$tab = 'experiments';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $experiment_id = (int)$_POST['experiment_id'];
  // Insert the variation
  $name = stripslashes($_POST['name']);
  $content = stripslashes($_POST['content']);
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."abtest_variations SET experiment_id=%d, name=%s, content=%s", $experiment_id, $name, $content));
  
  redirect_to('?page=abtest&action=show_experiment&id=' . $experiment_id);
} else {
  $experiment_id = (int)$_GET['experiment_id'];
  $name = '';
  $content = '';
}

$exp = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE id=%d", $experiment_id));
$themes = get_themes();
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Add variation</h3>
  <form method="post">
    <input type="hidden" name="experiment_id" value="<?php echo $experiment_id ?>" />
    <p>
      <label for="name">Variation name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <?php if ($exp->type == 'theme') {?>
      <p>
        <label for="content">Theme:</label><br />
        <select name="content">
          <option value="">Select a theme</option>
          <?php foreach ($themes as $theme) { ?>
            <option value="<?php echo $theme['Template'] ?>"><?php echo $theme['Name'] ?></option>
          <?php } ?>
        </select>
      </p>
    <?php } else { ?>
      <p>
        <label for="content">Variation content:</label><br />
        <textarea id="content" name="content" style="width: 500px; height: 200px;"><?php echo htmlspecialchars($content) ?></textarea>
      </p>
    <?php } ?>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Add variation" id="submitbutton" />
      or <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $experiment_id ?>">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>