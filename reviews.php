<?php
/*
Plugin Name: AVVO Reviews Plugin for Lawyers
Plugin URI: http://wiserbrand.com/blog/
Description: Plugin to show your star Avvo rating anywhere throughout your website. Automatically updates rating as it changes on Avvo.
Version: 1.2
Author: Wiserbrand.com
Author URI: http://wiserbrand.com/
License: GPL2
*/
?>
<?php
/*  Copyright 2017 Dmitry Fedoryaka  (email : dmitry@wiserbrand.com)

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
?>
<?php
	function avvowiserreviews_function($atts) {
		$context = stream_context_create(array(
    		'http' => array(
        		'header' => array('User-Agent: '.$_SERVER['HTTP_USER_AGENT']),
    		),
		));
		extract(shortcode_atts(array(
		"acc" => '',
		"rating" => '',
		"limit" => '',
		), $atts));
	if(!function_exists('file_get_html')){
		include_once('simple_html_dom.php');
	}
	$accsubstr = substr_replace($acc, "", -5, 5);
	$link = $accsubstr."/reviews.html";
	$linkget = file_get_contents($link, false, $context);
	$accget = file_get_contents($acc, false, $context);
    $html = str_get_html($linkget);
	$main = str_get_html($accget);
	$firstpageh3 = $html->find('.pull-right h3');
	$firstpagetext = $html->find('.is-truncated');
	$firstpagestars = $html->find('.u-vertical-margin-2 .text-nowrap meta');
	$reviewsnumber = $main->find('.small');
	$muted = $main->find('.small .text-muted');
	$countmutes = count($muted);
	if($countmutes == 1){
		$pagenumber = ceil($reviewsnumber[1]->content/10);
	}
	if($countmutes == 0){
		$pagenumber = ceil($reviewsnumber[0]->content/10);
	}
	$counth3 = count($firstpageh3);
	$firstpagestarschunk = array_chunk($firstpagestars, 3);
	$first = '';
	if($rating>5){
		$first .= 'Only digits 1 to 5 are allowed at your shortcode.';
	}
	if($rating<0){
		$first .= 'Only digits 1 to 5 are allowed at your shortcode.';
	}
		for($i=0;$i<$counth3&&$firstpagestarschunk[$i][0]->content>=$rating&&$rating<=5&&$rating>=0;$i++){
			$first .= '<div>'.$firstpageh3[$i].'</div>';
			if($firstpagestarschunk[$i][0]->content == 5.0){
				$first .= '<div style="color:#f1c40f">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
			}
				if($firstpagestarschunk[$i][0]->content == 4.0){
			$first .= '<div style="color:#f1c40f">&#9733;&#9733;&#9733;&#9733;</div>';
				}
			if($firstpagestarschunk[$i][0]->content == 3.0){
				$first .= '<div style="color:#f1c40f">&#9733;&#9733;&#9733;</div>';
			}
				if($firstpagestarschunk[$i][0]->content == 2.0){
			$first .= '<div style="color:#f1c40f">&#9733;&#9733;</div>';
			}
				if($firstpagestarschunk[$i][0]->content == 1.0){
			$first .= '<div style="color:#f1c40f">&#9733;</div>';
			}
			$first .= "<div>".$firstpagetext[$i+1]."</div><br/><br/><br/>?!!%";
		}
	for($m=2;$m<=$pagenumber;$m++){
			$linkwithpage = $link."?page=".$m;
			$html2 = file_get_html($linkwithpage);
			$rev = $html2->find('.pull-right h3');
			$revtext = $html2->find('.is-truncated');
			$revstars = $html2->find('.u-vertical-margin-2 .text-nowrap meta');
			$countrev = count($rev);
			$revstarschunk = array_chunk($revstars, 3);
			for($k=0;$k<$countrev&&$revstarschunk[$k][0]->content>=$rating&&$rating<=5&&$rating>=0;$k++){
				$first .= '<div>'.$rev[$k].'</div>';
				if($revstarschunk[$k][0]->content == 5.0){
					$first .= '<div style="color:#f1c40f">&#9733;&#9733;&#9733;&#9733;&#9733;</div>';
				}
				if($revstarschunk[$k][0]->content == 4.0){
					$first .= '<div style="color:#f1c40f">&#9733;&#9733;&#9733;&#9733;</div>';
				}
				if($revstarschunk[$k][0]->content == 3.0){
					$first .= '<div style="color:#f1c40f">&#9733;&#9733;&#9733;</div>';
				}
				if($revstarschunk[$k][0]->content == 2.0){
					$first .= '<div style="color:#f1c40f">&#9733;&#9733;</div>';
				}
				if($revstarschunk[$k][0]->content == 1.0){
					$first .= '<div style="color:#f1c40f">&#9733;</div>';
				}
			 	$first .= '<div>'.$revtext[$k].'</div><br/><br/><br/>?!!%';
		     }
	}
$home = get_option('home');
$all_options = get_option('avvo_reviews_options');
if($home == $all_options['my_text']){
	if($limit != ''){
		$firstarray = explode("?!!%", $first);
		$firstarraychunk = array_chunk($firstarray, $limit);
		$firstfinal='';
		foreach($firstarraychunk[0] as $i){
			$firstfinal .= $i;
		}
		return $firstfinal;
	}
	return str_replace("?!!%","",$first);			
}
if($home != $all_options['my_text']){
return "Plugin is not activated";
}
	}
add_shortcode('reviews', 'avvowiserreviews_function');

$true_page = 'reviews.php';

function true_options() {
	global $true_page;
	add_options_page( 'AVVO Reviews', 'AVVO Reviews', 'manage_options', $true_page, 'true_option_page');  
}
add_action('admin_menu', 'true_options');
 
function true_option_page(){
	global $true_page;
	?><div class="wrap">
		<h2>Reviews plugin activation</h2><br>
		<p><b>Please enter your website URL (<?php echo get_option('home'); ?>)</b></p>
		<form style="margin: 0;
    			     position: relative;
    			     top: -13px;" 
		method="post" enctype="multipart/form-data" action="options.php">
			<?php 
			settings_fields('avvo_reviews_options');
			do_settings_sections($true_page);
			?>
			<?php 
				$home = get_option('home');
				$all_options = get_option('avvo_reviews_options');
				if($all_options == true){
				if($home != $all_options['my_text']){
					echo "<span style=\"color:red;\">Wrong URL</span>";
				}
				if($home == $all_options['my_text']){
					echo "<span style=\"color:green;\">Thank you for activating the plugin!</span>";
				}
				}
			?>
			<p>By clicking the button you agree that we will receive the URL of your website. We don’t support spam and will not give your website URL to the third parties.</p>
			<p class="submit">  
				<input type="submit" class="button-primary" value="<?php _e('Send email and activate') ?>" />  
			</p>
		</form>
		<p style="position: relative;bottom: 20px;">Also you can subscribe to our <a href='http://wiserbrand.com/email-subscription'>SEO tips.</a></p>
		<?php 
			if($home == $all_options['my_text']){
			mail("wiserbrandfeedback@gmail.com", "Reviews plugin", $all_options['my_text']);
			}
		?>
	</div><?php
}
function avvoreviews_option_settings() {
	global $true_page;
	register_setting( 'avvo_reviews_options', 'avvo_reviews_options', 'true_validate_settings' );
 
	add_settings_section( 'true_section_1', '', '', $true_page );
 
	$true_field_params = array(
		'type'      => 'text',
		'id'        => 'my_text',
		'desc'      => '',
		'label_for' => 'my_text'
	);
	add_settings_field( 'my_text_field', '', 'true_option_display_settings', $true_page, 'true_section_1', $true_field_params );
}
add_action( 'admin_init', 'avvoreviews_option_settings' );

function true_option_display_settings($args) {
	extract( $args );
 
	$option_name = 'avvo_reviews_options';
 
	$o = get_option( $option_name );
 
	switch ( $type ) {  
		case 'text':  
			$o[$id] = esc_attr( stripslashes($o[$id]) );
			echo "<input style=\"position: relative;right: 223px;\" class='regular-text' type='text' id='$id' name='" . $option_name . "[$id]' value='$o[$id]' />";  
			echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
		break;
	}
}

function true_validate_settings($input) {
	foreach($input as $k => $v) {
		$valid_input[$k] = trim($v);
	}
	return $valid_input;
}
?>