<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / includes
 */
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since       1.0.0
 *
 * @package     BP_Profile_Status
 * @subpackage  BP_Profile_Status / includes
 */
class BP_Profile_Status_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 *
	 * @access  public
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bp-profile-status',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
