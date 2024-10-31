<?php

class ShortCodePlanningRoom {

	function ShortCodePlanningRoom(){}
	//Shoortcode
	function show_planning_room($atts,$content=null,$c=null,$ajax=false){
		//Get parameters
		if( $ajax )
		{
			$day 			= (isset($_POST['day'])) ? RP_utils::satanizeDataAjax($_POST['day']) : ROOMPLANNING_DAY;
			$room_id		= (isset($_POST['roomplanning_selRoom']) && !empty($_POST['roomplanning_selRoom'])) ? RP_utils::satanizeDataAjax($_POST['roomplanning_selRoom']) : 0;
			$member_id		= (isset($_POST['roomplanning_selMember']) && !empty($_POST['roomplanning_selMember'])) ? RP_utils::satanizeDataAjax($_POST['roomplanning_selMember']) : 0;
			$view_id		= (isset($_POST['roomplanning_selView']) && !empty($_POST['roomplanning_selView'])) ? RP_utils::satanizeDataAjax($_POST['roomplanning_selView']) : 'd';
			$shownavigation = (isset($_POST['roomplanning_shownavigation']) && !empty($_POST['roomplanning_shownavigation'])) ? RP_utils::satanizeDataAjax($_POST['roomplanning_shownavigation']) : 0;
			$showfilter 	= (isset($_POST['roomplanning_showfilter']) && !empty($_POST['roomplanning_showfilter'])) ? RP_utils::satanizeDataAjax($_POST['roomplanning_showfilter']) : 0;
			$showbooking 	= (isset($_POST['roomplanning_showbooking']) && !empty($_POST['roomplanning_showbooking'])) ? RP_utils::satanizeDataAjax($_POST['roomplanning_showbooking']) : 0;
		}
		else
		{
			extract(shortcode_atts(array(
			'day' => ROOMPLANNING_DAY,
			'room_id' => 0,
			'member_id' => 0,
			'view_id' => 'd',
			'shownavigation' => 0,
			'showfilter' => 0
			), $atts));
		}

		//Check date if is Sunday or Saturday and close in week
		//So, take the next monday
		$options = RoomPlanning::getOption();
		$day_name = date("D",strtotime($day));
		if(!$options['planningroom_weekopen'] AND ($day_name=='Sun' OR $day_name=='Sat'))
			$day = date("Y-m-d",strtotime('midnight next monday',strtotime($day)));
		//Check if date < today
		if(!RP_utils::is_admin_user() AND $day < ROOMPLANNING_DAY)
			$day = ROOMPLANNING_DAY;

		//Load data
		$datas = ShortCodePlanningRoom::loadData($day,$showdayempty,$room_id,$member_id,$view_id);
		//Starting output
		$msgReturn = '';
		//Create contener planningRoom
		if(!$ajax)
			ShortCodePlanningRoom::createRoomPlanning($day,$room_id,$member_id,$view_id,$shownavigation,$showdayempty,$showfilter);
		//Create navigation
		if( $shownavigation )
			$msgReturn .= ShortCodePlanningRoom::createNavigation($day,$view_id);
		//Create planning for each room
		$arrRoomsKey = array_keys($datas);
		$nbRoomKeys = count($arrRoomsKey);
		for($i=0; $i < $nbRoomKeys; $i++)
		{
			$room_id = $arrRoomsKey[$i];
			$msgReturn .= ShortCodePlanningRoom::createRoom($room_id,$datas[$room_id]);
		}
		//End of output
		$msgReturn .= '</div></div>';

		if(!$ajax)
			echo $msgReturn;
		else
		{
			if(RP_utils::is_admin_user() AND $day < ROOMPLANNING_DAY)
				$day = ROOMPLANNING_DAY;
			return array($msgReturn,$day,$room_id);
		}

	}
	//Create room planning contener
	function createRoomPlanning($day,$room_id,$member_id,$view_id,$shownavigation,$showdayempty,$showfilter){
		$options = RoomPlanning::getOption();
		if( !empty($options['planningroom_title'])) echo '<h2 class="planningRoomTitle">'.stripslashes($options['planningroom_title']).'</h2>';
		echo '<div id="planningRoom">';
		//Display filter bar
		$style = (!$showfilter) ? 'style="visibility:hidden;"' : '';
		echo '<div id="roomplanning_input_options">';
		echo '<div '.$style.'>';
		echo ShortCodePlanningRoom::createPanelOptions($day,$view_id,$room_id,$member_id,$showdayempty,$shownavigation);
		echo '</div></div>';
		//Calendar
		$style = ( !$shownavigation ) ? ' style="visibility:hidden;"' : '';
		echo '<div id="roomplanning_calendar"><input type="text" id="roomplanning_input_date" class="datepicker" '.$style.' value="'.$day.'"/></div>';
		//All time room
		echo '<div id="planningRoom_all">';
	}
	//Create and positionning booking for a room
	function createRoom($room_id, $allDatas){
		$msgReturn = ShortCodePlanningRoom::createHeaderRoom($room_id);
		$now = $this->_firstDay;
		$firstRoomTime = true;
		$options = RoomPlanning::getOption();
		$weekopen = $options['planningroom_weekopen'];
		//No loggin for admin user on administration page
		$admin_page = (RP_utils::is_admin_page()) ? '&admin_page=1' : '';

		$day_booking = $options['planningroom_day_booking'];
		$bool_booking = (!RP_utils::is_admin_page()) ? $options['planningroom_booking'] : true;
		do{
			$dayName = date("D",strtotime($now));
			if( !$weekopen AND ($dayName=='Sun' OR $dayName=='Sat'))
			{}
			else
			{
				$msgReturn .=  '<div class="roomplanning_conteneur">';
				$msgReturn .=  '<div class="roomplanning_time"></div>';
				$day_booking_end = date("Y-m-d",strtotime('+'.$day_booking.' day',strtotime(ROOMPLANNING_DAY)));
				if($now >= $day_booking_end && $bool_booking)
				{
					$url = admin_url( 'admin-ajax.php' );
					$params = '?action=booking&height=450&width=600&day='.$now.'&roomid='.$room_id.'&time='.time().'&security='.wp_create_nonce( 'ajax_security' ).$admin_page;
					$msgReturn .=  '<a href="'.$url.$params.'" title="'.__('Book room for','roomplanning').' '.ucfirst(date_i18n('D j F',strtotime($now))).'" class="roomplanning_maptime_contener thickbox"  style="background:bottom left url('.ROOMPLANNING_IMG_URL.$options['planningroom_backimg'].') repeat-x;">';
				}
				else
					$msgReturn .= '<div class="roomplanning_maptime_contener" style="background:bottom left url('.ROOMPLANNING_IMG_URL.$options['planningroom_backimg'].') repeat-x;">';
				$msgReturn .= '<div class="dayinfo">'.ucfirst(date_i18n('D j F',strtotime($now))).'</div>';
				if( array_key_exists($now,$allDatas))
				{
					$nbEvent = count($allDatas[$now]);
					for($j=0;$j<$nbEvent;$j++)
						$msgReturn .= ShortCodePlanningRoom::createMapTime($allDatas[$now][$j]->date_deb, $allDatas[$now][$j]->date_fin, $allDatas[$now][$j]->member_id );
				}
				if($now >= $day_booking_end && $bool_booking)
					$msgReturn .=  '</a>';
				else
					$msgReturn .=  '</div>';
				$msgReturn .=  '</div>';
			}
			$now = date("Y-m-d",strtotime('midnight next day',strtotime($now)));
		}while( $now <= $this->_endDay );
		$msgReturn .=  '</div>';
		$msgReturn .=  '</div>';
		return $msgReturn;
	}
	//Create header for a room
	function createHeaderRoom($room_id){
		global $wpdb;
		$row = $wpdb->get_row("SELECT * FROM ".BDD_PLANNINGROOM_ROOM." WHERE id_room = ".$room_id);
		$msgReturn  =  '<div class="header" id="'.$room_id.'"><img src="'.PATH_URL_PLANNING_ROOM.'img/header_point.png" alt=""/>'.stripslashes($row->room_name).' ';
		if(!empty($row->description))
			$msgReturn .=  '<a href="#"><img src="'.PATH_URL_PLANNING_ROOM.'img/icon.png" alt=""/><span>'.stripslashes($row->description).'</span></a>';
		$msgReturn .=  '</div>';
		$msgReturn .=  '<div class="roomplanning_room" id="roomplanning_room_'.$room_id.'">';
		$msgReturn .=  '<div class="roomplanning_track">';
		return $msgReturn;
	}
	//Display options filter
	function createPanelOptions($day,$view_id,$room_id,$member_id,$showemptyday,$shownavigation){
		echo '<form name="roomplanning_form_options" id="roomplanning_form_options">';
		echo '<input type="hidden" name="action" value="loadData"/>';
		echo '<input type="hidden" name="roomplanning_shownavigation" value="'.$shownavigation.'"/>';
		echo __('Filter :','roomplanning').'<select name="roomplanning_selView" id="roomplanning_selView">';
		$selected = ( $view_id == 'd' ) ? 'selected="selected"' : '';
		echo '<option value="d" '.$selected.'>'.__('Day','roomplanning').'</option>';
		$selected = ( $view_id == 'w' ) ? 'selected="selected"' : '';
		echo '<option value="w" '.$selected.'>'.__('Week','roomplanning').'</option>';
		$selected = ( $view_id == 'm' ) ? 'selected="selected"' : '';
		echo '<option value="m" '.$selected.'>'.__('Month','roomplanning').'</option>';
		if(RP_utils::is_admin_page())
		{
			$selected = ( $view_id == 'y' ) ? 'selected="selected"' : '';
			echo '<option value="y" '.$selected.'>'.__('Year','roomplanning').'</option>';
		}
		echo '</select>';
		RP_utils::selectRoom('roomplanning_selRoom',$room_id);
		if(RP_utils::is_admin_page())
			RP_utils::selectMember('roomplanning_selMember',$member_id);
		echo '</form>';
	}
	//Create navigation time
	function createNavigation($day,$view_id){
		$options = RoomPlanning::getOption();
		$isOpenWeek = $options['planningroom_weekopen'];
		$format = 'D j F';
		//Check day name for calculate period
		$day_name = date("D",strtotime($day));
		list($year,$month,$the_day) = explode('-',$day);
		$date_first = ROOMPLANNING_DAY;
		list($year_f,$month_f,$day_f) = explode('-',ROOMPLANNING_DAY);
		if( $view_id == 'w')
		{
			//Last monday
			if( $day_name != 'Mon' )
			{
				$date_before = mktime(0,0,0,$month,$the_day-7,$year);
				$date_before = date("Y-m-d",strtotime('midnight last monday',$date_before));
				$date_first = date("Y-m-d",strtotime('midnight last monday',mktime(0,0,0,$month_f,$day_f+7,$year_f)));
			}
			else
			{
				$date_before = date("Y-m-d",strtotime('midnight last monday',strtotime($day)));
				$date_first = $date_before;
			}

			//Next monday
			$date_after = date("Y-m-d",strtotime('midnight next monday',strtotime($day)));
		}
		//Month representation
		elseif( $view_id == 'm')
		{
			$date_before = date('Y-m-d',mktime(0,0,0,$month-1,1,$year));
			$date_after = date('Y-m-d',mktime(0,0,0,$month+1,1,$year));
			$date_first = date("Y-m-d",mktime(0,0,0,$month_f,1,$year_f));
		}
		//Year representation
		elseif( $view_id == 'y')
		{
			$date_before = date("Y-m-d",mktime(0,0,0,1,1,$year-1));
			$date_after  = date("Y-m-d",mktime(0,0,0,1,1,$year+1));
			$date_first = date("Y-m-d",mktime(0,0,0,1,1,$year_f));
			$format = 'D j F, Y';
		}
		else
		{
			$date_before = date("Y-m-d", strtotime($day)-86400);
			$date_after  = date("Y-m-d", strtotime($day)+86400);
			$day_before = date("D",strtotime($date_before));
			$day_after = date("D",strtotime($date_after));
			if(!$isOpenWeek && $day_before == 'Sun')
				$date_before = date("Y-m-d", strtotime($day)-(86400*3));
			if(!$isOpenWeek && $day_before == 'Sat')
				$date_before = date("Y-m-d", strtotime($day)-(86400*2));
			if(!$isOpenWeek && $day_after == 'Sun')
				$date_after = date("Y-m-d", strtotime($day)+(86400*2));
			if(!$isOpenWeek && $day_after == 'Sat')
				$date_after = date("Y-m-d", strtotime($day)+(86400*3));
		}
		$msgReturn = '<div id="roomplanning_navigation">';
		$msgReturn .= '<a href="#" onclick="myPlanningRoom.loadData(\''.$date_after.'\',0); return false;" class="roomplanning_after">'.date_i18n($format,strtotime($date_after)).' >></a>';
		if( !RP_utils::is_admin_user() AND $date_before >= $date_first)
			$msgReturn .= '<a href="#" onclick="myPlanningRoom.loadData(\''.$date_before.'\',0); return false;" class="roomplanning_before"><< '.date_i18n($format,strtotime($date_before)).'</a>';
		else
			$msgReturn .= '<a href="#" onclick="myPlanningRoom.loadData(\''.$date_before.'\',0); return false;" class="roomplanning_before"><< '.date_i18n($format,strtotime($date_before)).'</a>';
		$msgReturn .= '</div>';
		return $msgReturn;
	}
	//Create day planning
	function createMapTime($date_deb,$date_fin,$member_id){
		global $wpdb;

		$row = $wpdb->get_row("SELECT * FROM ".BDD_PLANNINGROOM_MEMBER." WHERE id_member = ".$member_id);
		$hour_deb = date("G",strtotime($date_deb));
		$min_deb = date("i",strtotime($date_deb));
		$hour_fin = date("G",strtotime($date_fin));
		$min_fin = intval(date("i",strtotime($date_fin)));
		if( $min_deb  > 0 )
		{
			$min_deb = $min_deb * 100 / 60;
			$diff_deb = floatval( $hour_deb.'.'.$min_deb);
		}
		else
		$diff_deb = floatval(date("G.i",strtotime($date_deb)));

		if( $min_fin  > 0 )
		{
			$min_fin = $min_fin * 100 / 60;
			$diff_end = floatval( $hour_fin.'.'.$min_fin);
		}
		else
		$diff_end = floatval(date("G.i",strtotime($date_fin)));

		$d  = date("G:i",strtotime($date_deb));
		$f  = date("G:i",strtotime($date_fin));

		$options = RoomPlanning::getOption();
		$title = ($options['planningroom_displaymembername']) ? stripslashes($row->member_name) : '';
		return '<div rel="'.$row->id.'" title="'.__('Booked from','roomplanning').' '.$title.' '.$d.' '.__('to','roomplanning').' '.$f.'" class="roomplaning_maptime" style="background:url('.ROOMPLANNING_IMG_URL.$options['planningroom_reservedimg'].') repeat-x;" attr="'.$diff_deb.' '.$diff_end.'">'.$title.'</div>';
	}
	//Create day planning with empty booking
	function createMapTimeEmpty($arrDatas,$nb_days,$room_id,$now){
		for($i=0; $i<$nb_days; $i++)
		{
			$tomorrow = date("Y-m-d",strtotime("+".$i." day",strtotime($now)));
			$arrDatas[$room_id][$tomorrow][$i] = array();
		}
		return $arrDatas;
	}
	//Load data from database
	function loadData($day,$showdayempty,$room_id,$member_id,$view_id){
		global $wpdb;

		$day_end = '';
		$arrDatas = array();

		//Init variables
		$this->_firstDay = '';
		$this->_endDay = '';

		//Create SQL
		$sql = "SELECT * FROM ".BDD_PLANNINGROOM;
		$sqlWhere = '';
		if(is_string($room_id))
			$sqlWhere .= " AND room_id IN (".$room_id.") ";
		elseif( $room_id > 0 ) $sqlWhere .= " AND room_id = ".$room_id;
		if( $member_id > 0 ) $sqlWhere .= " AND member_id = ".$member_id;

		if( $view_id != 'd')
		{
			//Check day name for calculate period
			$day_name = date("D",strtotime($day));
			//Weekly representation
			if( $view_id == 'w')
			{
				//Last monday
				if( $day_name != 'Mon' )
					$day_deb = date( "Y-m-d" , strtotime('midnight last monday',strtotime($day)));
				else
					$day_deb = $day;
				//Next sunday
				if( $day_name != 'Sun' )
					$day_end = date("Y-m-d" , strtotime('midnight next Sunday',strtotime($day)));
				else
					$day_end = $day;
			}
			//Month representation
			elseif( $view_id == 'm')
			{
				$day_deb = date("Y-m-01",strtotime($day));
				$day_end = date("Y-m-t 23:59:59", strtotime($day));
			}
			//Year representation
			elseif( $view_id == 'y')
			{
				$day_deb = date("Y").'-01-01';
				$day_end  = date("Y").'-12-31';
			}
			$sql .= ' WHERE LEFT(date_deb,10) >= "'.$day_deb.'" AND LEFT(date_deb,10) <= "'.$day_end.'" '.$sqlWhere;
			$this->_firstDay = $day_deb;
			$this->_endDay = $day_end;
		}
		else
		{
			$sql .= " WHERE LEFT(date_deb,10) = '".$day."' ".$sqlWhere;
			$this->_firstDay = $day;
			$this->_endDay = $day;
		}
		$sql .= " ORDER BY room_id ASC, date_deb ASC";
		//Create empty roomtimes
		//Nb days
		$nb_days = 1;
		if($view_id != 'd')
		{
			list($year1, $month1, $day1) = explode('-', $this->_firstDay);
			list($year2, $month2, $day2) = explode('-', $this->_endDay);
			$timestamp1 = mktime(0,0,0,$month1,$day1,$year1);
			$timestamp2 = mktime(0,0,0,$month2,$day2,$year2);
			$nb_days = intval(abs($timestamp2-$timestamp1)/86400);
		}

		//Display list room
		if(is_string($room_id))
		{
			$arrIdRoom = split(',',$room_id);
			$count = count($arrIdRoom);
			for($i=0; $i<$count; $i++)
				$arrDatas = ShortCodePlanningRoom::createMapTimeEmpty($arrDatas,$nb_days,$arrIdRoom[$i],$day);
		}
		//Display all rooms
		elseif($room_id == 0)
		{
			$arrIdRoom = RP_utils::listIdRoom();
			$count = count($arrIdRoom);
			for($i=0; $i<$count; $i++)
				$arrDatas = ShortCodePlanningRoom::createMapTimeEmpty($arrDatas,$nb_days,$arrIdRoom[$i],$day);
		}
		//Display one room
		else
			$arrDatas = ShortCodePlanningRoom::createMapTimeEmpty($arrDatas,$nb_days,$room_id,$day);

		//And complete roomtimes with data
		$lines = $wpdb->get_results( $sql );
		if( $lines )
		{
			$i = 0;
			$old_room_id = 0;
			$old_day = 0;
			foreach($lines AS $line)
			{
				$day = date("Y-m-d",strtotime($line->date_deb));
				if( $old_room_id != $line->room_id )
				{
					$old_room_id = $line->room_id;
					$i=0;
				}
				if( $old_day != $day )
				{
					$old_day = $day;
					$i=0;
				}
				$arrDatas[$line->room_id][$day][$i] = $line;
				$i++;
			}
		}
		return $arrDatas;
	}
}
?>