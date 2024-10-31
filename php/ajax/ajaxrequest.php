<?php
/**
 * CLASS AJAX REQUEST
 * All functions for request ajax
 */
class AjaxRequest {

	function AjaxRequest(){}

	function verifyForm(){
		if(!wp_verify_nonce($_POST['name_nonce_field'],'form_RoomPlanningSettings')) die('Security check');
	}
	/**
	* Admin Ajax
	*/
	function ajax_updateConfig(){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		if( isset($_POST['do_check_form_security'])) AjaxRequest::verifyForm();

		$options = array();
		if( isset($_POST['title'])) $options['planningroom_title'] = RP_utils::satanizeDataAjax($_POST['title']);
		$options['planningroom_openhour'] = $_POST['houropen'];
		$options['planningroom_openminutes'] = $_POST['openminutes'];
		if( intval($_POST['hourclose']) == 24 )
		{
			$options['planningroom_closehour'] = '24';
			$options['planningroom_closeminutes'] = '00';
		}
		else
		{
			$options['planningroom_closehour'] = $_POST['hourclose'];
			$options['planningroom_closeminutes'] = $_POST['closeminutes'];
		}
		$options['planningroom_wp_registration'] = $_POST['wp_registration'];
		$options['planningroom_day_booking'] = $_POST['day_booking'];
		$options['planningroom_booking'] = $_POST['booking_yesno'];
		$options['planningroom_weekopen'] = $_POST['week_openclose'];
		$options['planningroom_nbresult_admin'] = $_POST['nbline_result'];
		$options['planningroom_displaymembername'] = $_POST['displaymembername'];
		$options['planningroom_backimg'] = $_POST['planningroom_backimg'];
		$options['planningroom_reservedimg'] = $_POST['planningroom_reservedimg'];

		RoomPlanning::update_option($options);
		$html = "<div class='update'><p>".__('options updates','roomplanning').'</p></div>';
		echo json_encode(array('html'=> $html));
		die;
	}
	function ajax_refreshSel(){
		check_ajax_referer( 'ajax_security', 'security' );
		$room = RP_utils::selectRoom('sel_room_id',null,false);
		$member = RP_utils::selectMember('sel_member_id',null,false);
		echo json_encode(array('room'=>$room,'member'=>$member));
	}
	function ajax_addEvent(){
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		if( isset($_POST['do_check_form_security'])) AjaxRequest::verifyForm();

		//member id
		$sel_member_id	= (isset($_POST['sel_member_id'])) ? intval($_POST['sel_member_id']) : '';
		//room id
		$sel_room_id	= (isset($_POST['sel_room_id'])) ? intval($_POST['sel_room_id']) : '';

		//Date deb
		$event_date_deb			= (isset($_POST['event_date_deb'])) ? $_POST['event_date_deb'] : '';
		$event_date_fin 		= $event_date_deb;

		$event_date_deb_hour 	= (isset($_POST['event_date_deb_hour'])) ? intval($_POST['event_date_deb_hour']) : '00';
		$event_date_deb_min		= (isset($_POST['event_date_deb_min'])) ? intval($_POST['event_date_deb_min']) : '00';

		$event_date_fin_hour	= (isset($_POST['event_date_fin_hour'])) ? intval($_POST['event_date_fin_hour']) : '00';
		$event_date_fin_min		= (isset($_POST['event_date_fin_min'])) ? intval($_POST['event_date_fin_min']) : '00';

		$date_deb = '';
		$date_fin = '';
		//Check if empty field
		if( !empty($event_date_deb) && !empty($sel_member_id) && !empty($sel_room_id) )
		{
			/** Formate date **/
			$event_date_deb_hour = ($event_date_deb_hour < 10) ? '0'.$event_date_deb_hour : $event_date_deb_hour;
			$event_date_deb_min  = ($event_date_deb_min  < 10) ? '0'.$event_date_deb_min  : $event_date_deb_min;
			$event_date_fin_hour = ($event_date_fin_hour < 10) ? '0'.$event_date_fin_hour : $event_date_fin_hour;
			$event_date_fin_min  = ($event_date_fin_min  < 10) ? '0'.$event_date_fin_min  : $event_date_fin_min;

			//Check exception
			if($event_date_fin_hour == 24)
			{
				$date_deb = $event_date_deb.' '.$event_date_deb_hour.':'.$event_date_deb_min.':00';
				$date_fin = $event_date_fin.' 23:59:59';
			}
			if( $event_date_deb_hour == 0 AND $event_date_deb_min == 0)
			{
				$date_deb = $event_date_fin.' 00:00:01';
				if(empty($date_fin))
					$date_fin = $event_date_fin.' '.$event_date_fin_hour.':'.$event_date_fin_min.':00';
			}
			if(empty($date_fin))
			{
				$date_deb = $event_date_deb.' '.$event_date_deb_hour.':'.$event_date_deb_min.':00';
				$date_fin = $event_date_fin.' '.$event_date_fin_hour.':'.$event_date_fin_min.':00';
			}
			if( RP_utils::checkDateDiff($date_deb,$date_fin) )
			{
				//Check date fin 24:15 or 24:30..
				if(strtotime($date_fin) <= (strtotime($event_date_fin)+60*60*24-1))
				{
					if( Booking::isFree($date_deb,$date_fin,$sel_room_id) )
					{
						AdminCore::addEventPlanning( $sel_room_id, $sel_member_id,  $date_deb, $date_fin);
						$html = "<div class='update'>".__('Event added','roomplanning').'</p></div>';
						echo json_encode(array('error'=>2,'room_id' => $sel_room_id, 'html'=> $html, 'data'=>date("Y-m-d",strtotime($date_deb))));
						die;
					}
					else
						$html = "<div class='update'><p style='color:red;'>".__('Error: This room is not free for this date time','roomplanning').'</p></div>';
				}
				else
					$html = "<div class='update'><p style='color:red;'>".__('Error: Time not available','roomplanning').'</p></div>';
			}
			else
				$html = "<div class='update'><p style='color:red;'>".__('Error: Date end must be superior to date beginning','roomplanning').'</p></div>';
		}
		else
			$html = "<div class='update'><p style='color:red;'>".__('Error: all field must be fill','roomplanning').'</p></div>';
		echo json_encode(array('error'=>1, 'html'=> $html));
		die;
	}
	function ajax_addMember(){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		if( isset($_POST['do_check_form_security'])) AjaxRequest::verifyForm();

		$member_name = RP_utils::satanizeDataAjax($_POST['membername']);
		$member_desc = RP_utils::satanizeDataAjax($_POST['memberdesc']);
		$member_mail = RP_utils::satanizeDataAjax($_POST['memberemail']);
		if(!empty($_POST['membername']))
		{
			$user_id = AdminCore::addMember($member_name,$member_desc,$member_mail);
			if( $user_id == 0)
			{
				$html = '<div class="update"><p style="color:red;">'.__('User already exists','roomplanning').'</p></div>';
				echo json_encode(array('error'=>1, 'html'=> $html));
				die;
			}
			else
			{
				$html = "<div class='update'><p>".__('Member added','roomplanning').'</p></div>';
				$member_name = $wpdb->get_var('SELECT member_name FROM '.BDD_PLANNINGROOM_MEMBER.' WHERE id_member='.$user_id);
				echo json_encode(array('error'=>2, 'html'=>$html, 'data'=>$user_id, 'datahtml'=>stripslashes($member_name)));
				die;
			}
		}
		else
		{
			$html = "<div class='update'><p style='color:red;'>".__('Error: name of member must be fill','roomplanning').'</p></div>';
			echo json_encode(array('error'=>1, 'html'=> $html));
			die;
		}
	}
	function ajax_addRoom(){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		if( isset($_POST['do_check_form_security'])) AjaxRequest::verifyForm();

		$roomname = RP_utils::satanizeDataAjax($_POST['roomname']);
		$roomdesc = RP_utils::satanizeDataAjax($_POST['roomdesc']);
		if(!empty($roomname))
		{
			$idNewRoom = AdminCore::addRoom($roomname,$roomdesc);
			$html = "<div class='update'><p>".__('Room added','roomplanning')."</p></div>";
			echo json_encode(array('error'=>2, 'html'=>$html, 'data'=>$idNewRoom, 'datahtml'=>stripslashes($roomname)));
			die;
		}
		else
		{
			$html = "<div class='update'><p style='color:red;'>".__('Error: name of room must be fill','roomplanning')."</p></div>";
			echo json_encode(array('error'=>1, 'html'=> $html));
			die;
		}
	}
	function ajax_searchMember( ){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		$return = __('Results not found','roomplanning');
		$search = RP_utils::satanizeDataAjax( $_POST['searchterm'] );
		if( !Booking::isWPr() )
			$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM_MEMBER." WHERE member_name LIKE '".$search."%' ORDER BY member_name ASC");
		else
			$lines = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."users WHERE user_login LIKE '".$search."%' ORDER BY user_login ASC");
		if( $lines )
		{
			$return = '<ul id="suggestion" class="suggestion">';
			foreach ($lines as $member)
			{
				$idMember = ( Booking::isWPr() ) ? $member->ID : $member->id_member;
				$nameMember = ( Booking::isWPr() ) ? $member->user_login : $member->member_name;
				$return .= '<li id="'.$idMember.'" onclick="protoAdminEdit.memberSelect('.$idMember.');">'.stripslashes($nameMember).'</li>';
			}
			$return .= '</ul>';
		}
		echo $return;
		die();
	}
	function ajax_updateMember(){
		check_ajax_referer( 'ajax_security', 'security' );
		$id_member = intval($_POST['id_member']);
		$desc_member = RP_utils::satanizeDataAjax($_POST['update_member_desc'.$id_member]);
		$mail_member = RP_utils::satanizeDataAjax($_POST['update_member_mail'.$id_member]);
		AdminCore::editMember($id_member,$desc_member,$mail_member);
		die();
	}
	function ajax_updateRoom(){
		check_ajax_referer( 'ajax_security', 'security' );
		$id_room = intval($_POST['id_room']);
		$desc_room = RP_utils::satanizeDataAjax($_POST['update_room_'.$id_room]);
		AdminCore::editRoom($id_room,$desc_room);
		die();
	}
	function ajax_freeTime(){
		check_ajax_referer( 'ajax_security', 'security' );
		AdminCore::removeEventPlanning($_POST['id']) ;
		die();
	}
	function ajax_removeRoom(){
		check_ajax_referer( 'ajax_security', 'security' );
		AdminCore::removeRoom($_POST['id']) ;
		die();
	}
	function ajax_removeMember(){
		check_ajax_referer( 'ajax_security', 'security' );
		AdminCore::removeMember($_POST['id']) ;
		die();
	}
	function ajax_removeGroup(){
		check_ajax_referer( 'ajax_security', 'security' );
		$type = RP_utils::satanizeDataAjax($_POST['type']);
		$inputs = split(',',RP_utils::satanizeDataAjax($_POST['input']));
		$nb = count($inputs);
		for($i=0;$i<$nb;$i++)
		{
			$id = $inputs[$i];
			if(!empty($id))
			{
				if($type=='planning')
					AdminCore::removeEventPlanning($id);
				elseif($type=='member')
					AdminCore::removeMember($id);
				elseif($type=='room')
					AdminCore::removeRoom($id);
			}
		}
		die();
	}
	function ajax_pagination(){
		$options = RoomPlanning::getOption();

		$page_id = RP_utils::satanizeDataAjax($_POST['page_id']);
		$type = RP_utils::satanizeDataAjax($_POST['type']);
		switch($type)
		{
			case 'list_planning':
				$html = AdminCore::showPlanningRoom($page_id,false);
				$nbEvent = RP_utils::getNumberRecord('list_event');
				$objPagination = new Pagination($page_id,$nbEvent,$options['planningroom_nbresult_admin']);
				$pagination = $objPagination->getPagination('list_event',4);
				echo json_encode(array('html'=> $html, 'pagination' => $pagination));
				break;

			case 'list_member':
				$html = AdminCore::showMembers($page_id,false);
				$nbMember = RP_utils::getNumberRecord('list_member');
				$objPagination = new Pagination($page_id,$nbMember,$options['planningroom_nbresult_admin']);
				$pagination = $objPagination->getPagination('list_member',3);
				echo json_encode(array('html'=> $html, 'pagination' => $pagination));
				break;

			case 'list_room':
				$html = AdminCore::showRoom($page_id,false);
				$nbRoom = RP_utils::getNumberRecord('list_room');
				$objPagination = new Pagination($page_id,$nbRoom,$options['planningroom_nbresult_admin']);
				$pagination = $objPagination->getPagination('list_room',3);
				echo json_encode(array('html'=> $html, 'pagination' => $pagination));
				break;
		}
		unset($objPagination);
		die();
	}

	function ajax_loadData(){
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		list($html,$day,$room_id) = ShortCodePlanningRoom::show_planning_room(null,null,null,true);
		echo json_encode(array('html'=>$html,'day'=>$day,'room_id'=>$room_id));
		die();
	}

	function ajax_booking(){
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		$day_booking = RP_utils::satanizeDataAjax($_GET['day']);
		$room_id = RP_utils::satanizeDataAjax($_GET['roomid']);
		$admin_page = (isset($_GET['admin_page'])) ? $_GET['admin_page'] : 0;
		$nonce_ajax = wp_create_nonce('ajax_security');
		if( $admin_page || Booking::is_user_logged_in() )
		{
			$forcelog = ($admin_page) ? true : false;
			$booking_member_id = RP_utils::getInfoMember('id');
			$booking_day = date_i18n( 'D j F', strtotime($day_booking));
			require_once(PATH_DIR_PLANNING_ROOM_POPUP.'popupBooking.php');
			echo $msg;
		}
		else
			require_once(PATH_DIR_PLANNING_ROOM_POPUP.'popupLogin.php');
		die();
	}
	function ajax_bookingRoom(){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		$houropen = RP_utils::satanizeDataAjax ($_POST['booking_houropen']);
		$openminutes = RP_utils::satanizeDataAjax ($_POST['booking_openminutes']);
		$hourclose = RP_utils::satanizeDataAjax ($_POST['booking_hourclose']);
		$closeminutes = RP_utils::satanizeDataAjax ($_POST['booking_closeminutes']);
		$room_id = RP_utils::satanizeDataAjax ($_POST['booking_room']);
		$member_id = RP_utils::satanizeDataAjax ($_POST['booking_member_id']);
		$day = RP_utils::satanizeDataAjax ($_POST['booking_day']);

		if( !Booking::is_user_logged_in() && !isset($_POST['booking_force']))
		{
			echo json_encode( array( 'error' => 1 , 'html' => '<div id="message" class="update"><p>'.__('You must logged to booking a room','roomplanning').'</p></div>'));
			die();
		}
		//add event
		$date_deb = $day." ".$houropen.":".$openminutes.":00";
		$date_fin = $day." ".$hourclose.":".$closeminutes.":00";

		if( Booking::isFree($date_deb,$date_fin,$room_id) )
		{
			AdminCore::addEventPlanning( $room_id, $member_id,  $date_deb, $date_fin);
			echo json_encode( array( 'error' => 2 , 'room_id' => $room_id, 'html' => '<div id="message" class="update"><p>'.__('Booking confirmed','roomplanning').'</p></div>'));
		}
		else
		{
			echo json_encode( array( 'error' => 1 , 'html' => '<div id="message" class="update"><p>'.__('Room is not available for this time','roomplanning').'</p></div>'));
		}
		die();
	}
	function ajax_bookingRegisterMember(){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		$name = RP_utils::satanizeDataAjax ($_POST['booking_register_member_name']);
		$email = RP_utils::satanizeDataAjax ($_POST['booking_register_member_email']);
		if(empty($name) || empty($email))
		{
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('You must fill all field','roomplanning').'</p></div>'));
			die();
		}
		if(RP_utils::username_exists($name))
		{
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('This user already exist','roomplanning').'</p></div>'));
			die();
		}
		if(!is_email($email))
		{
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('Mail not valid','roomplanning').'</p></div>'));
			die();
		}
		$nonce_ajax = wp_create_nonce('ajax_security');
		$booking_member_id = AdminCore::addMember($name,'',$email);
		$booking_day = date_i18n( 'D j F', strtotime($_POST['day_booking']));
		$day_booking = $_POST['day_booking'];
		$room_id = intval($_POST['room_id']);
		$forcelog = true;

		Booking::setCookieMember($booking_member_id);
		require_once(PATH_DIR_PLANNING_ROOM_POPUP.'popupBooking.php');
		echo json_encode( array('error' => 2,'html' => '<div id="message" class="update"><p>'.__('Welcome','roomplanning').' '.stripslashes($name).'</p></div>' . $msg));
		die();
	}
	function ajax_bookingLogin(){
		global $wpdb;
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		$name = RP_utils::satanizeDataAjax ($_POST['booking_login_name']);
		$passwd = RP_utils::satanizeDataAjax ($_POST['booking_login_passwd']);
		$day_booking = RP_utils::satanizeDataAjax ($_POST['day_booking']);
		$room_id = RP_utils::satanizeDataAjax ($_POST['room_id']);
		if( empty($passwd) || empty($name) )
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('You must fill all field','roomplanning').'</p></div>'));
		else
		{
			$booking_member_id = RP_utils::username_exists($name);
			if( $booking_member_id == 0 )
			{
				echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('Name not valid','roomplanning').'</p></div>'));
				die();
			}
			if(!RP_utils::checkAuthentification($name,$passwd))
			{
				echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('Name and password not valid','roomplanning').'</p></div>'));
				die();
			}
			else
			{
				Booking::setCookieMember($booking_member_id);
				$booking_day = date_i18n( 'D j F', strtotime($day_booking));
				$nonce_ajax = wp_create_nonce( 'ajax_security' );
				require_once(PATH_DIR_PLANNING_ROOM_POPUP.'popupBooking.php');
				echo json_encode( array( 'error' => 2, 'html' => $msg ));
			}
		}
		die();
	}
	function ajax_bookingForgetPassword(){
		global $wpdb;
		require_once(ABSPATH . WPINC . '/registration.php');
		//Security referer
		check_ajax_referer( 'ajax_security', 'security' );
		$name = RP_utils::satanizeDataAjax ($_POST['booking_forget_name']);
		$email = RP_utils::satanizeDataAjax($_POST['booking_forget_email']);
		if(empty($email) OR empty($name))
		{
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('You must fill all field','roomplanning').'</p></div>'));
			die();
		}
		if(!is_email($email))
		{
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('Mail not valid','roomplanning').'</p></div>'));
			die();
		}
		$id_user = RP_utils::username_exists($name);
		if( $id_user == 0 )
		{
			echo json_encode( array( 'error' => 1, 'html' => '<div id="message" class="update"><p>'.__('User not registered','roomplanning').'</p></div>'));
			die();
		}
		$password = wp_generate_password( 12, false );
		if( !Booking::isWPr() )
			$sql = "UPDATE ".BDD_PLANNINGROOM_MEMBER." SET member_passwd = '$password' WHERE id_member = ".$id_user." LIMIT 1";
		else
			wp_set_password( $password,$id_user);

		$headers = 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'' . "\r\n";
		wp_mail( $email, __('Registration at','roomplanning').' '.get_bloginfo('name'), __('Your password:','roomplanning').' '.$password,$headers);
		echo json_encode( array( 'error' => 2, 'html' => '<div id="message" class="update"><p>'.__('Password was sent at','roomplanning').' '.$email.'</p></div>'));
		die();
	}
}
?>