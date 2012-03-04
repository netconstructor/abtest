<?php
function abtest_migrate_if_needed() {
	global $wpdb;

	$migs = abtest_migrations();
  $current_migration = get_option('abtest_current_migration', 0);
  $needed_migration = count($migs);
  
  if ($current_migration != $needed_migration) {
    for ($i = $current_migration; $i < $needed_migration; $i++) {
      $mig = $migs[$i];
      $wpdb->query($mig);
    }
  
    if ($current_migration == 0) {
      add_option('abtest_current_migration', $needed_migration);
    } else {
      update_option('abtest_current_migration', $needed_migration);
    }
  }
}

function abtest_migrations() {
  global $wpdb;
  
  $migs = array();
  
  // Create goals table
  $migs[] = '
  CREATE TABLE '.$wpdb->prefix.'abtest_experiments (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    type varchar(50) NOT NULL,
    PRIMARY KEY (id)
  )';
  
  // Create goals table
  $migs[] = '
  CREATE TABLE '.$wpdb->prefix.'abtest_goals (
    id int(11) NOT NULL AUTO_INCREMENT,
    experiment_id int(11) NOT NULL,
    name varchar(255) NOT NULL,
    PRIMARY KEY (id),
    KEY experiment_id (experiment_id)
  )';

  // Create goal hits table
  $migs[] = '
  CREATE TABLE '.$wpdb->prefix.'abtest_goal_hits (
    id int(11) NOT NULL AUTO_INCREMENT,
    goal_id int(11) NOT NULL,
    session_id varchar(40) NOT NULL,
    ip varchar(15) NOT NULL,
    date datetime NOT NULL,
    PRIMARY KEY (id),
    KEY goal_id (goal_id,session_id)
  )';
  
  // Create variations table
  $migs[] = '
  CREATE TABLE '.$wpdb->prefix.'abtest_variations (
    id int(11) NOT NULL AUTO_INCREMENT,
    experiment_id int(11) NOT NULL,
    name varchar(255) NOT NULL,
    content text NOT NULL,
    PRIMARY KEY (id),
    KEY experiment_id (experiment_id)
  )';
  
  // Create variation views table
  $migs[] = '
  CREATE TABLE '.$wpdb->prefix.'abtest_variation_views (
    id int(11) NOT NULL AUTO_INCREMENT,
    variation_id int(11) NOT NULL,
    session_id varchar(40) NOT NULL,
    ip varchar(15) NOT NULL,
    date datetime NOT NULL,
    PRIMARY KEY (id),
    KEY variation_id (variation_id,session_id)
  )';
  
  // Add example experiment
  $migs[] = "INSERT INTO ".$wpdb->prefix."abtest_experiments SET name='Experiment 1', type='content'";
  $migs[] = "INSERT INTO ".$wpdb->prefix."abtest_variations SET experiment_id=1, name='Variation 1', content='Content for variation 1'";
  $migs[] = "INSERT INTO ".$wpdb->prefix."abtest_variations SET experiment_id=1, name='Variation 2', content='Content for variation 2'";
  $migs[] = "INSERT INTO ".$wpdb->prefix."abtest_goals SET experiment_id=1, name='Goal 1'";
  
  // Add type field to experiments
  $migs[] = "ALTER TABLE ".$wpdb->prefix."abtest_experiments ADD type VARCHAR( 50 ) NOT NULL DEFAULT 'content', ADD INDEX ( type )";
  
  // Create IP filters table
  $migs[] = '
  CREATE TABLE '.$wpdb->prefix.'abtest_ip_filters (
    id int(11) NOT NULL AUTO_INCREMENT,
    ip varchar(15) NOT NULL,
    description varchar(255) NOT NULL,
    PRIMARY KEY (id),
    KEY ip (ip)
  )';
  
  // Add active field to variations
  $migs[] = "ALTER TABLE ".$wpdb->prefix."abtest_variations ADD active BOOLEAN NOT NULL DEFAULT '1', ADD INDEX ( active )";

  // Return the migrations
  return $migs;
}
?>