<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since 1.5.2
 *
 * @package    BP_Profile_Status
 * @subpackage BP_Profile_Status / admin
 */


/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version.
 *
 * @since 1.5.2
 *
 * @package    BP_Profile_Status
 * @subpackage BP_Profile_Status / admin
 */
class BP_Profile_Status_Admin {


	/**
	 * The ID of this plugin.
	 *
	 * @since 1.5.2
	 *
	 * @access private
	 *
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 *
	 * @since 1.5.2
	 *
	 * @access private
	 *
	 * @var string $version The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.5.2
	 *
	 * @access public
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}


	/**
	 * Add admin notice if BuddyPress plugin is not active.
	 *
	 * @since 1.5.2
	 *
	 * @access public
	 */
	public function bpps_add_admin_notice() {

		if ( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {

			?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
					/* translators: Placeholders: %1$s - <b>, %2$s - </b>, %3$s - <b>, %4$s - <a>, %5$s - </a>, %6$s - </b> */
					echo sprintf( __( '%1$sBP Profile Status%2$s requires %3$s%4$sBuddyPress%5$s%6$s plugin to be activated. Deactivating BP Profile Status plugin.', 'bp-profile-status' ), '<b>', '</b>', '<b>', '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">', '</a>', '</b>' );
					?>
				</p>
			</div>
			<?php

			deactivate_plugins( 'bp-profile-status/bp-profile-status.php' );
		}
	}

}
