<?php
$debug = (int)$_GET['debug'];

if ($debug == 1) {
  $_SESSION['abtest_debug'] = 1;
} else {
  unset($_SESSION['abtest_debug']);
}

redirect_to('?page=abtest&action=debug');
?>