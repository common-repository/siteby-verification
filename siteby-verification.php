<?php
/*
Plugin Name: SiteBy Verification
Description: Allows you to add your site verification tag to your WordPress site.
Version: 1.2
Author: SiteBy

Copyright 2017 SiteBy (email : support@siteby.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
    
*/
if (!class_exists("siteby_verification")) {
	class siteby_verification{
		//the constructor that initializes the class
		function siteby_verification() {
            
		}
		
		function sb_save_wonderful_metas($post_id) {
			// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
			// to do anything
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
			//stop values submitting if this is a "quick edit"
			if(!isset($_POST['sbvstopsubmit'])) return $post_id;
			
            $sbvCode = isset($_POST['sbvcode']) ? $_POST['sbvcode'] : '';
			add_post_meta($post_id, '_sb_v_code', $sbvCode, true) or update_post_meta($post_id, '_sb_v_code', $sbvCode);
		}
		
		function sb_show_on_website(){
			global $post;
			if(!is_404()){
				$isImplemeted = false;
				$verification_code = "";
				
				if(is_home() || is_front_page()){                    
                    $verification_code = (get_post_meta($post->ID, '_sb_v_code', true)!='') ? get_post_meta($post->ID, '_sb_v_code', true) : get_option('verification_code');
                    
					if($verification_code){
						$isImplemeted = true;
					}
				}

				if($isImplemeted){
					echo '<meta name="siteby-verification" content="'. htmlentities(stripslashes($verification_code),ENT_COMPAT,"UTF-8") .'" />' . "\n";
				}
			}
		}	
	}
	
	//initialize the class to a variable
	$sb_meta_var = new siteby_verification();
	

	
	//Actions and Filters	
	if (isset($sb_meta_var)) {
		//Actions
		add_action("save_post",array(&$sb_meta_var,'sb_save_wonderful_metas'));
		add_action("wp_head",array(&$sb_meta_var,'sb_show_on_website'));
		add_action('admin_init', 'register_default_meta_settings' );
		add_action('admin_menu', 'sb_add_wonder_box');
		
		function sb_add_wonder_box() {			
			add_options_page('SiteBy Verification defaults', 'SiteBy Verification', 'manage_options', 'sb_vc', 'sb_display_tag_options');	
		}
	}
	
	/**
	 * register_default_meta_settings
	 *
	 * Is run when the plugin is first installed.  It adds options into the
	 * wp-options 
	 */
	function register_default_meta_settings()
	{
		register_setting( 'siteby-verification-settings', 'verification_code' );
	}
	
	
	/**
	 * sb_display_tag_options()
	 *
	 * Paints the tag_options page
	 */	 
	function sb_display_tag_options()
	{
		?>
		<div class="wrap">
		<h2>SiteBy Verification Options</h2>
		<p>Copy the verification code from the Site Verification page and paste in below.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'siteby-verification-settings' ); ?>
			<table class="form-table">
				<tr valign="top">
                    <th scope="row">Verification Code</th>
					<td scope="row"><textarea rows="2" cols="40" style="max-width:420px; width: 100%" name="verification_code" ><?php echo get_option('verification_code'); ?></textarea></td>
				</tr>			
			</table>
			
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div>		
<?php }
}
?>