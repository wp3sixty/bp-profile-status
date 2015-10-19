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
            add_action( 'bp_template_content', array( $this, 'bpps_content' ) );

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

    /*
     * UI for adding status
     */

    public function bpps_content() {
        if( buddypress()->current_action != 'status' ) {
            return;
        }
        ?>
        <form method="post">
            <div class="bp-widget bpps-add-new">
                <textarea name="bpps_add_new_status" id="bpps_add_new_status" placeholder="Add New Status..."></textarea>
                <input type="submit" name="bpps_add_new" id="bpps_add_new" value="Add New" />
                <input type="submit" name="bpps_add_new_and_set" id="bpps_add_new_and_set" value="Add New & Set as Current" />
            </div>
        </form>
        <?php
        $this->bpps_add_new_status_action( $_POST );
    }

    /*
     * Adding new status action
     */

    public function bpps_add_new_status_action( $post_array ) {
        if( !empty( $post_array ) && $post_array[ 'bpps_add_new_status' ] != '' ) {
            $user_id = get_current_user_id();

            if( isset( $post_array[ 'bpps_add_new' ] ) ) {
                $bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

                if( !empty( $bpps_statuses ) && !in_array( trim( $post_array[ 'bpps_add_new_status' ] ), $bpps_statuses ) ) {
                    array_unshift( $bpps_statuses, trim( $post_array[ 'bpps_add_new_status' ] ) );
                } else {
                    $bpps_statuses = array( trim( $post_array[ 'bpps_add_new_status' ] ) );
                }

                update_user_meta( $user_id, 'bpps_old_statuses', $bpps_statuses );
            } else if( isset( $post_array[ 'bpps_add_new_and_set' ] ) ) {
                //update_user_meta( $user_id, 'bpps_current_status', trim( $post_array[ 'bpps_add_new_status' ] ) );
            }
        }
    }

}
