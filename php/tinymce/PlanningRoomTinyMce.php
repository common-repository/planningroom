<?php

/**
 * CLASS PlanningRoom Tiny Mce
 * Add button to Tiny Mce
 */
class PlanningRoomTinyMce{

	function PlanningRoomTinyMce(){}

	// Add button hooks to the Tiny MCE
	function add_tinymce_button() {
		global $mc_version;
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
			return;
		}
		if ( get_user_option('rich_editing') == 'true') {
			add_filter( 'tiny_mce_version', array(&$this,'mc_tiny_mce_version'), 0 );
			add_filter( 'mce_external_plugins', array(&$this,'mc_plugin'), 0 );
			add_filter( 'mce_buttons', array(&$this,'mc_button'), 0 );
		}
		// Register Hooks
		if (is_admin()) {
			// Add Quicktag
			add_action( 'edit_form_advanced', array(&$this,'mc_add_quicktags'));
			add_action( 'edit_page_form', array(&$this,'mc_add_quicktags'));

			// Queue Embed JS
			add_action( 'admin_head', array(&$this,'mc_admin_js_vars'));
			wp_enqueue_script( 'mprqt', plugins_url('/PlanningRoom/php/tinymce/tinymce_core.js'), array(), $mc_version );
		}
	}
	// Break the browser cache of TinyMCE
	function mc_tiny_mce_version( ) {
		global $mc_version;
		return 'mcb-' . $mc_version;
	}
	// Load the custom TinyMCE plugin
	function mc_plugin( $plugins ) {
		global $wp_plugin_url;
		$plugins['mprqt'] = $wp_plugin_url . '/PlanningRoom/php/tinymce/editor_plugin.js';
		return $plugins;
	}
	// Add the buttons: separator, custom
	function mc_button( $buttons ) {
		array_push( $buttons, 'separator', 'myPlanningRoom' );
		return $buttons;
	}
	// Add a button to the quicktag view (HTML Mode) >>>
	function mc_add_quicktags(){
	?>
	<script type="text/javascript" charset="utf-8">
	// <![CDATA[
	(function(){
		if (typeof jQuery === 'undefined') {
			return;
		}
		jQuery(document).ready(function(){
			// Add the buttons to the HTML view
			jQuery("#ed_toolbar").append('<input type="button" class="ed_button" onclick="myPlanningRoomQT.Tag.embed.apply(myPlanningRoomQT.Tag); return false;" title="Insert PlanningRoom" value="PlanningRoom" />');
		});
	}());
	// ]]>
	</script>
	<?php
	}
	function mc_admin_js_vars(){
		global $wp_plugin_url;
	?>
	<script type="text/javascript" charset="utf-8">
	// <![CDATA[
		if (typeof myPlanningRoomQT !== 'undefined' && typeof myPlanningRoomQT.Tag !== 'undefined') {
			myPlanningRoomQT.Tag.configUrl = "<?php echo WP_PLUGIN_URL . '/PlanningRoom/php/tinymce/generator.php'; ?>";
		}
	// ]]>
	</script>
	<?php
	}
}
?>