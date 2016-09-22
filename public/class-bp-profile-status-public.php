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
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
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

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->suffix       = $this->bpps_get_script_style_suffix();

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
			'bpps_max_character_alert'              => esc_html__( 'You have reached max character limit. \n\nPlease revise it.!', 'bp-profile-status' ),
			'bpps_update'                           => esc_html__( 'Update', 'bp-profile-status' ),
			'bpps_update_and_set_as_current'        => esc_html__( 'Update & Set as Current', 'bp-profile-status' ),
			'bpps_delete_current_status_confirm'    => esc_html__( 'Are you sure you want to delete current status?', 'bp-profile-status' ),
			'bpps_delete_current_status_success'    => esc_html__( 'Current status deleted successfully.!', 'bp-profile-status' ),
			'bpps_no_current_status_set'            => esc_html__( 'No current status is set yet.', 'bp-profile-status' ),
			'bpps_delete_status_confirm'            => esc_html__( 'Are you sure you want to delete this status?', 'bp-profile-status' ),
			'bpps_status_delete_success'            => esc_html__( 'Status deleted successfully.!', 'bp-profile-status' ),
			'bpps_status_set_success'               => esc_html__( 'Status set successfully.!', 'bp-profile-status' ),
		);

		wp_localize_script( $this->plugin_name, 'bpps_main_js', array(
			'set_current_status_nonce' => wp_create_nonce( 'set_current_status_nonce' ),
			'i18n'  => $bpps_localize_array,
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
		$proflie_link   = trailingslashit( $user_domain . 'profile' );
		$bpps_status    = array(
			'name'              => esc_html__( 'Status', 'bp-profile-status' ), // Display name for the nav item
			'slug'              => 'status', // URL slug for the nav item
			'parent_slug'       => 'profile', // URL slug of the parent nav item
			'parent_url'        => $proflie_link, // URL of the parent item
			'item_css_id'       => 'bpps-status', // The CSS ID to apply to the HTML of the nav item
			'user_has_access'   => true, // Can the logged in user see this nav item?
			'site_admin_only'   => false, // Can only site admins see this nav item?
			'position'          => 80, // Index of where this nav item should be positioned
			'screen_function'   => array( $this, 'settings_ui' ), // The name of the function to run when clicked
			'link'              => '',// The link for the subnav item; optional, not usually required.
		);

		// Adding Status menu to BuddyPress profile navigation.
		bp_core_new_subnav_item( $bpps_status );

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'bp-profile-action' ) ) {
			$this->bpps_add_new_status_action( wp_unslash( $_POST ) );
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

}
