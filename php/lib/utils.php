<?php
/**
* CLASS RP UTILS
* All function utilities
**/
class RP_utils {

	function RP_utils(){}
	//Determine if user display admin page or not
	function is_admin_page(){
		$arrPages = array('page=roomplanning','page=roomplanning_edit','page=roomplanning_list','page=roomplanning_help');
		$url = parse_url(RP_utils::curPageURL());
		return in_array($url['query'],$arrPages);
	}
	/**
	* Display HTML select Hour
	* @param: name						// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: limit						// Positionning hour with options config
	* @param: echo 						// display or not
	* @return echo $msg ou $msg
	*/
	function selectHour($name,$selectedIndex=0,$limit=true,$echo=true){
		if( $limit )
		{
			$options = RoomPlanning::getOption();
			$hourOpen = intval($options['planningroom_openhour']);
			$hourClose = intval($options['planningroom_closehour']);
		}
		else
		{
			$hourOpen = 0;
			$hourClose = 24;
		}

		$msg = '<select style="width:50px;" name="'.$name.'" id="'.$name.'">';
		for($i=$hourOpen;$i<$hourClose+1;$i++)
		{
			$var = zeroise($i,2);
			$msg .= '<option value="'.$var.'" '.selected($selectedIndex,$var,false).'>'.$var.'</option>';
		}
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	* Display HTML select Minute
	* @param: name						// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: echo 						// display or not
	* @return echo $msg ou $msg
	*/
	function selectMinutes($name,$selectedIndex=0,$echo=true){
		$msg = '<select style="width:50px;" name="'.$name.'" id="'.$name.'">';
		for($i=0;$i<60;$i = $i + 15)
		{
			$var = zeroise($i,2);
			$msg .= '<option value="'.$var.'" '.selected($selectedIndex,$i,false).'>'.$var.'</option>';
		}
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	 * Display HTML select view
	 * @param: name						// Name and id of element
	 * @param: selectedIndex		// Selected index
	 * @param: echo 						// display or not
	 * @return echo $msg ou $msg
	 */
	function selectView($name,$selectedIndex=0,$echo=true){
		$arr = array('d','w','m','y');
		$arrString = array();
		$arrString[] = __('Day','roomplanning');
		$arrString[] = __('Week','roomplanning');
		$arrString[] = __('Month','roomplanning');
		$arrString[] = __('Year','roomplanning');

		$nb = count($arr);
		$msg = '<select name="'.$name.'" id="'.$name.'">';
		for($i=0;$i<$nb;$i++)
			$msg .= '<option value="'.$arr[$i].'" '.selected($selectedIndex,$i,false).'>'.$arrString[$i].'</option>';
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	* Display HTML select Room
	* @param: name						// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: echo 						// display or not
	* @return echo $msg ou $msg
	*/
	function selectRoom($name='',$selectedIndex=0,$echo=true) {
		global $wpdb;
		$msg = '<select id="'.$name.'" name="'.$name.'">';
		$msg .= '<option value="">'.__('Choose room','roomplanning').'&nbsp;</option>';
		$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM_ROOM." ORDER BY room_name ASC");
		if( $lines )
			foreach ($lines as $room)
				$msg .= '<option value="'.$room->id_room.'" '.selected($selectedIndex,$room->id_room,false).'>'.stripslashes($room->room_name).'</option>';
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	function checkBoxRoom($name){
		global $wpdb;
		$msg = '';
		$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM_ROOM." ORDER BY room_name ASC");
		if( $lines )
			foreach ($lines as $room)
				$msg .= '<input type="checkbox" class="'.$name.'" name="'.$name.'" value="'.$room->id_room.'">'.stripslashes($room->room_name).'&nbsp;';
		return $msg;
	}
	/**
	* Display HTML select Member
	* @param: name						// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: echo 						// display or not
	* @return echo $msg ou $msg
	*/
	function selectMember($name='',$selectedIndex=0,$echo=true) {
		global $wpdb;
		$msg =  '<select id="'.$name.'" name="'.$name.'">';
		$msg .= '<option value="">'.__('Choose member','roomplanning').'&nbsp;</option>';
		$lines = $wpdb->get_results("SELECT * FROM ".BDD_PLANNINGROOM_MEMBER."	ORDER BY member_name ASC");
		if( $lines )
			foreach ($lines as $member)
				$msg .= '<option value="'.$member->id_member.'" '.selected($selectedIndex,$member->id_member,false).'>'.stripslashes($member->member_name).'</option>';
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	* Display HTML select Yes or No
	* @param: name						// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: echo 						// display or not
	* @return echo $msg ou $msg
	*/
	function selectYesNo($name,$selectedIndex=0,$echo=true){
		$msg = '<select id="'.$name.'" name="'.$name.'">';
		$msg .= '<option value="0" '.selected($selectedIndex,0,false).'>'.__('No','roomplanning').'</option>';
		$msg .= '<option value="1" '.selected($selectedIndex,1,false).'>'.__('Yes','roomplanning').'</option>';
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	* Display HTML select number of day for reservation
	* @param: name						// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: echo 						// display or not
	* @return echo $msg ou $msg
	*/
	function selectDayBooking($name,$selectedIndex=0,$echo=true){
		$msg = '<select id="'.$name.'" name="'.$name.'" style="width:50px;">';
		for($i=0;$i<8;$i++)
			$msg .= '<option value="'.$i.'" '.selected($selectedIndex,$i,false).'>'.$i.'</option>';
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	* Display HTML select number of pae for navigation
	* @param: name				// Name and id of element
	* @param: selectedIndex		// Selected index
	* @param: echo 				// display or not
	* @return echo $msg ou $msg
	*/
	function selectNumber($name,$selectedIndex=0,$echo=true){
		$msg = '<select name="'.$name.'" id="'.$name.'">';
		$msg .= '<option value="5" '.selected($selectedIndex,5,false).'>5</options>';
		$msg .= '<option value="10" '.selected($selectedIndex,10,false).'>10</options>';
		$msg .= '<option value="20" '.selected($selectedIndex,20,false).'>20</options>';
		$msg .= '<option value="50" '.selected($selectedIndex,50,false).'>50</options>';
		$msg .= '<option value="100" '.selected($selectedIndex,100,false).'>100</options>';
		$msg .= '</select>';
		if( $echo )
			echo $msg;
		else
			return $msg;
	}
	/**
	* Satanize Post data
	* @param: data
	* @return data
	*/
	function satanizeDataAjax($data) {
		$data = wp_strip_all_tags($data);
		return mysql_real_escape_string(stripslashes($data));
	}
	//Parse url
	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	/**
	* Check if date on is < date two
	* @param: date1
	* @return date2
	*/
	function checkDateDiff($date1,$date2){
		return ( strtotime($date1) - strtotime($date2) > 0 ) ? false : true;
	}
	//Check if username exist in database
	function username_exists($member_name){
		global $wpdb;
		if( !Booking::isWPr() )
		{
			$line = $wpdb->get_row('SELECT * FROM '.BDD_PLANNINGROOM_MEMBER.' WHERE member_name = "'.$member_name.'"');
			if( $line ) return $line->id_member;
			return 0;
		}
		else
		{
			$user_id = username_exists( $member_name );
			return (!$user_id) ?  0 : 1;
		}
	}

	function checkAuthentification($user_login,$user_pass){
		if(Booking::isWPr())
			return user_pass_ok($user_login,$user_pass);
		else{
			$line = $wpdb->get_row( $wpdb->prepare('SELECT * FROM '.BDD_PLANNINGROOM_MEMBER.' WHERE member_name = %d AND member_passwd=%d',$user_login,$user_pass));
			if( $line ) return 1;
			return 0;
		}
	}
	//Return info for current user
	function getInfoMember($param=''){
		global $wpdb, $current_user;
		if( Booking::isWPr() && is_user_logged_in() )
		{
			if( empty( $param ) ) return $current_user;
			switch($param)
			{
				case 'id' : return $current_user->ID; break;
				case 'mail': return $current_user->user_email; break;
				case 'login': return $current_user->user_login; break;
			}
		}
		if( !Booking::isWPr() &&  Booking::is_user_logged_in() && isset($_COOKIE['roomplanning_log']) )
		{
			$id_user = intval($_COOKIE['roomplanning_log']);
			$line_user = $wpdb->get_row('SELECT * FROM '.BDD_PLANNINGROOM_MEMBER.' WHERE id_member = '.$id_user);
			if( empty( $param ) ) return $line_user;
			switch($param)
			{
				case 'id' : return $id_user; break;
				case 'mail': return $line_user->member_email; break;
				case 'login': return $line_user->member_name; break;
			}
		}
		return null;
	}
	//Determine if user is administrator
	function is_admin_user() {
    	global $current_user;
    	return $current_user->caps['administrator'];
	}
	//Display all background img
	function listBackground($type,$selectedIndex){
		$name = ($type=="back") ? 'planningroom_backimg' : 'planningroom_reservedimg';
		$arrFormatImg = array('jpg','jpeg','gif','png');
		$msgReturn = '';
		foreach ( glob(ROOMPLANNING_IMG_DIR.'*') as $filename)
		{
			if( in_array( end(explode(".", $filename)), $arrFormatImg ) )
			{
				if($type=='back')
					$selected = ($selectedIndex==basename($filename)) ? 'checked' : '';
				elseif($type=='reserved')
					$selected = ($selectedIndex==basename($filename)) ? 'checked' : '';
				$msgReturn .= '<div class="backgroundChoose"><input type="radio" '.$selected.' name="'.$name.'" value="'.basename($filename).'"><span class="img_background" style="background:url('.ROOMPLANNING_IMG_URL.basename($filename).');"></span></div>';
			}
		}
		echo $msgReturn;
	}
	//Format for javascript times options
	function getTimesOptions($day='',$month='',$year=''){
		$options = RoomPlanning::getOption();

		$day_deb  = (!empty($year)) ? $year.'-' : date("Y-");
		$day_deb .= (!empty($month))? $month 	: date("m");
		$day_deb .= (!empty($day))	? '-'.$day 	: date("-d");

		if($options['planningroom_openhour']=='00' AND $options['planningroom_openminutes']=='00')
			$day_deb.= ' 00:00:01';
		else
			$day_deb .= ' '.$options['planningroom_openhour'].':'.$options['planningroom_openminutes'].':00';

		$day_fin  = (!empty($year))  ? $year.'-': date("Y-");
		$day_fin .= (!empty($month)) ? $month 	: date("m");
		$day_fin .= (!empty($day))   ? '-'.$day : date("-d");
		if($options['planningroom_closehour']== '24')
			$day_fin .= ' 23:59:59';
		else
			$day_fin .= ' '.$options['planningroom_closehour'].':'.$options['planningroom_closeminutes'].':00';
		return array( $day_deb, $day_fin );
	}
	/** Return all id room in array */
	function listIdRoom(){
		global $wpdb;
		$arrRoom = array();
		$lines = $wpdb->get_results('SELECT id_room FROM '.BDD_PLANNINGROOM_ROOM.' ORDER BY id_room ASC');
		if($lines)
			foreach($lines as $line)
				$arrRoom[] = $line->id_room;
		return $arrRoom;
	}
	//Calcultate nb recors for room, member and event
	//Use for navigation system
	function getNumberRecord($type){
		global $wpdb;
		if( $type == 'list_planning')
			return $wpdb->get_var("SELECT COUNT(id) AS nbEvent FROM ".BDD_PLANNINGROOM." WHERE LEFT(date_deb,10) >= '".date("Y-m-d")."'");
		if( $type == 'list_room')
			return $wpdb->get_var("SELECT COUNT(id_room) AS nbRoom FROM ".BDD_PLANNINGROOM_ROOM);
		if( $type == 'list_member')
		{
			if( !Booking::isWPr())
				return $wpdb->get_var("SELECT COUNT(id_member) AS nbMember FROM ".BDD_PLANNINGROOM_MEMBER);
			else
				return $wpdb->get_var("SELECT COUNT(ID) AS nbMember FROM ".$wpdb->prefix."users");
		}
	}
	//Return language for js date picker
	function getIsoLanguage(){
		$lang = split('-',get_bloginfo('language'));
		return $lang[0];
	}
}
?>