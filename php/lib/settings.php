<?php
//Database structure version
global $_roomPlanning_db_version,$wpdb;
$_roomPlanning_db_version = "1.0";

//Define path
if(!defined('PATH_DIR_PLANNING_ROOM'))
	define('PATH_DIR_PLANNING_ROOM', WP_PLUGIN_DIR.'/PlanningRoom/');

if(!defined('PATH_URL_PLANNING_ROOM'))
	define('PATH_URL_PLANNING_ROOM', WP_PLUGIN_URL.'/PlanningRoom/');

if(!defined('PATH_DIR_PLANNING_ROOM_POPUP'))
	define('PATH_DIR_PLANNING_ROOM_POPUP', WP_PLUGIN_DIR.'/PlanningRoom/php/booking/');

//Path to directory image
if(!defined('ROOMPLANNING_IMG_DIR'))
	define('ROOMPLANNING_IMG_DIR', WP_PLUGIN_DIR.'/PlanningRoom/images/');

if(!defined('ROOMPLANNING_IMG_URL'))
	define('ROOMPLANNING_IMG_URL', WP_PLUGIN_URL.'/PlanningRoom/images/');

//Options name
if(!defined('ROOMPLANNING_OPTIONS'))
	define('ROOMPLANNING_OPTIONS','RoomPlanningOptions');

//Tables BDD
if(!defined('BDD_PLANNINGROOM'))
	define('BDD_PLANNINGROOM',$wpdb->prefix.'planningroom');

if(!defined('BDD_PLANNINGROOM_ROOM'))
	define('BDD_PLANNINGROOM_ROOM',$wpdb->prefix.'planningroom_room');

if(!defined('BDD_PLANNINGROOM_MEMBER'))
	define('BDD_PLANNINGROOM_MEMBER',$wpdb->prefix.'planningroom_members');

//Define day now
if(!defined('ROOMPLANNING_DAY'))
	define('ROOMPLANNING_DAY',date("Y-m-d"));
?>