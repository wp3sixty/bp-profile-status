<?php
/**
 * Description of BPPS_Profile_Status
 *
 * @author sanket
 * @package bp-profile-status
 */
class BPPS_Profile_Status {

	/**
	 * BPPS_Profile_Status constructor.
	 */
	public function __construct() {
		if ( false === class_exists( 'BuddyPress' ) ) {
			return;
		}
		add_action( 'bp_init', array( $this, 'bpps_add_profile_status_menu' ) );
		add_action( 'bp_template_content', array( $this, 'bpps_content' ), 1 );
		add_action( 'bp_before_member_header_meta', array( $this, 'bpps_display_current_status' ) );
		add_action( 'bp_directory_members_item', array( $this, 'bpps_display_current_status_member_list' ) );

		add_filter( 'bp_settings_admin_nav', array( $this, 'bpps_profile_status_nav' ), 3 );
	}

	/*
     * Adding profile status menu in Profile
     */

	public function bpps_add_profile_status_menu() {
		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}

		$proflie_link = trailingslashit( $user_domain . 'profile' );
		$bpps_status  = array(
			'name'            => __( 'Status', 'bp-profile-status' ), // Display name for the nav item
			'slug'            => 'status', // URL slug for the nav item
			'parent_slug'     => 'profile', // URL slug of the parent nav item
			'parent_url'      => $proflie_link, // URL of the parent item
			'item_css_id'     => 'bpps-status', // The CSS ID to apply to the HTML of the nav item
			'user_has_access' => true, // Can the logged in user see this nav item?
			'site_admin_only' => false, // Can only site admins see this nav item?
			'position'        => 80, // Index of where this nav item should be positioned
			'screen_function' => array( $this, 'settings_ui' ), // The name of the function to run when clicked
			'link'            => '',// The link for the subnav item; optional, not usually required.
		);

		bp_core_new_subnav_item( $bpps_status );

