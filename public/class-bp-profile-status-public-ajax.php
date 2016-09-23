<?php

/**
 * The ajax actions for public-facing of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / public
 */
/**
 * The ajax actions for public-facing of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / public
 */
class BP_Profile_Status_Public_Ajax {

	/**
	 * BP_Profile_Status_Public_Ajax constructor.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function __construct() {

	}

	/**
	 * Deleting current status of the user
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_delete_current_status() {

		// Filtering input post array.
		$post_array = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		if ( false === isset( $post_array['nonce'] ) ) {
			wp_send_json_error();
		}

		if ( false === wp_verify_nonce( wp_unslash( $post_array['nonce'] ), 'bpps_delete_current_status_nonce' ) ) {
			wp_send_json_error();
		}

		$current_status    = sanitize_text_field( wp_unslash( $post_array['status'] ) );
		$user_id           = get_current_user_id();
		$bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
		$key               = array_search( $current_status, $bpps_old_statuses, true );

		unset( $bpps_old_statuses[ $key ] );

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );
		delete_user_meta( $user_id, 'bpps_current_status' );

		wp_send_json_success();

	}

	/**
	 * Deleting status
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_delete_status() {

		// Filtering input post array.
		$post_array = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		if ( false === isset( $post_array['nonce'] ) ) {
			wp_send_json_error();
		}

		if ( false === wp_verify_nonce( wp_unslash( $post_array['nonce'] ), 'bpps_delete_status_nonce' ) ) {
			wp_send_json_error();
		}

		$current_status    = sanitize_text_field( wp_unslash( $post_array['status'] ) );
		$user_id           = get_current_user_id();
		$bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
		$key               = array_search( $current_status, $bpps_old_statuses, true );

		unset( $bpps_old_statuses[ $key ] );

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );

		wp_send_json_success();

	}

	/**
	 * Setting current status
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_set_current_status() {

		// Filtering input post array.
		$post_array = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		if ( false === isset( $post_array['nonce'] ) ) {
			wp_send_json_error();
		}

		if ( false === wp_verify_nonce( wp_unslash( $post_array['nonce'] ), 'set_current_status_nonce' ) ) {
			wp_send_json_error();
		}

		$current_status = sanitize_text_field( wp_unslash( $post_array['status'] ) );
		$user_id        = get_current_user_id();

		update_user_meta( $user_id, 'bpps_current_status', $current_status );

		$bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

		if ( false === empty( $bpps_statuses ) ) {
			$bpps_status_count   = count( $bpps_statuses );
			$bpps_current_status = get_user_meta( $user_id, 'bpps_current_status', true );
			$key                 = false;

			if ( false === in_array( $current_status, $bpps_statuses, true ) ) {
				array_unshift( $bpps_statuses, $current_status );
			}

			if ( $bpps_current_status ) {
				$key = array_search( $bpps_statuses, $bpps_statuses, true );
			}

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
