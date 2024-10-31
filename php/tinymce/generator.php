<?php
// Load WordPress core files
$iswin = preg_match('/:\\\/', dirname(__file__));
$slash = ($iswin) ? "\\" : "/";
$wp_path = preg_split('/(?=((\\\|\/)wp-content)).*/', dirname(__file__));
$wp_path = (isset($wp_path[0]) && $wp_path[0] != "") ? $wp_path[0] : $_SERVER["DOCUMENT_ROOT"];
require_once($wp_path . $slash . 'wp-load.php');
require_once($wp_path . $slash . 'wp-admin' . $slash . 'admin.php');

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') )
	wp_die(__( "You don't have access to this function.", 'roomplanning' ));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
<title><?php bloginfo('name') ?> &rsaquo; <?php _e("PlanningRoom Shortcode Generator",'roomplanning'); ?> &#8212; WordPress</title>
<?php
// WordPress styles
wp_admin_css( 'css/global' );
wp_admin_css();
wp_admin_css( 'css/colors' );
wp_admin_css( 'css/ie' );
$hook_suffix = '';
if ( isset($page_hook) )
	$hook_suffix = "$page_hook";
else if ( isset($plugin_page) )
	$hook_suffix = "$plugin_page";
else if ( isset($pagenow) )
	$hook_suffix = "$pagenow";
do_action("admin_print_styles-$hook_suffix");
do_action('admin_print_styles');
do_action("admin_print_scripts-$hook_suffix");
do_action('admin_print_scripts');
do_action("admin_head-$hook_suffix");
do_action('admin_head');

global $mc_version;
$version = 'mcb-' . $mc_version;
?>
<link rel="stylesheet" href="<?php echo plugins_url('/PlanningRoom/php/tinymce/generator.css'); ?>?ver=<?php echo $version; ?>" type="text/css" media="screen" charset="utf-8" />
<script src="<?php echo plugins_url('/PlanningRoom/php/tinymce/tinymce_core.js'); ?>" type="text/javascript" charset="utf-8"></script>
</head>
<body class="<?php echo apply_filters( 'admin_body_class', '' ); ?>">
	<div id="planningroom_generator" class="wrap">
		<h2 class="center"><?php _e("PlanningRoom Shortcode Generator",'roomplanning'); ?></h2>
		<form action="#" mode="POST">
			<p class="center"><?php _e('Shortcode Attributes', 'roomplanning'); ?></legend>
			<p><label><?php _e('Choose room','roomplanning'); ?></label><span name="list_room" id="list_room"><?php echo RP_utils::checkBoxRoom('room_id');?></span></p>
			<p><label><?php _e('Choose view','roomplanning'); ?></label><?php RP_utils::selectView('view_id');?></p>
			<p><label><?php _e('Display navigation','roomplanning'); ?></label><?php RP_utils::selectYesNo('shownavigation');?></p>
			<p><label><?php _e('Display filters','roomplanning'); ?></label><?php RP_utils::selectYesNo('showfilter');?></p>
			<p class="center"><input type="button" class="button" id="planningroom_submit" name="generate" value="<?php _e('Generate Shortcode', 'roomplanning'); ?>"/></p>
		</form>
	</div>
	<script type="text/javascript" charset="utf-8">
		// <![CDATA[
		jQuery(document).ready(function(){
			try {
				myPlanningRoomQT.Tag.Generator.initialize();
			} catch (e) {
				throw "<?php _e("PlanningRoom: this generator isn't going to put the shortcode in your page. Sorry!", 'roomplanning'); ?>";
			}
		});
		// ]]>
	</script>
</body>
</html>