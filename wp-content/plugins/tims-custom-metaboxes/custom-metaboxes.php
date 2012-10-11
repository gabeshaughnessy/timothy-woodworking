<?php 
/*
Plugin Name: Timothy's Custom Metaboxes
Plugin URI: http://timothyswoodworking.com
Description: Create custom metabox input fields for the site
Version: 1.0
Author: Gabe's Imagination
Author URI: http://gabesimagination.com
*/

require_once('lib/price-mb.php');

// Initialize the metabox class
add_action( 'init', 'initialize_cmb_meta_boxes', 9999 );
function initialize_cmb_meta_boxes() {
	if ( !class_exists( 'cmb_Meta_Box' ) ) {
		require_once( 'lib/init.php' );
	}
}
?>