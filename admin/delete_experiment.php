<?php
$tab = 'experiments';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Delete the experiment
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_goal_hits WHERE goal_id IN (SELECT id FROM ".$wpdb->prefix."abtest_goals WHERE experiment_id=%d)", $id));
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_goals WHERE experiment_id=%d", $id));
  
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_variation_views WHERE variation_id IN (SELECT id FROM ".$wpdb->prefix."abtest_variations WHERE experiment_id=%d)", $id));
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_variations WHERE experiment_id=%d", $id));
  
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_experiments WHERE id=%d", $id));

  redirect_to('?page=abtest');
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Delete experiment</h3>
  <form method="post">
    <p>
      Are you sure you want to delete the experiment <em><?php echo htmlspecialchars($var->name) ?></em>? This can't be undone.
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Delete experiment" id="submitbutton" />
      or <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $var->id ?>">Cancel</a>
    </p>
  </form>
</div>