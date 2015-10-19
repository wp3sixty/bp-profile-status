<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BPPS_Profile_Status
 *
 * @author sanket
 */
class BPPS_Profile_Status {

    /*
     * constructor
     */

    public function __construct() {
        if( class_exists( 'BuddyPress' ) ) {
            add_action( 'bp_init', array( $this, 'bpps_add_profile_status_menu' ) );

            add_filter( 'bp_settings_admin_nav', array( $this, 'bpps_profile_status_nav' ), 3 );
        }
    }

    /*
     * Adding profile status menu in Profile
     */

    public function bpps_add_profile_status_menu() {
        if( bp_displayed_user_domain() ) {
            $user_domain = bp_displayed_user_domain();
        } elseif( bp_loggedin_user_domain() ) {
            $user_domain = bp_loggedin_user_domain();
        } else {
            return;
        }

        $proflie_link = trailingslashit( $user_domain . 'profile' );
        $bpps_status = array(
            'name' => __( 'Status', 'bp-profile-status' ), // Display name for the nav item
            'slug' => 'status', // URL slug for the nav item
            'parent_slug' => 'profile', // URL slug of the parent nav item
            'parent_url' => $proflie_link, // URL of the parent item
            'item_css_id' => 'bpps-status', // The CSS ID to apply to the HTML of the nav item
            'user_has_access' => true, // Can the logged in user see this nav item?
            'site_admin_only' => false, // Can only site admins see this nav item?
            'position' => 80, // Index of where this nav item should be positioned
            'screen_function' => array( $this, 'settings_ui' ), // The name of the function to run when clicked
            'link' => ''  // The link for the subnav item; optional, not usually required.
        );

        bp_core_new_subnav_item( $bpps_status );
    }

    function settings_ui() {
        if( bp_action_variables() ) {
            bp_do_404();

            return;
        }

        // Load the template
        bp_core_load_template( 'members/single/plugins' );
    }

    /*
     * Adding status nav in BuddyPress admin navs
     */

    public function bpps_profile_status_nav( $wp_admin_nav ) {
        if( bp_displayed_user_domain() ) {
            $user_domain = bp_displayed_user_domain();
        } elseif( bp_loggedin_user_domain() ) {
            $user_domain = bp_loggedin_user_domain();
        } else {
            return;
        }

        $proflie_link = trailingslashit( $user_domain . 'profile' );

        // Add the "Profile" subnav item
        $wp_admin_nav[] = array(
            'parent' => 'my-account-' . buddypress()->profile->id,
            'id' => 'my-account-' . buddypress()->profile->id . '-status',
            'title' => _x( 'Status', 'My Profile Status sub nav', 'bp-profile-status' ),
            'href' => trailingslashit( $proflie_link . 'status' )
        );

        return $wp_admin_nav;
    }

}
