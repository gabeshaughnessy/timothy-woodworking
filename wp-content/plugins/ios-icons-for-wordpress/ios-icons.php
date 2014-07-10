<?php
/*
Plugin Name: IOS Icons for Wordpress
Plugin URI: http://www.hebeisenconsulting.com
Description:  IOS Icons for Wordpress
Version: 1.2.1
Author: Hebeisen Consulting - R Bueno
Author URI: http://www.hebeisenconsulting.com
License: A "Slug" license name e.g. GPL2
*/
/*  Copyright 2011 Hebeisen Consulting

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
//Administrator menu
add_action('admin_menu', 'ios_icons_menu');
add_action('wp_head', 'ios_icons_head');

//Define plugin path
define('WP_PLUGIN_URL', ABSPATH . 'wp-content/plugins/ios-icons-for-wordpress');
define('WP_PLUGIN_TARGET_PATH', ABSPATH . '/wp-content/plugins/ios-icons-for-wordpress/');
define('WP_PLUGIN_TARGET_FILE', get_bloginfo('siteurl') . '/wp-content/plugins/ios-icons-for-wordpress/');

//plugin installation
//create new table upon activating plugin
function ios_icons_install()
{
    global $wpdb;
    $table = $wpdb->prefix . "ios_icons";
    	if($wpdb->get_var("show tables like '$table'") != $table) {				
		 $sql = "CREATE TABLE " . $table . " (
			id int(11) NOT NULL AUTO_INCREMENT,
			title varchar(13) NOT NULL,
			img_name varchar(250) NOT NULL,
			img_url varchar(250) NOT NULL,
			effect int(1) NOT NULL,
			used int(1) NOT NULL,
			PRIMARY KEY (id)
			)";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		  dbDelta($sql);
	}
}

register_activation_hook(__FILE__,'ios_icons_install');

//Plugin uninstall
//Drop table
function ios_icons_deactivate()
{
	global $wpdb;
	$table = $wpdb->prefix . "ios_icons";
	if($wpdb->get_var("show tables like '$table'") == $table) 
		{
			//$sql = "DROP TABLE IF EXISTS". $table;
			$wpdb->query("DROP TABLE IF EXISTS $table");
		}	
}

register_deactivation_hook(__FILE__, 'ios_icons_deactivate' );



//Wordpress admin menu
function ios_icons_menu()
{
	$page = add_options_page('iOS Icon for Wordpress', 'iOS Icon for Wordpress', 'manage_options', 'ios-icon', 'ios_icon_option');	
	//add action to insert javascript in admin page <head> only when plugin is trigerred
	add_action("admin_print_scripts-" . $page, "ios_icons_head");
}

//Wordpress admin additional Javascript for uploadify to work
function ios_icons_head()
{
	global $wpdb;
	$icon = $wpdb->get_results("SELECT * FROM wp_ios_icons WHERE used = '1'");
				foreach ( $icon as $icon )
					{
						$icon_preview = $icon->img_url;
						$icon_title = $icon->title;
						$icon_effect = $icon->effect;
					}		
		echo '<!-- IOS Icon for Wordpress -->';
		echo "\n";
		if( $icon_effect == "0")
		{
			echo "\t" . '<link rel="apple-touch-icon-precomposed" href="'. $icon_preview .'" />';
		}
		else
		{
			echo "\t" . '<link rel="apple-touch-icon" href="'. $icon_preview .'" />';
		}	
		if( $icon_title != "" )
		{
		echo "\n";
?>
		<script type="text/javascript">
		
		var iconName = <?php echo $icon_title; ?>
		
		  if( navigator.userAgent.match(/iPhone/i) || 		
		      navigator.userAgent.match(/iPod/i) || 		
		      navigator.userAgent.match(/iPad/i)		
		    ) {
		         document.title = "<?php echo $icon_title; ?>";		
		      }
		</script>
<?php
		}		
		echo "\n";	
		echo '<!-- IOS Icon for Wordpress -->';			 	
}

//Wordpress Admin section
function ios_icon_option()
{
	global $wpdb;
	switch ( $_GET['a'] )
		{
			case 'upload':
				
				// *** Include the class
				include( ABSPATH . "wp-content/plugins/ios-icons-for-wordpress/resize_class.php");
				
				//Prepare initial data needed
				
				$fileName = $_FILES['userfile']['name'];
				$tmpName = $_FILES['userfile']['tmp_name'];
				$fileSize = $_FILES['userfile']['size'];
				$fileType = $_FILES['userfile']['type'];
				
				//Random image name
				$image_name = md5(rand());									
				
				if( $_FILES['userfile']['name'] == "")
				{
					echo '<div id="message" class="updated fade"><p>No image file uploaded!.</p></div>';
				}
				else
				{
				
					//determine image type
					 if(exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_GIF )
					 {
					  $target_path = WP_PLUGIN_TARGET_PATH . "/icons/raw/" . $image_name . ".gif"; 
					  $image = $image_name . ".gif";
					  $url = WP_PLUGIN_TARGET_FILE . "/icons/final/" . $image_name . ".gif";
					 }
					 
					 if(exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_JPEG )
					 {
					  $target_path = WP_PLUGIN_TARGET_PATH . "/icons/raw/" . $image_name . ".jpeg"; 
					  $image = $image_name . ".jpeg";
					  $url = WP_PLUGIN_TARGET_FILE . "/icons/final/" . $image_name . ".jpeg";
					 }
					 
					 if(exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_PNG )
					 {
					  $target_path = WP_PLUGIN_TARGET_PATH . "/icons/raw/" . $image_name . ".png"; 
					  $image = $image_name . ".png";
					  $url = WP_PLUGIN_TARGET_FILE . "/icons/final/" . $image_name . ".png";
					 }
				
				//Check file type
				if( (exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_JPEG ) || (exif_imagetype($_FILES['userfile']['tmp_name']) == IMAGETYPE_PNG ))
					{
						
						//check if overlay image is selected
						/*if( $_POST['overlay'] == "")
						{
							echo '<div id="message" class="updated fade"><p>Please select overlay image!.</p></div>';					
						}
						else
						{*/
							//Upload
							if(move_uploaded_file($_FILES['userfile']['tmp_name'], $target_path)) 
								{				  
									//Do Nothing
								}
							else
								{
								echo '<div id="message" class="updated fade"><p>There was an error uploading the file, please try again!.</p></div>';				   
								}					
							
							 //Image resize
							 $resizeObj = new resize($target_path);
							 
							 $resizeObj -> resizeImage(114, 114, 'crop');
							 
							 $resizeObj -> saveImage( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image , 100);
							 
							 //check first what type of extension
							 //if not png, convert						 
							/* if(exif_imagetype( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image ) == IMAGETYPE_GIF )
							 {
							 	$overlayImage = imagecreatefrompng( WP_PLUGIN_TARGET_PATH . "/overlay/demo.png");
							 	$imgObject = imagecreatefromgif( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image );
							 	//$mergeImage = imagecopy( $imgObject, $overlayImage, 0,0,0,0,114,114);
							 	imagepng( $imgObject, WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image_name . ".png");
							 	
							 	$imgObjectFinal = imagecreatefrompng( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image_name . ".png" );
							 	imagecopy( $imgObjectFinal, $overlayImage, 0,0,0,0,114, 114);
							 	imagepng( $imgObjectFinal, WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image_name . ".png");
							 	
							 	$imgUrl = WP_PLUGIN_TARGET_FILE .  "icons/final/" . $image_name . ".png";
							 }*/
							 
							 if(exif_imagetype( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image ) == IMAGETYPE_JPEG )
							 {
							 	$overlayImage = imagecreatefrompng(WP_PLUGIN_TARGET_PATH . "/overlay/demo.png");
							 	//$overlayImage = imagecreatefrompng(WP_PLUGIN_TARGET_PATH . "/overlay/1.png");
							 	$imgObject = imagecreatefromjpeg( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image );
							 	//$mergeImage = imagecopy( $imgObject, $overlayImage, 0,0,0,0,114, 114); 
							 	imagepng($imgObject, WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image_name . ".png");
							 	$imgUrl = WP_PLUGIN_TARGET_FILE .  "icons/final/" . $image_name . ".png";
							 	$imgName = $image_name . ".png";
							 }					 
							 
							 if(exif_imagetype( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image ) == IMAGETYPE_PNG )
							 {
							 	$overlayImage = imagecreatefrompng(WP_PLUGIN_TARGET_PATH . "/overlay/demo.png");
							 	//$overlayImage = imagecreatefrompng(WP_PLUGIN_TARGET_PATH . "/overlay/1.png");
							 	$imgObject = imagecreatefrompng( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image );
							 	//$mergeImage = imagecopy( $imgObject, $overlayImage, 0,0,0,0,114, 114); 
							 	imagepng($imgObject, WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $image_name . ".png");
							 	$imgUrl = WP_PLUGIN_TARGET_FILE .  "icons/final/" . $image_name . ".png";
							 	$imgName = $image_name . ".png";
							 }
							 
							 //Time to save to database
							 //first, update all 'used' column to 0
							 $wpdb->query( "UPDATE wp_ios_icons SET used = '0' WHERE used = '1'" );
							 
							 //insert database
							 $wpdb->insert( 'wp_ios_icons', array(
							 				'title' => $_POST['title'],
							 				'img_url' => $imgUrl,
											'img_name' => $imgName,
											'used' => '1' )
											);
							 echo '<div id="message" class="updated fade"><p>New icon has been uploaded.</p></div>';
						//}	 
					}
				else
					{
		  	 
					  	//unrecognised, declare error and stop operation
					   	echo '<div id="message" class="updated fade"><p>File type is not supported. Make sure it is either .PNG and .JPEG only.</p></div>';
					  }
				}			
			break;
			case'with-effect':
				echo '<div id="message" class="updated fade"><p>Success!</div>';
				$wpdb->query( "UPDATE wp_ios_icons SET effect = '1' WHERE used = '1'" );
			break;
			case'without-effect':
				echo '<div id="message" class="updated fade"><p>Success!</div>';
				$wpdb->query( "UPDATE wp_ios_icons SET effect = '0' WHERE used = '1'" );
			break;
		}
		
		$icon = $wpdb->get_results("SELECT * FROM wp_ios_icons WHERE used = '1'");
				foreach ( $icon as $icon )
					{
						$icon_title = $icon->title;
					}
