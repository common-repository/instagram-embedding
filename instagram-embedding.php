<?php
/*
Plugin Name: Instagram Embedding
Plugin URI: http://wp-time.com/instagram-embedding/
Description: One shortcode to embedding instagram images with full customize.
Version: 1.5
Author: Qassim Hassan
Author URI: http://qass.im
License: GPLv2 or later
*/

/*  Copyright 2015  Qassim Hassan  (email : qassim.pay@gmail.com)

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
// WP Time Page
if( !function_exists('WP_Time_Ghozylab_Aff') ) {
	function WP_Time_Ghozylab_Aff() {
		add_menu_page( 'WP Time', 'WP Time', 'update_core', 'WP_Time_Ghozylab_Aff', 'WP_Time_Ghozylab_Aff_Page');
		function WP_Time_Ghozylab_Aff_Page() {
			?>
            	<div class="wrap">
                	<h2>WP Time</h2>
                    
					<div class="tool-box">
                		<h3 class="title">Thanks for using our plugins!</h3>
                    	<p>For more plugins, please visit <a href="http://wp-time.com" target="_blank">WP Time Website</a> and <a href="https://profiles.wordpress.org/qassimdev/#content-plugins" target="_blank">WP Time profile on WordPress</a>.</p>
                        <p>For contact or support, please visit <a href="http://wp-time.com/contact/" target="_blank">WP Time Contact Page</a>.</p>
					</div>
                    
            	<div class="tool-box">
					<h3 class="title">Recommended Links</h3>
					<p>Get collection of 87 WordPress themes for $69 only, a lot of features and free support! <a href="http://j.mp/ET_WPTime_ref_pl" target="_blank">Get it now</a>.</p>
					<p>See also:</p>
						<ul>
							<li><a href="http://j.mp/GL_WPTime" target="_blank">Must Have Awesome Plugins.</a></li>
							<li><a href="http://j.mp/CM_WPTime" target="_blank">Premium WordPress themes on CreativeMarket.</a></li>
							<li><a href="http://j.mp/TF_WPTime" target="_blank">Premium WordPress themes on Themeforest.</a></li>
							<li><a href="http://j.mp/CC_WPTime" target="_blank">Premium WordPress plugins on Codecanyon.</a></li>
							<li><a href="http://j.mp/BH_WPTime" target="_blank">Unlimited web hosting for $3.95 only.</a></li>
						</ul>
					<p><a href="http://j.mp/GL_WPTime" target="_blank"><img src="<?php echo plugins_url( '/banner/global-aff-img.png', __FILE__ ); ?>" width="728" height="90"></a></p>
					<p><a href="http://j.mp/ET_WPTime_ref_pl" target="_blank"><img src="<?php echo plugins_url( '/banner/570x100.jpg', __FILE__ ); ?>"></a></p>
                    <p><a href="http://j.mp/Avada_WP_Theme" target="_blank"><img src="<?php echo plugins_url( '/banner/avada.jpg', __FILE__ ); ?>"></a></p>
				</div>
                
                </div>
			<?php
		}
	}
	add_action( 'admin_menu', 'WP_Time_Ghozylab_Aff' );
}


/* Include Instagram Embedding Styles */
function instagram__embedding__style(){
	wp_enqueue_style( 'instagram-embedding-fontello', plugins_url( '/css/fontello.css', __FILE__ ), false, null);
	wp_enqueue_style( 'instagram-embedding-style', plugins_url( '/css/instagram-embedding-style.css', __FILE__ ), false, null);
}
add_action('wp_enqueue_scripts', 'instagram__embedding__style');


