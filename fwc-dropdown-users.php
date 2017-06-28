<?php 
/*
 * Plugin Name: FWC Dropdown Users
 * Plugin URI: http://bugatan.com
 * Description: Override Dropdown User Wordpress
 * Author: Ebenhaezer BM
 * Author URI: http://ebenhaezerbm.com
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 */

defined( 'ABSPATH' ) OR exit;

defined( 'WPINC' ) OR exit;

require_once('inc/class-users.php');
require_once('inc/class-metabox.php');

add_action( 'admin_enqueue_scripts', 'fwc_dropdown_users_assets' );
add_action( 'wp_enqueue_scripts', 'fwc_dropdown_users_assets' );
function fwc_dropdown_users_assets( $hook ) {
	// Select2
	wp_enqueue_style( 'select2', plugins_url('assets/css/select2.css', __FILE__), array(), true);
	wp_enqueue_script( 'select2-js', plugins_url('assets/js/select2.min.js', __FILE__), array( 'jquery' ), '', false );

	wp_enqueue_style( 'fwc-dropdown-users', plugins_url('assets/css/style.css', __FILE__), array(), true);
	wp_enqueue_script( 'fwc-dropdown-users-js', plugins_url('assets/js/scripts.js', __FILE__), array( 'jquery' ), '', true );

	// Localize the script with new data
	wp_localize_script( 'fwc-dropdown-users-js', 'fwc_dropdown_users', array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'baseURI' => home_url()
		) 
	);
}

