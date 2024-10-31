<?php
/**
 * ADMINISTRATION PAGE
 *
 * Page help
 */
?>
<div class="wrap">
<div class="icon32" id="icon-edit"><br></div>
<h2><?php _e( 'F.A.Q','roomplanning' ); ?></h2>
<p><?php _e('For your convenience, please click on the','roomplanning');?> <a href="post-new.php"><img style="vertical-align:middle;" src="<?php echo PATH_URL_PLANNING_ROOM;?>php/tinymce/wand.png" alt=""/></a> <?php _e('to generate your forms on shoortcode editions','roomplanning');?></p>
<br/>
<table cellspacing="0" class="wp-list-table widefat plugins">
		<thead>
		<tr>
		<th class="check-column" scope="row"></th>
		<th style="" class="manage-column column-name" scope="col"><?php _e('Parameters','roomplanning'); ?></th>
		<th style="" class="manage-column column-name" scope="col"><?php _e('Description','roomplanning'); ?></th>
		<th style="" class="manage-column column-name" scope="col"><?php _e('Example','roomplanning'); ?></th>
	  </tr>
		</thead>
		<tfoot>
		<tr>
	  	<th class="check-column" scope="row"></th>
	  	<th style="" class="manage-column column-name" scope="col"><?php _e('Parameters','roomplanning'); ?></th>
		<th style="" class="manage-column column-name" scope="col"><?php _e('Description','roomplanning'); ?></th>
		<th style="" class="manage-column column-name" scope="col"><?php _e('Example','roomplanning'); ?></th>
	  </tr>
	  </tfoot>
	  <tbody id="the-list">
	  	<tr>
	  		<th class="check-column" scope="row"></th>
			<td class="plugin-title">Null</td>
			<td class="plugin-title"><?php _e('Display planning room with no options','roomplanning');?></td>
  			<td class="plugin-code"><code>[show_planning_room]</code></td>
  		</tr>
		<tr>
			<th class="check-column" scope="row"></th>
			<td class="plugin-title">day</td>
			<td class="plugin-title"><?php _e('Display planning room for one day','roomplanning');?></td>
  			<td class="plugin-code"><code>[show_planning_room day=<?php echo date("Y-m-d");?>]</code></td>
  		</tr>
  		<tr>
			<th class="check-column" scope="row"></th>
			<td class="plugin-title">room_id</td>
			<td class="plugin-title"><?php _e('Display planning room for one room','roomplanning');?><br/><?php _e( "Check list room to find room's ID",'roomplanning' ); ?></td>
  			<td class="plugin-code"><code>[show_planning_room room_id=1]</code></td>
  		</tr>
  		<tr>
			<th class="check-column" scope="row"></th>
			<td class="plugin-title">view_id</td>
			<td class="plugin-title"><?php _e('Display planning room by day, week, month or year','roomplanning');?><br/>d, w, m <?php _e('or','roomplanning');?> y (<?php _e('default','roomplanning');?> d)</td>
  			<td class="plugin-code"><code>[show_planning_room view_id=d]</code></td>
  		</tr>
  		<tr>
			<th class="check-column" scope="row"></th>
			<td class="plugin-title">shownavigation</td>
			<td class="plugin-title"><?php _e('Display planning room with navigation day','roomplanning');?><br/>0 <?php _e('or','roomplanning');?> 1 (<?php _e('default','roomplanning');?> 0)</td>
  			<td class="plugin-code"><code>[show_planning_room shownavigation=1]</code></td>
  		</tr>
  		<tr>
			<th class="check-column" scope="row"></th>
			<td class="plugin-title">showfilter</td>
			<td class="plugin-title"><?php _e('Display planning room with filter','roomplanning');?><br/>0 <?php _e('or','roomplanning');?> 1 (<?php _e('default','roomplanning');?> 0)</td>
  			<td class="plugin-code"><code>[show_planning_room showfilter=1]</code></td>
  		</tr>
	  </tbody>
	</table>
</div>