/* Instagram Embedding Shortcode */
function instagram__embedding__shortcode($atts, $content = null){ // Shortcode Function Start
	
	Extract(
		shortcode_atts(
			array(
				"url"			=>	"", // $url var, default is none
				"before"		=>	"", // $before var, default is none, option: "lightbox" to activate lightbox link
				"wrap_margin"	=>	"20", // $wrap_margin var, default is 20px for margin top and bottom only
				"wrap_bg"		=>	"#ffffff", // $wrap_bg var, default color is white #ffffff
				"color"			=>	"#3f729b", // $color var, default color is #3f729b
				"text_color"	=>	"#ffffff", // $text_color var, default color is white #ffffff
				"caption"		=>	"full", // $caption var, default full, options: false, excerpt, full
				"icon_size"		=>	"34", // $icon_size var, default is 34px
				"font_size"		=>	"14", // $font_size var, default is 14px
				"s"				=>	"f" // $s var, default is f
			),$atts
		)
	);
	
	if( !empty($url) and preg_match("/(instagram.com)|(instagr.am)+/", $url) ){ // Check if correct instagram link
	
		$instagram_api	= wp_remote_get("http://api.instagram.com/oembed?url=$url"); // Instagram API Link with $url var
		$retrieve		= wp_remote_retrieve_body( $instagram_api ); // Retrieve Body
		$response		= json_decode($retrieve); // JSON Response
		if( preg_match('/(No Media Match)|(No URL Match)+/', $retrieve) ){ // If deleted link or error link
			return '<p>Sorry! Maybe error link or deleted link.</p>';
			return false;
		}else{ // If not deleted link
			$auther_url		= $response->author_url; // Get Auther Link
			$auther_name	= $response->author_name; // Get Auther Name
			$thumbnail_url	= $response->thumbnail_url; // Get Image Link
			$get_caption	= $response->title; // Get Image Caption
			$caption_strlen	= mb_strlen( utf8_decode($get_caption) ); // Count Characters Of Caption
			$emoji_regex = array(
				'/[\x{1F600}-\x{1F64F}]/u',
				'/[\x{1F300}-\x{1F5FF}]/u',
				'/[\x{1F680}-\x{1F6FF}]/u',
				'/[\x{2600}-\x{26FF}]/u',
				'/[\x{2700}-\x{27BF}]/u'); // Array For Emoji Icons
			$clean_caption = preg_replace($emoji_regex, '', $get_caption); // Remove Emoji Icons
			
			/* Caption */
			if($caption == 'false' or empty($get_caption) ){
				$div_caption  = null;
			}
			elseif($caption == 'excerpt'){
				if($caption_strlen >= 41){ // If characters of caption more than 41 or equal 41
					$caption_text = mb_substr($clean_caption, 0, 40, 'utf-8').' <a title="Read More" class="read_more_caption" href="'.$url.'" target="_blank" style="color:'.$text_color.';">...</a>';
					$div_caption  = '<div class="instagram_image_caption" style="color:'.$text_color.';background-color: '.$color.';font-size:'.$font_size.'px;">'.$caption_text.'</div>';
				}
				if($caption_strlen < 41){ // If characters of caption less than 41
					$caption_text = $clean_caption;
					$div_caption  = '<div class="instagram_image_caption" style="color:'.$text_color.';background-color: '.$color.';font-size:'.$font_size.'px;">'.$caption_text.'</div>';
				}
			}
			else{
				$caption_text = $clean_caption;
				$div_caption  = '<div class="instagram_image_caption" style="color:'.$text_color.';background-color: '.$color.';font-size:'.$font_size.'px;">'.$caption_text.'</div>';
			}
			
			/* Lightbox */
			if( $before == "lightbox" ){ // Check if lightbox is activate
				$a_start 	=	'<a class="instagram_before lightbox_true" href="'.$thumbnail_url.'">';
				$a_end 		=	'</a>';
			}
			elseif( !empty($before) ){ // Check if have link before image
				$a_start 	=	'<a class="instagram_before" href="'.$before.'">';
				$a_end 		=	'</a>';
			}
			else{
				$a_start 	=	null;
				$a_end 		=	null;
			}
			
			/* Result */
			
			if( $s == 't' or $s == 'T' ){ // if standard image
				return '<p><img class="standard-instagram-image" src="'.$thumbnail_url.'"></p>';
				return false;
			}
			
			// if not standard image
			return '
				<div class="instagram_embedding_wrap" style="margin:'.$wrap_margin.'px 0px;border:1px solid '.$color.';background-color:'.$wrap_bg.';">
					<div class="instagram_embedding_content">
						<div class="instagram_embedding_header">
							<div class="instagram_embedding_icon" style="color: '.$color.' !important;font-size:'.$icon_size.'px;"></div>
							<a class="instagram_author_url" target="_blank" href="'.$auther_url.'" style="color:'.$color.';font-size:'.$font_size.'px;">By '.$auther_name.'</a>
						</div>
						'.$a_start.'
						<img id="instagram_image_link" class="instagram_image_link" alt="'.$response->title.'" src="'.$thumbnail_url.'">
						'.$a_end.'
					</div>
					'.$div_caption.'
				</div>';
		} // End if deleted link or error link
	
	} // End if correct instagram link
	
	elseif( !preg_match("/(http:\/\/)|(http:\/\/)+/", $url) or preg_match("/(https:\/\/)|(https:\/\/)+/", $url) ){ // If instagram link without http://
		return '<p>Please enter instagram link with http://</p>';
		return false;
	}
	
	else{ // If error instagram link
		return '<p>Please enter correct instagram link.</p>';
	}
	
} // Shortcode Function End
add_shortcode("instagram_emb", "instagram__embedding__shortcode"); // Add shortcode [instagram_emb url=""]

?>