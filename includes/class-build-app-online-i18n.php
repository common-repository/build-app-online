<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://buildapp.online
 * @since      1.0.0
 *
 * @package    Build_App_Online
 * @subpackage Build_App_Online/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Build_App_Online
 * @subpackage Build_App_Online/includes
 * @author     Abdul Hakeem <hakeem.nala@gmail.com>
 */
class Build_App_Online_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'build-app-online',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
