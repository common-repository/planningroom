<?php
/**
 * ADMINISTRATION PAGE
 *
 * Page add event, room and member
 */
?>
<div class="wrap" id="pageEdit">
	<br/><br/>
  <?php
  	echo do_shortcode('[show_planning_room room_id="12,7" shownavigation=1 showfilter=1]');
  ?>

	<a name="ancre_panel_event"></a>
	<div class="icon32" id="icon-plugins"><br></div>
	<h2><?php _e( 'Add Event','roomplanning' ); ?></h2>
    <div id="admin_panel_2" class="accordion">
	    <form name="form_event" id="form_event" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?page=roomplanning_edit&_wpnonce=<?php echo $nonce ?>">
	    <div id="ajax_response_event"></div>
	    <?php wp_nonce_field('form_RoomPlanningSettings','name_nonce_field'); ?>
	    <input type="hidden" name="do_check_form_security" value=""/>
	    <input type="hidden" name="action" value="addEvent"/>
    	<table class="form-table">
	        <tr valign="top">
	        <th scope="row"><label for="sel_room_id"><?php _e('Room Name','roomplanning'); ?></label></th>
	        <td id="td_room"><?php RP_utils::selectRoom('sel_room_id',$sel_room_id);?></td>
	        </tr>
	        <th scope="row"><label for="sel_member_id"><?php _e('Member Name','roomplanning'); ?></label></th>
	        <td id="td_member"><?php RP_utils::selectMember('sel_member_id');?>
	        <div style="position:relative;display:inline;">
	        	<label for="member-search-input" class=""><?php _e('Or search member by name','roomplanning'); ?></label>
	        	<input type="text" value="" name="member-search-input" id="member-search-input">
	        	<div id="ajax-response"></div>
	        </div>
	        </td>
	        </tr>
	        <th scope="row"><label for="event_date_deb"><?php _e('Date beg','roomplanning'); ?></label></th>
	        <td><input name="event_date_deb" type="text" id="event_date_deb" value="" class="datepicker" />&nbsp;</td>
	        </tr>
	        <th scope="row"><label for="event_date_fin_hour"><?php _e('Times','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectHour('event_date_deb_hour'); ?>H:<?php RP_utils::selectMinutes('event_date_deb_min'); ?>min<br/><?php _e('to','roomplanning');?><br/><?php RP_utils::selectHour('event_date_fin_hour'); ?>H:<?php RP_utils::selectMinutes('event_date_fin_min'); ?>min</td>
	        </tr>
	    </table>
	    <p class="submit inline-edit-save">
				<span style="display:none;" class="loading alignleft" id="waiting_options_event"><img alt="" src="<?php echo admin_url();?>images/wpspin_light.gif"><?php _e('Loading','roomplanning');?>...</span>
				<a accesskey="s" href="#" class="button-primary save alignleft" onclick="protoAdminEdit.submitForm('event',false);return false"><?php _e('Add Event', 'roomplanning') ?></a>
				<a class="button-secondary cancel alignleft" title="<?php _e('Reset','roomplanning');?>" href="javascript:$('form_event').reset();" accesskey="c"><?php _e('Reset','roomplanning');?></a>
				<br class="clear">
		</p>
	    </form>
	</div>

	<a name="ancre_panel_member" style="clear:both;"></a>
	<div class="icon32" id="icon-users"><br></div>
	<h2><?php _e( 'Add new Member','roomplanning' ); ?></h2>
    <div id="admin_panel_4" class="accordion">
	    <form id="form_member" name="form_member" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?page=roomplanning_edit&_wpnonce=<?php echo $nonce ?>#ancre_panel_member">
	    <div id="ajax_response_member"></div>
	    <?php wp_nonce_field('form_RoomPlanningSettings','name_nonce_field'); ?>
	    <input type="hidden" name="do_check_form_security" value=""/>
	    <input type="hidden" name="action" value="addMember"/>
    	<table class="form-table">
	        <tr valign="top">
	        <th scope="row"><label for="membername"><?php _e('Member Name','roomplanning'); ?></label></th>
	        <td><input name="membername" type="text" id="membername" value="<?php echo $member_name;?>" class="regular-text" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><label for="memberemail"><?php _e('Mail','roomplanning'); ?></label></th>
	        <td><input name="memberemail" type="text" id="memberemail" value="<?php echo $member_mail;?>" class="regular-text" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><label for="memberdesc"><?php _e('Description','roomplanning'); ?></label></th>
	        <td><textarea cols="37" rows="5" name="memberdesc" id="memberdesc" class="regular-text"><?php echo esc_textarea($member_desc);?></textarea></td>
	        </tr>
	    </table>
	    <?php echo $error_msg_admin_panel4;?>
	    <p class="submit inline-edit-save">
				<span style="display:none;" id="waiting_options_member" class="loading alignleft"><img alt="" src="<?php echo admin_url();?>images/wpspin_light.gif"><?php _e('Loading','roomplanning');?>...</span>
				<a accesskey="s" href="#" class="button-primary save alignleft" onclick="protoAdminEdit.submitForm('member',true);return false"><?php _e('Add Member', 'roomplanning') ?></a>
				<a class="button-secondary cancel alignleft" title="<?php _e('Reset','roomplanning');?>" href="javascript:$('form_member').reset();" accesskey="c"><?php _e('Reset','roomplanning');?></a>
			<br class="clear">
			</p>
	    </form>
	</div>

	<a name="ancre_panel_room" style="clear:both;"></a>
    <div class="icon32" id="icon-themes"><br></div>
    <h2><?php _e( 'Add new Room','roomplanning' ); ?></h2>
    <div id="admin_panel_3" class="accordion">
	    <form id="form_room" name="form_room" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?page=roomplanning_edit&_wpnonce=<?php echo $nonce ?>#ancre_panel_room">
	    <div id="ajax_response_room"></div>
	    <?php wp_nonce_field('form_RoomPlanningSettings','name_nonce_field'); ?>
	    <input type="hidden" name="action" value="addRoom"/>
    	<input type="hidden" name="do_check_form_security" value=""/>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row"><label for="roomname"><?php _e('Room Name','roomplanning'); ?></label></th>
	        <td><input name="roomname" type="text" id="roomname" value="" class="regular-text" /></td>
	        </tr>
	        <tr valign="top">
	        <th scope="row"><label for="roomdesc"><?php _e('Description','roomplanning'); ?></label></th>
	        <td><textarea cols="37" rows="5" name="roomdesc" id="roomdesc" class="regular-text"><?php echo $roomdesc;?></textarea></td>
	        </tr>
	    </table>
	    <?php echo $error_msg_admin_panel3;?>
	    <p class="submit inline-edit-save">
				<span style="display:none;" class="loading alignleft" id="waiting_options_room"><img alt="" src="<?php echo admin_url();?>images/wpspin_light.gif"><?php _e('Loading','roomplanning');?>...</span>
				<a accesskey="s" href="#" class="button-primary save alignleft" onclick="protoAdminEdit.submitForm('room',true);return false"><?php _e('Add Room', 'roomplanning') ?></a>
				<a class="button-secondary cancel alignleft" title="<?php _e('Reset','roomplanning');?>" href="javascript:$('form_room').reset();" accesskey="c"><?php _e('Reset','roomplanning');?></a>
				<br class="clear">
			</p>
	   </form>
	</div>
</div>