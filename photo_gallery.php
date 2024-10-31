<?php
/*
Plugin Name: PhotoStack Gallery
Plugin URI: http://www.wpoets.com/plugins/photostack-gallery-a-portfolio-and-image-gallery-plugin-for-wordpress/
Description: PhotoStack gallery plugin allows you to create image gallery that looks like photo stack.
Author: Savita at WPoets
Version: 0.4.1
Author URI: http://www.wpoets.com
*/

class WPPhotoGallery
{
	function __construct()
	{
		if(!defined('WP_GAL_URL')){define('WP_GAL_URL', plugin_dir_url( __FILE__ ));}				
		
		add_action('init', array(&$this,'register_photo_gallery')); 

		//shortcode	
		add_shortcode('show_gallery', array(&$this,'show_gallery'));		
	}
	function add_admin_menu()
	{		
		add_submenu_page('edit.php?post_type=potostackgallery','PhotoStack Gallery','Help', 9,'wpgal',array(&$this, 'photo_gallery'));
	}
	function photo_gallery()
	{
		?>
		<div class='wrap nosubsub'>
			<h2>PhotoStack Gallery Help</h2>
			<div style='margin-top:30px;width:600px;float:left;'>
				<strong>Q: How to use this Plugin?</strong><br>
				<strong>Ans:</strong> After installation of the Plugin it will create a custom post type Gallery in Admin left menu bar, you need to create various posts under this gallery and attach feature image with each post. For showing gallery you can upload various images from the post 'Upload' option. Now for showing gallery in frontend create a page or post and enter this shortcode [show_gallery] and you are done. 
			</div>
			<div style='width:300px;float:right;padding:20px;' class='updated'>				
				Pluign is developed by <a href='http://wpoets.com'>WPoets</a> Team <br><br>
				Suggestions and feedbacks are welcome, <br><a href="http://wpoets.com/">Contact us</a>
			</div>
		</div>
		<?php
	}
	function register_photo_gallery() {			 
		$labels = array(
			'name' => _x('Gallery', 'post type general name'),
			'singular_name' => _x('Gallery Item', 'post type singular name'),
			'add_new' => _x('Add New', 'Gallery '),
			'add_new_item' => __('Add New Gallery '),
			'edit_item' => __('Edit Gallery '),
			'new_item' => __('New Gallery'),
			'view_item' => __('View Gallery'),
			'search_items' => __('Search Gallery'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
		);
	 
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => plugin_dir_url( __FILE__ ). 'images/application_view_gallery.png',
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail')
		  ); 
		add_theme_support( 'post-thumbnails', array( 'post', 'potostackgallery' ) );
		register_post_type( 'potostackgallery' , $args );
		flush_rewrite_rules();				
	}
	function show_gallery($atts)
	{	
		
		$counts = wp_count_posts('potostackgallery');
		$posts = get_posts(array('post_type'=>'potostackgallery', 'numberposts'=> $counts->publish)); 		 
		?>
			<link rel="stylesheet" href="<?php echo plugin_dir_url( __FILE__ )?>css/photo.css">
			<script src="<?php echo plugin_dir_url( __FILE__ ) . 'js/photo.js'?>" type='text/javascript'>	</script>
			<div id="ps_slider" class="ps_slider">
				<a class="prev disabled"></a>
				<a class="next disabled"></a>
				<div id="ps_albums" >
					<?php foreach($posts as $post) 
					{ 
					?>
						<div class="ps_album" id='album_<?php echo $post->ID?>' style="opacity:0;" ><?php echo get_the_post_thumbnail($post->ID,array(137,137)); ?>
							<div class="ps_desc"><h2><?php echo $post->title ?></h2>
							<span><?php echo $post->post_content ?></span></div>
						</div>			
					<?php 
					}
					?>
					
				</div>	
			</div>
				<div id="ps_overlay" class="ps_overlay" style="display:none;"></div>
				<a id="ps_close" class="ps_close" style="display:none;"></a>
				<div id="ps_container" class="ps_container" style="display:none;">
				<a id="ps_next_photo" class="ps_next_photo" style="display:none;"></a>
				</div>
			
		
		<?php
	}
	function photostack()
	{
		global $wpdb, $post;	
		$gallery_post_id = substr($_POST['postid'],6, strlen($_POST['postid']) );
				
		$sql ='SELECT ID, post_title, post_name, post_content, post_parent, guid, menu_order FROM ' . $wpdb->posts . ' WHERE post_parent='.$gallery_post_id.' AND post_type="attachment" AND post_mime_type LIKE "imag%" ORDER BY menu_order'; 	
		
		$images = $wpdb->get_results($sql);
		if(!empty($images))
		{
			$imageurl = array();
			foreach($images as $image)
			{
				if($image)
				{
					$image_arr = wp_get_attachment_image_src($image->ID,'medium');
					$imageurl[] = $image_arr[0];
				}
			}
			
			$encoded = json_encode($imageurl);
			echo $encoded;
		}
		
		exit;
	} 
	
}
$wpgal=new WPPhotoGallery();
add_action('admin_menu', array(&$wpgal, 'add_admin_menu'));


// if both logged in and not logged in users can send this AJAX request,
// add both of these actions, otherwise add only the appropriate one
add_action( 'wp_ajax_nopriv_myajax-submit', array(&$wpgal,'photostack'));
add_action( 'wp_ajax_myajax-submit', array(&$wpgal,'photostack'));	

wp_enqueue_script('jquery');	
wp_localize_script( 'jquery', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );	
?>