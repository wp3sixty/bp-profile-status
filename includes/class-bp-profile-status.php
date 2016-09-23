<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across the
 * public-facing side of the site.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, and public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / includes
 */
class BP_Profile_Status {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     BP_Profile_Status_Loader    $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     string      $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  protected
	 *
	 * @var     string      $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the public-facing
	 * side of the site.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function __construct() {

		$this->plugin_name  = 'bp-profile-status';
		$this->version      = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();
		$this->define_public_ajax_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - BP_Profile_Status_Loader.      Orchestrates the hooks of the plugin.
	 * - BP_Profile_Status_i18n.        Defines internationalization functionality.
	 * - BP_Profile_Status_Public.      Defines all hooks for the public side of the site.
	 * - BP_Profile_Status_Public_Ajax. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-profile-status-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bp-profile-status-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-profile-status-public.php';

		/**
		 * The class responsible for defining all ajax actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-bp-profile-status-public-ajax.php';

		$this->loader = new BP_Profile_Status_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the BP_Profile_Status_i18n class in order to set the domain and to register
	 * the hook with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function set_locale() {

		$plugin_i18n = new BP_Profile_Status_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  private
	 */
	private function define_public_hooks() {

		$plugin_public = new BP_Profile_Status_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		/**
		 * Detect plugin. For use on Front End only.
		 */
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Checking if BuddyPress plugin is activated.
		if ( true === is_plugin_active( 'buddypress/bp-loader.php' ) ) {
			$this->loader->add_action( 'bp_init', $plugin_public, 'bpps_add_profile_status_menu' );
			$this->loader->add_action( 'bp_template_content', $plugin_public, 'bpps_content' );
			$this->loader->add_action( 'bp_before_member_header_meta', $plugin_public, 'bpps_display_current_status' );
			$this->loader->add_action( 'bp_directory_members_item', $plugin_public, 'bpps_display_current_status_member_list' );

			$this->loader->add_filter( 'bp_settings_admin_nav', $plugin_public, 'bpps_profile_status_nav' );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function run() {

		$this->loader->run();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  string  The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;

	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  BP_Profile_Status_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @return  string  The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	}

	private function define_public_ajax_hooks() {

		$plugin_public_ajax = new BP_Profile_Status_Public_Ajax();

		$this->loader->add_action( 'wp_ajax_bpps_delete_current_status', $plugin_public_ajax, 'bpps_delete_current_status' );
		$this->loader->add_action( 'wp_ajax_bpps_delete_status', $plugin_public_ajax, 'bpps_delete_status' );
		$this->loader->add_action( 'wp_ajax_bpps_set_current_status', $plugin_public_ajax, 'bpps_set_current_status' );

	}

}
