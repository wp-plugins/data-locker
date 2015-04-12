<?php
/*
Plugin Name: Data Locker
Plugin URI: https://spideb.in/data-locker
Description: Lock your important data and let the user share or like first to view it.
Version: 1.0.0
Author: Bhuvnesh Gupta
Author URI: https://wordpress.org/support/profile/bhuvnesh
*/


$wp_scripts = new WP_Scripts();
wp_enqueue_script("jquery");
wp_deregister_script('facebooksdk');
wp_register_script('facebooksdk', 'http://connect.facebook.net/en_US/all.js#xfbml=1');
wp_enqueue_script("facebooksdk");
wp_deregister_script('plusone');
wp_register_script('plusone', 'https://apis.google.com/js/plusone.js');
wp_enqueue_script("plusone");
wp_deregister_script('twittersdk');
wp_register_script('twittersdk', 'https://platform.twitter.com/widgets.js');
wp_enqueue_script("twittersdk");


class DataLockerClass {
	
	function __construct() {
		if (is_admin()) {
			add_filter('mce_external_plugins', array(&$this, "mce_external_plugin"));
			add_filter('mce_buttons', array(&$this, "mce_button"), 0);
			add_action('wp_ajax_datalocker', array(&$this, "datalocker_callback"));
			add_action('wp_ajax_nopriv_datalocker', array(&$this, "datalocker_callback"));
		} else {
			add_action("wp_head", array(&$this, "front_header"));
			add_action("wp_footer", array(&$this, "front_footer"));
			add_shortcode('data-locker', array(&$this, "data_locker_fun"));
		}
	}

	function mce_button($buttons) {
		array_push($buttons, "separator", "datalockerplugin");
		return $buttons;
	}

	function mce_external_plugin($plugin_array){
		$url = plugins_url('js/button.js', __FILE__);
		$plugin_array['datalockerplugin'] = $url;
		return $plugin_array;
	}

	function datalocker_callback() {
		/* logic will change here */
		global $wpdb;
		$page_url			=	urlencode($_POST['page_url']);
		$name				=	sanitize_text_field($_POST['name']);
		setcookie("datalocker_".$page_url, "bhuvi", time()+60*60*24*30, "/");		
		die;
	}	

	function front_header() {
		/* add css file */
		wp_register_style('datalocker_css', plugins_url('custom-style.css', __FILE__) );
		wp_enqueue_style('datalocker_css' );
	}

	function front_footer() {
		echo '
		<div id="fb-root"></div>
		<script type="text/javascript">
			FB.XFBML.parse();
		</script>';
	}
	
	function data_locker_fun($_atts,	$content=null) {
	
		global $wpdb;
		/* Defining Variables */
		$message	=	'Data Locker';
		$page_url = get_permalink(get_the_ID());
		$cookie_name 	=	"datalocker_".$page_url;
		$cookie_name	=	str_replace('.','_',$cookie_name);
		$cookie_value = "";	
				

		if(!empty($_COOKIE[$cookie_name])){
			$cookie_value = $_COOKIE[$cookie_name];
		}
		
		if($cookie_value == "bhuvi"){
			$content = do_shortcode($content);
		}
		else {
			
			$content = '
				<div class="datalocker_box">
					'.$message.'
					<div><fb:like id="fbLikeButton" href="'.$page_url.'" show_faces="false" width="450"></fb:like></div>
					<hr>
					<div><g:plusone size="medium" annotation="inline" callback="datalocker_plusone" href="'.$page_url.'"></g:plusone></div>
					<hr>
					<div><a href="http://twitter.com/share" class="twitter-share-button"  data-url="'.$page_url.'" data-count="horizontal" data-lang="en">Tweet</a></div>				
				</div>';
			?>
			<script type="text/javascript">
				var ajaxurl	=	'<?php echo admin_url('admin-ajax.php')?>';
				function setCookie(name){
					var data = {action: "datalocker",page_url: '<?php echo $page_url; ?>', name: name};
					jQuery.post(ajaxurl, data, function(response) {
						setTimeout(function() {								
							window.location.reload();					 
						}, '2000');											
					});
				}
				function datalocker_plusone(plusone) {
					if (plusone.state == "on") {
						setCookie('google');
					}
				}
				FB.init();
				jQuery(document).ready(function() {
					FB.Event.subscribe("edge.create", function(href, widget) {
						setCookie('facebook');
					});
					twttr.ready(function (twttr) {
						twttr.events.bind("tweet", function(event) {
							setCookie('twitter');
						});
					});
				});
			</script>
			<?php 
		}
		
		return $content;
	}
	
}
$datalocker = new DataLockerClass();
?>
