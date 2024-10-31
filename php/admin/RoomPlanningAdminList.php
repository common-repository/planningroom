<?php
/**
 * ADMINISTRATION PAGE
 *
 * Listing: room, members and room booking
 */
?>
<div class="wrap" id="pageList">
	<div class="icon32" id="icon-edit-pages"><br></div>
	<h2><?php _e( 'List Planning','roomplanning' ); ?> <small><?php _e('Today','roomplanning');  echo ' : '.date("d-m");?></small></h2>
    <div class="tablenav top">
		<div class="alignleft actions">
			<select name="action" class="alignleft">
				<option selected="selected" value="-1"><?php _e('Actions','roomplanning');?></option>
				<option value="trash"><?php _e('Free','roomplanning');?></option>
			</select>
			<a class="button-secondary action alignleft" href="javascript:protoAdminList.groupAction('planning');"><?php _e('Apply','roomplanning');?></a>
		</div>
		<br class="clear">
	</div>
	<div id="admin_panel_6" class="accordion">
	    <table cellspacing="0" class="wp-list-table widefat fixed">
	    <thead>
		<tr>
		  <th style="" class="manage-column column-cb check-column" id="cb_planning" scope="col"><input type="checkbox"></th>
	      <th style="" class="manage-column column-name" scope="col">Room</th>
	      <th style="" class="manage-column column-name" scope="col">Member</th>
	      <th style="" class="manage-column column-rel"  scope="col">Date deb</th>
	      <th style="" class="manage-column column-rel"  scope="col">Date fin</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
		  <th style="" class="manage-column column-cb check-column" id="cb_planning" scope="col"><input type="checkbox"></th>
	      <th style="" class="manage-column column-name" scope="col">Room</th>
	      <th style="" class="manage-column column-name" scope="col">Member</th>
	      <th style="" class="manage-column column-rel"  scope="col">Date deb</th>
	      <th style="" class="manage-column column-rel"  scope="col">Date fin</th>
		</tr>
	    </tfoot>
		<tbody id="list_planning">
			<?php AdminCore::showPlanningRoom(); ?>
		 </tbody>
		 </table>
		 <div id="list_planning_pagination">
			<?php echo $objPgEvent->getPagination('list_planning',4); ?>
		</div>
	</div>

	<div class="icon32" id="icon-edit-pages"><br></div>
	<h2><?php _e( 'List Room','roomplanning' ); ?></h2>
	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="action" class="alignleft">
				<option selected="selected" value="-1"><?php _e('Actions','roomplanning');?></option>
				<option value="trash"><?php _e('Delete','roomplanning');?></option>
			</select>
			<a class="button-secondary action alignleft" href="javascript:protoAdminList.groupAction('room');"><?php _e('Apply','roomplanning');?></a>
		</div>
		<br class="clear">
	</div>
    <div id="admin_panel_7" class="accordion">
		<table cellspacing="0" class="wp-list-table widefat fixed">
		<thead>
		<tr>
			<th style="" class="manage-column column-cb check-column" id="cb_room" scope="col"><input type="checkbox"></th>
		  	<th class="manage-column column-posts" style="width:2%;" scope="col">ID</th>
		  	<th style="" class="manage-column column-name" scope="col"><?php _e('Room','roomplanning'); ?></th>
			<th style="" class="manage-column column-rel" scope="col"></th>
			<th style="" class="manage-column column-name" scope="col"><?php _e('information','roomplanning'); ?></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th style="" class="manage-column column-cb check-column" id="cb_room" scope="col"><input type="checkbox"></th>
		  	<th class="manage-column column-posts"  style="width:2%;" scope="col">ID</th>
			<th class="manage-column column-name" scope="col"><?php _e('Room','roomplanning'); ?></th>
			<th class="manage-column column-rel" scope="col"></th>
			<th class="manage-column column-name" scope="col"><?php _e('information','roomplanning'); ?></th>
		</tr>
		</tfoot>
		<tbody id="list_room">
		<?php AdminCore::showRoom(); ?>
		</tbody>
		</table>
		<div id="list_room_pagination">
			<?php echo $objPgRoom->getPagination('list_room',3); ?>
		</div>
	</div>

	<div class="icon32" id="icon-edit-pages"><br></div>
	<h2><?php _e( 'List Members','roomplanning' ); ?></h2>
	<div class="tablenav top">
		<div class="alignleft actions">
			<select name="action" class="alignleft">
				<option selected="selected" value="-1"><?php _e('Actions','roomplanning');?></option>
				<option value="trash"><?php _e('Delete','roomplanning');?></option>
			</select>
			<a class="button-secondary action alignleft" href="javascript:protoAdminList.groupAction('member');"><?php _e('Apply','roomplanning');?></a>
		</div>
		<br class="clear">
	</div>
	<div id="admin_panel_8" class="accordion">
		  <table cellspacing="0" class="wp-list-table widefat fixed">
			<thead>
			<tr>
		  	 	<th style="" class="manage-column column-cb check-column" id="cb_member" scope="col"><input type="checkbox"></th>
		  	 	<th class="manage-column column-posts"  style="width:2%;" scope="col">ID</th>
        		<th class="manage-column column-name" scope="col"><?php _e('Member','roomplanning'); ?></th>
				<th class="manage-column column-rel" scope="col"></th>
				<th class="manage-column column-name" scope="col"><?php _e('information','roomplanning'); ?></th>
		  	</tr>
			</thead>
			<tfoot>
			<tr>
		  	 	<th style="" class="manage-column column-cb check-column" id="cb_member" scope="col"><input type="checkbox"></th>
		  	 	<th class="manage-column column-posts"  style="width:2%;" scope="col">ID</th>
        		<th class="manage-column column-name" scope="col"><?php _e('Member','roomplanning'); ?></th>
				<th class="manage-column column-rel" scope="col"></th>
				<th class="manage-column column-name" scope="col"><?php _e('information','roomplanning'); ?></th>
		  	</tr>
		  </tfoot>
			<tbody id="list_member">
				<?php AdminCore::showMembers(); ?>
		  </tbody>
		</table>
		<div id="list_member_pagination">
			<?php echo $objPgMember->getPagination('list_member',3); ?>
		</div>
	</div>
</div>