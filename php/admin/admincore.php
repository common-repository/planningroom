<?php
/**
* CLASS ADMIN CORE
* All functions for admin page
* Manage Events, Rooms and Members
* Show list Events, Rooms and Members
*/
class AdminCore {

	function AdminCore(){}

	//Load admin options page
	function administrationAdminPage(){
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		$nonce = wp_create_nonce('roomplanning-securityform');
		$options = RoomPlanning::getOption();
		require_once(dirname(__FILE__).'/RoomPlanningAdminAdministration.php');
	}
	//Load admin edit page
	function editAdminPage(){
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		$nonce = wp_create_nonce('roomplanning-securityform');
		require_once(dirname(__FILE__).'/RoomPlanningAdminEdit.php');
	}
	//Load admin help page
	function helpAdminPage(){
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		$nonce = wp_create_nonce('roomplanning-securityform');
		require_once(dirname(__FILE__).'/RoomPlanningAdminHelp.php');
	}
	//Load admin list page
	function listAdminPage(){
		global $wpdb;
		if ( ! current_user_can( 'manage_options' ) )
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		$nonce = wp_create_nonce('roomplanning-securityform');
		$options = RoomPlanning::getOption();

		//For pagination
		$nbEvent = RP_utils::getNumberRecord('list_planning');
		$nbRoom = RP_utils::getNumberRecord('list_room');
		$nbMember = RP_utils::getNumberRecord('list_member');
		$objPgEvent = new Pagination(1,$nbEvent,$options['planningroom_nbresult_admin']);
		$objPgRoom = new Pagination(1,$nbRoom,$options['planningroom_nbresult_admin']);
		$objPgMember = new Pagination(1,$nbMember,$options['planningroom_nbresult_admin']);

		require_once(dirname(__FILE__).'/RoomPlanningAdminList.php');

		unset($objPgEvent);
		unset($objPgRoom);
		unset($objPgMember);
	}

	//Add room
	function addRoom($name,$desc=''){
		global $wpdb;
		$wpdb->insert( BDD_PLANNINGROOM_ROOM, array( 'room_name' => $name, 'description' => $desc ), array( '%s','%s' ) );
		return $wpdb->insert_id;
	}
	//Remove room
	function removeRoom($id){
		global $wpdb;
		$wpdb->query( "DELETE FROM ".BDD_PLANNINGROOM_ROOM." WHERE id_room = ".intval($id));
		$wpdb->query( "DELETE FROM ".BDD_PLANNINGROOM." WHERE LEFT(date_deb,10) >= '".ROOMPLANNING_DAY."' AND  room_id= ".intval($id));
	}
	//Edit room
	function editRoom($id,$desc=''){
		global $wpdb;
		$wpdb->update( BDD_PLANNINGROOM_ROOM,array('description' => nl2br($desc)),array('id_room' => intval($id)),array('%s'),array('%d'));
	}

	//Add member
	function addMember($user_name,$desc='',$user_email=null){
		global $wpdb;
		require_once(ABSPATH . WPINC . '/registration.php');

		$random_password = wp_generate_password( 12, false );
		if( RP_utils::username_exists($user_name) )
			return 0;
		$user_id = 0;
		//Use roomplanning registration
		if( !Booking::isWPr() )
		{
			$wpdb->insert( BDD_PLANNINGROOM_MEMBER, array( 'member_name' => $user_name, 'desc' => $desc, 'member_email' =>  $user_email, 'member_passwd' => $random_password), array( '%s','%s','%s','%s' ) );
			$user_id = $wpdb->insert_id;
		}
		//Use wordpress registration
		else
		{
			$user_id = wp_create_user( $user_name, $random_password, $user_email );
			add_user_meta( $user_id, 'desc', $desc);
		}
		if( $user_id > 0 AND !is_null($user_email))
		{
			$headers = 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>' . "\r\n";
			$text = __('Welcome','roomplanning').' '.$user_name. "\r\n";
			$text .= __('Here is your login and password on','roomplanning').' '.get_bloginfo('name'). "\r\n";
			$text .= __('Your login','roomplanning').': '.$user_name. "\r\n";
			$text .= __('Your password','roomplanning').': '.$random_password. "\r\n";
			$text .= '----------------------------------------';
			wp_mail( $user_email, __('Registration at','roomplanning').' '.get_bloginfo('name'), $text,$headers);
		}
		return $user_id;
	}
	//Remove member
	function removeMember($id){
		global $wpdb;
		//Use roomplanning registration
		if( !Booking::isWPr() )
			$wpdb->query( "DELETE FROM ".BDD_PLANNINGROOM_MEMBER." WHERE id_member= ".intval($id));
		//Use wordpress registration
		else
			wp_delete_user( $id );
		//Delete all event for this member
		$wpdb->query( "DELETE FROM ".BDD_PLANNINGROOM." WHERE LEFT(date_deb,10) >= '".ROOMPLANNING_DAY."' AND member_id= ".intval($id));
	}
	//Edit member
	function editMember($id,$desc,$email){
		global $wpdb;
		//Use roomplanning registration
		if( !Booking::isWPr() )
			$wpdb->update( BDD_PLANNINGROOM_MEMBER, array('desc' => nl2br($desc),'member_email' => $email), array('id_member' => intval($id)),array('%s','%s'),array('%d'));
		//Use wordpress registration
		else
			wp_update_user( array ('ID' => $id, 'user_desc' => $desc, 'user_email ' => $email) ) ;
	}

