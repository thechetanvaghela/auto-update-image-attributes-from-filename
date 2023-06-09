<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/thechetanvaghela
 * @since      1.0.0
 *
 * @package    Auto_Update_Image_Attributes_From_Filename
 * @subpackage Auto_Update_Image_Attributes_From_Filename/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Auto_Update_Image_Attributes_From_Filename
 * @subpackage Auto_Update_Image_Attributes_From_Filename/includes
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Auto_Update_Image_Attributes_From_Filename_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'auto-update-image-attributes-from-filename',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
