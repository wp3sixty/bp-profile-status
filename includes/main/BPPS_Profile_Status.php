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
            add_action( 'bp_template_content', array( $this, 'bpps_content' ), 1 );
            add_action( 'bp_before_member_header_meta', array( $this, 'bpps_display_current_status' ), 9999 );

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

        $this->bpps_add_new_status_action( $_POST );
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

        $bpps_current_status = get_user_meta( bp_displayed_user_id(), 'bpps_current_status', true );
        $bpps_old_statuses = get_user_meta( bp_displayed_user_id(), 'bpps_old_statuses', true );

        if( ( get_current_user_id() == bp_displayed_user_id() ) ) {
            ?>
            <p><strong><?php echo __( 'Note', 'bp-profile-status' ) . ": "; ?></strong><?php echo __( 'You can store only 10 status. Old status will be deleted if you add more than 10 status.', 'bp-profile-status' ); ?></p>
            <form method="post">
                <div class="bp-widget bpps-add-new">
                    <textarea name="bpps_add_new_status" id="bpps_add_new_status" placeholder="<?php echo __( 'Add New Status...', 'bp-profile-status' ); ?>"></textarea>
                    <input name="bpps-eidt-status-org" id="bpps-eidt-status-org" type="hidden" value="" />
                    <input type="submit" name="bpps_add_new" id="bpps_add_new" value="<?php echo __( 'Add New', 'bp-profile-status' ); ?>" />
                    <input type="submit" name="bpps_add_new_and_set" id="bpps_add_new_and_set" value="<?php echo __( 'Add New & Set as Current', 'bp-profile-status' ); ?>" />

                    <input type="submit" name="bpps_update_status" class="bpps_hide" id="bpps_update_status" value="<?php echo __( 'Update', 'bp-profile-status' ); ?>" />
                    <input type="submit" name="bpps_update_status_and_set" class="bpps_hide" id="bpps_update_status_and_set" value="<?php echo __( 'Update & Set as Current', 'bp-profile-status' ); ?>" />
                    <input type="reset" name="bpps_cancel" class="bpps_hide" id="bpps_cancel" value="<?php echo __( 'Cancel', 'bp-profile-status' ); ?>" />
                    <span><span>140</span><?php echo " " . __( 'characters left', 'bp-profile-status' ); ?></span>
                </div>
            </form>
            <?php
        }

        if( !empty( $bpps_old_statuses ) ) {
            if( ($key = array_search( $bpps_current_status, $bpps_old_statuses )) !== false ) {
                unset( $bpps_old_statuses[ $key ] );
            }
        }
        ?>
        <div class="bp-widget bpps-old-statuses">
            <h4><?php echo __( 'Old Statuses', 'bp-profile-status' ); ?></h4>
            <table class="bpps-old-statuses-table">
                <tbody>
                    <?php
                    $count = 1;

                    if( !empty( $bpps_old_statuses ) ) {
                        foreach( $bpps_old_statuses as $bpps_old_status ) {
                            ?>
                            <tr>
                                <td class="bpps-old-status-count"><?php echo $count; ?></td>
                                <td>
                                    <?php echo convert_smilies( $bpps_old_status ); ?>
                                    <input type="hidden" value="<?php echo $bpps_old_status; ?>" />
                                </td>
                            </tr>
                            <?php
                            $count++;
                        }
                    } else {
                        ?>
                        <tr>
                            <td><?php echo __( 'No statuses available.', 'bp-profile-status' ); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /*
     * Adding new status action
     */

    public function bpps_add_new_status_action( $post_array ) {
        if( !empty( $post_array ) && isset( $post_array[ 'bpps_add_new_status' ] ) && $post_array[ 'bpps_add_new_status' ] != '' ) {
            $user_id = get_current_user_id();

            if( isset( $post_array[ 'bpps_add_new' ] ) ) {
                $this->bpps_store_status_usermeta( $user_id, $post_array );
            } else if( isset( $post_array[ 'bpps_add_new_and_set' ] ) ) {
                update_user_meta( $user_id, 'bpps_current_status', trim( $post_array[ 'bpps_add_new_status' ] ) );

                $this->bpps_store_status_usermeta( $user_id, $post_array );
            } else if( isset( $post_array[ 'bpps_update_status_and_set' ] ) ) {
                update_user_meta( $user_id, 'bpps_current_status', trim( $post_array[ 'bpps_add_new_status' ] ) );

                $this->bpps_update_status_in_usermeta( $post_array );
            }
        }
    }

    /*
     * Storing new statuses in user meta
     */

    public function bpps_store_status_usermeta( $user_id, $post_array ) {
        $bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

        if( !empty( $bpps_statuses ) ) {
            $bpps_status_count = count( $bpps_statuses );
            $bpps_current_status = get_user_meta( $user_id, 'bpps_current_status', true );
            $key = 0;

            if( !in_array( trim( $post_array[ 'bpps_add_new_status' ] ), $bpps_statuses ) ) {
                array_unshift( $bpps_statuses, trim( $post_array[ 'bpps_add_new_status' ] ) );
            }

            if( $bpps_current_status ) {
                $key = array_search( $bpps_current_status, $bpps_statuses );
            }

            if( $bpps_status_count > 10 ) {
                if( $key != 0 && $key != false && $key == 11 ) {
                    unset( $bpps_statuses[ 10 ] );
                } else {
                    unset( $bpps_statuses[ 11 ] );
                }
            }
        } else {
            $bpps_statuses = array( trim( $post_array[ 'bpps_add_new_status' ] ) );
        }

        update_user_meta( $user_id, 'bpps_old_statuses', $bpps_statuses );
    }

    /*
     * Displaying current status
     */

    public function bpps_display_current_status() {
        $bpps_current_status = get_user_meta( bp_displayed_user_id(), 'bpps_current_status', true );
        ?>
        <div id="bpps-current-status">
            <?php
            if( $bpps_current_status ) {
                ?>
                <span id="bpps-current-status-text"><?php echo convert_smilies( $bpps_current_status ); ?></span>
                <?php
                if( ( get_current_user_id() == bp_displayed_user_id() ) ) {
                    ?>
                    <input id="bpps-current-status-org" type="hidden" value="<?php echo $bpps_current_status; ?>" />
                    <?php echo wp_nonce_field( 'bpps_delete_current_status_nonce', 'bpps_delete_current_status_nonce' ); ?>
                    <a id="bpps-current-status-edit" title="<?php echo __( 'Edit Current Status', 'bp-profile-status' ); ?>">
                        <i class="dashicons dashicons-edit"></i>
                    </a>
                    <a id="bpps-current-status-delete" title="<?php echo __( 'Delete Current Status', 'bp-profile-status' ); ?>">
                        <i class="dashicons dashicons-trash"></i>
                    </a>
                    <?php
                }
            } else {
                echo __( 'No current status is set yet.', 'bp-profile-status' );
            }
            ?>
        </div>
        <?php
    }

    /*
     * Update status in usermeta
     */

    public function bpps_update_status_in_usermeta( $post_array ) {
        $user_id = get_current_user_id();
        $bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );
        $key = array_search( trim( $post_array[ 'bpps-eidt-status-org' ] ), $bpps_old_statuses );
        $bpps_old_statuses[ $key ] = trim( $post_array[ 'bpps_add_new_status' ] );

        update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );
    }

}
