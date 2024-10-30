<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://buildapp.online
 * @since      1.0.0
 *
 * @package    Build_App_Online
 * @subpackage Build_App_Online/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Build_App_Online
 * @subpackage Build_App_Online/includes
 * @author     Abdul Hakeem <hakeem.nala@gmail.com>
 */
class Build_App_Online_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

	  // Remove the rewrite rule on deactivation
	  global $wp_rewrite;
	  $wp_rewrite->flush_rules();

	}

}
