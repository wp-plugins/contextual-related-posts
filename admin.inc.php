<?php
/**********************************************************************
*					Admin Page										*
*********************************************************************/
// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
// Guess the location
$crp_path = WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__));
$crp_url = WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__));


function crp_options() {
	
	global $wpdb;
    $poststable = $wpdb->posts;

	$crp_settings = crp_read_options();

	if($_POST['crp_save']){
		$crp_settings[title] = ($_POST['title']);
		$crp_settings[limit] = ($_POST['limit']);
		$crp_settings[exclude_cat_slugs] = ($_POST['exclude_cat_slugs']);
		$crp_settings[add_to_content] = (($_POST['add_to_content']) ? true : false);
		$crp_settings[add_to_page] = (($_POST['add_to_page']) ? true : false);
		$crp_settings[add_to_feed] = (($_POST['add_to_feed']) ? true : false);
		$crp_settings[match_content] = (($_POST['match_content']) ? true : false);
		$crp_settings[exclude_pages] = (($_POST['exclude_pages']) ? true : false);
		$crp_settings[blank_output] = (($_POST['blank_output'] == 'blank' ) ? true : false);
		
		
		$exclude_categories_slugs = explode(", ",$crp_settings[exclude_cat_slugs]);
		
		$exclude_categories = '';
		foreach ($exclude_categories_slugs as $exclude_categories_slug) {
			$catObj = get_category_by_slug($exclude_categories_slug);
			$exclude_categories .= $catObj->term_id . ',';
		}
		$crp_settings[exclude_categories] = substr($exclude_categories, 0, -2);

		update_option('ald_crp_settings', $crp_settings);
		
		$str = '<div id="message" class="updated fade"><p>'. __('Options saved successfully.','ald_crp_plugin') .'</p></div>';
		echo $str;
	}
	
	if ($_POST['crp_default']){
		delete_option('ald_crp_settings');
		$crp_settings = crp_default_options();
		update_option('ald_crp_settings', $crp_settings);
		
		$str = '<div id="message" class="updated fade"><p>'. __('Options set to Default.','ald_crp_plugin') .'</p></div>';
		echo $str;
	}
?>

<div class="wrap">
  <h2>Contextual Related Posts </h2>
  <div style="border: #ccc 1px solid; padding: 10px">
    <fieldset class="options">
    <legend>
    <h3>
      <?php _e('Support the Development','ald_crp_plugin'); ?>
    </h3>
    </legend>
    <p>
      <?php _e('If you find ','ald_crp_plugin'); ?>
      <a href="http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/">Contextual Related Posts</a>
      <?php _e('useful, please do','ald_crp_plugin'); ?>
      <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&amp;business=donate@ajaydsouza.com&amp;item_name=Related%20Posts%20(From%20WP-Admin)&amp;no_shipping=1&amp;return=http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/&amp;cancel_return=http://ajaydsouza.com/wordpress/plugins/contextual-related-posts/&amp;cn=Note%20to%20Author&amp;tax=0&amp;currency_code=USD&amp;bn=PP-DonationsBF&amp;charset=UTF-8" title="Donate via PayPal"><?php _e('drop in your contribution','ald_crp_plugin'); ?></a>.
	  (<a href="http://ajaydsouza.com/donate/"><?php _e('Some reasons why you should.','ald_crp_plugin'); ?></a>)</p>
    </fieldset>
  </div>
  <form method="post" id="crp_options" name="crp_options" style="border: #ccc 1px solid; padding: 10px" onsubmit="return checkForm()">
    <fieldset class="options">
    <legend>
    <h3>
      <?php _e('Options:','ald_crp_plugin'); ?>
    </h3>
    </legend>
    <p>
      <label>
      <?php _e('Number of related posts to display: ','ald_crp_plugin'); ?>
      <input type="textbox" name="limit" id="limit" value="<?php echo stripslashes($crp_settings[limit]); ?>">
      </label>
    </p>
    <p>
      <label>
      <?php _e('Title of related posts: ','ald_crp_plugin'); ?>
      <input type="textbox" name="title" id="title" value="<?php echo stripslashes($crp_settings[title]); ?>">
      </label>
    </p>
    <p><?php _e('Exclude Categories: ','ald_crp_plugin'); ?></p>
	<div style="position:relative;text-align:left">
		<table id="MYCUSTOMFLOATER" class="myCustomFloater" style="position:absolute;top:50px;left:0;background-color:#cecece;display:none;visibility:hidden">
		<tr><td><!--
				please see: http://chrisholland.blogspot.com/2004/09/geekstuff-css-display-inline-block.html
				to explain why i'm using a table here.
				You could replace the table/tr/td with a DIV, but you'd have to specify it's width and height
				-->
			<div class="myCustomFloaterContent">
			you should never be seeing this
			</div>
		</td></tr>
		</table>
		<textarea class="wickEnabled:MYCUSTOMFLOATER" cols="50" rows="3" wrap="virtual" name="exclude_cat_slugs"><?php echo stripslashes($crp_settings[exclude_cat_slugs]); ?></textarea>
	</div>
	<p><?php _e('When there are no posts, what should be shown?','ald_crp_plugin'); ?><br />
		<label>
		<input type="radio" name="blank_output" value="blank" id="blank_output_0" <?php if ($crp_settings['blank_output']) echo 'checked="checked"' ?> />
		<?php _e('Blank Output','ald_crp_plugin'); ?></label>
		<br />
		<label>
		<input type="radio" name="blank_output" value="noposts" id="blank_output_1" <?php if (!$crp_settings['blank_output']) echo 'checked="checked"' ?> />
		<?php _e('Display "No Related Posts"','ald_crp_plugin'); ?></label>
		<br />
	</p>
    <p>
      <label>
      <input type="checkbox" name="add_to_content" id="add_to_content" <?php if ($crp_settings[add_to_content]) echo 'checked="checked"' ?> />
      <?php _e('Add related posts to the post content on single posts. <br />If you choose to disable this, please add <code>&lt;?php if(function_exists(\'echo_ald_crp\')) echo_ald_crp(); ?&gt;</code> to your template file where you want it displayed','ald_crp_plugin'); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="add_to_page" id="add_to_page" <?php if ($crp_settings[add_to_page]) echo 'checked="checked"' ?> />
      <?php _e('Add related posts to pages. <br />If you choose to disable this, please add <code>&lt;?php if(function_exists(\'echo_ald_crp\')) echo_ald_crp(); ?&gt;</code> to your template file where you want it displayed','ald_crp_plugin'); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="add_to_feed" id="add_to_feed" <?php if ($crp_settings[add_to_feed]) echo 'checked="checked"' ?> />
      <?php _e('Add related posts to feed','ald_crp_plugin'); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="match_content" id="match_content" <?php if ($crp_settings[match_content]) echo 'checked="checked"' ?> />
      <?php _e('Find related posts based on content as well as title. If unchecked, only posts titles are used. (I recommend using a caching plugin if you enable this)','ald_crp_plugin'); ?>
      </label>
    </p>
    <p>
      <label>
      <input type="checkbox" name="exclude_pages" id="exclude_pages" <?php if ($crp_settings[exclude_pages]) echo 'checked="checked"' ?> />
      <?php _e('Exclude Pages in Related Posts','ald_crp_plugin'); ?>
      </label>
    </p>
    <p>
      <input type="submit" name="crp_save" id="crp_save" value="Save Options" style="border:#00CC00 1px solid" />
      <input name="crp_default" type="submit" id="crp_default" value="Default Options" style="border:#FF0000 1px solid" onclick="if (!confirm('<?php _e('Do you want to set options to Default? If you don\'t have a copy of the username, please hit Cancel and copy it first.','ald_crp_plugin'); ?>')) return false;" />
    </p>
    </fieldset>
  </form>
</div>
<?php

}


