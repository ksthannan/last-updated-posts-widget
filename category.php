<?php

get_header(); ?>

<?php get_template_part( 'template-parts/featured-area'); ?>

<?php get_template_part( 'template-parts/ads/below-header' ); ?>

<?php if ( function_exists('yoast_breadcrumb') ) : ?>
	<?php yoast_breadcrumb('<div id="mks-breadcrumbs" class="container mks-bredcrumbs-container"><p id="breadcrumbs">','</p></div>'); ?>
<?php endif; ?>

<div id="content" class="container site-content">
	<?php global $vce_sidebar_opts; ?>
	<?php if ( $vce_sidebar_opts['use_sidebar'] == 'left' ) { get_sidebar(); } ?>

	<div id="primary" class="vce-main-content">

		<div class="main-box">

		<?php get_template_part( 'template-parts/archive-title' ); ?>

			<div class="main-box-inside">

			<?php if ( have_posts() ) : ?>

				<?php $ad_position = vce_get_option('ad_between_posts') ? absint( vce_get_option('ad_between_posts_position') ) : false ; ?>

				<?php $cat_posts = vce_get_category_layout(); ?>


			<?php 
				$q_cat = get_query_var('cat');
				$stk_args = array(
					'posts_per_page' => -1,
					'cat' => $q_cat
				);

				$stk_query = new WP_Query($stk_args);	
				
				
				while ( $stk_query->have_posts() ) : $stk_query->the_post(); 
				
					$get_sticky_cats = get_post_meta(get_the_ID(), '_sticky_with_categories', true);
					$cats_explode = explode(',', $get_sticky_cats);
				
					foreach($cats_explode as $cat){
						if(intval($q_cat) == intval(trim($cat))){
							get_template_part( 'template-parts/loops/layout-b'); 
						}
					}
				
				endwhile;
				
				wp_reset_postdata();
				
				?>	
				
	<?php $i = 0; while ( have_posts() ) : the_post(); 
				
				$get_sticky_cats = get_post_meta(get_the_ID(), '_sticky_with_categories', true);
				$cats_explode = explode(',', $get_sticky_cats);
				
				$post_available = false;

				foreach($cats_explode as $cat){
					
					if(intval($q_cat) == intval(trim($cat))){
						$post_available = false;
					}else{
						$post_available = true;
						break;
					}
				}	
				
				if($post_available):
					$i++;
					?>
				
					<?php echo vce_loop_wrap_div( $cat_posts, $i, count( $wp_query->posts )); ?>

					<?php get_template_part( 'template-parts/loops/layout-'.vce_module_layout($cat_posts, $i) ); ?>

					<?php if( $i === $ad_position ) { get_template_part('template-parts/ads/between-posts'); } ?>

					<?php if ( $i == ( count( $wp_query->posts ) ) ) : ?>
					</div>
					<?php endif;?>
				
				<?php endif;
			
				endwhile;  
				wp_reset_postdata();
			
				?>

				<?php get_template_part( 'template-parts/pagination/'.vce_get_category_pagination() ); ?>

			<?php else: ?>
					
				<?php get_template_part( 'template-parts/content-none'); ?>

			<?php endif; ?>

			</div>

		</div>

	</div>

	<?php if ( $vce_sidebar_opts['use_sidebar'] == 'right' ) { get_sidebar(); } ?>

</div>

<?php get_footer(); ?>