?>
	<div class="wrap">
	 <h2>Welcome to iOS Icon for Wordpress</h2>
		<div class="postbox" style = "padding: 10px;">
		<h2>Upload Icon</h2>	
		<form method="post" enctype="multipart/form-data" action = "options-general.php?page=ios-icon&a=upload">	
			<table width="700px" border="0" cellpadding="5" cellspacing="1" class="box" align = "center">
			<tr> 
				<td>
					Icon Title (Maximum 12 character):
				</td>
				<td>
					 <input name="title" type="text" maxlength="12" value="<?php echo $icon_title; ?>"> 
				</td>
			</tr>
			<tr> 
				<td>
					Upload icon: 
				</td>
				<td>
					<input name="userfile" type="file" id="userfile"> 
				</td>
				<td width="80"><input name="upload" type="submit" class="box" id="upload_image_button" value=" Upload "></td>
			</tr>
		</table>
		</form>
		</div>
		
<?php
	global $wpdb;
	$record = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_ios_icons WHERE used = '1';"));
		if ( $record != "0" )
			{
?>		<div class="postbox" style = "padding: 10px;">
				<table width = "700px">
				 <tr>
				  <td colspan="4">Select Icon View</td>
				 </tr>
<?php
			$icon = $wpdb->get_results("SELECT * FROM wp_ios_icons WHERE used = '1'");
				foreach ( $icon as $icon )
					{
						$icon_preview = $icon->img_url;
						$icon_name = $icon->img_name;
						$used = $icon->used; 
					}
				$overlayImage = imagecreatefrompng(WP_PLUGIN_TARGET_PATH . "/overlay/demo.png");
				$imgObject = imagecreatefrompng( WP_PLUGIN_TARGET_PATH .  "/icons/final/" . $icon_name );	
				imagecopy( $imgObject, $overlayImage, 0,0,0,0,114, 114);
				imagepng($imgObject, WP_PLUGIN_TARGET_PATH .  "/admin-preview/" . $icon_name );								
?>					
				<tr>
					<td>
						<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="114" height="114" id="overlay" align="middle">
						<param name="movie" value="<?php echo WP_PLUGIN_TARGET_FILE .  "overlay/overlay.swf"; ?>" />
						<param name=FlashVars value="pic=<?php echo WP_PLUGIN_TARGET_FILE .  "admin-preview/" . $icon_name; ?>">
						<!--[if !IE]>-->
						<object type="application/x-shockwave-flash" data="<?php echo WP_PLUGIN_TARGET_FILE .  "overlay/overlay.swf"; ?>" width="114" height="114">
							<param name="movie" value="<?php echo WP_PLUGIN_TARGET_FILE .  "overlay/overlay.swf"; ?>" />
							<param name=FlashVars value="pic=<?php echo WP_PLUGIN_TARGET_FILE .  "admin-preview/" . $icon_name; ?>">
						<!--<![endif]-->
							<a href="http://www.adobe.com/go/getflash">
								<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
							</a>
						<!--[if !IE]>-->
						</object>
						<!--<![endif]-->
						</object>	
					</td>
					<td valign="middle">	
						<input type="radio" name="effect" value="with_effect" style="margin: auto;" onClick="location.href='options-general.php?page=ios-icon&a=with-effect';">	With Effect			
					</td>
					<td>
						<img src = "<?php echo WP_PLUGIN_TARGET_FILE .  "admin-preview/" . $icon_name; ?>" />
					</td>
					<td valign="middle">	
						<input type="radio"  name="effect" value="without_effect" style="margin: auto;" onClick="location.href='options-general.php?page=ios-icon&a=without-effect';">	Without Effect			
					</td>
				</tr>
				</table>
		</div>
<?php			
			}
?>    

	</div>
<?php
}


?>