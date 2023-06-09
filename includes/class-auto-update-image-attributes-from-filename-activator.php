<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/thechetanvaghela
 * @since      1.0.0
 *
 * @package    Auto_Update_Image_Attributes_From_Filename
 * @subpackage Auto_Update_Image_Attributes_From_Filename/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Auto_Update_Image_Attributes_From_Filename
 * @subpackage Auto_Update_Image_Attributes_From_Filename/includes
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Auto_Update_Image_Attributes_From_Filename_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		# Default options
		update_option('auiaff-enable-auto-update-attributes','yes');
		update_option('auiaff-remove-characters','yes');
		update_option('auiaff-remove-numbers','yes');
		update_option('auiaff-remove-spaces','yes');
	}

}
