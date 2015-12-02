<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BPPS_Main_Ajax
 *
 * @author sanket
 */
class BPPS_Main_Ajax {

    /*
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_bpps_delete_current_status', array( $this, 'bpps_delete_current_status' ) );
        add_action( 'wp_ajax_bpps_delete_status', array( $this, 'bpps_delete_status' ) );
        add_action( 'wp_ajax_bpps_set_current_status', array( $this, 'bpps_set_current_status' ) );
    }

    /*
     * Delete current status
     */
    public function bpps_delete_current_status() {
        if( wp_verify_nonce( $_POST[ 'nonce' ], 'bpps_delete_current_status_nonce' ) ) {
            $user_id = get_current_user_id();
            $bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
            $key = array_search( $_POST[ 'status' ], $bpps_old_statuses );

            unset( $bpps_old_statuses[ $key ] );

            update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );
            delete_user_meta( $user_id, 'bpps_current_status' );

            echo "1";
            die();
        }

        echo "0";
        die();
    }

    /*
     * Delete status
     */
    public function bpps_delete_status() {
        if( wp_verify_nonce( $_POST[ 'nonce' ], 'bpps_delete_status_nonce' ) ) {
            $user_id = get_current_user_id();
            $bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
            $key = array_search( $_POST[ 'status' ], $bpps_old_statuses );

            unset( $bpps_old_statuses[ $key ] );

            update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );

            echo "1";
            die();
        }

        echo "0";
        die();
    }

    /*
     * Set current status directly
     */
    public function bpps_set_current_status() {
        $user_id = get_current_user_id();
        update_user_meta( $user_id, 'bpps_current_status', trim( $_POST[ 'status' ] ) );

        $bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

        if( !empty( $bpps_statuses ) ) {
            $bpps_status_count = count( $bpps_statuses );
            $key = 0;

            if( !in_array( trim( $_POST[ 'status' ] ), $bpps_statuses ) ) {
                array_unshift( $bpps_statuses, trim( $_POST[ 'status' ] ) );
            }

            $key = array_search( trim( $_POST[ 'status' ] ), $bpps_statuses );

            if( $bpps_status_count > 10 ) {
                if( $key != 0 && $key != false && $key == 11 ) {
                    unset( $bpps_statuses[ 10 ] );
                } else {
                    unset( $bpps_statuses[ 11 ] );
                }
            }
        } else {
            $bpps_statuses = array( trim( $_POST[ 'status' ] ) );
        }

        update_user_meta( $user_id, 'bpps_old_statuses', $bpps_statuses );

        $this_user_profile_url = bp_core_get_user_domain( $user_id );
        $action_String = "<a href=\"" . $this_user_profile_url . "\">" . bp_core_get_username( $user_id ) . "</a> " . __( 'added new status', 'bp-profile-status' );
        $params = array(
            'action' => $action_String,
            'content' => trim( $_POST[ 'status' ] ),
            'component' => 'activity',
            'type' => 'bpps_activity_update',
        );
        bp_activity_add( $params );

        echo "1";
        die();
    }

}
