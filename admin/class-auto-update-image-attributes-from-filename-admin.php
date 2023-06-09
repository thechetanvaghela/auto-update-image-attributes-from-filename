<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/thechetanvaghela
 * @since      1.0.0
 *
 * @package    Auto_Update_Image_Attributes_From_Filename
 * @subpackage Auto_Update_Image_Attributes_From_Filename/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Auto_Update_Image_Attributes_From_Filename
 * @subpackage Auto_Update_Image_Attributes_From_Filename/admin
 * @author     Chetan Vaghela <ckvaghela92@gmail.com>
 */
class Auto_Update_Image_Attributes_From_Filename_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Auto_Update_Image_Attributes_From_Filename_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Auto_Update_Image_Attributes_From_Filename_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/auto-update-image-attributes-from-filename-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Auto_Update_Image_Attributes_From_Filename_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Auto_Update_Image_Attributes_From_Filename_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/auto-update-image-attributes-from-filename-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Update Image attributes
	 *
	 * @since    1.0.0
	 */
	public function auiaff_add_attachment_callback($post_id) 
	{
		# get value of enable auto update
		$enable_autoupdate = esc_attr(get_option('auiaff-enable-auto-update-attributes'));
		if(!empty($enable_autoupdate) && $enable_autoupdate == 'yes')
		{
			# Return if there is no image ID
			if( $post_id === NULL ) return;
			# get a information from the image
			$image_url			= wp_get_attachment_url($post_id);
			$image_extension 	= pathinfo($image_url);
			$image_name 		= basename($image_url, '.'.$image_extension['extension']);
			
			if ( !empty($image_name) ) 
			{
				# get value of remove char
				$remove_char = esc_attr(get_option('auiaff-remove-characters'));
				# get value of post number
				$remove_num = esc_attr(get_option('auiaff-remove-numbers'));
				# get value of post Spaces
				$remove_spaces = esc_attr(get_option('auiaff-remove-spaces'));
				
				# Remove characters, extra spaces and all numbers
				if(!empty($remove_char) && $remove_char == 'yes')
				{
					$remove_chars = array('.','-','_');
					$image_name = str_replace( $remove_chars, ' ', $image_name );
				}
				if(!empty($remove_num) && $remove_num == 'yes')
				{
					$image_name = preg_replace('/[0-9]+/', '', $image_name);
				}
				if(!empty($remove_spaces) && $remove_spaces == 'yes')
				{
					$image_name = preg_replace('/\s\s+/', ' ', $image_name);
					$image_name = trim($image_name);
				}
			}
			$imagedata			= array();
			$imagedata['ID'] 	= $post_id;
			$imagedata['post_title'] = sanitize_text_field($image_name);
			$imagedata['post_excerpt'] = sanitize_text_field($image_name);
			$imagedata['post_content'] = sanitize_text_field($image_name);	
			update_post_meta( $post_id, '_wp_attachment_image_alt', $image_name ); 
				
			$image_id = wp_update_post( $imagedata ); 
			if ( $image_id == 0 ) 
			{	
				return false;
			}
		}
		return;
	}

	/**
	 * Admin menu Page
	 *
	 * @since    1.0.0
	 */
	public function auiaff_setting_admin_menu() {

		add_menu_page('Auto Image Attributes','Auto Image Attributes','manage_options','auiaff_settings_page',array($this, 'auiaff_settings_page_callback' ),'dashicons-images-alt');
	}

	public function auiaff_settings_page_callback() {
			# define empty variables
			$form_msg = "";
			
			# if user can manage options
			if ( current_user_can('manage_options') ) {
				# submit form action
				if (isset($_POST['auiaff-form-settings'])) {
					# verifing nonce
					if ( ! isset( $_POST['auiaff_setting_field_nonce'] ) || ! wp_verify_nonce( $_POST['auiaff_setting_field_nonce'], 'auiaff_setting_action_nonce' ) ) {
						# form data not saved message
						$form_msg = '<b style="color:red;">Sorry, your nonce did not verify.</b>';
					} else {
						# Enable Auto image update
						if (isset($_POST['auiaff-enable-auto-update-attributes'])) {
							$enable_value = sanitize_text_field($_POST['auiaff-enable-auto-update-attributes']);
							$enable_value = !empty($enable_value) ? $enable_value : "no";
							# update Enable comment value option value
							update_option('auiaff-enable-auto-update-attributes', $enable_value);
						}

						# Remove Special characters
						if (isset($_POST['auiaff-remove-characters'])) {
							$remove_char = sanitize_text_field($_POST['auiaff-remove-characters']);
							$remove_char = !empty($remove_char) ? $remove_char : "no";
							update_option('auiaff-remove-characters', $remove_char);
						}
						# Remove Numbers
						if (isset($_POST['auiaff-remove-numbers'])) {
							$remove_num = sanitize_text_field($_POST['auiaff-remove-numbers']);
							$remove_num = !empty($remove_num) ? $remove_num : "no";
							update_option('auiaff-remove-numbers', $remove_num);
						}

						# Remove Spaces
						if (isset($_POST['auiaff-remove-spaces'])) {
							$remove_spaces = sanitize_text_field($_POST['auiaff-remove-spaces']);
							$remove_spaces = !empty($remove_spaces) ? $remove_spaces : "no";
							update_option('auiaff-remove-spaces', $remove_spaces);
						}

						# form data saved message
						$form_msg = '<b style="color:green;">Settings Saved.</b><br/>';
					}
				}
			}
			# get value of enable auto update
			$enable_autoupdate = esc_attr(get_option('auiaff-enable-auto-update-attributes'));
			# get value of remove char
			$remove_char = esc_attr(get_option('auiaff-remove-characters'));
			# get value of post number
			$remove_num = esc_attr(get_option('auiaff-remove-numbers'));
			# get value of post Spaces
			$remove_spaces = esc_attr(get_option('auiaff-remove-spaces'));
			?>
			<!-- auiaff Settings -->
			<div class="wrap">
				<h2><?php esc_html_e('Auto Update Image Attributes From Filename','auto-update-image-attributes-from-filename'); ?></h2>
				<div id="auiaff-setting-container">
					<div id="auiaff-body">
						<div id="auiaff-body-content">
							<div class="">
								<br/><?php _e($form_msg,'auto-update-image-attributes-from-filename'); ?><hr/><br/>
								<form method="post">
									<table>
										<tr valign="top">
											<th scope="row">
												<label for="auiaff-enable-auto-update-attributes"><?php _e('Enable Auto Update? &nbsp;&nbsp;&nbsp;','auto-update-image-attributes-from-filename'); ?></label></th>
											<td>	
												<?php $yes_checked = ($enable_autoupdate == "yes") ? 'checked="checked"' : "";?>
												<?php $no_checked = ($enable_autoupdate == "no") ? 'checked="checked"' : "";?>
												<input type="radio" name="auiaff-enable-auto-update-attributes" id="enable-yes" value="yes" <?php echo esc_attr($yes_checked); ?> ><label for="enable-yes"><?php _e('Yes','auto-update-image-attributes-from-filename'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="radio" name="auiaff-enable-auto-update-attributes" id="enable-no" value="no" <?php echo esc_attr($no_checked); ?>><label for="enable-no"><?php _e('No','auto-update-image-attributes-from-filename'); ?></label>
											</td>
										</tr>
									</table>
									<span><?php _e('Enable Auto Update Image attributes on new image upload','auto-update-image-attributes-from-filename'); ?></span>
									<br/><hr><br/>
									<?php 
									if(!empty($enable_autoupdate) && $enable_autoupdate == 'yes')
									{ 	?>
										<!-- Remove characters Section -->
										<table>
											<tr valign="top">
												<th scope="row">
													<label for="auiaff-remove-characters"><?php _e('Remove characters &nbsp;&nbsp;&nbsp;&nbsp;','auto-update-image-attributes-from-filename'); ?></label></th>
												<td>	
													<?php $yes_checked = ($remove_char == "yes") ? 'checked="checked"' : "";?>
													<?php $no_checked = ($remove_char == "no") ? 'checked="checked"' : "";?>
													<input type="radio" name="auiaff-remove-characters" id="remove_char-yes" value="yes" <?php echo esc_attr($yes_checked); ?> ><label for="remove_char-yes"><?php _e('Yes','auto-update-image-attributes-from-filename'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="auiaff-remove-characters" id="remove_char-no" value="no" <?php echo esc_attr($no_checked); ?>><label for="remove_char-no"><?php _e('No','auto-update-image-attributes-from-filename'); ?></label>
												</td>
											</tr>
										</table>
										<span><?php _e('Remove Special characters (Dots, hyphens, underscores) from Image file name','auto-update-image-attributes-from-filename'); ?></span>
										<br/><hr><br/>
										<!-- Remove characters Section end -->

										<!-- Remove Number Section -->
										<table>
											<tr valign="top">
												<th scope="row">
													<label for="auiaff-remove-numbers"><?php _e('Remove Numbers &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','auto-update-image-attributes-from-filename'); ?></label></th>
												<td>	
													<?php $yes_checked = ($remove_num == "yes") ? 'checked="checked"' : "";?>
													<?php $no_checked = ($remove_num == "no") ? 'checked="checked"' : "";?>
													<input type="radio" name="auiaff-remove-numbers" id="remove_num-yes" value="yes" <?php echo esc_attr($yes_checked); ?> ><label for="remove_num-yes"><?php _e('Yes','auto-update-image-attributes-from-filename'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="auiaff-remove-numbers" id="remove_num-no" value="no" <?php echo esc_attr($no_checked); ?>><label for="remove_num-no"><?php _e('No','auto-update-image-attributes-from-filename'); ?></label>
												</td>
											</tr>
										</table>
										<span><?php _e('Remove numbers from Image file name','auto-update-image-attributes-from-filename'); ?></span>
										<br/><hr><br/>
										<!-- Remove Number Section end -->

										<!-- Remove Space Section -->
										<table>
											<tr valign="top">
												<th scope="row">
													<label for="auiaff-remove-spaces"><?php _e('Remove Extra Spaces &nbsp;&nbsp;&nbsp;','auto-update-image-attributes-from-filename'); ?></label></th>
												<td>	
													<?php $yes_checked = ($remove_spaces == "yes") ? 'checked="checked"' : "";?>
													<?php $no_checked = ($remove_spaces == "no") ? 'checked="checked"' : "";?>
													<input type="radio" name="auiaff-remove-spaces" id="remove_num-yes" value="yes" <?php echo esc_attr($yes_checked); ?> ><label for="remove_num-yes"><?php _e('Yes','auto-update-image-attributes-from-filename'); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
													<input type="radio" name="auiaff-remove-spaces" id="remove_num-no" value="no" <?php echo esc_attr($no_checked); ?>><label for="remove_num-no"><?php _e('No','auto-update-image-attributes-from-filename'); ?></label>
												</td>
											</tr>
										</table>
										<span><?php _e('Remove extra spaces from Image file name','auto-update-image-attributes-from-filename'); ?></span>
										<br/><hr><br/>
										<!-- Remove Space Section end -->
										<?php 
									} ?>
									<?php wp_nonce_field( 'auiaff_setting_action_nonce', 'auiaff_setting_field_nonce' ); ?>
									<?php  submit_button( 'Save Settings', 'primary', 'auiaff-form-settings'  ); ?>
								</form>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
			</div>
		<?php

	}

}
