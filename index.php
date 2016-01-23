<?php

/*
 * Plugin Name: BP Profile Status
 * Plugin URI: https://github.com/wp3sixty/bp-profile-status/?utm_source=dashboard&utm_medium=plugin&utm_campaign=bp-profile-status
 * Description: Profile Status for BuddyPress.
 * Version: 1.2
 * Author: wp3sixty
 * Author URI: https://github.com/wp3sixty?utm_source=dashboard&utm_medium=plugin&utm_campaign=bp-profile-status
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bp-profile-status
 */

/**
 *  The server file system path to the plugin directory
 */
if( !defined( 'BPPS_PATH' ) ) {
    define( 'BPPS_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The url to the plugin directory
 */
if( !defined( 'BPPS_URL' ) ) {
    define( 'BPPS_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * The base name of the plugin directory
 */
if( !defined( 'BPPS_BASE_NAME' ) ) {
    define( 'BPPS_BASE_NAME', plugin_basename( __FILE__ ) );
}

/**
 * The version of the plugin
 */
if( !defined( 'BPPS_VERSION' ) ) {
    define( 'BPPS_VERSION', '1.2' );
}

/**
 * Register the autoloader function into spl_autoload
 */
spl_autoload_register( 'bpps_autoloader' );

/**
 * Auto Loader Function
 *
 * Autoloads classes on instantiation. Used by spl_autoload_register.
 *
 * @param string $class_name The name of the class to autoload
 */
function bpps_autoloader( $class_name ) {
    $rtlibpath = array(
        'includes/' . $class_name . '.php',
        'includes/main/' . $class_name . '.php'
    );

    foreach( $rtlibpath as $path ) {
        $path = BPPS_PATH . $path;

        // Checking if file_exists or not
        if( file_exists( $path ) ) {
            // Including class file
            include $path;

            break;
        }
    }
}

/*
 * main class object
 */

new BPPS_Main();
