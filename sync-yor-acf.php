<?php
/*
Plugin Name: Sync Yor ACF
Description: Checks if there are unsynchronized ACF files and shows a message bar.
Version: 0.9
Author: Szymon Polaczy - Get Over Online
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; //Exit if accessed directrly
}

if ( ! class_exists( 'Sync_Yor_ACF' ) ) {
	class Sync_Yor_ACF {
		public function __construct() {
			add_action( 'acf/init', array( $this, 'check_acf_sync' ) );
		}

		public function check_acf_sync() {
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
				add_action( 'admin_notices', array( $this, 'show_acf_sync_message' ) );
				add_action( 'save_post', array( $this, 'prevent_save_my_custom_post_type' ) );
			}
		}


		public function prevent_save_my_custom_post_type( $post_id ) {
			if ( get_post_type() !== 'acf-field-group' ) {
				return;
			}

			wp_die( esc_html_e( 'Saving acf groups is not allowed until you sync your ACF files.', 'sync-yor-acf' ) );
		}


		public function show_acf_sync_message() {
			?>
			<div class="notice notice-warning">
				<p><?php esc_html_e( 'Sync Your ACF Files to Ensure Data Integrity!', 'sync-yor-acf' ); ?></p>
			</div>
			<?php
		}
	}

	$sync_plugin = new Sync_Yor_ACF();
} else {
	add_action( 'admin_notices', function() {
		?>
		<div class="notice notice-warning">
			<p>
				<?php esc_html_e( 'Plugin Sync Yor ACF is doing nothing right now as a class named Sync_Yor_ACF already exists' , 'sync-yor-acf' ); ?>
			</p>
		</div>
		<?php
	});
}