	//Add event
	function addEventPlanning($id_room,$member_id,$date_beg,$date_end){
		global $wpdb;
		$wpdb->insert( BDD_PLANNINGROOM, array( 'room_id' => $id_room, 'member_id' => $member_id, 'date_deb' => $date_beg, 'date_fin' => $date_end), array( '%d','%d','%s','%s' ) );
		return $wpdb->insert_id;
	}
	//Remove event
	function removeEventPlanning($id) {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".BDD_PLANNINGROOM." WHERE id = ".intval( $id ) );
	}
	//Modify event
	function editEventPlanning($id_room,$member_id,$date_beg,$date_end){
		global $wpdb;
		$wpdb->update( BDD_PLANNINGROOM, array( 'member_id' => $member_id, 'date_deb' => $date_beg, 'date_fin' => $date_end), array( 'id' => intval( $id_room ) ) , array( '%d','%s','%s' ), array( '%d' )  ) ;
	}

	//Show list of room with planning
	function showPlanningRoom($page_id=1,$echo=true) {
		global $wpdb;

		$msg = '';

		$options = RoomPlanning::getOption();
		$limit = $options['planningroom_nbresult_admin'];

		$yesterday  = date("Y-m-d", strtotime("-1 day"))." 23:59:59";
		$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM." as p, ".BDD_PLANNINGROOM_ROOM." as r , ".BDD_PLANNINGROOM_MEMBER." as m WHERE p.room_id = r.id_room AND p.member_id = m.id_member AND p.date_deb >= '".$yesterday."' ORDER BY p.room_id ASC, p.date_deb ASC LIMIT ".(($page_id-1)*$limit).",".$limit);
		if( $lines )
		{
			$i=0;
			foreach ($lines as $planning)
			{
				$style_current_day = ( date("Y-m-d") == date("Y-m-d",strtotime($planning->date_deb) )) ? 'style="background:#FDF0CA;"' : '';

				$className = ($i%2==0) ? 'alternate':'';
				$msg .= '<tr class="'.$className.'" valign="top" id="tr'.$planning->id.'" '.$style_current_day.'>';
				$msg .= '<th class="check-column" scope="row"><input type="checkbox" class="chk_planning" value="'.$planning->id.'" name="planning_free[]"></th>';
				$msg .= '<td class="plugin-title">';
				$msg .= '<strong><a class="row-title" href="#" onclick="return false;">'.stripslashes($planning->room_name).'</a></strong>';
				$msg .= '<div class="row-actions">';
				$msg .= '<span class="deactivate"><a title="'.__('Remove','roomplanning').'" href="javascript:protoAdminList.free('.$planning->id.');">'.__('Free','roomplanning').'</a></span>';
				$msg .= '</div>';
				$msg .= '</td>';

				$msg .= '<td>'.stripslashes($planning->member_name).'</td>';

				$date_d = ( !empty( $style_current_day ) ) ? date("H:i",strtotime($planning->date_deb)) : substr( $planning->date_deb, 0, -3 );
				$msg .= '<td>'.$date_d.'</td>';

				$date_f = ( !empty( $style_current_day ) ) ? date("H:i",strtotime($planning->date_fin)) : substr( $planning->date_fin, 0, -3);
				$msg .= '<td>'.$date_f.'</td>';
				$msg .= '</tr>';
				$i = $i+1;
			}
		}
		else
		{
			$msg .= '<tr class="active">';
			$msg .= '<td class="plugin-title" colspan="5" style="text-align:center;">'.__('Results not found','roomplanning').'</td>';
			$msg .= '</tr>';
		}
		if($echo)
			echo $msg;
		else
			return $msg;
	}
	//Show list of rooms
	function showRoom($page_id=1,$echo=true) {
		global $wpdb;

		$msg = '';
		$options = RoomPlanning::getOption();
		$limit = $options['planningroom_nbresult_admin'];
		$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM_ROOM." ORDER BY room_name ASC LIMIT ".(($page_id-1)*$limit).",".$limit);
		if( $lines )
		{
			$i=0;
			foreach ($lines as $room)
			{
				$name = stripslashes($room->room_name);
				$desc = stripslashes($room->description);
				$idRoom = $room->id_room;

				$className = ($i%2==0) ? 'alternate':'';
				$msg .= '<tr class="'.$className.'" valign="top" id="tr_room_'.$idRoom.'">';
				$msg .= '<th class="check-column" scope="row"><input type="checkbox" class="chk_room" value="'.$idRoom.'" name="room_remove[]"></th>';
				$msg .= '<td class="column-posts" style="width:2%;"><strong>'.$idRoom.'</strong></td>';
				$msg .= '<td class="column-title">';
				$msg .= '<strong><a class="row-title" href="#" onclick="return false;">'.$name.'</a></strong>';
				$msg .= '<div class="row-actions">';
				$msg .= '<span class="deactivate"><a title="'.__('Remove','roomplanning').'" href="#" onclick="protoAdminList.removeRoom('.$idRoom.'); return false;">'.__('Remove','roomplanning').'</a> | </span>';
				$msg .= '<span class="edit"><a class="edit" id="a_room_'.$idRoom.'"  title="'.__('Edit','roomplanning').'" href="javascript:protoAdminList.editRow('.$idRoom.',\'room\');">'.__('Edit','roomplanning').'</a></span>';
				$msg .= '</div>';
				$msg .= '</td>';
				$msg .= '<td class="lab">';
				$msg .= __('Description','roomplanning');
				$msg .= '</td>';
				$msg .= '<td class="column-description">';
				$msg .= '<div class="plugin-description" id="room_desc_'.$idRoom.'">'.$desc.'</div>';
				$msg .= '<form class="inline-edit-row" name="form_room_'.$idRoom.'" id="form_room_'.$idRoom.'" style="display:none;">';
				$msg .= '<input type="hidden" name="action" value="updateRoom"/>';
				$msg .= '<input type="hidden" name="id_room" value="'.$idRoom.'"/>';
				$msg .= '<textarea style="width:100%;" name="update_room_'.$idRoom.'" id="update_room_desc'.$idRoom.'">'.$desc.'</textarea>';
				$msg .= '<p class="submit inline-edit-save">';
				$msg .= '<a class="button-secondary cancel alignleft" title="'.__('Cancel','roomplanning').'" href="javascript:protoAdminList.cancelUpdate('.$idRoom.',\'room\');" accesskey="c">'.__('Cancel','roomplanning').'</a>';
				$msg .= '<a accesskey="s" href="#" class="button-primary save alignright" onclick="protoAdminList.submitUpdateRoom('.$idRoom.');return false;">'.__('Update room', 'roomplanning').'</a>';
				$msg .= '<img id="waiting_room_'.$idRoom.'" alt="" src="'.admin_url().'images/wpspin_light.gif" style="display:none;" class="waiting">';
				$msg .= '<br class="clear"></p>';
				$msg .= '</form>';
				$msg .= '</td>';
				$msg .= '</tr>';
				$i = $i+1;
			}
		}
		else
		{
			$msg .= '<tr class="active">';
			$msg .= '<td class="plugin-title" colspan="5" style="text-align:center;">'.__('Results not found','roomplanning').'</td>';
			$msg .= '</tr>';
		}
		if($echo)
			echo $msg;
		else
			return $msg;
	}
	//Show list of members
	function showMembers($page_id=1,$echo=true){
		global $wpdb;

		$msg = '';
		$options = RoomPlanning::getOption();
		$limit = $options['planningroom_nbresult_admin'];

		//Use roomplanning registration
		if( Booking::isWPr() )
			$lines = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "users ORDER BY user_login ASC LIMIT ".(($page_id-1)*$limit).",".$limit);
		else
			$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM_MEMBER." ORDER BY member_name ASC LIMIT ".(($page_id-1)*$limit).",".$limit);

		if( $lines )
		{
			$i=0;
			foreach ($lines as $member)
			{
				$idMember =   ( Booking::isWPr() ) ? $member->ID : $member->id_member;
				$nameMember = ( Booking::isWPr() ) ? $member->user_login : $member->member_name;
				$descMember = ( Booking::isWPr() ) ? $member->user_desc : $member->desc;
				$mailMember = ( Booking::isWPr() ) ? $member->user_email : $member->member_email;

				$nameMember = ( empty( $nameMember ) ) ? '-' : stripslashes($nameMember);
				$mailMember = ( empty( $mailMember ) ) ? '-' : stripslashes($mailMember);
				$descMember = ( empty( $descMember ) ) ? '-' : stripslashes($descMember);



				$className = ($i%2==0) ? 'alternate':'';
				$msg .= '<tr class="'.$className.'" valign="top" id="tr_member_'.$idMember.'">';
				$msg .= '<th class="check-column" scope="row"><input type="checkbox" class="chk_member" value="'.$idMember.'" name="member_remove[]"></th>';
				$msg .= '<td class="column-posts" style="width:2%;"><strong>'.$idMember.'</strong></td>';
				$msg .= '<td class="plugin-title">';
				$msg .= '<strong><a class="row-title" href="#" onclick="return false;">'.$nameMember.'</a></strong>';
				$msg .= '<div class="row-actions">';
				$msg .= '<span class="deactivate"><a title="'.__('Remove','roomplanning').'" href="javascript:protoAdminList.removeMember('.$idMember.');">'.__('Remove','roomplanning').'</a> | </span>';
				$msg .= '<span class="edit"><a id="a_member_'.$idMember.'" class="edit" title="'.__('Edit','roomplanning').'" href="javascript:protoAdminList.editRow('.$idMember.',\'member\');">'.__('Edit','roomplanning').'</a></span>';
				$msg .= '</div>';
				$msg .= '</td>';
				$msg .= '<td class="lab">';
				$msg .= __('Mail','roomplanning').'<br/>'.__('Description','roomplanning');
				$msg .= '</td>';
				$msg .= '<td class="column-description desc">';
				$msg .= '<div id="member_desc_'.$idMember.'">';
				$msg .= '<span id="ref_member_mail'.$idMember.'" class="member_mail">'.$mailMember.'</span>';
				$msg .= '<br/><span id="ref_member_desc'.$idMember.'" class="plugin-description">'.$descMember.'</span>';
				$msg .= '</div>';

				$msg .= '<form class="inline-edit-row" name="form_member_'.$idMember.'" id="form_member_'.$idMember.'" style="display:none;">';
				$msg .= '<span class="input-text-wrap">';
				$msg .= '<input type="text" name="update_member_mail'.$idMember.'" id="update_member_mail'.$idMember.'" value="'.$mailMember.'"/></textarea>';
				$msg .= '<input type="hidden" name="action" value="updateMember"/>';
				$msg .= '<input type="hidden" name="id_member" value="'.$idMember.'"/>';
				$msg .= '</span>';
				$msg .= '<br/>';
				$msg .= '<textarea style="width:100%;" name="update_member_desc'.$idMember.'" id="update_member_desc'.$idMember.'">'.$descMember.'</textarea>';
				$msg .= '<p class="submit inline-edit-save">';
				$msg .= '<a class="button-secondary cancel alignleft" title="'.__('Cancel','roomplanning').'" href="javascript:protoAdminList.cancelUpdate('.$idMember.',\'member\');" accesskey="c">'.__('Cancel','roomplanning').'</a>';
				$msg .= '<a accesskey="s" href="#" class="button-primary save alignright" onclick="protoAdminList.submitUpdateMember('.$idMember.');return false;">'.__('Update member', 'roomplanning').'</a>';
				$msg .= '<img id="waiting_member_'.$idMember.'" alt="" src="'.admin_url().'images/wpspin_light.gif" style="display: none;" class="waiting">';
				$msg .= '<br class="clear"></p>';
				$msg .= '</form>';
				$msg .= '</td>';
				$msg .= '</tr>';
				$i = $i+1;
			}
		}
		else
		{
			$msg .= '<tr class="active">';
			$msg .= '<td class="plugin-title" colspan="5" style="text-align:center;">'.__('Results not found','roomplanning').'</td>';
			$msg .= '</tr>';
		}
		if($echo)
			echo $msg;
		else
			return $msg;
	}
}
?>