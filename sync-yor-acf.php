<?php
/*
Plugin Name: Sync Yor ACF
Description: Checks if there are unsynchronized ACF files and shows a message bar.
Version: 0.7
Author: Szymon Polaczy - Get Over Online
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; //Exit if accessed directrly
}

function check_acf_sync() {
	if ( ! class_exists( 'ACF_Admin_Internal_Post_Type_List' ) ) {
		return;
	}

	if ( isset( $_GET['post_type'] ) && 'acf-field-group' === $_GET['post_type']
		&& isset( $_GET['post_status'] ) && 'sync' === $_GET['post_status']
	) {
		return;
	}

	$acf_internal = new ACF_Admin_Internal_Post_Type_List();

	$acf_internal->post_type = 'acf-field-group';

	$acf_internal->setup_sync();
	$acf_internal->check_sync();

	if ( count( $acf_internal->sync ) > 0 ) {
		add_action( 'admin_notices', 'show_acf_sync_message' );
		add_action( 'save_post', 'prevent_save_my_custom_post_type' );
	}
}


function prevent_save_my_custom_post_type( $post_id ) {
	wp_die( 'Saving is not allowed until you sync your ACF files.' );
}


function show_acf_sync_message() {
	?>
	<div class="notice notice-warning">
		<p><?php esc_html_e( 'Sync Your ACF Files to Ensure Data Integrity!', 'sync-yor-acf' ); ?></p>
	</div>
	<?php
}

add_action( 'acf/init', 'check_acf_sync' );

