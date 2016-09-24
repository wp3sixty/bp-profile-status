<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 *
 * @package           BP_Profile_Status
 *
 * @wordpress-plugin
 * Plugin Name:       BP Profile Status
 * Plugin URI:        https://github.com/wp3sixty/bp-profile-status/
 * Description:       Profile Status for BuddyPress.
 * Version:           1.3.0
 * Author:            wp3sixty
 * Author URI:        http://wp3sixty.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bp-profile-status
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bp-profile-status.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bp_profile_status() {

	$bp_profile_status = new BP_Profile_Status();

	$bp_profile_status->run();

}
run_bp_profile_status();
