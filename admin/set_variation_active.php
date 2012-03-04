<?php
$id = (int)$_GET['id'];
$active = (bool)$_GET['active'];

$var = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_variations WHERE id=%d", $id));
$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."abtest_variations SET active=%d WHERE id=%d", $active, $id));

redirect_to("?page=abtest&action=show_experiment&id=" . $var->experiment_id);
?>