<?php
$tab = 'experiments';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Insert the experiment
  $name = stripslashes($_POST['name']);
  $type = stripslashes($_POST['type']);
  
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."abtest_experiments SET name=%s, type=%s", $name, $type));
  $id = $wpdb->insert_id;
  
  // Insert variations
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."abtest_variations SET experiment_id=%d, name=%s", $id, 'Variation 1'));
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."abtest_variations SET experiment_id=%d, name=%s", $id, 'Variation 2'));
  
  // Insert goal
  $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."abtest_goals SET experiment_id=%d, name=%s", $id, 'Goal 1'));
  
  redirect_to('?page=abtest&action=show_experiment&id=' . $id);
} else {
  $name = '';
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Create experiment</h3>
  <form method="post">
    <p>
      <label for="name">Experiment name:</label><br />
      <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name) ?>" style="width: 300px;" />
    </p>
    <p>
      <label for="type">Experiment type:</label><br />
      <select id="type" name="type">
        <option value="content">Content – inserted manually into posts, pages, and widgets</option>
        <option value="stylesheet">Stylesheet – inserted automatically into the stylesheet</option>
        <option value="javascript">Javascript – inserted automatically into the javascript</option>
        <option value="theme">Theme – to switch between themes</option>
      </select>
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Create experiment" id="submitbutton" />
      or <a href="?page=abtest">Cancel</a>
    </p>
  </form>
</div>

<script type="text/javascript">
  jQuery('#name').focus();
</script>