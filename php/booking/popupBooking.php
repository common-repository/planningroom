<?php
/**
 * POPUP BOOKING
 */
?>
<?php
$msg = '<div id="booking_frame">
<!-- FORM BOOKING -->
<h2>'.__('Booking','roomplanning').'</h2>
<div id="booking"></div>
<form id="form_booking" name="form_booking" class="form" method="post">
	<input type="hidden" name="security" value="'.$nonce_ajax.'"/>
    <input type="hidden" name="action" value="bookingRoom"/>
    <input type="hidden" name="do_check_form_security" value=""/>
    <input type="hidden" name="booking_day" value="'.$day_booking.'"/>';
	if($forcelog)
		$msg .= '<input type="hidden" name="booking_force" value="1"/>';
	$msg .= '<table id="booking_tabs"class="form-table">';
if(RP_utils::is_admin_user())
	$msg .= '<tr valign="top"><td><label>'.__('Member Name','roomplanning').'</label></td><td>'.RP_utils::selectMember('booking_member_id',$booking_member_id,false).'</td></tr>';
else
	$msg .= '<tr valign="top"><td colspan="2"><input type="hidden" name="booking_member_id" value="'.$booking_member_id.'"/></td></tr>';
$msg .= '<tr valign="top"><td><label>'.__('Desired time','roomplanning').':</label></td><td>';
$msg .= RP_utils::selectHour('booking_houropen',null,true,false).' H:'.RP_utils::selectMinutes('booking_openminutes',null,false);
$msg .=  __('at','roomplanning');
$msg .= RP_utils::selectHour('booking_hourclose',null,true,false).' H:'.RP_utils::selectMinutes('booking_closeminutes',null,false);
$msg .= '</td></tr>';
$msg .= '<tr valign="top"><td><label>'.__('Choose your room','roomplanning').':</label></td><td>'. RP_utils::selectRoom('booking_room',$room_id,false).'</td></tr>';
$msg .= '</table>';
$msg .= '<div class="submit">';
$msg .= '<a class="submit" href="#" onclick="planningRoomBooking.submitBooking(\'form_booking\',\'booking\',\''.$day_booking.'\');return false;">'.__('Booking', 'roomplanning').'</a>';
$msg .= '</div>
	</form>
</div>
</div>';
?>