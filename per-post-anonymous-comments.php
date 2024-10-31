<?php
/*
Plugin Name: Per post anonymous comments
Plugin URI: http://dev.wp-plugins.org/browser/per-post-anonymous-comments/
Description: Wordpress has a setting called "Users must be registered and logged in to comment". This setting is system wide, so there is no option to allow anonymous comments in desired posts or pages (such as a guestbook). This plugin adds a checkbox to the edit screen to bypass the system wide restriction.
Version: 0.1
Author: Choan C. Gálvez <choan.galvez@gmail.com>
Author URI: http://dizque.lacalabaza.net/
*/

/*  
    Copyright 2006  Choan C. Gálvez  (email: choan.galvez@gmail.com)

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
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function ppac_filter($opt) {
	global $post;
	global $comment_post_ID;
	if (!$opt || is_admin()) return $opt;
	$id = 0;
	if ($post && $post->ID) {
		$id = $post->ID;
	} else {
		$id = $comment_post_ID;
	}
	$allow = get_post_meta($id, "_anonymous_comments", true);
	return $allow == 1 ? 0 : 1;
}

function ppac_checkbox() {
	if (!get_settings("comment_registration")) return;
	$allow = get_post_meta($_REQUEST['post'], '_anonymous_comments', true);
	$check = $allow ? 'checked="checked" ' : '';
	echo '<fieldset id="ppac_dbx" class="dbx-box">';
	echo '<h3 class="dbx-handle">', __('Anonymous comments', 'ppac'), '</h3>'; 
	echo '<div class="dbx-content">';
	echo '<label for="ppac" class="selectit"><input type="checkbox" name="ppac" id="ppac" value="1" '. $check . '/> '. __('Allow anonymous comments', 'ppac') . '</label></div></fieldset>';
}

function ppac_update_post($id) {
	delete_post_meta($id, '_anonymous_comments');
	$setting = (isset($_POST["ppac"]) && $_POST["ppac"] == "1") ? 1 : 0;
	add_post_meta($id, '_anonymous_comments', $setting);
}

load_plugin_textdomain('ppac', 'wp-content/plugins/per-post-anonymous-comments');

add_filter("option_comment_registration", "ppac_filter");
add_action("dbx_post_sidebar", "ppac_checkbox");
add_action("dbx_page_sidebar", "ppac_checkbox");
add_action('save_post', 'ppac_update_post');
add_action('edit_post', 'ppac_update_post');
add_action('publish_post', 'ppac_update_post');

?>