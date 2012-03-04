<?php
require 'admin/functions.php';
if (isset($_GET['action'])) {
  include 'admin/' . $_GET['action'] . '.php';
} else {
  include 'admin/list_experiments.php';
}
?>