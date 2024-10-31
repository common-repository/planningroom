<?php
/**
 * POPUP LOGIN
 */
?>
<div id="booking_frame">
<!-- FORM LOGIN MEMBER -->
<fieldset id="booking_login">
	<legend><?php _e('Login','roomplanning');?></legend>
	<div id="rep_login"></div>
	<div id="booking_login_tabs" class="booking_tabs">
		<form id="form_login" name="form_login" class="form" method="post">
		   <input type="hidden" id="security" name="security" value="<?php echo $nonce_ajax;?>"/>
		   <input type="hidden" name="action" value="bookingLogin"/>
		   <input type="hidden" id="day_booking" name="day_booking" value="<?php echo $day_booking;?>"/>
		   <input type="hidden" id="room_id" name="room_id" value="<?php echo $room_id;?>"/>
		   <input type="hidden" name="do_check_form_security" value=""/>
		   <table class="form-table">
		    <tr valign="top">
		    <td><label for="booking_login_name"><?php _e('Name','roomplanning'); ?></label></td>
		    <td><input name="booking_login_name" type="text" id="booking_login_name" value="" class="regular-text"/></td>
		    </tr>
		    <tr valign="top">
		    <td><label for="booking_login_passwd"><?php _e('Password','roomplanning'); ?></label></td>
		    <td><input name="booking_login_passwd" type="password" id="booking_login_passwd" value="" class="regular-text" /></td>
		    </tr>
		   </table>
		   <div class="submit">
		   <a class="submit" href="#" onclick="submit('form_login','rep_login');return false;"><?php _e('Send', 'roomplanning') ?></a>
		   </div>
		</form>
		<br/><br/>
		<a href="#" onclick="openclose();return false;"><?php _e('Forget password','roomplanning');?></a>
		<div id="booking_forget_tabs" class="booking_tabs" style="display:none;">
			<div id="rep_passw"></div>
			<form id="form_passw" name="form_passw" class="form" method=>
			   <input type="hidden" id="security" name="security" value="<?php echo $nonce_ajax;?>"/>
			   <input type="hidden" name="action" value="bookingForgetPassword"/>
			   <input type="hidden" name="do_check_form_security" value=""/>
			   <table class="form-table">
			   		<tr valign="top">
						<td><label for="booking_forget_name"><?php _e('Name','roomplanning'); ?></label></td>
						<td><input name="booking_forget_name" type="text" id="booking_forget_name" value="" class="regular-text" /></td>
						</tr>
			      <tr valign="top">
						<td><label for="booking_forget_email"><?php _e('Mail','roomplanning'); ?></label></td>
						<td><input name="booking_forget_email" type="text" id="booking_forget_email" value="" class="regular-text" /></td>
						</tr>
			   </table>
			   <div class="submit">
			   <a class="submit" href="#" onclick="submit('form_passw','rep_passw');return false;"><?php _e('Get your password', 'roomplanning') ?></a>
			   </div>
			</form>
		</div>
	</div>
</fieldset>

<!-- FORM REGISTER MEMBER -->
<fieldset id="booking_register_member">
	<legend><?php _e('Registration','roomplanning');?></legend>
	<div id="rep_member"></div>
	<div id="booking_register_tabs" class="booking_tabs">
		<form onsubmit="return false;" id="form_register" name="form_register" method="post"">
		   <input type="hidden" id="security" name="security" value="<?php echo $nonce_ajax;?>"/>
		   <input type="hidden" name="do_check_form_security" value=""/>
		   <input type="hidden" name="action" value="bookingRegisterMember"/>
		   <input type="hidden" id="day_booking" name="day_booking" value="<?php echo $day_booking;?>"/>
		   <input type="hidden" id="room_id" name="room_id" value="<?php echo $room_id;?>"/>
		   <table class="form-table">
		   	<tr valign="top">
		   	<td><label for="booking_register_member_name"><?php _e('Member Name','roomplanning'); ?></label></td>
		   	<td><input name="booking_register_member_name" type="text" id="booking_register_member_name" value="" class="regular-text" /></td>
		   	</tr>
		   	<tr valign="top">
			<td><label for="booking_register_member_email"><?php _e('Mail','roomplanning'); ?></label></td>
			<td><input name="booking_register_member_email" type="text" id="booking_register_member_email" value="" class="regular-text" /></td>
			</tr>
		   </table>
		   <div class="submit">
		   <a class="submit" href="#" onclick="submit('form_register','rep_member');return false;"><?php _e('Registration', 'roomplanning') ?></a>
		   </div>
		</form>
	</div>
</fieldset>
</div>
<script type="text/javascript">
function openclose(id){
	if( $('booking_forget_tabs').getStyle('display') == "none" )
		$('booking_forget_tabs').setStyle({display:"block"});
	else
		$('booking_forget_tabs').setStyle({display:"none"});
}
function submit(idForm,idRep){
	var options = $(idForm).serialize(true);
	$(idRep).innerHTML = '<img src="'+RoomParams.wait+'" alt=""/><?php _e('Loading','roomplanning')?>';
	new Ajax.Request('<?php echo admin_url( 'admin-ajax.php' );?>', {
		method: 'post',
		parameters: options,
		onSuccess: function(response) {
			var rep = eval('(' + response.responseText + ')');
			if( rep.error == 1 )
				$(idRep).innerHTML = rep.html;
			if( rep.error == 2 )
			{
				$(idRep).remove();
				$('booking_frame').innerHTML = rep.html;
			}
		}
	});
	return false;
}
</script>