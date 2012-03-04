<?php
$tab = 'experiments';

$id = (int)$_GET['id'];
$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE id=%d", $id));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Delete the variation
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_goal_hits WHERE goal_id IN (SELECT id FROM ".$wpdb->prefix."abtest_goals WHERE experiment_id=%d)", $id));
  $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."abtest_variation_views WHERE variation_id IN (SELECT id FROM ".$wpdb->prefix."abtest_variations WHERE experiment_id=%d)", $id));
  
  redirect_to('?page=abtest&action=show_experiment&id=' . $id);
}
?>

<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Reset experiment</h3>
  <form method="post">
    <p>
      Are you sure you want to reset the experiment <em><?php echo htmlspecialchars($var->name) ?></em>? This can't be undone.
    </p>
    <p>
      <input class="button-primary" type="submit" name="Save" value="Reset variation" id="submitbutton" />
      or <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $var->experiment_id ?>">Cancel</a>
    </p>
  </form>
</div>