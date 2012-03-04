<?php
require '../../../wp-config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
abtest_variation_view($id);

echo "OK"
?>