<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://salonmanager.us
 * @since      1.0.0
 *
 * @package    Smappointmentbooker
 * @subpackage Smappointmentbooker/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Smappointmentbooker
 * @subpackage Smappointmentbooker/includes
 * @author     Thang Cao <bobuchacha@gmail.com>
 */
class Smappointmentbooker_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'smappointmentbooker',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
