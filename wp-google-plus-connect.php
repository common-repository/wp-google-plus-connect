<?php
/*
Plugin Name: WP Google+ Connect
Plugin URI: http://webdevstudios.com/plugin/wp-google-plus-connect/
Description: Display a Google+ Direct Connect Badge on your site via widget or shortcode, allow your members to login/register via their Google+ account or sync your Google+ stream with your blog posts or BuddyPress activity via the Google+ API.
Author: Brian Messenlehner of WebDevStudios.com
Author URI: http://webdevstudios.com
Version: 1.0.5.1
*/

//check for buddypress
add_action( 'bp_include', 'wds_google_connect_buddypress' );
function wds_google_connect_buddypress() {
    require( dirname( __FILE__ ) . '/buddypress.php' );
}
require( dirname( __FILE__ ) . '/functions.php' );
require( dirname( __FILE__ ) . '/admin.php' );

?>