<?php 
/*
Plugin Name: Last updated post widget
Description: Last updated post widget
Version: 1.0.0
Author: ###
Author URI: #
License: GPLv2
Text Domain: voice
 */

 
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Meta field for updated post 
 * */
// Add the metabox to the post editor screen
function add_updated_info_metabox() {
    add_meta_box(
        'updated_info_metabox',           // Unique ID for the metabox
        'Last Updated Info',                   // Title of the metabox
        'display_updated_info_metabox',   // Callback function to display the metabox content
        'post',                    // Post type where you want to add the metabox
        'side',                    // Position of the metabox (e.g., 'side', 'normal', 'advanced')
        'high'                  // Priority of the metabox
    );
}
add_action('add_meta_boxes', 'add_updated_info_metabox');

// Callback function to display the metabox content
function display_updated_info_metabox($post) {
    // Retrieve the current value of the "ready" checkbox
    $updated_info = get_post_meta($post->ID, '_updated_info', true);
	$updated_info_time = get_post_meta($post->ID, '_updated_info_time', true);
    ?>
	<div class="updated_info <?php echo $updated_info ? 'checked' : '';?>">
		<?php if($updated_info_time):
		
	$timestamp = strtotime($updated_info_time);
	$formatted_date = date('l, F j, Y, \a\t g a', $timestamp);

		?>
		<p>
			<?php _e('Last updated:', 'voice');?> <br> <?php echo $formatted_date;?>
		</p>
		<?php endif;?>
	</div>
    <label for="updated_info_checkbox">
        <input type="checkbox" id="updated_info_checkbox" name="updated_info_checkbox" <?php checked($updated_info, 'on'); ?> />
        <?php _e('Select this post as last updated', 'voice');?>
    </label>
    <?php
}

// Save the checkbox value when the post is saved
function save_updated_info_metabox($post_id) {
    if (isset($_POST['updated_info_checkbox'])) {
        update_post_meta($post_id, '_updated_info', 'on');
		update_post_meta($post_id, '_updated_info_time', current_time('mysql'));
    } else {
        delete_post_meta($post_id, '_updated_info');
    }
}
add_action('save_post', 'save_updated_info_metabox');

function consider_last_updated_post_widget($atts){
	ob_start();
	
	$atts = shortcode_atts(	
        array(
			'heading_color' => '',
            'post_color' => '',
            'post_hover_color' => '',
			'font_family' => '',
			'show_date' => 'yes',
        ),
        $atts
    );
	
	$args = array(
		'post_type' => 'post', // Change this to your custom post type if needed
		'posts_per_page' => 10, // Number of recent posts to display
		'meta_query' => array(
			array(
				'key' => '_updated_info', // Replace with your meta key
				'value' => 'on', // Replace with your specific meta value
				'compare' => '=', // Use '=' for exact match
			),
		),
		'orderby' => 'date',
		'order' => 'DESC',
	);

	$recent_posts = new WP_Query($args);
	?>
<style>
	.widget.last_updated_posts h2.wp-block-heading{
		<?php if($atts['heading_color']):?>
		color: <?php echo $atts['heading_color'];?> !important;
		<?php endif;?>
		<?php if($atts['font_family']):?>
		font-family: <?php echo $atts['font_family'];?> !important;
		<?php endif;?>
	}
	.widget.last_updated_posts ul li a{
		<?php if($atts['post_color']):?>
		color: <?php echo $atts['post_color'];?> !important;
		<?php endif;?>
		<?php if($atts['font_family']):?>
		font-family: <?php echo $atts['font_family'];?> !important;
		<?php endif;?>
	}
	.widget.last_updated_posts ul li a:hover{
		<?php if($atts['post_hover_color']):?>
		color: <?php echo $atts['post_hover_color'];?> !important;
		<?php endif;?>
	}
</style>
<div class="widget widget_block last_updated_posts">
<h2 class="wp-block-heading"><?php _e('Last Updated Posts', 'voice');?></h2>
<ul class="wp-block-latest-posts__list wp-block-latest-posts">
	
	
<?php 
	if ($recent_posts->have_posts()) {
		while ($recent_posts->have_posts()) {
			
			$recent_posts->the_post();
			
			$updated_info_time = get_post_meta(get_the_ID(), '_updated_info_time', true);
			$timestamp = strtotime($updated_info_time);
			$formatted_date = date('l, F j, Y, \a\t g a', $timestamp);
			
			if($atts['show_date'] !== 'no'){
				$date_preview = '<div class="last_updated_date_widget"><p>' . $formatted_date . '</p></div>';
			}else{
				$date_preview = '';
			}
			// Display the post title or other post content as needed
			echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> ' . $date_preview . '</li>';
			
		}
		wp_reset_postdata();
	} else {
		echo '<li>' . __('No recent posts found with the specified meta value.', 'voice') . '</li>';
	}
?>
	</ul>
	</div>
	<?php 
	return ob_get_clean();
}
add_shortcode('last_updated_posts', 'consider_last_updated_post_widget');

function consider_update_time(){
	ob_start();
	global $post;
	$updated_info_time = get_post_meta($post->ID, '_updated_info_time', true);
	$timestamp = strtotime($updated_info_time);
	$formatted_date = date('l, F j, Y, \a\t g a', $timestamp);
	echo $formatted_date;
	return ob_get_clean();
}
add_shortcode('last_updated_time', 'consider_update_time');

// function consider_modify_archive_query($query) {
//     if ( $query->is_archive() && $query->is_main_query() ) {
//         $category_posts = array();
//         $sticky_posts = get_option( 'sticky_posts' );

//         if ( $sticky_posts ) {
//             foreach ( $sticky_posts as $sticky_post ) {
//                 $sticky_post_category = get_the_category( $sticky_post );
//                 if ( ! empty( $sticky_post_category ) ) {
//                     $category_id = $sticky_post_category[0]->cat_ID;
//                     if ( ! in_array( $category_id, $category_posts ) ) {
//                         $category_posts[] = $category_id;
//                     }
//                 }
//             }
//         }
		
// 		$query->set( 'post__not_in', $sticky_posts );
		
//     }
// }
// add_action( 'pre_get_posts', 'consider_modify_archive_query' );

function consider_admin_scripts(){
	?>
<style>
.updated_info.checked {
	background: #523F6D;
	color: #fff;
	padding: 5px 10px !important;
	margin-bottom: 10px;
	text-align: center;
}
	.updated_info.checked p {
	font-weight: bold;
	font-size: 20px;
}
</style>
<?php 
}
add_action('admin_head', 'consider_admin_scripts', 99);

function consider_frontend_scripts(){
	?>
<style>
/* 	.last_updated_posts{
		padding:20px;
	} */
</style>
<?php 
}
add_action('wp_head', 'consider_frontend_scripts', 99);
