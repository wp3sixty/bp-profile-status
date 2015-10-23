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

}
