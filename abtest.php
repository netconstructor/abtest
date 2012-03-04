<?php
/*
Plugin Name:  A/B Test for WordPress
Plugin URI:   http://lassebunk.dk/plugins/abtest/
Version:      1.0.6
Description:  Easily perform A/B tests on any WordPress site.
Author:       Lasse Bunk
Author URI:   http://lassebunk.dk/
*/

// Start the session
session_start();

// We check database migrations on each call to ensure migrations when the plugin is updated
require 'includes/install.php';
abtest_migrate_if_needed();

// Ensure that widgets can also contain A/B Test shortcodes
add_filter('widget_text', 'do_shortcode');
add_filter('widget_title', 'do_shortcode');
add_filter('the_title', 'do_shortcode');

// Use the jQuery framework
wp_enqueue_script("jquery");

// Set up the viewed variations and hit goals queue
$abtest_viewed_variations = array();
$abtest_hit_goals = array();

// Load experiments so they are ready, e.g. if you want to track variables before the variation has been shown
add_action('setup_theme', 'abtest_load_experiments');
function abtest_load_experiments() {
  global $wpdb;

  $experiments = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."abtest_experiments");
  
  foreach ($experiments as $exp) {
    if (!isset($_SESSION['abtest_experiment_'.$exp->id.'_variation']) || $_SESSION['abtest_debug']) {
      // Get variation
      $variation = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."abtest_variations WHERE experiment_id=%d AND active=1 ORDER BY RAND() LIMIT 1", $exp->id) );

      // Set session
      $_SESSION['abtest_experiment_'.$exp->id.'_id'] = $variation->id;
      $_SESSION['abtest_experiment_'.$exp->id.'_variation'] = $variation->content;
      $_SESSION['abtest_experiment_'.$exp->id.'_name'] = $variation->name;
    }
  }
}

function abtest_experiment($id) {
  echo abtest_get_experiment($id);
}

function abtest_get_experiment($id) {
  global $abtest_viewed_variations;
  
  $variation_id = $_SESSION['abtest_experiment_'.$id.'_id'];

  if (!in_array($variation_id, $abtest_viewed_variations)) {
    // Add to viewed variations array
    $abtest_viewed_variations[] = $variation_id;
  }

  return $_SESSION['abtest_experiment_'.$id.'_variation'];
}

function abtest_name($experiment_id) {
  echo abtest_get_name($experiment_id);
}

function abtest_get_name($experiment_id) {
  return $_SESSION['abtest_experiment_'.$experiment_id.'_name'];
}

