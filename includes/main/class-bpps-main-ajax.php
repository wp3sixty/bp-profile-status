<?php
/**
 * Description of BPPS_Main_Ajax
 *
 * @package bp-profile-status
 * @author  sanket
 **/

/**
 * Class BPPS_Main_Ajax
 */
class BPPS_Main_Ajax {

	/**
	 * BPPS_Main_Ajax constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_bpps_delete_current_status', array( $this, 'delete_current_status' ) );
		add_action( 'wp_ajax_bpps_delete_status', array( $this, 'delete_status' ) );
		add_action( 'wp_ajax_bpps_set_current_status', array( $this, 'set_current_status' ) );
	}

	/**
	 * BPPS_Main_Ajax delete_current_status
	 */
	public function delete_current_status() {
		if ( false === isset( $_POST['nonce'] ) ) {
			wp_send_json_error();
		}
		if ( false === wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'bpps_delete_current_status_nonce' ) ) {
			wp_send_json_error();
		}
		$current_status    = sanitize_text_field( wp_unslash( $_POST['status'] ) );
		$user_id           = get_current_user_id();
		$bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
		$key               = array_search( $current_status, $bpps_old_statuses );

		unset( $bpps_old_statuses[ $key ] );

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );
		delete_user_meta( $user_id, 'bpps_current_status' );

		wp_send_json_success();

	}


	/**
	 * BPPS_Main_Ajax delete_status
	 */
	public function delete_status() {
		if ( false === isset( $_POST['nonce'] ) ) {
			wp_send_json_error();
		}
		if ( false === wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'bpps_delete_status_nonce' ) ) {
			wp_send_json_error();
		}
		$current_status    = sanitize_text_field( wp_unslash( $_POST['status'] ) );
		$user_id           = get_current_user_id();
		$bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
		$key               = array_search( $current_status, $bpps_old_statuses );

		unset( $bpps_old_statuses[ $key ] );

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );
		wp_send_json_success();

	}

	/**
	 * BPPS_Main_Ajax set_current_status
	 */
	public function set_current_status() {
		if ( false === isset( $_POST['nonce'] ) ) {
			wp_send_json_error();
		}
		if ( false === wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'set_current_status_nonce' ) ) {
			wp_send_json_error();
		}

		$current_status = sanitize_text_field( wp_unslash( $_POST['status'] ) );
		$user_id        = get_current_user_id();
		update_user_meta( $user_id, 'bpps_current_status', $current_status );

		$bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

		if ( false === empty( $bpps_statuses ) ) {
			$bpps_status_count = count( $bpps_statuses );
			$key               = false;

			if ( false === in_array( $current_status, $bpps_statuses ) ) {
				array_unshift( $bpps_statuses, $current_status );
			}

			$key = array_search( $bpps_statuses, $bpps_statuses );

			if ( $bpps_status_count > 10 ) {
				if ( $key && false !== $key && 11 === $key ) {
					unset( $bpps_statuses[10] );
				} else {
					unset( $bpps_statuses[11] );
				}
			}
		} else {
			$bpps_statuses = array( $current_status );
		}

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_statuses );

		$this_user_profile_url = bp_core_get_user_domain( $user_id );
		$action_string         = '<a href="' . esc_url( $this_user_profile_url ) . '">' . esc_html( htmlentities( bp_core_get_username( $user_id ) ) ) . '</a> ' . esc_html__( 'added new status', 'bp-profile-status' );
		$params                = array(
			'action'    => $action_string,
			'content'   => $current_status,
			'component' => 'activity',
			'type'      => 'bpps_activity_update',
		);
		bp_activity_add( $params );

		wp_send_json_success();
	}
}
