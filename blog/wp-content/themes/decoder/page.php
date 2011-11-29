<?php get_header(); ?>

<div id="content" class="clearfix">

	<div id="content-left">

<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
	
			<div id="post-<?php the_ID(); ?>" <?php if(function_exists('post_class')) : post_class(); else : echo 'class="post"'; endif; ?>>
			
				<h3 class="post-title"><?php the_title(); ?></h3>
				
				<?php the_content(); ?>
			
				<div class="clear"></div>
				
				<?php edit_post_link('[edit page]', '<p>', '</p>'); ?>
				
			</div>
		
		<?php endwhile; ?>
		
		<?php else : ?>
		
		<div class="box-left" id="searchform">

			<h3 class="post-title">Not found!</h3>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		
		</div>

<?php endif; ?>
	
	  </div><!-- end content-left -->
	  
	  <?php get_sidebar(); ?>
	
</div><!-- end content -->
	
<?php get_footer(); ?>