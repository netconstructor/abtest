<?php
require '../../../wp-config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : '';

abtest_goal_hit($id);

if ($type == 'link') {
  $url = isset($_GET['url']) ? $_GET['url'] : '';
  header('Location: '.$url);
} else {
  echo 'OK';
}
?>