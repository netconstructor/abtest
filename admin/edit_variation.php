<?php
$tab = 'experiments';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_variations WHERE id=%d", $id));
$exp = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE id=%d", $var->experiment_id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Save the variation
  $name = stripslashes($_POST['name']);
  $content = stripslashes($_POST['content']);
  
  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."abtest_variations SET name=%s, content=%s WHERE id=%d", $name, $content, $id));
  
  redirect_to('?page=abtest&action=show_experiment&id=' . $var->experiment_id);
} else {
  $name = $var->name;
  $content = $var->content;
}

$themes = get_themes();
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Edit variation</h3>
  <form method="post">
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
            <option value="<?php echo $theme['Template'] ?>"<?php if ($theme['Template'] == $content) echo ' selected' ?>><?php echo $theme['Name'] ?></option>
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
      <input class="button-primary" type="submit" name="Save" value="Update variation" id="submitbutton" />
      or <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $var->experiment_id ?>">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>