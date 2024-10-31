<?php
/**
* ADMINISTRATION PAGE
*
* Page configuration options global
*/
?>
<div class="wrap" id="pageOption">
<a name="ancre_panel_options"></a>
<div id="icon-options-general" class="icon32"><br/></div>
<h2><?php _e('General Settings','roomplanning') ?></h2>
<!-- paypal form -->
<form style="padding-left:5px;margin-top:10px;" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/FR/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
<img alt="" border="0" src="https://www.paypalobjects.com/WEBSCR-640-20110429-1/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>

<form id="form_option" name="form_option" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?page=roomplanning&_wpnonce=<?php echo $nonce ?>#ancre_panel_options">
  <div id="ajax_response"></div>
	<?php wp_nonce_field('form_RoomPlanningSettings','name_nonce_field'); ?>
    <input type="hidden" name="do_check_form_security" value=""/>
    <input type="hidden" name="action" value="updateConfig"/>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><label for="title"><?php _e('Title of planning','roomplanning'); ?></label></th>
        <td><input name="title" type="text" id="title" value="<?php echo $options['planningroom_title']; ?>" class="regular-text" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><label for="houropen"><?php _e('Hour open','roomplanning'); ?></label></th>
        <td><?php RP_utils::selectHour('houropen',$options['planningroom_openhour'],false); ?>H:<?php RP_utils::selectMinutes('openminutes',$options['planningroom_openminutes']); ?></td>
        </tr>
        <tr valign="top">
        <th scope="row"><label for="hourclose"><?php _e('Hour close','roomplanning'); ?></label></th>
        <td><?php RP_utils::selectHour('hourclose',$options['planningroom_closehour'],false); ?>H:<?php RP_utils::selectMinutes('closeminutes',$options['planningroom_closeminutes']); ?></td>
			</tr>
			<tr valign="top">
	        <th scope="row"><label for="week_openclose"><?php _e('Open on week-end','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectYesNo('week_openclose',$options['planningroom_weekopen']); ?></td>
			</tr>
			<tr valign="top">
	        <th scope="row"><label for="displaymembername"><?php _e('Display member name ?','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectYesNo('displaymembername',$options['planningroom_displaymembername']); ?></td>
			</tr>
			<tr valign="top">
	        <th scope="row"><label for="nbline_result"><?php _e('Number of results by page ?','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectNumber('nbline_result',$options['planningroom_nbresult_admin']); ?></td>
			</tr>
			<tr valign="top">
	        <th scope="row"><label for="booking_yesno"><?php _e('Use booking room functionality ?','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectYesNo('booking_yesno',$options['planningroom_booking']); ?></td>
			</tr>
			<tr valign="top" id="trOptionDayBooking">
	        <th scope="row"><label for="day_booking"><?php _e('Date limit for booking room ?','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectDayBooking('day_booking',$options['planningroom_day_booking']); ?> <?php _e('days','roomplanning'); ?></td>
			</tr>
			<tr valign="top" id="trWpRegistration">
	        <th scope="row"><label for="wp_registration"><?php _e('Use Wordpress registration for new member ?','roomplanning'); ?></label></th>
	        <td><?php RP_utils::selectYesNo('wp_registration',$options['planningroom_wp_registration']); ?></td>
			</tr>
			<tr valign="top">
	        <th scope="row"><label for="background"><?php _e('Choose background image','roomplanning'); ?></label></th>
	        <td><?php RP_utils::listBackground('back',$options['planningroom_backimg']);?></td>
			</tr>
			<tr valign="top">
	        <th scope="row"><label for="background"><?php _e('Choose booking image','roomplanning'); ?></label></th>
	        <td><?php RP_utils::listBackground('reserved',$options['planningroom_reservedimg']);?></td>
			</tr>
    </table>
    <p class="submit inline-edit-save">
			<span style="display:none;" class="loading alignleft" id="waiting_options"><img alt="" src="<?php echo admin_url();?>images/wpspin_light.gif"><?php _e('Loading','roomplanning');?>...</span>
			<a accesskey="s" href="#" class="button-primary save alignleft" onclick="protoAdminOptions.updateForm();return false;"><?php _e('Update Settings', 'roomplanning');?></a>
			<br class="clear">
		</p>
</form>
