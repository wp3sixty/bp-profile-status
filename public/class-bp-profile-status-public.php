<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / public
 */
class BP_Profile_Status_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @var     string  $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @var     string  $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The suffix for CSS / JS files for minification.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @var     string  $suffix The current version of this plugin.
	 */
	private $suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @param   string  $plugin_name    The name of the plugin.
	 * @param   string  $version        The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->suffix      = $this->bpps_get_script_style_suffix();

	}

	/**
	 * Checking if SCRIPT_DEBUG constant is defined or not
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  string  suffix for CSS / JS files.
	 */
	public function bpps_get_script_style_suffix() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && ( true === constant( 'SCRIPT_DEBUG' ) ) ) ? '' : '.min';

		return $suffix;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function enqueue_styles() {

		$file_name = 'bp-profile-status-public' . $this->suffix . '.css';

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/' . $file_name, array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function enqueue_scripts() {

		$file_name = 'bp-profile-status-public' . $this->suffix . '.js';

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/' . $file_name, array( 'jquery' ), $this->version, false );

		$bpps_localize_array = array(
			'bpps_max_character_alert'			 => esc_html__( "You have reached max character limit. \n\nPlease revise it.!", 'bp-profile-status' ),
			'bpps_update'                        => esc_html__( 'Update', 'bp-profile-status' ),
			'bpps_update_and_set_as_current'     => esc_html__( 'Update & Set as Current', 'bp-profile-status' ),
			'bpps_delete_current_status_confirm' => esc_html__( 'Are you sure you want to delete current status?', 'bp-profile-status' ),
			'bpps_delete_current_status_success' => esc_html__( 'Current status deleted successfully.!', 'bp-profile-status' ),
			'bpps_no_current_status_display'     => apply_filters( 'bpps_no_current_status_display', true ),
			'bpps_no_current_status_set'         => esc_html__( 'No current status is set yet.', 'bp-profile-status' ),
			'bpps_delete_status_confirm'         => esc_html__( 'Are you sure you want to delete this status?', 'bp-profile-status' ),
			'bpps_status_delete_success'         => esc_html__( 'Status deleted successfully.!', 'bp-profile-status' ),
			'bpps_status_set_success'            => esc_html__( 'Status set successfully.!', 'bp-profile-status' ),
			'bpps_status_link'					 => esc_js( bp_loggedin_user_domain() . 'profile/status/' ),
			'bpps_add_new_status'				 => esc_html__( 'Add New Status', 'bp-profile-status' ),
			'bpps_text_count'				 	 => esc_js( apply_filters( 'bpps_text_counter', 140 ) ),
		);

		wp_localize_script( $this->plugin_name, 'bpps_main_js', array(
			'set_current_status_nonce' => wp_create_nonce( 'set_current_status_nonce' ),
			'i18n'  				   => $bpps_localize_array,
		) );

	}

	/**
	 * Adding profile status menu in BuddyPress Profile
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_add_profile_status_menu() {

		if ( bp_displayed_user_domain() ) {
			$user_domain = bp_displayed_user_domain();
		} elseif ( bp_loggedin_user_domain() ) {
			$user_domain = bp_loggedin_user_domain();
		} else {
			return;
		}

		// Creating profile link of user.
		$proflie_link = trailingslashit( $user_domain . 'profile' );
		$bpps_status  = array(
			'name'            => esc_html__( 'Status', 'bp-profile-status' ), // Display name for the nav item
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

		// Adding Status menu to BuddyPress profile navigation.
		bp_core_new_subnav_item( $bpps_status );

		// Filtering input post array.
		$post_array = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

		if ( ! empty( $post_array['nonce'] ) && wp_verify_nonce( $post_array['nonce'], 'bp-profile-action' ) ) {
			$this->bpps_add_new_status_action( wp_unslash( $post_array ) );
		}

	}

	/**
	 * Loading BuddyPress - Users Plugins Template
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	function settings_ui() {

		if ( bp_action_variables() ) {
			bp_do_404();

			return;
		}

		// Load the template
		bp_core_load_template( 'members/single/plugins' );

	}

	/**
	 * HTML markup for Set Status page on BuddyPress Profile
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_content() {

		if ( 'status' !== buddypress()->current_action ) {
			return;
		}

		if ( ( get_current_user_id() === bp_displayed_user_id() ) ) {
			?>
			<p>
				<strong><?php esc_html_e( 'Note: ', 'bp-profile-status' ); ?></strong>
				<?php esc_html_e( 'You can store only 10 status. Old status will be deleted if you add more than 10 status.', 'bp-profile-status' ); ?>
			</p>
			<form method="post" action="" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bp-profile-action', 'nonce' ); ?>
				<div class="bp-widget bpps-add-new">
					<textarea name="bpps_add_new_status" id="bpps_add_new_status" placeholder="<?php esc_attr_e( 'Add New Status...', 'bp-profile-status' ); ?>"></textarea>
					<input name="bpps-edit-status-org" id="bpps-edit-status-org" type="hidden" value="" />
					<input type="submit" name="bpps_add_new" id="bpps_add_new" value="<?php esc_attr_e( 'Add New', 'bp-profile-status' ); ?>" />
					<input type="submit" name="bpps_add_new_and_set" id="bpps_add_new_and_set" value="<?php esc_attr_e( 'Add New & Set as Current', 'bp-profile-status' ); ?>" />
					<input type="submit" name="bpps_update_status" class="bpps_hide" id="bpps_update_status" value="<?php esc_attr_e( 'Update', 'bp-profile-status' ); ?>" />
					<input type="submit" name="bpps_update_status_and_set" class="bpps_hide" id="bpps_update_status_and_set" value="<?php esc_attr_e( 'Update & Set as Current', 'bp-profile-status' ); ?>"/>
					<input type="reset" name="bpps_cancel" class="bpps_hide" id="bpps_cancel" value="<?php esc_attr_e( 'Cancel', 'bp-profile-status' ); ?>"/>
					<span><span><?php echo esc_html( number_format( apply_filters( 'bpps_text_counter', 140 ) ) ); ?></span>&nbsp;<?php esc_html_e( 'characters left', 'bp-profile-status' ); ?></span>
				</div>
			</form>
			<?php
		}

		$bpps_current_status = get_user_meta( bp_displayed_user_id(), 'bpps_current_status', true );
		$bpps_old_statuses   = get_user_meta( bp_displayed_user_id(), 'bpps_old_statuses', true );

		if ( ! empty( $bpps_old_statuses ) ) {
			if ( false !== ( $key = array_search( $bpps_current_status, $bpps_old_statuses, true ) ) ) {
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
								<input type="hidden" class="bpps_old_status_org" value="<?php echo esc_attr( $bpps_old_status ); ?>" />
							</td>
							<td>
								<a class="bpps-set-status" title="<?php esc_attr_e( 'Set as Current Status', 'bp-profile-status' ); ?>">
									<i class="dashicons dashicons-yes"></i>
								</a>
								<?php if ( ( get_current_user_id() === bp_displayed_user_id() ) ) { ?>
									<a class="bpps-status-edit" title="<?php esc_attr_e( 'Edit this Status', 'bp-profile-status' ); ?>">
										<i class="dashicons dashicons-edit"></i>
									</a>
									<a class="bpps-status-delete" title="<?php esc_attr_e( 'Delete this Status', 'bp-profile-status' ); ?>">
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
						<td><?php esc_html_e( 'No statuses available.', 'bp-profile-status' ); ?></td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
		<?php

	}

	/**
	 * Displaying current status of the User
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_display_current_status() {

		$bpps_current_status = get_user_meta( bp_displayed_user_id(), 'bpps_current_status', true );
		?>
		<div id="bpps-current-status">
			<?php
			if ( ! empty( $bpps_current_status ) ) {
				?>
				<span id="bpps-current-status-text"><?php echo wp_kses_post( convert_smilies( $bpps_current_status ) ); ?></span>
				<form method="post" action="" enctype="multipart/form-data" class="bpps_hide" id="bpps-current-status-direct-edit">
					<input id="bpps-current-status-org" name="bpps-current-status-org" type="hidden" value="<?php echo esc_attr( $bpps_current_status ); ?>" />
					<textarea name="bpps-current-status-textarea" id="bpps-current-status-textarea"></textarea>
					<input type="submit" name="bpps_update_status_update" id="bpps_update_status_update" value="<?php esc_attr_e( 'Update', 'bp-profile-status' ); ?>">
					<input type="reset" name="bpps_cancel_update" id="bpps_cancel_update" value="<?php esc_attr_e( 'Cancel', 'bp-profile-status' ); ?>">
					<?php wp_nonce_field( 'bp-profile-action', 'nonce' ); ?>
				</form>
				<?php
				if ( ( get_current_user_id() === bp_displayed_user_id() ) ) {
					wp_nonce_field( 'bpps_delete_current_status_nonce', 'bpps_delete_current_status_nonce' );
					?>
					<a id="bpps-current-status-edit" title="<?php esc_attr_e( 'Edit Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-edit"></i>
					</a>
					<a id="bpps-current-status-delete" title="<?php esc_attr_e( 'Delete Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-trash"></i>
					</a>
					<?php
				} else {
					?>
					<a class="bpps-set-status" title="<?php esc_attr_e( 'Set as Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-yes"></i>
					</a>
					<?php
				}
			} else {
				$no_status_display = apply_filters( 'bpps_no_current_status_display', true );

				if ( true === $no_status_display ) {
					esc_html_e( 'No current status is set yet.', 'bp-profile-status' );
				}

				if ( ( get_current_user_id() === bp_displayed_user_id() ) ) {
					$status_link 	= bp_loggedin_user_domain() . 'profile/status/';
					$add_new_status = __( 'Add New Status', 'bp-profile-status' );
					?>
					<a href="<?php echo esc_attr( $status_link ); ?>" title="<?php echo esc_attr( $add_new_status ); ?>"><?php echo esc_html( $add_new_status ); ?></a>
					<?php
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Adding new status action
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @param   array   $post_array
	 */
	private function bpps_add_new_status_action( $post_array ) {

		if ( ! empty( $post_array ) && ( ( isset( $post_array['bpps_add_new_status'] ) && '' !== $post_array['bpps_add_new_status'] ) || ( isset( $post_array['bpps-current-status-textarea'] ) && '' !== $post_array['bpps-current-status-textarea'] ) ) ) {
			$user_id = get_current_user_id();

			if ( isset( $post_array['bpps_add_new'] ) ) {
				$this->bpps_store_status_usermeta( $user_id, $post_array );
			} elseif ( isset( $post_array['bpps_add_new_and_set'] ) ) {
				update_user_meta( $user_id, 'bpps_current_status', trim( $post_array['bpps_add_new_status'] ) );

				$this->bpps_store_status_usermeta( $user_id, $post_array );
				$this->bpps_add_status_activity( $user_id, $post_array );
			} elseif ( isset( $post_array['bpps_update_status_and_set'] ) ) {
				update_user_meta( $user_id, 'bpps_current_status', trim( $post_array['bpps_add_new_status'] ) );

				$this->bpps_update_status_in_usermeta( $post_array );
			} elseif ( isset( $post_array['bpps_update_status'] ) ) {
				$this->bpps_update_status_in_usermeta( $post_array );
			} elseif ( isset( $post_array['bpps_update_status_update'] ) ) {
				update_user_meta( $user_id, 'bpps_current_status', trim( $post_array['bpps-current-status-textarea'] ) );

				$this->bpps_update_status_in_usermeta( $post_array );
			}
		}

	}

	/**
	 * Adding new activity while adding status
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @param   int     $user_id
	 * @param   array   $post_array
	 */
	private function bpps_add_status_activity( $user_id, $post_array ) {

		$this_user_profile_url = bp_core_get_user_domain( $user_id );
		$action_string         = '<a href="' . esc_url( $this_user_profile_url ) . '">' . bp_core_get_username( $user_id ) . '</a> ' . __( 'added new status', 'bp-profile-status' );
		$params                = array(
			'action'    => $action_string,
			'content'   => $post_array['bpps_add_new_status'],
			'component' => 'activity',
			'type'      => 'bpps_activity_update',
		);

		bp_activity_add( $params );

	}

	/**
	 * Storing new statuses in user meta
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @param   int     $user_id
	 * @param   array   $post_array
	 */
	private function bpps_store_status_usermeta( $user_id, $post_array ) {

		$bpps_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

		if ( ! empty( $bpps_statuses ) ) {
			$bpps_status_count   = count( $bpps_statuses );
			$bpps_current_status = get_user_meta( $user_id, 'bpps_current_status', true );
			$key                 = false;

			if ( ! in_array( trim( $post_array['bpps_add_new_status'] ), $bpps_statuses, true ) ) {
				array_unshift( $bpps_statuses, trim( $post_array['bpps_add_new_status'] ) );
			}

			if ( $bpps_current_status ) {
				$key = array_search( $bpps_current_status, $bpps_statuses, true );
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

	/**
	 * Update status in usermeta
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 *
	 * @param   array   $post_array
	 */
	private function bpps_update_status_in_usermeta( $post_array ) {

		$user_id           = get_current_user_id();
		$bpps_old_statuses = get_user_meta( $user_id, 'bpps_old_statuses', true );

		if ( isset( $post_array['bpps_update_status_update'] ) ) {
			$key = array_search( trim( $post_array['bpps-current-status-org'] ), $bpps_old_statuses, true );

			$bpps_old_statuses[ $key ] = trim( $post_array['bpps-current-status-textarea'] );
		} elseif ( isset( $post_array['bpps_add_new_status'] ) ) {
			$key = array_search( trim( $post_array['bpps-edit-status-org'] ), $bpps_old_statuses, true );

			$bpps_old_statuses[ $key ] = trim( $post_array['bpps_add_new_status'] );
		}

		update_user_meta( $user_id, 'bpps_old_statuses', $bpps_old_statuses );

	}

	/**
	 * Displaying current status on member list
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function bpps_display_current_status_member_list() {

		$user_id     = bp_get_member_user_id();
		$bpps_status = get_user_meta( $user_id, 'bpps_current_status', true );
		?>
		<div class="bpps-status">
			<?php
			if ( $bpps_status ) {
				?>
				<span class="bpps-status-text"><?php echo esc_html( convert_smilies( $bpps_status ) ); ?></span>
				<?php
				if ( get_current_user_id() !== $user_id ) {
					?>
					<input class="bpps-status-org" type="hidden" value="<?php echo esc_attr( $bpps_status ); ?>" />
					<a class="bpps-set-status" title="<?php esc_attr_e( 'Set as Current Status', 'bp-profile-status' ); ?>">
						<i class="dashicons dashicons-yes"></i>
					</a>
					<?php
				}
			} else {
				$no_status_display = apply_filters( 'bpps_no_current_status_display', true );

				if ( true === $no_status_display ) {
					esc_html_e( 'No current status is set yet.', 'bp-profile-status' );
				}

				if ( ( get_current_user_id() === $user_id ) ) {
					$status_link 	= bp_loggedin_user_domain() . 'profile/status/';
					$add_new_status = __( 'Add New Status', 'bp-profile-status' );
					?>
					<a href="<?php echo esc_attr( $status_link ); ?>" title="<?php echo esc_attr( $add_new_status ); ?>"><?php echo esc_html( $add_new_status ); ?></a>
					<?php
				}
			}
			?>
		</div>
		<?php

	}

	/**
	 * Adding status nav in BuddyPress admin profile navigation
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @param   array       $wp_admin_nav
	 *
	 * @return  array|void
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

		// Add the "Profile" sub-nav item.
		$wp_admin_nav[] = array(
			'parent' => 'my-account-' . buddypress()->profile->id,
			'id'     => 'my-account-' . buddypress()->profile->id . '-status',
			'title'  => _x( 'Status', 'My Profile Status sub nav', 'bp-profile-status' ),
			'href'   => trailingslashit( $proflie_link . 'status' ),
		);

		return $wp_admin_nav;

	}

}
