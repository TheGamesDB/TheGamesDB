<?php get_header(); ?>

<div id="content" class="clearfix">

	<div id="content-left">

<?php if (have_posts()) : ?>

		<h2 id="category-title">Search Results for <em><?php echo $s; ?></em></h2>

		<?php while (have_posts()) : the_post(); ?>
	
			<div id="post-<?php the_ID(); ?>" <?php if(function_exists('post_class')) : post_class(); else : echo 'class="post"'; endif; ?>>
			
				<h3 class="post-title">
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
				</h3>
				
				<?php the_content(__('&rsaquo; Continue reading')); ?>
				
				<?php the_tags('<p class="tags">Tags: ', ', ', '</p>'); ?>
				
				<?php edit_post_link('[edit post]', '<p>', '</p>'); ?>
				
				<div class="meta">
					<span class="meta-date"><?php the_time('l, F jS, Y'); ?></span>
					<span class="meta-categories"><?php the_category(', '); ?></span>
					<span class="meta-comments"><?php comments_popup_link(__('No Comments'), __('1 Comment'), __('% Comments')); ?></span>
				</div>
				
			</div>
		
		<?php endwhile; ?>
		
		<div class="box-left navigation">
		
        	<?php next_posts_link('&laquo; Previous Entries') ?> <?php previous_posts_link('Next Entries &raquo;') ?>
        	
		</div>
		
		<?php else : ?>
		
		<div class="box-left" id="searchform">

			<h3>Not found!</h3>
			<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
			<?php include (TEMPLATEPATH . "/searchform.php"); ?>
		
		</div>

<?php endif; ?>
	
	  </div><!-- end content-left -->
	  
	  <?php get_sidebar(); ?>
	  
	  <div class="clear"></div>
	
</div><!-- end content -->
	
<?php get_footer(); ?>