function abtest_variation_view($variation_id) {
  global $wpdb;
  
  if (!$_SESSION['abtest_debug']) {
    // Increase views on the variation
    $wpdb->query( $wpdb->prepare("UPDATE ".$wpdb->prefix."abtest_variations SET views=views+1 WHERE id=%d", $variation_id) );

    $wpdb->query( $wpdb->prepare("
        INSERT INTO ".$wpdb->prefix."abtest_variation_views(variation_id, session_id, ip, date)
        VALUES(%d, %s, %s, NOW())", $variation_id, session_id(), $_SERVER['REMOTE_ADDR'] ) );
  }
}

function abtest_goal_hit($goal_id) {
  global $wpdb;
  
  if (!$_SESSION['abtest_debug']) {
    $wpdb->query( $wpdb->prepare("
        INSERT INTO ".$wpdb->prefix."abtest_goal_hits(goal_id, session_id, ip, date)
        VALUES(%d, %s, %s, NOW())", $goal_id, session_id(), $_SERVER['REMOTE_ADDR'] ) );
  }
}

add_action('wp_head', 'abtest_head_script');
function abtest_head_script() {
  ?>
  <script src="<?php echo WP_PLUGIN_URL.'/abtest/abtest.js' ?>" type="text/javascript"></script>
  <script type="text/javascript">
    // Initialize A/B test
    abtest.basePath = '<?php echo WP_PLUGIN_URL.'/abtest' ?>';
  </script>
  <?php
}

add_action('wp_head', 'abtest_experiments_script');
function abtest_experiments_script() {
  global $wpdb;
  $experiments = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE type='javascript'");

  if (count($experiments) > 0) {
    ?>
    <script type="text/javascript">
      // A/B Test script
      <?php
      foreach ($experiments as $exp) {
        abtest_experiment($exp->id);
      }
      ?>
    </script>
    <?php
  }
}

add_action('wp_head', 'abtest_experiments_stylesheet');
function abtest_experiments_stylesheet() {
  global $wpdb;
  $experiments = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE type='stylesheet'");
  
  if (count($experiments) > 0) {
    ?>
    <style type="text/css">
      /* A/B Test stylesheet */
      <?php
      foreach ($experiments as $exp) {
        abtest_experiment($exp->id);
      }
      ?>
    </style>
    <?php
  }
}

add_action('wp_footer', 'abtest_footer_script');
function abtest_footer_script() {
  global $abtest_viewed_variations;
  global $abtest_hit_goals;
  
  ?>
  <script type="text/javascript">
    // Track viewed A/B test variations
    <?php
    foreach($abtest_viewed_variations as $id) {
      ?>
      abtest.trackVariation(<?php echo $id ?>);
      <?php
    }
    ?>
    // Track hit A/B test goals
    <?php
    foreach($abtest_hit_goals as $id) {
      ?>
      abtest.trackGoal(<?php echo $id ?>);
      <?php
    }
    ?>
  </script>
  <?php
}

add_action('wp_head', 'abtest_ob_start');
function abtest_ob_start() {
  if (get_option('abtest_do_shortcode_on_output_buffer')) {
    ob_start('abtest_ob_callback');
  }
}

add_action('wp_footer', 'abtest_ob_end');
function abtest_ob_end() {
  if (get_option('abtest_do_shortcode_on_output_buffer')) {
    ob_end();
  }
}

function abtest_ob_callback($content) {
  return do_shortcode($content);
}

// [abtest experiment="123"]
add_shortcode( 'abtest', 'abtest_handle_shortcode' );
function abtest_handle_shortcode($atts) {
  global $abtest_hit_goals;
  
	extract( shortcode_atts( array(
		'experiment' => '',
		'goal' => '',
		'variable' => '',
	), $atts ) );
  
  if ($experiment != '') {
    if ($variable == 'name') {
    	return abtest_get_name($experiment);
    } else {
    	return abtest_get_experiment($experiment);
    }
  } elseif ($goal != '') {
  	$abtest_hit_goals[] = $goal;
  } else {
    return "Unknown A/B test";
  }
}

add_filter('template', 'change_theme');
add_filter('stylesheet', 'change_theme');
function change_theme($theme) {
  global $wpdb;
  $exp = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."abtest_experiments WHERE type='theme'");
  
  if ($exp) {
    $new_theme = abtest_get_experiment($exp->id);
    
    if ($new_theme != '') {
      $theme = $new_theme;
    }
  }
  
  return $theme;
}

add_action('admin_menu', 'abtest_admin_menu');
function abtest_admin_menu() {
  add_menu_page('A/B Testing', 'A/B Testing', 'manage_options', 'abtest', 'abtest_admin');
}

function abtest_admin() {
  global $wpdb;
  include 'abtest_admin.php';
}

function abtest_rss(){
    echo '<div class="rss-widget">';

    ?>
    <a href="?page=abtest&amp;action=close_dashboard_widget" style="float: right;" onclick="return confirm('Are you sure?');">Close</a>
    <?php
     
       wp_widget_rss_output(array(
            'url' => 'http://lassebunk.dk/tag/ab-test-for-wordpress/feed/',
            'items' => 3,
            'show_summary' => 1,
            'show_date' => 1
       ));

       echo "</div>";
}

add_action('wp_dashboard_setup', 'abtest_rss_widget');
function abtest_rss_widget(){
  if (get_option('abtest_show_dashboard_widget', 1)) {
    wp_add_dashboard_widget( 'abtest-rss', 'A/B Test for WordPress', 'abtest_rss');
  }
}?>