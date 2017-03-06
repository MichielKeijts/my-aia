<?php
/*
Plugin Name: Members page only for logged in users
Description: Only logged in users can view the members page. Non logged in users will be redirected to either register/login page.
Version: 1.4.1
Author: Narendran TS
Modified by: Bernard Bos
Author URI: https://twitter.com/tnarendran/
Plugin URI: https://twitter.com/tnarendran/

Copyright 2016  Narendran TS (https://twitter.com/tnarendran/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
ob_start();
add_action('admin_menu', 'members_page_only_for_logged_in_users_option_menu');

function members_page_only_for_logged_in_users_option_menu()
{
   add_submenu_page('tools.php', __('Members page only for logged in users','BPMO'), __('Members page only for logged in users','BPMO'), 'level_10', 'bpmemberonly', 'members_page_only_for_logged_in_users_setting');
}

function members_page_only_for_logged_in_users_setting()
{
		global $wpdb;
		$m_bpmoregisterpageurl = get_option('registerpageurl');

		if (isset($_POST['submitnew']))
		{
			if (isset($_POST['registerpageurl']))
			{
				$m_registerpageurl = $wpdb->escape($_POST['registerpageurl']);
			}
				
				update_option('registerpageurl',$m_registerpageurl);
			
			members_page_only_for_logged_in_users_message("Changes saved.");
		}
		echo "<br />";

		$saved_register_page_url = get_option('registerpageurl');
		?>
		
	<div class="wrap">

		<h1><?php _e( 'Members Page Only for Logged In Users Settings', 'buddypress' ); ?> </h1>

		<form id="form" name="form" action="" method="POST">
	
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="registerpageurl"><?php _e( 'Register page url', 'buddypress' ); ?></label>
					</th>
					<td>
						<input id="registerpageurl" name="registerpageurl" type="text" size="70" value="<?php  echo $saved_register_page_url; ?>" /><br />
						<p class="description">https://www.athletesinaction.nl/mijn-aia/join-us</p>
					</td>
				</tr>				
			</tbody>
		</table>
		
			<p class="submit">
				<input type="submit" name="submitnew" id="submitnew" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'buddypress' ); ?>" />
			</p>
			
	</form>	
			
	</div>		
		
		<?php
		}				

	
function members_page_only_for_logged_in_users_message($p_message)
{

	echo "<div id='message' class='updated fade'>";

	echo $p_message;

	echo "</div>";

}

function members_page_only_for_logged_in_users()
{
	if (is_front_page()) return;
	if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
	{
		if ( bp_is_register_page() || bp_is_activation_page() )
		{
			return;
		}
	}
	$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
         if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 

	       $current_url = str_ireplace('https://','',$current_url);
         }else{
                 $current_url = str_ireplace('http://','',$current_url);
         }
	$current_url = str_ireplace('www.','',$current_url);
	$saved_register_page_url = get_option('registerpageurl');
         if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 

	      $saved_register_page_url = str_ireplace('https://','',$saved_register_page_url);

        }else{
               $saved_register_page_url = str_ireplace('http://','',$saved_register_page_url);

      }
	$saved_register_page_url = str_ireplace('www.','',$saved_register_page_url);
	
	if (stripos($current_url,$saved_register_page_url) === false)
	{

	}
	else 
	{
		return;
	}
	//Naren - start
	
	if ( is_user_logged_in() == false && ( bp_is_activity_component() || bp_is_groups_component() || bp_is_forums_component() || bp_is_blogs_component() || bp_is_page( BP_MEMBERS_SLUG ) || strpos($current_url,'/profile/')==true || strpos($current_url,'/friends/')==true || strpos($current_url,'/following/')==true || strpos($current_url,'/followers/')==true))
	{
		if (empty($saved_register_page_url))
		{
			$current_url = $_SERVER['REQUEST_URI'];
			//$redirect_url = wp_login_url( get_option('siteurl').$current_url );
			$redirect_url = wp_login_url( );
			header( 'Location: ' . $redirect_url );
			die();			
		}
		else 
		{
                         if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 

			$saved_register_page_url = 'https://'.$saved_register_page_url;

                          }else{
                            $saved_register_page_url = 'http://'.$saved_register_page_url;
                         }
			header( 'Location: ' . $saved_register_page_url );
			die();
		}
	}
}

if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
{
	add_action('wp','members_page_only_for_logged_in_users');
}
else 
{
	add_action('wp_head','members_page_only_for_logged_in_users');
}