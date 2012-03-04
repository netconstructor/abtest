<?php
$tab = 'experiments';

$experiments = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."abtest_experiments ORDER BY name");
?>
<?php include 'tabs.php' ?>
<div class="wrap">
  <h3>Experiments</h3>

  <table class="wp-list-table widefat" cellspacing="0">
  	<thead>
    	<tr>
    		<th>Name</th>
    	</tr>
  	</thead>

  	<tbody>
  	  <?php
  	  foreach ($experiments as $experiment) {
  	    ?>
  			<tr>
  			  <td>
  			    <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $experiment->id ?>"><strong><?php echo $experiment->name ?></strong></a><br />
  			    <small>
  			      <a href="?page=abtest&amp;action=show_experiment&amp;id=<?php echo $experiment->id ?>">Show</a>
  			      |
  			      <a href="?page=abtest&amp;action=edit_experiment&amp;id=<?php echo $experiment->id ?>">Edit</a>
  			      |
  			      <a href="?page=abtest&amp;action=delete_experiment&amp;id=<?php echo $experiment->id ?>">Delete</a>
  			  </td>
  			</tr>
  	    <?php
  	  }
  	  ?>
  	</tbody>
  </table>

  <p>
    <input type="button" value="Create new experiment" class="button-secondary" onclick="document.location = '?page=abtest&amp;action=create_experiment';" />
  </p>
  
  <!--
  <h3>Donate</h3>
  <p>
    If you like this plugin and use it, I'd be glad if you'd consider donating a small amount via PayPal:
  </p>
  
  <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="YG6AVTCGWSWS2">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
  </form>
  -->
  
</div>