		if ( isset( $post_array['nonce'] ) && wp_verify_nonce( $post_array['nonce'], 'bp-profile-action' ) ) {
			$this->bpps_add_new_status_action( wp_unslash( $_POST ) );
		}
	}

	function settings_ui() {
		if ( bp_action_variables() ) {
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
		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}

		$proflie_link = trailingslashit( $user_domain . 'profile' );

		// Add the "Profile" subnav item
		$wp_admin_nav[] = array(
			'parent' => 'my-account-' . buddypress()->profile->id,
			'id'     => 'my-account-' . buddypress()->profile->id . '-status',
			'title'  => _x( 'Status', 'My Profile Status sub nav', 'bp-profile-status' ),
			'href'   => trailingslashit( $proflie_link . 'status' ),
		);

		return $wp_admin_nav;
	}

	/*
     * UI for adding status
     */

	public function bpps_content() {
		if ( buddypress()->current_action != 'status' ) {
			return;
		}

		if ( ( get_current_user_id() == bp_displayed_user_id() ) ) {
			?>
			<p>
				<strong><?php esc_html_e( 'Note', 'bp-profile-status' ) . ': '; ?></strong><?php esc_html_e( 'You can store only 10 status. Old status will be deleted if you add more than 10 status.', 'bp-profile-status' ); ?>
			</p>
			<form method="post" action="" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bp-profile-action', 'nonce' ); ?>
				<div class="bp-widget bpps-add-new">
					<textarea name="bpps_add_new_status" id="bpps_add_new_status"
					          placeholder="<?php esc_attr_e( 'Add New Status...', 'bp-profile-status' ); ?>"></textarea>
					<input name="bpps-eidt-status-org" id="bpps-eidt-status-org" type="hidden" value=""/>
					<input type="submit" name="bpps_add_new" id="bpps_add_new"
					       value="<?php esc_attr_e( 'Add New', 'bp-profile-status' ); ?>"/>
					<input type="submit" name="bpps_add_new_and_set" id="bpps_add_new_and_set"
					       value="<?php esc_attr_e( 'Add New & Set as Current', 'bp-profile-status' ); ?>"/>

					<input type="submit" name="bpps_update_status" class="bpps_hide" id="bpps_update_status"
					       value="<?php esc_attr_e( 'Update', 'bp-profile-status' ); ?>"/>
					<input type="submit" name="bpps_update_status_and_set" class="bpps_hide"
					       id="bpps_update_status_and_set"
					       value="<?php esc_attr_e( 'Update & Set as Current', 'bp-profile-status' ); ?>"/>
					<input type="reset" name="bpps_cancel" class="bpps_hide" id="bpps_cancel"
					       value="<?php esc_attr_e( 'Cancel', 'bp-profile-status' ); ?>"/>
					<span><span>140</span>&nbsp;<?php esc_attr_e( 'characters left', 'bp-profile-status' ); ?></span>
				</div>
			</form>
			<?php
		}

		$bpps_current_status = get_user_meta( bp_displayed_user_id(), 'bpps_current_status', true );
		$bpps_old_statuses   = get_user_meta( bp_displayed_user_id(), 'bpps_old_statuses', true );

		if ( ! empty( $bpps_old_statuses ) ) {
			if ( ( $key = array_search( $bpps_current_status, $bpps_old_statuses ) ) !== false ) {
				unset( $bpps_old_statuses[ $key ] );
			}
		}
		?>
		<div class="bp-widget bpps-old-statuses">
			<h4><?php esc_html_e( 'Old Statuses', 'bp-profile-status' ); ?></h4>
			<?php wp_nonce_field( 'bpps_delete_status_nonce', 'bpps_delete_status_nonce' ); ?>
			<table class="bpps-old-statuses-table">
				<tbody>
				<?php
				if ( ! empty( $bpps_old_statuses ) ) {
					foreach ( $bpps_old_statuses as $bpps_old_status ) {
						?>
						<tr>
							<td>
								<?php echo wp_kses_post( convert_smilies( $bpps_old_status ) ); ?>
								<input type="hidden" class="bpps_old_status_org"
								       value="<?php echo esc_attr( $bpps_old_status ); ?>"/>
							</td>
							<td>
								<a class="bpps-set-status"
								   title="<?php esc_attr_e( 'Set as Current Status', 'bp-profile-status' ); ?>">
									<i class="dashicons dashicons-yes"></i>
								</a>
								<?php if ( ( get_current_user_id() == bp_displayed_user_id() ) ) { ?>
									<a class="bpps-status-edit"
									   title="<?php esc_attr_e( 'Edit this Status', 'bp-profile-status' ); ?>">
										<i class="dashicons dashicons-edit"></i>
									</a>
									<a class="bpps-status-delete"
									   title="<?php esc_attr_e( 'Delete this Status', 'bp-profile-status' ); ?>">
										<i class="dashicons dashicons-trash"></i>
									</a>
								<?php } ?>
							</td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr>
						<td><?php esc_attr_e( 'No statuses available.', 'bp-profile-status' ); ?></td>
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
		if ( ! empty( $post_array ) && ( ( isset( $post_array['bpps_add_new_status'] ) && '' !== $post_array['bpps_add_new_status'] ) || ( isset( $post_array['bpps-current-status-textarea'] ) && '' !== $post_array['bpps-current-status-textarea'] ) ) ) {
			$user_id = get_current_user_id();

			if ( isset( $post_array['bpps_add_new'] ) ) {
				$this->bpps_store_status_usermeta( $user_id, $post_array );
			} else if ( isset( $post_array['bpps_add_new_and_set'] ) ) {
				update_user_meta( $user_id, 'bpps_current_status', trim( $post_array['bpps_add_new_status'] ) );

				$this->bpps_store_status_usermeta( $user_id, $post_array );
				$this->bpps_add_status_activity( $user_id, $post_array );
			} else if ( isset( $post_array['bpps_update_status_and_set'] ) ) {
				update_user_meta( $user_id, 'bpps_current_status', trim( $post_array['bpps_add_new_status'] ) );

				$this->bpps_update_status_in_usermeta( $post_array );
			} else if ( isset( $post_array['bpps_update_status'] ) ) {
				$this->bpps_update_status_in_usermeta( $post_array );
			} else if ( isset( $post_array['bpps_update_status_update'] ) ) {
				update_user_meta( $user_id, 'bpps_current_status', trim( $post_array['bpps-current-status-textarea'] ) );

				$this->bpps_update_status_in_usermeta( $post_array );
			}
		}
	}

	/**
	 * Ading new activity while adding status
	 */
	public function bpps_add_status_activity( $user_id, $post_array ) {
		$this_user_profile_url = bp_core_get_user_domain( $user_id );
		$action_string         = '<a href="' . $this_user_profile_url . '">' . bp_core_get_username( $user_id ) . '</a> ' . __( 'added new status', 'bp-profile-status' );
		$params                = array(
			'action'    => $action_string,
			'content'   => $post_array['bpps_add_new_status'],
			'component' => 'activity',
			'type'      => 'bpps_activity_update',
		);
		bp_activity_add( $params );
	}

	/*
     * Storing new statuses in user meta
     */

	public function bpps_store_status_usermeta( $user_id, $post_array ) {
		$bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

		if ( ! empty( $bpps_statuses ) ) {
			$bpps_status_count   = count( $bpps_statuses );
			$bpps_current_status = get_user_meta( $user_id, 'bpps_current_status', true );
			$key                 = false;

			if ( ! in_array( trim( $post_array['bpps_add_new_status'] ), $bpps_statuses ) ) {
				array_unshift( $bpps_statuses, trim( $post_array['bpps_add_new_status'] ) );
			}

			if ( $bpps_current_status ) {
				$key = array_search( $bpps_current_status, $bpps_statuses );
			}

			if ( $bpps_status_count > 10 ) {
				if ( $key && false !== $key && 11 === $key ) {
					unset( $bpps_statuses[10] );
				} else {
					unset( $bpps_statuses[11] );
				}
			}
		} else {
			$bpps_statuses = array( trim( $post_array['bpps_add_new_status'] ) );
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
			if ( $bpps_current_status ) {
				?>
				<span id="bpps-current-status-text"><?php echo wp_kses_post( convert_smilies( $bpps_current_status ) ); ?></span>
				<form method="post" action="" enctype="multipart/form-data" class="bpps_hide"
				      id="bpps-current-status-direct-edit">
					<input id="bpps-current-status-org" name="bpps-current-status-org" type="hidden"
					       value="<?php echo esc_attr( $bpps_current_status ); ?>"/>
					<textarea name="bpps-current-status-textarea" id="bpps-current-status-textarea"></textarea>
					<input type="submit" name="bpps_update_status_update" id="bpps_update_status_update" value="Update">
					<input type="reset" name="bpps_cancel_update" id="bpps_cancel_update" value="Cancel">
				</form>
				<?php
				if ( ( get_current_user_id() == bp_displayed_user_id() ) ) {
					wp_nonce_field( 'bpps_delete_current_status_nonce', 'bpps_delete_current_status_nonce' );
					?>
					<a id="bpps-current-status-edit"
					   title="<?php esc_html_e( 'Edit Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-edit"></i>
					</a>
					<a id="bpps-current-status-delete"
					   title="<?php esc_html_e( 'Delete Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-trash"></i>
					</a>
					<?php
				} else {
					?>
					<a class="bpps-set-status"
					   title="<?php esc_html_e( 'Set as Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-yes"></i>
					</a>
					<?php
				}
			} else {
				esc_html_e( 'No current status is set yet.', 'bp-profile-status' );
			}
			?>
		</div>
		<?php
	}

	/*
     * Update status in usermeta
     */

	public function bpps_update_status_in_usermeta( $post_array ) {
		$user_id           = get_current_user_id();
		$bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

		if ( isset( $post_array['bpps_update_status_update'] ) ) {
			$key = array_search( trim( $post_array['bpps-current-status-org'] ), $bpps_old_statuses );

			$bpps_old_statuses[ $key ] = trim( $post_array['bpps-current-status-textarea'] );
		} else if ( isset( $post_array['bpps_add_new_status'] ) ) {
			$key = array_search( trim( $post_array['bpps-eidt-status-org'] ), $bpps_old_statuses );

			$bpps_old_statuses[ $key ] = trim( $post_array['bpps_add_new_status'] );
		}

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );
	}

	/*
     * Displaying current status on member list
     */

	public function bpps_display_current_status_member_list() {
		$user_id     = bp_get_member_user_id();
		$bpps_status = get_user_meta( $user_id, 'bpps_current_status', true );
		?>
		<div class="bpps-status">
			<?php
			if ( $bpps_status ) {
				?>
				<span class="bpps-status-text"><?php echo wp_kses( convert_smilies( $bpps_status ) ); ?></span>
				<?php
				if ( get_current_user_id() !== $user_id ) {
					?>
					<input class="bpps-status-org" type="hidden" value="<?php echo esc_attr( $bpps_status ); ?>"/>
					<a class="bpps-set-status"
					   title="<?php esc_html_e( 'Set as Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-yes"></i>
					</a>
					<?php
				}
			} else {
				echo esc_html_e( 'No current status is set yet.', 'bp-profile-status' );
			}
			?>
		</div>
		<?php
	}
}
