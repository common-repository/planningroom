<?php
/**
* CLASS BOOKING
*
*/
class Booking {

	function Booking(){}
	//Determine if room is open and free for time user
	function isFree($date_deb,$date_fin,$sel_room_id){
		global $wpdb;
		//Forbidden
		if( $date_deb >= $date_fin ) return false;

		//Check options openning time
		list($date_deb_option,$date_fin_option ) = RP_utils::getTimesOptions();
		if( date("H:i",strtotime($date_deb)) < date("H:i",strtotime($date_deb_option))) return false;
		if( date("H:i",strtotime($date_deb)) > date("H:i",strtotime($date_fin_option))) return false;
		if( date("H:i",strtotime($date_fin)) > date("H:i",strtotime($date_fin_option))) return false;
		if( date("H:i",strtotime($date_fin)) < date("H:i",strtotime($date_deb_option))) return false;

		//Check all time reserved
		$lines = $wpdb->get_results( "SELECT * FROM ".BDD_PLANNINGROOM." WHERE room_id = ".$sel_room_id." AND LEFT(date_deb,10) = LEFT('".$date_deb."',10) ORDER BY date_deb ASC");
		if( $lines )
		{
			foreach( $lines AS $date )
			{
				$date_deb_mysql = $date->date_deb;
				$date_fin_mysql = $date->date_fin;
				if(
				( $date_deb > $date_deb_mysql && $date_deb < $date_fin_mysql ) ||
				( $date_fin > $date_deb_mysql && $date_fin < $date_fin_mysql ) ||
				( $date_deb_mysql > $date_deb && $date_deb_mysql < $date_fin ) ||
				( $date_fin_mysql > $date_deb && $date_fin_mysql < $date_fin ) ||
				( $date_deb == $date_deb_mysql && $date_fin == $date_fin_mysql )
				)
				return false;
			}
		}
		return true; //free
	}
	//Determine if user use wordpress registration or not
	function isWPr(){
		$options = RoomPlanning::getOption();
		return ($options['planningroom_wp_registration'] == 0) ? false : true;
	}
	//Set booking login cookie
	function setCookieMember($user_id){
		if( isset($_COOKIE["roomplanning_log"]))
		{
			setcookie("roomplanning_log", false, time() - 3600);
			unset($_COOKIE["roomplanning_log"]);
		}
		$url = parse_url(get_bloginfo('url'));
		setcookie("roomplanning_log", $user_id, time()+3600, $url['path'], $url['host'] );
	}
	//Determine if user is logged
	function is_user_logged_in(){
		if( Booking::isWPr())
			return is_user_logged_in();
		else
			return (isset($_COOKIE["roomplanning_log"])) ? true : false;
	}
}
?>