function crp_adminmenu() {
	if (function_exists('current_user_can')) {
		// In WordPress 2.x
		if (current_user_can('manage_options')) {
			$crp_is_admin = true;
		}
	} else {
		// In WordPress 1.x
		global $user_ID;
		if (user_can_edit_user($user_ID, 0)) {
			$crp_is_admin = true;
		}
	}

	if ((function_exists('add_options_page'))&&($crp_is_admin)) {
		add_options_page(__("Related Posts", 'myald_crp_plugin'), __("Related Posts", 'myald_crp_plugin'), 9, 'crp_options', 'crp_options');
		}
}
add_action('admin_menu', 'crp_adminmenu');

function crp_adminhead() {
	global $crp_url;

?>
<link rel="stylesheet" type="text/css" href="<?php echo $crp_url ?>/wick/wick.css" />
<script type="text/javascript" language="JavaScript">
function checkForm() {
answer = true;
if (siw && siw.selectingSomething)
	answer = false;
return answer;
}//
</script>
<?php }
add_action('admin_head', 'crp_adminhead');

function crp_adminfoot() {
	global $crp_url;

?>
<script type="text/javascript" src="<?php echo $crp_url ?>/wick/sample_data.js.php"></script>
<script type="text/javascript" src="<?php echo $crp_url ?>/wick/wick.js"></script>
<?php }
add_action('admin_footer', 'crp_adminfoot');


?>