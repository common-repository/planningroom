<?php
/*
Plugin Name: Room Planning
Plugin URI: http://zicanotes.com/wordpress
Description: Manage planning
Version: 2.1.0
Author: Chaffer sébastien
Author URI: http://zicanotes.com/wordpress
License: GPLv2 or later
*/

/*
Copyright 2009  SEBASTIEN CHAFFER  (email : sebchaffer@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(version_compare(PHP_VERSION,'5.0.0','<')){
	_e('PlanningRoom plugin must run under PHP version >= 5.0.0');
	exit;
}

//Libraries
require_once(dirname(__FILE__).'/php/lib/settings.php');
require_once(dirname(__FILE__).'/php/admin/admincore.php');
require_once(dirname(__FILE__).'/php/ajax/ajaxrequest.php');
require_once(dirname(__FILE__).'/php/lib/utils.php');
require_once(dirname(__FILE__).'/php/booking/booking.php');
require_once(dirname(__FILE__).'/RoomPlanningShortcode.php');
require_once(dirname(__FILE__).'/php/lib/pagination.php');
require_once(dirname(__FILE__).'/php/tinymce/PlanningRoomTinyMce.php');

class RoomPlanning {

	/***
	* Define tables names
	**/
	var $_roomPlanning_table_name = '';
	var $_room_table_name = '';
	var $_members_table_name = '';

	/***
	* Variable globale
	**/

	var $_hourClose = '';
	//Url plugin for ajax request
	var $_url_plugin = '';
	//Navigation results
	var $_nextDay = '';
	var $_beforeDay = '';
	//Period result
	var $_endDay = '';
	var $_firstDay = '';

	//Classes
	var $_AdminCore = null;
	var $_PlanningRoomBooking = null;
	var $_AjaxRequest = null;
	var $_Utils = null;
	var $_Shortcode = null;
	var $_TinyMCE = null;

	function RoomPlanning(){
		//Implementation librairies
		$this->_AdminCore = new AdminCore();
		$this->_PlanningRoomBooking = new Booking();
		$this->_AjaxRequest = new AjaxRequest();
		$this->_Utils = new RP_utils();
		$this->_Shortcode = new ShortCodePlanningRoom();
		$this->_TinyMCE = new PlanningRoomTinyMce();

		//ACTIONS
		//Load translation
		add_action('init', array( $this, 'roomplanning_load_plugin_textdomain'));
		add_action('wp_head', array($this, 'addHeaderCode'), 1);

		//js
		add_action('admin_init', array($this,'addHeaderCode'));
		//init panel admin
		add_action('admin_menu', array( $this, 'RoomPlanningActivateAdminPanel'));
		//Add links on extension page
		add_filter("plugin_action_links_".plugin_basename(__FILE__), array( $this, 'roomPlanningSettingsLink'));

		//Refresh data
		add_action( 'wp_ajax_nopriv_loadData',  array(&$this->_AjaxRequest, 'ajax_loadData'));
		add_action( 'wp_ajax_loadData',  array(&$this->_AjaxRequest, 'ajax_loadData'));
		//Booking (display popup)
		add_action( 'wp_ajax_nopriv_booking',  array(&$this->_AjaxRequest, 'ajax_booking'));
		add_action( 'wp_ajax_booking',  array(&$this->_AjaxRequest, 'ajax_booking'));
		//Booking (registration booking)
		add_action( 'wp_ajax_nopriv_bookingRoom',  array(&$this->_AjaxRequest, 'ajax_bookingRoom'));
		add_action( 'wp_ajax_bookingRoom',  array(&$this->_AjaxRequest, 'ajax_bookingRoom'));
		//Booking (register member)
		add_action( 'wp_ajax_nopriv_bookingRegisterMember',  array(&$this->_AjaxRequest, 'ajax_bookingRegisterMember'));
		add_action( 'wp_ajax_bookingRegisterMember',  array(&$this->_AjaxRequest, 'ajax_bookingRegisterMember'));
		//Booking (login member)
		add_action( 'wp_ajax_nopriv_bookingLogin',  array(&$this->_AjaxRequest, 'ajax_bookingLogin'));
		add_action( 'wp_ajax_bookingLogin',  array(&$this->_AjaxRequest, 'ajax_bookingLogin'));
		//Booking (forget password)
		add_action( 'wp_ajax_nopriv_bookingForgetPassword',  array(&$this->_AjaxRequest, 'ajax_bookingForgetPassword'));
		add_action( 'wp_ajax_bookingForgetPassword',  array(&$this->_AjaxRequest, 'ajax_bookingForgetPassword'));

		//ShortCodes for displaying planning
		add_shortcode('show_planning_room',array(&$this->_Shortcode,'show_planning_room'));

		//Ajax Administration pages
		add_action( 'wp_ajax_searchMember',  array(&$this->_AjaxRequest, 'ajax_searchMember'));
		add_action( 'wp_ajax_addEvent',  array(&$this->_AjaxRequest, 'ajax_addEvent'));
		add_action( 'wp_ajax_addMember',  array(&$this->_AjaxRequest, 'ajax_addMember'));
		add_action( 'wp_ajax_addRoom',  array(&$this->_AjaxRequest, 'ajax_addRoom'));
		add_action( 'wp_ajax_updateConfig',  array(&$this->_AjaxRequest, 'ajax_updateConfig'));
		add_action( 'wp_ajax_updateMember',  array(&$this->_AjaxRequest, 'ajax_updateMember'));
		add_action( 'wp_ajax_updateRoom',  array(&$this->_AjaxRequest, 'ajax_updateRoom'));
		add_action( 'wp_ajax_removeMember',  array(&$this->_AjaxRequest, 'ajax_removeMember'));
		add_action( 'wp_ajax_removeRoom',  array(&$this->_AjaxRequest, 'ajax_removeRoom'));
		add_action( 'wp_ajax_freeTime',  array(&$this->_AjaxRequest, 'ajax_freeTime'));
		add_action( 'wp_ajax_pagination',  array(&$this->_AjaxRequest, 'ajax_pagination'));
		add_action( 'wp_ajax_refreshSel',  array(&$this->_AjaxRequest, 'ajax_refreshSel'));
		add_action( 'wp_ajax_removeGroup',  array(&$this->_AjaxRequest, 'ajax_removeGroup'));

		//Add button to tinymce
		add_action('init', array(&$this->_TinyMCE,'add_tinymce_button'));

		//HOOK Create options
		register_activation_hook(__FILE__,array($this,'options_install'));
		//HOOK Create database
		register_activation_hook(__FILE__,array($this,'bdd_install'));
		//HOOK uninstall plugin
		register_deactivation_hook(__FILE__,array($this,'plugin_uninstall'));

	}
	//Add link to plugin page
	function roomPlanningSettingsLink($links){
		$settings_link = '<a href="'.admin_url().'?page=roomplanning">'.__('Settings','roomplanning').'</a> | ';
		$settings_link .= '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5GGFJ8VCS6NC6&lc=FR&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donate_SM%2egif%3aNonHosted">'.__('Donate','roomplanning').'</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
	// Set default options
	function options_install(){
		RoomPlanning::getPlanningRoomOptions();
	}
	//Register JS
	function addHeaderCode(){
		$options = RoomPlanning::getOption();

		//Classe javascript core
		wp_register_script( 'roomplanning_script', plugin_dir_url( __FILE__ ).'js/room_planning.js',array('prototype','scriptaculous'));
		//Style core
		wp_register_style( 'roomplanning_style', plugin_dir_url( __FILE__ )  . 'css/style.css');

		//Admin page
		if(RP_utils::is_admin_page())
		{
			wp_register_script( 'roomplanning_script_admin', plugin_dir_url( __FILE__ ) . 'js/room_planning_admin.js',array('prototype'));
			wp_register_style( 'roomplanning_style_admin', plugin_dir_url( __FILE__ )  . 'css/style_admin.css');
		}

		//Add script and css to booking
		if($options['planningroom_booking'])
		{
			wp_register_script( 'roomplanning_booking', plugin_dir_url( __FILE__ ) . 'js/room_planning_booking.js',array('prototype'));
			wp_register_style( 'roomplanning_booking_style', plugin_dir_url( __FILE__ )  .'css/booking.css');
		}
		//Date picker
		wp_register_script( 'roomplanning_script_date', plugin_dir_url( __FILE__ ) . 'js/datepicker.js', array('prototype','scriptaculous'));
		wp_register_style( 'roomplanning_style_date', plugin_dir_url( __FILE__ )  . 'css/datepicker.css');

		//Display javascript
		RoomPlanning::showJavascript();
	}
	//Show JS
	function showJavascript(){
		$options = RoomPlanning::getOption();
		$houropen = $options['planningroom_openhour'];
		//30 = 0.5
		$minutesopen = $options['planningroom_openminutes'] * 100 / 60;
		$hourclose = $options['planningroom_closehour'];
		$minutesclose = $options['planningroom_closeminutes'] * 100 / 60;
		$houropen = floatval($houropen . '.'.$minutesopen);
		$hourclose = floatval($hourclose . '.'.$minutesclose);

		wp_enqueue_script('roomplanning_script');

		if(RP_utils::is_admin_page())
		{
			wp_enqueue_script( 'roomplanning_script_admin');
			wp_localize_script( 'roomplanning_script_admin', 'RoomParams', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxnonce' => wp_create_nonce( 'ajax_security' ),
				'hourOpen' => $houropen,
				'hourClose' => $hourclose,
				'weekopen' => $options['planningroom_weekopen'],
				'wait' => admin_url().'images/wpspin_light.gif',
				'waiting' =>"&nbsp;".__('Loading...','roomplanning'),
				'modifyRoom' => __('Update room','roomplanning'),
				'modifyMember' => __('Update member','roomplanning'),
				'cancel' =>__('Cancel','roomplanning'),
				'edit' =>__('Edit','roomplanning'),
				'blogurl' => home_url(),
				'language' => RP_utils::getIsoLanguage()
			));
		}
		else
		{
			wp_localize_script( 'roomplanning_script', 'RoomParams', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'ajaxnonce' => wp_create_nonce( 'ajax_security' ),
				'hourOpen' => $houropen,
				'hourClose' => $hourclose,
				'weekopen' => $options['planningroom_weekopen'],
				'wait' => admin_url().'images/wpspin_light.gif',
				'waiting' =>__('Loading...','roomplanning'),
				'blogurl' => home_url(),
				'language' => RP_utils::getIsoLanguage()
			));
		}
		if($options['planningroom_booking'])
		{
			wp_enqueue_script('roomplanning_booking');
			wp_enqueue_style( 'roomplanning_booking' );
		}
		unset( $options );
		wp_enqueue_script('roomplanning_script_date');
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('roomplanning_style');
		wp_enqueue_style('roomplanning_booking_style');
		wp_enqueue_style('roomplanning_style_date');
		if(RP_utils::is_admin_page())
		{
			wp_enqueue_style('roomplanning_style_admin');
		}
	}
	//Initialize the admin panel
	function RoomPlanningActivateAdminPanel(){
		if (function_exists('add_menu_page'))
		{
			$page =  add_menu_page(__('RoomPlanning','roomplanning'), __('RoomPlanning','roomplanning'), 'manage_options', 'roomplanning',  array(&$this->_AdminCore, 'administrationAdminPage'));
			add_action('admin_print_styles-' . $page, array( $this, 'showJavascript'));
			$page = add_submenu_page('roomplanning', __('Configuration tools','roomplanning'), __('Configuration','roomplanning'), 'manage_options', 'roomplanning', array(&$this->_AdminCore, 'administrationAdminPage'));
			add_action('admin_print_styles-' . $page, array( $this, 'showJavascript'));
			$page = add_submenu_page('roomplanning', __('Add Events','roomplanning'), __('Add','roomplanning'), 'manage_options', 'roomplanning_edit', array(&$this->_AdminCore, 'editAdminPage'));
			add_action('admin_print_styles-' . $page, array( $this, 'showJavascript'));
			$page = add_submenu_page('roomplanning', __('Listing Events','roomplanning'), __('List','roomplanning'), 'manage_options', 'roomplanning_list', array(&$this->_AdminCore, 'listAdminPage'));
			add_action('admin_print_styles-' . $page, array( $this, 'showJavascript'));
			$page = add_submenu_page('roomplanning', __('How to','roomplanning'), __('How to','roomplanning'), 'manage_options', 'roomplanning_help', array(&$this->_AdminCore, 'helpAdminPage'));
		}
	}
	//Load textdomain
	function roomplanning_load_plugin_textdomain(){
		load_plugin_textdomain('roomplanning',false,dirname(plugin_basename( __FILE__ )).'/translations');
	}
	// Create Database on install
	function bdd_install(){
		global $wpdb, $_roomPlanning_db_version;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
		$installed_ver = get_option("roomplanning_db_version", "1.0" );

		//Updated database
		if( $installed_ver != $_roomPlanning_db_version )
		{
			//For next update
			//update_option( "roomplanning_db_version", $_roomPlanning_db_version );
		}
		elseif($wpdb->get_var("show tables like {BDD_PLANNINGROOM}") != BDD_PLANNINGROOM)
		{

			$sql = "CREATE TABLE  `" . BDD_PLANNINGROOM . "` (
			`id` MEDIUMINT NOT NULL AUTO_INCREMENT ,
			`room_id` INT NOT NULL ,
			`member_id` INT NOT NULL ,
			`date_deb` DATETIME NOT NULL ,
			`date_fin` DATETIME NOT NULL ,
			PRIMARY KEY (`id`) ,
			INDEX (`room_id`,`member_id`)
			)";
			dbDelta($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `".BDD_PLANNINGROOM_ROOM."` (
			  `id_room` smallint(6) NOT NULL AUTO_INCREMENT,
			  `room_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `description` varchar(4000) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id_room`),
			  UNIQUE KEY `room_name` (`room_name`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			dbDelta($sql);

			$sql = "CREATE TABLE IF NOT EXISTS `".BDD_PLANNINGROOM_MEMBER."` (
			  `id_member` mediumint(9) NOT NULL AUTO_INCREMENT,
			  `member_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			  `member_email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
			  `member_passwd` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
			  `desc` varchar(4000) COLLATE utf8_unicode_ci NOT NULL,
			  PRIMARY KEY (`id_member`),
			  UNIQUE KEY `member_name` (`member_name`)
			) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
			dbDelta($sql);

			add_option("roomplanning_db_version", $_roomPlanning_db_version);
		}
	}
	//uninstall plugin
	function plugin_uninstall() {
		global $wpdb;
		delete_option('roomplanning_db_version');
		delete_option(ROOMPLANNING_OPTIONS);
		$wpdb->query("DROP TABLE IF EXISTS ".BDD_PLANNINGROOM);
		$wpdb->query("DROP TABLE IF EXISTS ".BDD_PLANNINGROOM_ROOM);
		$wpdb->query("DROP TABLE IF EXISTS ".BDD_PLANNINGROOM_MEMBER);
	}
	//Planning room options
	function getPlanningRoomOptions(){
		$options = array(
		'planningroom_title' => 'Planning',
		'planningroom_openhour' => 8,
		'planningroom_openminutes' => 0,
		'planningroom_closehour' => 24,
		'planningroom_closeminutes' => 0,
		'planningroom_wp_registration' => 0,
		'planningroom_day_booking' => 0,
		'planningroom_booking' => false,
		'planningroom_nbresult_admin' => 10,
		'planningroom_displaymembername' => 0,
		'planningroom_backimg' => 'bg2.png',
		'planningroom_reservedimg' => 'bg0.png'
		);
		$roomplanning_options =  RoomPlanning::getOption();
		if(!empty($roomplanning_options))
		{
			foreach($roomplanning_options AS $key => $value)
				$options[$key] = $value;
		}
		RoomPlanning::update_option($options);
		return $options;
	}
	//Return options
	function getOption(){
		return get_option(ROOMPLANNING_OPTIONS);
	}
	//Update options
	function update_option($options){
		update_option(ROOMPLANNING_OPTIONS,$options);
	}
}

global $dl_roomPlanning;
if (class_exists("RoomPlanning") && !isset($dl_roomPlanning)){
	$dl_roomPlanning = new RoomPlanning();
}
?>